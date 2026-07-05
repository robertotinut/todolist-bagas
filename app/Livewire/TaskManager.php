<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Area;
use App\Models\Project;
use App\Models\Task;
use App\Models\Subtask;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TaskManager extends Component
{
    use WithFileUploads;

    // Scopes & Landing Page Selection
    public $userRole = 'member';
    public $selectedAreaId = null; // null = Category Selector Landing view
    public $currentView = 'kanban'; // 'kanban' or 'table'
    public $tableFilter = 'all'; // 'all', 'today', 'important'
    
    // Filters
    public $search = '';
    public $filterProject = '';
    public $filterPriority = '';
    public $filterDueDate = '';

    // Form fields
    public $title = '';
    public $description = '';
    public $area_id = '';
    public $project_id = '';
    public $priority = 'medium';
    public $due_date = '';
    public $estimate_time = '';
    
    // Subtask builder
    public $subtaskTitle = '';
    public $tempSubtasks = []; // array of strings for new subtasks

    // UI States
    public $showForm = false;
    public $isEditing = false;
    public $selectedTaskId = null;
    public $expandedTaskIds = []; // tracks which task cards show their checklists
    public $showDetailModal = false;
    public $detailTask = null;
    public $attachmentFile = null;
    public $formAttachmentFile = null; // Attachment uploaded via create/edit form

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string|max:1000',
        'area_id' => 'required|exists:areas,id',
        'project_id' => 'nullable|exists:projects,id',
        'priority' => 'required|in:low,medium,high,critical',
        'due_date' => 'nullable|date',
        'estimate_time' => 'nullable|integer|min:1',
        'formAttachmentFile' => 'nullable|file|max:10240',
    ];

    public function mount()
    {
        $workspace = Auth::user()->workspaces()->first();
        if ($workspace) {
            $pivot = $workspace->users()->where('users.id', Auth::id())->first()->pivot;
            $this->userRole = $pivot ? $pivot->role : 'member';
        }
    }

    public function selectArea($id)
    {
        $this->selectedAreaId = $id;
        $this->area_id = $id; // Lock form area selection to current area
        $this->currentView = 'kanban';
        $this->tableFilter = 'all';
        
        // Reset filters
        $this->search = '';
        $this->filterProject = '';
        $this->filterPriority = '';
        $this->filterDueDate = '';
        $this->resetForm();
    }

    public function backToCategories()
    {
        $this->selectedAreaId = null;
        $this->resetForm();
    }

    public function getCategoryStatsProperty()
    {
        $workspace = Auth::user()->workspaces()->first();
        if (!$workspace) return [];

        $areasQuery = Area::where('workspace_id', $workspace->id)->where('is_archived', false);
        if ($this->userRole === 'member') {
            $areasQuery->where(function($q) {
                $q->where('is_default', true)
                  ->orWhere('created_by', Auth::id());
            });
        }
        $areas = $areasQuery->get();

        $stats = [];
        foreach ($areas as $area) {
            $taskQuery = Task::where('area_id', $area->id);
            if ($this->userRole === 'member') {
                $taskQuery->where('created_by', Auth::id());
            }
            $total = $taskQuery->count();
            $completed = $taskQuery->where('status', 'done')->count();
            $rate = $total > 0 ? round(($completed / $total) * 100) : 0;

            $stats[] = (object) [
                'id' => $area->id,
                'name' => $area->name,
                'icon' => $area->icon,
                'color' => $area->color,
                'total' => $total,
                'completed' => $completed,
                'rate' => $rate,
            ];
        }
        return $stats;
    }

    public function getSelectedAreaModelProperty()
    {
        if (!$this->selectedAreaId) return null;
        return Area::find($this->selectedAreaId);
    }

    public function getAreasProperty()
    {
        $workspace = Auth::user()->workspaces()->first();
        if (!$workspace) return [];

        $query = Area::where('workspace_id', $workspace->id)->where('is_archived', false);
        if ($this->userRole === 'member') {
            $query->where(function($q) {
                $q->where('is_default', true)
                  ->orWhere('created_by', Auth::id());
            });
        }
        return $query->get();
    }

    public function getProjectsProperty()
    {
        $workspace = Auth::user()->workspaces()->first();
        if (!$workspace) return [];

        // Projects available for filter (restricted to selected Area if any)
        $query = Project::where('is_archived', false);
        if ($this->selectedAreaId) {
            $query->where('area_id', $this->selectedAreaId);
        } else {
            $areaIds = Area::where('workspace_id', $workspace->id)->pluck('id');
            $query->whereIn('area_id', $areaIds);
        }

        if ($this->userRole === 'member') {
            $query->where(function($q) {
                $q->where('is_default', true)
                  ->orWhere('created_by', Auth::id());
            });
        }
        return $query->get();
    }

    public function getFilteredFormProjectsProperty()
    {
        $activeArea = $this->selectedAreaId ?: $this->area_id;
        if (!$activeArea) return [];

        // Filter projects in the form based on selected Area
        $query = Project::where('area_id', $activeArea)->where('is_archived', false);
        if ($this->userRole === 'member') {
            $query->where(function($q) {
                $q->where('is_default', true)
                  ->orWhere('created_by', Auth::id());
            });
        }
        return $query->get();
    }

    public function loadTasks()
    {
        $workspace = Auth::user()->workspaces()->first();
        if (!$workspace) return collect();

        $query = Task::where('workspace_id', $workspace->id)
            ->with(['area', 'project', 'subtasks']);

        // Mandatory Category Filter (Landing scope)
        if ($this->selectedAreaId) {
            $query->where('area_id', $this->selectedAreaId);
        }

        // Scoping by user role
        if ($this->userRole === 'member') {
            $query->where('created_by', Auth::id());
        }

        // Apply filters
        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        if (!empty($this->filterProject)) {
            $query->where('project_id', $this->filterProject);
        }

        if ($this->currentView === 'kanban') {
            if (!empty($this->filterPriority)) {
                $query->where('priority', $this->filterPriority);
            }

            if (!empty($this->filterDueDate)) {
                $today = Carbon::today();
                if ($this->filterDueDate === 'today') {
                    $query->whereDate('due_date', $today);
                } elseif ($this->filterDueDate === 'week') {
                    $query->whereBetween('due_date', [$today->startOfWeek(), Carbon::today()->endOfWeek()]);
                } elseif ($this->filterDueDate === 'overdue') {
                    $query->whereDate('due_date', '<', $today)->where('status', '!=', 'done');
                }
            }
        } else {
            // Apply quick table filters
            if ($this->tableFilter === 'today') {
                $today = Carbon::today();
                $query->where(function($q) use ($today) {
                    $q->whereDate('due_date', $today)
                      ->orWhere('status', 'in_progress');
                });
            } elseif ($this->tableFilter === 'important') {
                $query->whereIn('priority', ['high', 'critical']);
            }
        }

        return $query->orderBy('due_date', 'asc')->get();
    }

    public function getStatsProperty()
    {
        $tasks = $this->loadTasks();
        $total = $tasks->count();
        $completed = $tasks->where('status', 'done')->count();
        $pending = $total - $completed;
        $rate = $total > 0 ? round(($completed / $total) * 100) : 0;

        return [
            'total' => $total,
            'completed' => $completed,
            'pending' => $pending,
            'rate' => $rate,
        ];
    }

    public function toggleExpand($taskId)
    {
        if (in_array($taskId, $this->expandedTaskIds)) {
            $this->expandedTaskIds = array_diff($this->expandedTaskIds, [$taskId]);
        } else {
            $this->expandedTaskIds[] = $taskId;
        }
    }

    public function changeStatus($taskId, $newStatus)
    {
        $task = Task::findOrFail($taskId);
        
        // Scope protection
        if ($this->userRole === 'member' && $task->created_by !== Auth::id()) {
            session()->flash('error', 'Anda tidak memiliki akses untuk mengubah status tugas ini.');
            return;
        }

        if (in_array($newStatus, ['todo', 'in_progress', 'done'])) {
            $task->update(['status' => $newStatus]);
            session()->flash('message', 'Status tugas berhasil diperbarui.');
        }
    }

    public function toggleSubtask($subtaskId)
    {
        $subtask = Subtask::findOrFail($subtaskId);
        $task = $subtask->task;

        // Scope protection
        if ($this->userRole === 'member' && $task->created_by !== Auth::id()) {
            session()->flash('error', 'Anda tidak memiliki akses untuk memperbarui sub-tugas ini.');
            return;
        }

        $subtask->update([
            'is_completed' => !$subtask->is_completed
        ]);
    }

    public function addTempSubtask()
    {
        if (!empty(trim($this->subtaskTitle))) {
            $this->tempSubtasks[] = trim($this->subtaskTitle);
            $this->subtaskTitle = '';
        }
    }

    public function removeTempSubtask($index)
    {
        if (isset($this->tempSubtasks[$index])) {
            unset($this->tempSubtasks[$index]);
            $this->tempSubtasks = array_values($this->tempSubtasks);
        }
    }

    public function toggleForm()
    {
        $this->showForm = !$this->showForm;
        if ($this->showForm) {
            $this->area_id = $this->selectedAreaId; // Lock area
        } else {
            $this->resetForm();
        }
    }

    public function saveTask()
    {
        if ($this->selectedAreaId) {
            $this->area_id = $this->selectedAreaId;
        }

        $this->validate();
        $workspace = Auth::user()->workspaces()->first();

        if (!$workspace) {
            session()->flash('error', 'Workspace tidak ditemukan.');
            return;
        }

        $dueDateVal = $this->due_date ? Carbon::parse($this->due_date) : Carbon::now();
        $estTimeVal = $this->estimate_time ? intval($this->estimate_time) : null;

        if ($this->isEditing) {
            $task = Task::findOrFail($this->selectedTaskId);

            // Scope check
            if ($this->userRole === 'member' && $task->created_by !== Auth::id()) {
                session()->flash('error', 'Anda tidak memiliki akses untuk mengubah tugas ini.');
                return;
            }

            $task->update([
                'area_id' => $this->area_id,
                'project_id' => $this->project_id ?: null,
                'title' => $this->title,
                'description' => $this->description,
                'priority' => $this->priority,
                'due_date' => $dueDateVal,
                'estimate_time' => $estTimeVal,
            ]);

            foreach ($this->tempSubtasks as $subTitle) {
                Subtask::create([
                    'task_id' => $task->id,
                    'title' => $subTitle,
                    'is_completed' => false,
                ]);
            }

            session()->flash('message', 'Tugas berhasil diperbarui.');
        } else {
            $task = Task::create([
                'workspace_id' => $workspace->id,
                'area_id' => $this->area_id,
                'project_id' => $this->project_id ?: null,
                'title' => $this->title,
                'description' => $this->description,
                'priority' => $this->priority,
                'status' => 'todo',
                'due_date' => $dueDateVal,
                'estimate_time' => $estTimeVal,
                'created_by' => Auth::id(),
            ]);

            foreach ($this->tempSubtasks as $subTitle) {
                Subtask::create([
                    'task_id' => $task->id,
                    'title' => $subTitle,
                    'is_completed' => false,
                ]);
            }

            session()->flash('message', 'Tugas baru berhasil ditambahkan.');
        }

        if ($this->formAttachmentFile) {
            $originalName = $this->formAttachmentFile->getClientOriginalName();
            $size = $this->formAttachmentFile->getSize();
            
            if ($size >= 1048576) {
                $formattedSize = round($size / 1048576, 2) . ' MB';
            } else {
                $formattedSize = round($size / 1024, 2) . ' KB';
            }

            $path = $this->formAttachmentFile->store('attachments', 'public');

            \App\Models\TaskAttachment::create([
                'task_id' => $task->id,
                'file_path' => $path,
                'file_name' => $originalName,
                'file_size' => $formattedSize,
                'uploaded_by' => Auth::id(),
            ]);
        }

        $this->resetForm();
        $this->showForm = false;
    }

    public function editTask($id)
    {
        $task = Task::findOrFail($id);

        // Scope check
        if ($this->userRole === 'member' && $task->created_by !== Auth::id()) {
            session()->flash('error', 'Anda tidak memiliki akses untuk mengubah tugas ini.');
            return;
        }

        $this->selectedTaskId = $task->id;
        $this->area_id = $task->area_id;
        $this->project_id = $task->project_id ?: '';
        $this->title = $task->title;
        $this->description = $task->description;
        $this->priority = $task->priority;
        $this->due_date = $task->due_date ? Carbon::parse($task->due_date)->format('Y-m-d\TH:i') : '';
        $this->estimate_time = $task->estimate_time;
        
        $this->tempSubtasks = []; // reset temp builder
        $this->isEditing = true;
        $this->showForm = true;
    }

    public function deleteTask($id)
    {
        $task = Task::findOrFail($id);

        // Scope check
        if ($this->userRole === 'member' && $task->created_by !== Auth::id()) {
            session()->flash('error', 'Anda tidak memiliki akses untuk menghapus tugas ini.');
            return;
        }

        $task->delete();
        session()->flash('message', 'Tugas berhasil dihapus.');
    }

    public function resetForm()
    {
        $this->title = '';
        $this->description = '';
        $this->area_id = $this->selectedAreaId ?: '';
        $this->project_id = '';
        $this->priority = 'medium';
        $this->due_date = \Carbon\Carbon::now()->format('Y-m-d\TH:i');
        $this->estimate_time = '';
        $this->subtaskTitle = '';
        $this->tempSubtasks = [];
        $this->isEditing = false;
        $this->selectedTaskId = null;
        $this->formAttachmentFile = null;
        $this->resetValidation();
    }

    public function setView($view)
    {
        if (in_array($view, ['kanban', 'table'])) {
            $this->currentView = $view;
        }
    }

    public function setTableFilter($filter)
    {
        if (in_array($filter, ['all', 'today', 'important'])) {
            $this->tableFilter = $filter;
        }
    }

    public function openTaskDetail($taskId)
    {
        $this->detailTask = Task::with(['area', 'project', 'subtasks', 'attachments.uploader'])->findOrFail($taskId);
        $this->showDetailModal = true;
        $this->attachmentFile = null;
    }

    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->detailTask = null;
        $this->attachmentFile = null;
    }

    public function uploadAttachment()
    {
        $this->validate([
            'attachmentFile' => 'required|file|max:10240', // Max 10MB
        ]);

        if (!$this->detailTask) {
            return;
        }

        $originalName = $this->attachmentFile->getClientOriginalName();
        $size = $this->attachmentFile->getSize();
        
        if ($size >= 1048576) {
            $formattedSize = round($size / 1048576, 2) . ' MB';
        } else {
            $formattedSize = round($size / 1024, 2) . ' KB';
        }

        $path = $this->attachmentFile->store('attachments', 'public');

        \App\Models\TaskAttachment::create([
            'task_id' => $this->detailTask->id,
            'file_path' => $path,
            'file_name' => $originalName,
            'file_size' => $formattedSize,
            'uploaded_by' => Auth::id(),
        ]);

        $this->attachmentFile = null;
        session()->flash('message', 'File lampiran berhasil diunggah.');

        // Refresh details
        $this->openTaskDetail($this->detailTask->id);
    }

    public function deleteAttachment($attachmentId)
    {
        $attachment = \App\Models\TaskAttachment::findOrFail($attachmentId);
        $task = $attachment->task;

        if ($this->userRole === 'member' && $task->created_by !== Auth::id()) {
            session()->flash('error', 'Anda tidak memiliki akses untuk menghapus lampiran ini.');
            return;
        }

        \Illuminate\Support\Facades\Storage::disk('public')->delete($attachment->file_path);
        $attachment->delete();

        session()->flash('message', 'File lampiran berhasil dihapus.');

        // Refresh details
        $this->openTaskDetail($task->id);
    }

    public function render()
    {
        $categoryStats = [];
        $todoTasks = collect();
        $inProgressTasks = collect();
        $doneTasks = collect();
        $allTasks = collect();

        if ($this->selectedAreaId === null) {
            $categoryStats = $this->categoryStats;
        } else {
            $tasks = $this->loadTasks();
            $allTasks = $tasks;
            $todoTasks = $tasks->where('status', 'todo');
            $inProgressTasks = $tasks->where('status', 'in_progress');
            $doneTasks = $tasks->where('status', 'done');
        }

        return view('livewire.task-manager', [
            'categoryStats' => $categoryStats,
            'allTasks' => $allTasks,
            'todoTasks' => $todoTasks,
            'inProgressTasks' => $inProgressTasks,
            'doneTasks' => $doneTasks,
        ])->layout('partials.layouts.master');
    }
}
