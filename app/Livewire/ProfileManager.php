<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileManager extends Component
{
    use WithFileUploads;

    public $name = '';
    public $email = '';
    public $avatar = null;
    public $currentAvatar = null;
    
    public $password = '';
    public $password_confirmation = '';

    public function mount()
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->currentAvatar = $user->avatar;
    }

    public function saveProfile()
    {
        $user = Auth::user();

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'avatar' => 'nullable|image|max:2048', // Max 2MB image
        ];

        if ($this->password) {
            $rules['password'] = ['required', 'confirmed', Password::min(6)];
        }

        $this->validate($rules);

        // Update fields
        $user->name = $this->name;
        $user->email = $this->email;

        if ($this->avatar) {
            $path = $this->avatar->store('avatars', 'public');
            $user->avatar = $path;
            $this->currentAvatar = $path;
            $this->avatar = null; // Clear file input
        }

        if ($this->password) {
            $user->password = Hash::make($this->password);
            $this->password = '';
            $this->password_confirmation = '';
        }

        $user->save();

        session()->flash('message', 'Profil berhasil diperbarui.');
        $this->dispatch('profile-updated'); // Optional event
    }

    public function render()
    {
        return view('livewire.profile-manager')->layout('partials.layouts.master');
    }
}
