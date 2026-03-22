<?php

namespace App\Http\Controllers;

use App\Models\TickTickProject;
use App\Models\TickTickTask;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TaskController extends Controller
{
    public function index(Request $request): Response
    {
        $query = TickTickTask::query()->with('project');

        if ($request->filled('status')) {
            $query->where('status', $request->integer('status'));
        } else {
            $query->active();
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->integer('project_id'));
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->integer('priority'));
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', '%'.$request->string('search').'%');
        }

        $tasks = $query->orderBy('priority', 'desc')->orderBy('due_date')->paginate(25)->withQueryString();

        return Inertia::render('tasks/index', [
            'tasks' => $tasks,
            'projects' => TickTickProject::query()->orderBy('name')->get(['id', 'name', 'color']),
            'filters' => $request->only(['status', 'project_id', 'priority', 'search']),
        ]);
    }
}
