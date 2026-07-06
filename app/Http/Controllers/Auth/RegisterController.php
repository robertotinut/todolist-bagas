<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    /**
     * Show the registration form.
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle a registration request.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Assign to the default Workspace (just like Google Auth)
        $workspace = Workspace::first();
        if (!$workspace) {
            $workspace = Workspace::create([
                'name' => 'Personal Workspace',
                'slug' => 'personal-workspace',
                'created_by' => $user->id,
            ]);
            $workspace->users()->attach($user->id, ['role' => 'owner']);
        } else {
            if (!$workspace->users()->where('users.id', $user->id)->exists()) {
                $workspace->users()->attach($user->id, ['role' => 'member']);
            }
        }

        // Authenticate the user
        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
