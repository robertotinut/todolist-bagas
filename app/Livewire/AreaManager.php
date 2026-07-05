<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Area;
use Illuminate\Support\Facades\Auth;

class AreaManager extends Component
{
    public $areas = [];
    public $name = '';
    public $icon = '';
    public $color = 'blue';
    public $selectedAreaId = null;
    public $isEditing = false;
    public $userRole = 'member';

    protected $rules = [
        'name' => 'required|string|max:255',
        'icon' => 'nullable|string|max:50',
        'color' => 'nullable|string|max:50',
    ];

    public function mount()
    {
        $workspace = Auth::user()->workspaces()->first();
        if ($workspace) {
            $pivot = $workspace->users()->where('users.id', Auth::id())->first()->pivot;
            $this->userRole = $pivot ? $pivot->role : 'member';
        }
        $this->loadAreas();
    }

    public function loadAreas()
    {
        $workspace = Auth::user()->workspaces()->first();
        if ($workspace) {
            $pivot = $workspace->users()->where('users.id', Auth::id())->first()->pivot;
            $role = $pivot ? $pivot->role : 'member';

            $query = Area::where('workspace_id', $workspace->id);
            
            // Limit regular members to only see default areas OR areas they created
            if ($role === 'member') {
                $query->where(function($q) {
                    $q->where('is_default', true)
                      ->orWhere('created_by', Auth::id());
                });
            }

            $this->areas = $query->orderBy('is_default', 'desc')
                ->orderBy('is_archived', 'asc')
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $this->areas = [];
        }
    }

    public function save()
    {
        $this->validate();
        $workspace = Auth::user()->workspaces()->first();

        if (!$workspace) {
            session()->flash('error', 'No active workspace found.');
            return;
        }

        $pivot = $workspace->users()->where('users.id', Auth::id())->first()->pivot;
        $role = $pivot ? $pivot->role : 'member';

        if ($this->isEditing) {
            $area = Area::findOrFail($this->selectedAreaId);
            
            // Protect default categories from member modification
            if ($area->is_default && $role === 'member') {
                session()->flash('error', 'Kategori bawaan (default) tidak dapat diubah oleh Member.');
                return;
            }

            // Enforce access control for custom categories
            if (!$area->is_default && $role === 'member' && $area->created_by !== Auth::id()) {
                session()->flash('error', 'Anda tidak memiliki akses untuk mengubah kategori ini.');
                return;
            }

            $area->update([
                'name' => $this->name,
                'icon' => $this->icon,
                'color' => $this->color,
            ]);
            session()->flash('message', 'Area updated successfully.');
        } else {
            Area::create([
                'workspace_id' => $workspace->id,
                'name' => $this->name,
                'icon' => $this->icon,
                'color' => $this->color,
                'created_by' => Auth::id(),
                'is_default' => false,
            ]);
            session()->flash('message', 'Area created successfully.');
        }

        $this->resetForm();
        $this->loadAreas();
    }

    public function edit($id)
    {
        $area = Area::findOrFail($id);
        $workspace = Auth::user()->workspaces()->first();
        $pivot = $workspace->users()->where('users.id', Auth::id())->first()->pivot;
        $role = $pivot ? $pivot->role : 'member';

        // Protect default categories from member editing
        if ($area->is_default && $role === 'member') {
            session()->flash('error', 'Kategori bawaan (default) tidak dapat diubah oleh Member.');
            return;
        }

        // Enforce access check for custom categories
        if (!$area->is_default && $role === 'member' && $area->created_by !== Auth::id()) {
            session()->flash('error', 'Anda tidak memiliki akses untuk mengubah kategori ini.');
            return;
        }

        $this->selectedAreaId = $area->id;
        $this->name = $area->name;
        $this->icon = $area->icon;
        $this->color = $area->color;
        $this->isEditing = true;
    }

    public function delete($id)
    {
        $area = Area::findOrFail($id);
        $workspace = Auth::user()->workspaces()->first();
        $pivot = $workspace->users()->where('users.id', Auth::id())->first()->pivot;
        $role = $pivot ? $pivot->role : 'member';

        // Protect default categories from member deletion
        if ($area->is_default && $role === 'member') {
            session()->flash('error', 'Kategori bawaan (default) tidak dapat dihapus oleh Member.');
            return;
        }

        // Enforce access check for custom categories
        if (!$area->is_default && $role === 'member' && $area->created_by !== Auth::id()) {
            session()->flash('error', 'Anda tidak memiliki akses untuk menghapus kategori ini.');
            return;
        }

        $area->delete();

        session()->flash('message', 'Area deleted successfully.');
        $this->loadAreas();
        if ($this->selectedAreaId === $id) {
            $this->resetForm();
        }
    }

    public function toggleArchive($id)
    {
        $area = Area::findOrFail($id);
        $workspace = Auth::user()->workspaces()->first();
        $pivot = $workspace->users()->where('users.id', Auth::id())->first()->pivot;
        $role = $pivot ? $pivot->role : 'member';

        // Protect default categories from member archiving
        if ($area->is_default && $role === 'member') {
            session()->flash('error', 'Kategori bawaan (default) tidak dapat diarsipkan oleh Member.');
            return;
        }

        // Enforce access check for custom categories
        if (!$area->is_default && $role === 'member' && $area->created_by !== Auth::id()) {
            session()->flash('error', 'Anda tidak memiliki akses untuk mengarsipkan kategori ini.');
            return;
        }

        $area->update([
            'is_archived' => !$area->is_archived
        ]);

        session()->flash('message', $area->is_archived ? 'Area archived.' : 'Area unarchived.');
        $this->loadAreas();
    }

    public function resetForm()
    {
        $this->name = '';
        $this->icon = '';
        $this->color = 'blue';
        $this->selectedAreaId = null;
        $this->isEditing = false;
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.area-manager')->layout('partials.layouts.master');
    }
}
