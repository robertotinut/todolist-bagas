<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Livewire\UserManager;
use App\Livewire\AreaManager;
use App\Livewire\ProjectManager;
use App\Livewire\TaskManager;
use App\Livewire\ReportManager;
use App\Http\Controllers\CalendarController;

// Redirect root to dashboard (which redirects to login if unauthenticated)
Route::redirect('/', '/dashboard');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    // Google OAuth
    Route::get('/auth/google', [\App\Http\Controllers\Auth\SocialAuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [\App\Http\Controllers\Auth\SocialAuthController::class, 'handleGoogleCallback']);

    // Forgot & Reset Password
    Route::get('/forgot-password', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'resetPassword'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/tasks', TaskManager::class)->name('tasks');
    Route::get('/apps-calendar', [CalendarController::class, 'index'])->name('calendar');
    Route::get('/reports', ReportManager::class)->name('reports');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Master CRUDs
    Route::get('/master/users', UserManager::class)->name('master.users');
    Route::get('/master/areas', AreaManager::class)->name('master.areas');
    Route::get('/master/projects', ProjectManager::class)->name('master.projects');

    // Topbar Pages
    Route::get('/about', function () {
        return view('pages-about');
    })->name('about');

    Route::get('/help', function () {
        return view('pages-help');
    })->name('help');

    Route::get('/profile', \App\Livewire\ProfileManager::class)->name('profile');

    Route::get('/donation', function () {
        return view('pages-donation');
    })->name('donation');
});
