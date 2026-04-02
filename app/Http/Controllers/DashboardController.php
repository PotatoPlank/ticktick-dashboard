<?php

namespace App\Http\Controllers;

use App\Models\TickTickHabit;
use App\Models\TickTickTask;
use App\Services\TickTick\TickTickUserClient;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __invoke(Request $request, TickTickUserClient $client)
    {
        return Inertia::render('dashboard', [
            'stats' => Inertia::defer(static fn () => [
                'total_active' => TickTickTask::query()->active()->count(),
                'due_today' => TickTickTask::query()->active()->dueToday()->count(),
                'overdue' => TickTickTask::query()->overdue()->count(),
                'completed_this_week' => TickTickTask::query()->completed()
                    ->where('completed_time', '>=', now()->startOfWeek())
                    ->count(),
            ]),
            'habits' => Inertia::defer(static function () {
                $habits = TickTickHabit::all()->filter(static fn ($habit) => ! $habit->completed);
                $now = now('America/New_York');
                $morningStart = $now->setTime(0, 0);
                $morningEnd = $now->setTime(11, 59);
                $afternoonStart = $now->setTime(12, 0);
                $afternoonEnd = $now->setTime(16, 59);
                $eveningStart = $now->setTime(17, 00);
                $eveningEnd = $now->setTime(23, 59);
                $morningHabits = $habits
                    ->filter(
                        static fn ($habit) => $habit->remindAt->reduce(
                            static fn ($carry, $reminderDate) => $carry || $reminderDate->isBetween($morningStart, $morningEnd), false
                        )
                    );

                $afternoonHabits = $habits
                    ->filter(
                        static fn ($habit) => $habit->remindAt->reduce(
                            static fn ($carry, $reminderDate) => $carry || $reminderDate->isBetween($afternoonStart, $afternoonEnd), false
                        )
                    );
                $eveningHabits = $habits
                    ->filter(
                        static fn ($habit) => $habit->remindAt->reduce(
                            static fn ($carry, $reminderDate) => $carry || $reminderDate->isBetween($eveningStart, $eveningEnd), false
                        )
                    );

                return [
                    'morning' => $morningHabits->values(),
                    'afternoon' => $afternoonHabits->values(),
                    'evening' => $eveningHabits->values(),
                    'all_day' => $habits->values(),
                ];
            }),
            'today_tasks' => Inertia::defer(static function () {
                return TickTickTask::query()
                    ->active()
                    ->dueToday()
                    ->dueAllDay()
                    ->with('project')
                    ->orderBy('priority', 'desc')
                    ->get();
            }
            ),
            'morning_tasks' => Inertia::defer(static function () {
                return TickTickTask::query()
                    ->active()
                    ->dueMorning()
                    ->with('project')
                    ->orderBy('priority', 'desc')
                    ->orderBy('due_date')
                    ->get();
            }
            ),
            'afternoon_tasks' => Inertia::defer(static function () {
                if (\Illuminate\Support\now(config('app.user_timezone'))->isBefore('11:00')) {
                    return [];
                }

                return TickTickTask::query()
                    ->active()
                    ->dueAfterNoon()
                    ->with('project')
                    ->orderBy('priority', 'desc')
                    ->orderBy('due_date')
                    ->get();
            }
            ),
            'evening_tasks' => Inertia::defer(static function () {
                if (\Illuminate\Support\now(config('app.user_timezone'))->isBefore('16:00')) {
                    return [];
                }

                return TickTickTask::query()
                    ->active()
                    ->dueEvening()
                    ->with('project')
                    ->orderBy('priority', 'desc')
                    ->orderBy('due_date')
                    ->get();
            }
            ),
            'overdue_tasks' => Inertia::defer(static function () {
                return TickTickTask::query()
                    ->overdue()
                    ->with('project')
                    ->orderBy('due_date')
                    ->limit(20)
                    ->get();
            }
            ),
            'task_count' => Inertia::defer(static fn () => TickTickTask::query()
                ->active()
                ->dueToday()
                ->count()),
        ]);
    }
}
