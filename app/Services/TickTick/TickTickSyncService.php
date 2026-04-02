<?php

namespace App\Services\TickTick;

use App\Models\TickTickHabit;
use App\Models\TickTickHabitCheckIn;
use App\Models\TickTickProject;
use App\Models\TickTickTag;
use App\Models\TickTickTask;
use Illuminate\Support\Carbon;

class TickTickSyncService
{
    public function __construct(
        private readonly TickTickClient $client,
        private readonly TickTickUserClient $userClient,
    ) {}

    public function syncAll(): void
    {
        $this->syncProjects();
        // $this->syncTags();
        $this->syncTasks();
        $this->syncHabits();
    }

    public function syncProjects(): void
    {
        $projects = $this->client->get('/open/v1/project');

        $rows = array_map(static fn (array $project) => [
            'ticktick_id' => $project['id'],
            'name' => $project['name'],
            'color' => $project['color'] ?? null,
            'view_mode' => $project['viewMode'] ?? null,
            'kind' => $project['kind'] ?? null,
            'is_closed' => $project['closed'] ?? false,
            'sort_order' => $project['sortOrder'] ?? null,
            'synced_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'created_at' => Carbon::now(),
        ], $projects);

        if (empty($rows)) {
            return;
        }

        TickTickProject::query()->upsert($rows, ['ticktick_id'], [
            'name', 'color', 'view_mode', 'kind', 'is_closed', 'sort_order', 'synced_at', 'updated_at',
        ]);
    }

    public function syncTags(): void
    {
        $tags = $this->client->get('/api/v2/tags');

        $rows = array_map(static fn (array $tag) => [
            'name' => $tag['name'],
            'color' => $tag['color'] ?? null,
            'sort_order' => $tag['sortOrder'] ?? null,
            'synced_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'created_at' => Carbon::now(),
        ], $tags);

        if (empty($rows)) {
            return;
        }

        TickTickTag::query()->upsert($rows, ['name'], [
            'color', 'sort_order', 'synced_at', 'updated_at',
        ]);
    }

    public function syncTasks(?string $projectTickTickId = null): void
    {
        TickTickTask::truncate();
        $projectIds = $projectTickTickId
            ? [$projectTickTickId]
            : TickTickProject::query()->pluck('ticktick_id')->toArray();

        foreach ($projectIds as $projectId) {
            $this->syncTasksForProject($projectId);
        }
    }

    public function syncHabits(): void
    {
        // TickTickHabit::truncate();
        $habits = $this->userClient->get('/api/v2/habits', headers: [
            'Accept' => 'application/json, text/plain, */*',
        ]);

        $habitIds = array_map(static fn ($habit) => $habit['id'], $habits);
        $this->syncHabitList($habits);

        $this->syncHabitCheckIns($habitIds);

    }
    private function syncHabitList(array $habits): void
    {
        $rows = array_map(static fn (array $habit) => [
            'ticktick_id' => $habit['id'],
            'name' => $habit['name'],
            'color' => $habit['color'] ?? null,
            'status' => $habit['status'] ?? null,
            'type' => $habit['type'] ?? null,
            'goal' => $habit['goal'] ?? false,
            'step' => $habit['step'] ?? null,
            'unit' => $habit['unit'] ?? null,
            'repeat_rule' => $habit['repeatRule'] ?? null,
            'encouragement' => $habit['encouragement'] ?? null,
            'reminders' => ! empty($habit['reminders']) ? implode(';', $habit['reminders']) : null,
            'synced_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'created_at' => Carbon::now(),
        ], $habits);

        if (empty($rows)) {
            return;
        }

        TickTickHabit::query()->upsert($rows, ['ticktick_id'], [
            'name', 'color', 'status', 'type', 'goal', 'step', 'repeat_rule', 'encouragement', 'reminders', 'unit', 'synced_at', 'updated_at',
        ]);
    }

    private function syncHabitCheckIns(array $habitIds): void
    {
        if (empty($habitIds) || empty($habitIds[0])) {
            return;
        }

        $checkIns = $this->userClient->post('/api/v2/habitCheckins/query',
            data: [
                'afterStamp' => Carbon::yesterday()->format('Ymd'),
                'habitIds' => $habitIds,
            ],
            headers: [
                'Accept' => 'application/json, text/plain, */*',
                'Content-Type' => 'application/json;charset=UTF-8',
                'Referer' => 'https://ticktick.com',
                'X-Tz' => 'America/New_York',
            ]);
        if(!isset($checkIns['checkins'])){
            return;
        }

        $rows = array_map(static function (array $habitCheckIns) {
            if (empty($habitCheckIns)) {
                return [];
            }

            return array_map(static fn (array $checkIn) => [
                'ticktick_id' => $checkIn['id'],
                'habit_id' => $checkIn['habitId'],
                'checkin_stamp' => $checkIn['checkinStamp'],
                'checkin_time' => $checkIn['checkinTime'],
                'op_time' => $checkIn['opTime'],
                'value' => $checkIn['value'],
                'goal' => $checkIn['goal'] ?? '',
                'status' => $checkIn['status'] ?? '',
                'synced_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_at' => Carbon::now(),
            ], $habitCheckIns);
        }, $checkIns['checkins']);

        $rows = array_filter($rows, static fn (array $checkIn) => ! empty($checkIn));

        if (empty($rows)) {
            return;
        }

        foreach ($rows as $row) {
            TickTickHabitCheckIn::query()->upsert($row, ['ticktick_id'], [
                'checkin_stamp', 'checkin_time', 'op_time', 'value', 'goal', 'status', 'synced_at', 'updated_at',
            ]);
        }
    }

    private function syncTasksForProject(string $projectTickTickId): void
    {

        $data = $this->client->get("/open/v1/project/{$projectTickTickId}/data");
        $tasks = $data['tasks'] ?? [];

        if (empty($tasks)) {
            return;
        }

        $projectLocalId = TickTickProject::query()
            ->where('ticktick_id', $projectTickTickId)
            ->value('id');

        $rows = array_map(static fn (array $task) => [
            'project_id' => $projectLocalId,
            'ticktick_id' => $task['id'],
            'title' => $task['title'] ?? '',
            'content' => $task['content'] ?? null,
            'description' => $task['desc'] ?? null,
            'status' => $task['status'] ?? 0,
            'priority' => $task['priority'] ?? 0,
            'start_date' => isset($task['startDate']) ? Carbon::parse($task['startDate']) : null,
            'due_date' => isset($task['dueDate']) ? Carbon::parse($task['dueDate']) : null,
            'completed_time' => isset($task['completedTime']) ? Carbon::parse($task['completedTime']) : null,
            'timezone' => $task['timeZone'] ?? null,
            'is_all_day' => $task['isAllDay'] ?? false,
            'sort_order' => $task['sortOrder'] ?? null,
            'tags' => json_encode($task['tags'] ?? []),
            'items' => json_encode($task['items'] ?? []),
            'repeat_flag' => $task['repeatFlag'] ?? null,
            'synced_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'created_at' => Carbon::now(),
        ], $tasks);

        $rows = array_filter($rows, static fn (array $task) => $task['due_date'] === null || Carbon::now()->endOfDay()->gte($task['due_date']));

        TickTickTask::query()->upsert($rows, ['ticktick_id'], [
            'project_id', 'title', 'content', 'description', 'status', 'priority',
            'start_date', 'due_date', 'completed_time', 'timezone', 'is_all_day',
            'sort_order', 'tags', 'items', 'repeat_flag', 'synced_at', 'updated_at',
        ]);
    }
}
