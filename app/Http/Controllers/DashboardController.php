<?php

namespace App\Http\Controllers;

use App\Models\TickTickTask;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
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
