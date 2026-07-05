<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Area;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;

class ProjectManager extends Component
{
    public $projects = [];
    public $areas = [];
    public $area_id = '';
    public $name = '';
    public $description = '';
    public $selectedProjectId = null;
    public $isEditing = false;
    public $userRole = 'member';

    protected $rules = [
        'area_id' => 'required|exists:areas,id',
        'name' => 'required|string|max:255',
        'description' => 'nullable|string|max:1000',
    ];

    public function mount()
    {
        $workspace = Auth::user()->workspaces()->first();
        if ($workspace) {
            $pivot = $workspace->users()->where('users.id', Auth::id())->first()->pivot;
            $this->userRole = $pivot ? $pivot->role : 'member';
        }
        $this->loadData();
    }

    public function loadData()
    {
        $workspace = Auth::user()->workspaces()->first();
        if ($workspace) {
            $pivot = $workspace->users()->where('users.id', Auth::id())->first()->pivot;
            $role = $pivot ? $pivot->role : 'member';

            // Scoping active areas (select options)
            $areasQuery = Area::where('workspace_id', $workspace->id)
                ->where('is_archived', false);
            
            if ($role === 'member') {
                $areasQuery->where(function($q) {
                    $q->where('is_default', true)
                      ->orWhere('created_by', Auth::id());
                });
            }
            $this->areas = $areasQuery->get();

            // Scoping projects list
            $areaIds = Area::where('workspace_id', $workspace->id)->pluck('id');
            $projectsQuery = Project::whereIn('area_id', $areaIds)->with('area');

            if ($role === 'member') {
                $projectsQuery->where(function($q) {
                    $q->where('is_default', true)
                      ->orWhere('created_by', Auth::id());
                });
            }

            $this->projects = $projectsQuery->orderBy('is_default', 'desc')
                ->orderBy('is_archived', 'asc')
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $this->areas = [];
            $this->projects = [];
        }
    }

    public function save()
    {
        $this->validate();
        $workspace = Auth::user()->workspaces()->first();
        $pivot = $workspace->users()->where('users.id', Auth::id())->first()->pivot;
        $role = $pivot ? $pivot->role : 'member';

        if ($this->isEditing) {
            $project = Project::findOrFail($this->selectedProjectId);

            // Protect default projects from member modification
            if ($project->is_default && $role === 'member') {
                session()->flash('error', 'Project bawaan (default) tidak dapat diubah oleh Member.');
                return;
            }

            // Enforce access control for custom projects
            if (!$project->is_default && $role === 'member' && $project->created_by !== Auth::id()) {
                session()->flash('error', 'Anda tidak memiliki akses untuk mengubah project ini.');
                return;
            }

            $project->update([
                'area_id' => $this->area_id,
                'name' => $this->name,
                'description' => $this->description,
            ]);
            session()->flash('message', 'Project updated successfully.');
        } else {
            Project::create([
                'area_id' => $this->area_id,
                'name' => $this->name,
                'description' => $this->description,
                'created_by' => Auth::id(),
                'is_default' => false,
            ]);
            session()->flash('message', 'Project created successfully.');
        }

        $this->resetForm();
        $this->loadData();
    }

    public function edit($id)
    {
        $project = Project::findOrFail($id);
        $workspace = Auth::user()->workspaces()->first();
        $pivot = $workspace->users()->where('users.id', Auth::id())->first()->pivot;
        $role = $pivot ? $pivot->role : 'member';

        // Protect default projects from member editing
        if ($project->is_default && $role === 'member') {
            session()->flash('error', 'Project bawaan (default) tidak dapat diubah oleh Member.');
            return;
        }

        // Enforce access control for custom projects
        if (!$project->is_default && $role === 'member' && $project->created_by !== Auth::id()) {
            session()->flash('error', 'Anda tidak memiliki akses untuk mengubah project ini.');
            return;
        }

        $this->selectedProjectId = $project->id;
        $this->area_id = $project->area_id;
        $this->name = $project->name;
        $this->description = $project->description;
        $this->isEditing = true;
    }

    public function delete($id)
    {
        $project = Project::findOrFail($id);
        $workspace = Auth::user()->workspaces()->first();
        $pivot = $workspace->users()->where('users.id', Auth::id())->first()->pivot;
        $role = $pivot ? $pivot->role : 'member';

        // Protect default projects from member deletion
        if ($project->is_default && $role === 'member') {
            session()->flash('error', 'Project bawaan (default) tidak dapat dihapus oleh Member.');
            return;
        }

        // Enforce access control for custom projects
        if (!$project->is_default && $role === 'member' && $project->created_by !== Auth::id()) {
            session()->flash('error', 'Anda tidak memiliki akses untuk menghapus project ini.');
            return;
        }

        $project->delete();

        session()->flash('message', 'Project deleted successfully.');
        $this->loadData();
        if ($this->selectedProjectId === $id) {
            $this->resetForm();
        }
    }

    public function toggleArchive($id)
    {
        $project = Project::findOrFail($id);
        $workspace = Auth::user()->workspaces()->first();
        $pivot = $workspace->users()->where('users.id', Auth::id())->first()->pivot;
        $role = $pivot ? $pivot->role : 'member';

        // Protect default projects from member archiving
        if ($project->is_default && $role === 'member') {
            session()->flash('error', 'Project bawaan (default) tidak dapat diarsipkan oleh Member.');
            return;
        }

        // Enforce access control for custom projects
        if (!$project->is_default && $role === 'member' && $project->created_by !== Auth::id()) {
            session()->flash('error', 'Anda tidak memiliki akses untuk mengarsipkan project ini.');
            return;
        }

        $project->update([
            'is_archived' => !$project->is_archived
        ]);

        session()->flash('message', $project->is_archived ? 'Project archived.' : 'Project unarchived.');
        $this->loadData();
    }

    public function resetForm()
    {
        $this->area_id = '';
        $this->name = '';
        $this->description = '';
        $this->selectedProjectId = null;
        $this->isEditing = false;
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.project-manager')->layout('partials.layouts.master');
    }
}
