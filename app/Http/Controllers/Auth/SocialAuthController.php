<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google and authenticate.
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['email' => 'Gagal masuk menggunakan Google. Silakan coba lagi.']);
        }

        // Find or create the user
        $user = User::where('google_id', $googleUser->id)
                    ->orWhere('email', $googleUser->email)
                    ->first();

        if ($user) {
            // Update Google ID if not set
            if (!$user->google_id) {
                $user->google_id = $googleUser->id;
                $user->save();
            }
        } else {
            // Register new user
            $user = User::create([
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'google_id' => $googleUser->id,
                'password' => null, // Google users do not have a local password by default
            ]);
        }

        // Assign to a Workspace
        $workspace = Workspace::first();
        if (!$workspace) {
            // Create a default workspace if none exists
            $workspace = Workspace::create([
                'name' => 'Personal Workspace',
                'slug' => 'personal-workspace',
                'created_by' => $user->id,
            ]);
            $workspace->users()->attach($user->id, ['role' => 'owner']);
        } else {
            // Attach as member if not already attached
            if (!$workspace->users()->where('users.id', $user->id)->exists()) {
                $workspace->users()->attach($user->id, ['role' => 'member']);
            }
        }

        // Log the user in
        Auth::login($user, true);

        return redirect()->intended(route('dashboard'));
    }
}
