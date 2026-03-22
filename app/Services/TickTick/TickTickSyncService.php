<?php

namespace App\Services\TickTick;

use App\Models\TickTickProject;
use App\Models\TickTickTag;
use App\Models\TickTickTask;
use Illuminate\Support\Carbon;

class TickTickSyncService
{
    public function __construct(private readonly TickTickClient $client) {}

    public function syncAll(): void
    {
        $this->syncProjects();
        //$this->syncTags();
        $this->syncTasks();
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
