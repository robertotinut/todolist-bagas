<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserManager extends Component
{
    public $members = [];
    public $name = '';
    public $email = '';
    public $password = '';
    public $role = 'member';
    public $selectedUserId = null;
    public $isEditing = false;

    public function mount()
    {
        $workspace = Auth::user()->workspaces()->first();
        if ($workspace) {
            $pivot = $workspace->users()->where('users.id', Auth::id())->first()->pivot;
            $role = $pivot ? $pivot->role : 'member';
            if ($role === 'member') {
                abort(403, 'Anda tidak diizinkan untuk mengakses halaman ini.');
            }
        } else {
            abort(403, 'Workspace tidak ditemukan.');
        }

        $this->loadMembers();
    }

    public function loadMembers()
    {
        $workspace = Auth::user()->workspaces()->first();
        if ($workspace) {
            $this->members = $workspace->users()
                ->orderBy('workspace_user.role', 'asc')
                ->orderBy('users.name', 'asc')
                ->get();
        } else {
            $this->members = [];
        }
    }

    public function save()
    {
        $workspace = Auth::user()->workspaces()->first();
        if (!$workspace) {
            session()->flash('error', 'No active workspace found.');
            return;
        }

        if ($this->isEditing) {
            // Update role inside this workspace
            $workspace->users()->updateExistingPivot($this->selectedUserId, [
                'role' => $this->role,
            ]);
            
            // Also update basic details if editing
            $user = User::findOrFail($this->selectedUserId);
            $user->update([
                'name' => $this->name,
                'email' => $this->email,
            ]);

            session()->flash('message', 'User updated successfully.');
        } else {
            $this->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'password' => 'required|string|min:6',
                'role' => 'required|in:owner,admin,member',
            ]);

            // Check if user already exists
            $user = User::where('email', $this->email)->first();

            if (!$user) {
                // Create user
                $user = User::create([
                    'name' => $this->name,
                    'email' => $this->email,
                    'password' => Hash::make($this->password),
                ]);
            }

            // Check if already in workspace
            if ($workspace->users()->where('users.id', $user->id)->exists()) {
                session()->flash('error', 'User is already a member of this workspace.');
                return;
            }

            // Attach user to workspace
            $workspace->users()->attach($user->id, ['role' => $this->role]);

            session()->flash('message', 'User added to workspace successfully.');
        }

        $this->resetForm();
        $this->loadMembers();
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $workspace = Auth::user()->workspaces()->first();
        $pivot = $workspace->users()->where('users.id', $user->id)->first()->pivot;

        $this->selectedUserId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $pivot->role;
        $this->isEditing = true;
    }

    public function remove($id)
    {
        // Don't allow user to remove themselves
        if (Auth::id() === $id) {
            session()->flash('error', 'You cannot remove yourself from the workspace.');
            return;
        }

        $workspace = Auth::user()->workspaces()->first();
        
        // Don't allow removing the last owner
        $ownersCount = $workspace->users()->where('workspace_user.role', 'owner')->count();
        $userToRemove = $workspace->users()->where('users.id', $id)->first();
        if ($userToRemove && $userToRemove->pivot->role === 'owner' && $ownersCount <= 1) {
            session()->flash('error', 'Workspace must have at least one owner.');
            return;
        }

        $workspace->users()->detach($id);

        session()->flash('message', 'User removed from workspace successfully.');
        $this->loadMembers();
        if ($this->selectedUserId === $id) {
            $this->resetForm();
        }
    }

    public function resetForm()
    {
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->role = 'member';
        $this->selectedUserId = null;
        $this->isEditing = false;
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.user-manager')->layout('partials.layouts.master');
    }
}
