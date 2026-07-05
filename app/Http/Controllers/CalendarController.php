<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Task;

class CalendarController extends Controller
{
    /**
     * Display the calendar with tasks as events.
     */
    public function index()
    {
        $workspace = Auth::user()->workspaces()->first();
        if (!$workspace) {
            return view('apps-calendar', [
                'eventsJson' => json_encode([]),
                'reminders' => collect(),
            ]);
        }

        // Fetch User role in the active workspace
        $pivot = $workspace->users()->where('users.id', Auth::id())->first()->pivot;
        $role = $pivot ? $pivot->role : 'member';

        // Base query for tasks based on role scoping
        $tasksQuery = Task::where('workspace_id', $workspace->id)->with(['area', 'project']);
        if ($role === 'member') {
            $tasksQuery->where('created_by', Auth::id());
        }

        $tasks = $tasksQuery->get();

        $events = [];
        foreach ($tasks as $task) {
            // Map priority to theme color class
            $className = match($task->priority) {
                'critical' => 'bg-danger-subtle text-danger border-0',
                'high' => 'bg-warning-subtle text-warning border-0',
                'low' => 'bg-success-subtle text-success border-0',
                default => 'bg-primary-subtle text-primary border-0'
            };

            $events[] = [
                'id' => $task->id,
                'title' => ($task->area ? $task->area->icon . ' ' : '') . $task->title,
                'start' => $task->due_date ? $task->due_date->toIso8601String() : now()->toIso8601String(),
                'className' => $className,
                'description' => $task->description ?: '',
                'priority' => ucfirst($task->priority),
                'category' => $task->area ? $task->area->name : 'General',
                'project' => $task->project ? $task->project->name : 'General',
            ];
        }

        // Top 5 upcoming tasks for the reminders panel
        $reminders = (clone $tasksQuery)
            ->where('status', '!=', 'done')
            ->whereNotNull('due_date')
            ->orderBy('due_date', 'asc')
            ->limit(5)
            ->get();

        return view('apps-calendar', [
            'eventsJson' => json_encode($events),
            'reminders' => $reminders,
        ]);
    }
}
