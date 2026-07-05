<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Workspace;
use App\Models\Area;
use App\Models\Project;
use App\Models\Task;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index()
    {
        $workspace = Auth::user()->workspaces()->first();
        if (!$workspace) {
            return view('dashboard', [
                'stats' => ['total' => 0, 'completed' => 0, 'pending' => 0, 'rate' => 0, 'projects' => 0],
                'projectsProgress' => [],
                'recentTasks' => collect(),
                'categoryStats' => [],
            ]);
        }

        // Fetch User role in the active workspace
        $pivot = $workspace->users()->where('users.id', Auth::id())->first()->pivot;
        $role = $pivot ? $pivot->role : 'member';

        // Base query for tasks based on role scoping
        $tasksQuery = Task::where('workspace_id', $workspace->id);
        if ($role === 'member') {
            $tasksQuery->where('created_by', Auth::id());
        }

        // Metrics
        $totalTasks = (clone $tasksQuery)->count();
        $completedTasks = (clone $tasksQuery)->where('status', 'done')->count();
        $pendingTasks = $totalTasks - $completedTasks;
        $rate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

        // Scoped projects query
        $areaIds = Area::where('workspace_id', $workspace->id)->pluck('id');
        $projectsQuery = Project::whereIn('area_id', $areaIds)->where('is_archived', false);
        if ($role === 'member') {
            $projectsQuery->where(function($q) {
                $q->where('is_default', true)
                  ->orWhere('created_by', Auth::id());
            });
        }
        $totalProjects = $projectsQuery->count();

        // Recent / Due soon tasks (top 5 pending tasks sorted by due_date)
        $recentTasks = (clone $tasksQuery)
            ->where('status', '!=', 'done')
            ->whereNotNull('due_date')
            ->with(['area', 'project'])
            ->orderBy('due_date', 'asc')
            ->limit(5)
            ->get();

        // Project Completion progress lists
        $allProjects = $projectsQuery->get();
        $projectsProgress = [];
        foreach ($allProjects as $project) {
            $projTasksQuery = Task::where('project_id', $project->id);
            if ($role === 'member') {
                $projTasksQuery->where('created_by', Auth::id());
            }
            $projTotal = $projTasksQuery->count();
            if ($projTotal > 0) {
                $projCompleted = $projTasksQuery->where('status', 'done')->count();
                $projRate = round(($projCompleted / $projTotal) * 100);
                $projectsProgress[] = (object) [
                    'name' => $project->name,
                    'total' => $projTotal,
                    'completed' => $projCompleted,
                    'rate' => $projRate,
                ];
            }
        }

        // Tasks count grouped by Area (Category)
        $areasQuery = Area::where('workspace_id', $workspace->id)->where('is_archived', false);
        if ($role === 'member') {
            $areasQuery->where(function($q) {
                $q->where('is_default', true)
                  ->orWhere('created_by', Auth::id());
            });
        }
        $areas = $areasQuery->get();
        $categoryStats = [];
        foreach ($areas as $area) {
            $areaTasksQuery = Task::where('area_id', $area->id);
            if ($role === 'member') {
                $areaTasksQuery->where('created_by', Auth::id());
            }
            $areaCount = $areaTasksQuery->count();
            if ($areaCount > 0) {
                $categoryStats[] = (object) [
                    'icon' => $area->icon,
                    'name' => $area->name,
                    'color' => $area->color,
                    'count' => $areaCount,
                ];
            }
        }

        return view('dashboard', [
            'stats' => [
                'total' => $totalTasks,
                'completed' => $completedTasks,
                'pending' => $pendingTasks,
                'rate' => $rate,
                'projects' => $totalProjects,
            ],
            'projectsProgress' => $projectsProgress,
            'recentTasks' => $recentTasks,
            'categoryStats' => $categoryStats,
        ]);
    }
}
