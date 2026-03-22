<?php

namespace App\Http\Controllers;

use App\Models\TickTickProject;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    public function index(): Response
    {
        $projects = TickTickProject::query()
            ->withCount(['tasks as active_tasks_count' => fn ($q) => $q->active()])
            ->withCount(['tasks as total_tasks_count'])
            ->orderBy('sort_order')
            ->get();

        return Inertia::render('projects/index', [
            'projects' => $projects,
        ]);
    }
}
