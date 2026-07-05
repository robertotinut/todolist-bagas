<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Workspace;
use App\Models\Area;
use App\Models\Project;
use App\Models\Task;
use App\Models\Subtask;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Default Users (Owner and Member)
        $user = User::factory()->create([
            'name' => 'Admin User (Owner)',
            'email' => 'test@example.com',
        ]);

        $member = User::factory()->create([
            'name' => 'Regular Member',
            'email' => 'member@example.com',
        ]);

        // 2. Create Workspace for User
        $workspace = Workspace::create([
            'name' => 'Personal Workspace',
            'slug' => 'personal-workspace',
            'created_by' => $user->id,
        ]);

        // 3. Attach Users to Workspace
        $workspace->users()->attach($user->id, ['role' => 'owner']);
        $workspace->users()->attach($member->id, ['role' => 'member']);

        // 4. Create default Areas
        $pekerjaan = Area::create([
            'workspace_id' => $workspace->id,
            'name' => 'Pekerjaan',
            'icon' => '💼',
            'color' => 'blue',
            'created_by' => $user->id,
            'is_default' => true,
        ]);

        $pribadi = Area::create([
            'workspace_id' => $workspace->id,
            'name' => 'Pribadi',
            'icon' => '❤️',
            'color' => 'rose',
            'created_by' => $user->id,
            'is_default' => true,
        ]);

        $keuangan = Area::create([
            'workspace_id' => $workspace->id,
            'name' => 'Keuangan',
            'icon' => '💰',
            'color' => 'emerald',
            'created_by' => $user->id,
            'is_default' => true,
        ]);

        $bisnis = Area::create([
            'workspace_id' => $workspace->id,
            'name' => 'Bisnis',
            'icon' => '🚀',
            'color' => 'purple',
            'created_by' => $user->id,
            'is_default' => true,
        ]);

        // 5. Create Projects
        $websiteCompany = Project::create([
            'area_id' => $pekerjaan->id,
            'name' => 'Website Company',
            'description' => 'Project to build company profile website.',
            'created_by' => $user->id,
            'is_default' => true,
        ]);

        $sladaSaas = Project::create([
            'area_id' => $bisnis->id,
            'name' => 'SLADA SaaS',
            'description' => 'Developing the SLADA productivity platform.',
            'created_by' => $user->id,
            'is_default' => true,
        ]);

        // 6. Create Tasks
        $task1 = Task::create([
            'workspace_id' => $workspace->id,
            'area_id' => $pekerjaan->id,
            'project_id' => $websiteCompany->id,
            'title' => 'Desain UI Halaman Dashboard',
            'description' => 'Membuat mockup desain UI dashboard untuk website profil perusahaan.',
            'priority' => 'high',
            'status' => 'in_progress',
            'due_date' => now()->addDays(3),
            'estimate_time' => 120,
            'created_by' => $user->id,
        ]);

        $task2 = Task::create([
            'workspace_id' => $workspace->id,
            'area_id' => $pekerjaan->id,
            'project_id' => $websiteCompany->id,
            'title' => 'Setup Server dan Database',
            'description' => 'Konfigurasi VPS Linux, web server Nginx, dan database MySQL.',
            'priority' => 'critical',
            'status' => 'todo',
            'due_date' => now()->addDay(),
            'estimate_time' => 60,
            'created_by' => $user->id,
        ]);

        $task3 = Task::create([
            'workspace_id' => $workspace->id,
            'area_id' => $pribadi->id,
            'project_id' => null,
            'title' => 'Olahraga Pagi',
            'description' => 'Lari pagi 30 menit keliling perumahan.',
            'priority' => 'medium',
            'status' => 'todo',
            'due_date' => now(),
            'estimate_time' => 30,
            'created_by' => $user->id,
        ]);

        $task4 = Task::create([
            'workspace_id' => $workspace->id,
            'area_id' => $keuangan->id,
            'project_id' => null,
            'title' => 'Bayar Tagihan Hosting',
            'description' => 'Bayar biaya bulanan VPS untuk deployment aplikasi klien.',
            'priority' => 'high',
            'status' => 'todo',
            'due_date' => now()->addDays(5),
            'estimate_time' => 15,
            'created_by' => $user->id,
        ]);

        // 7. Create Subtasks
        Subtask::create([
            'task_id' => $task2->id,
            'title' => 'Install PHP 8.2 & Nginx',
            'is_completed' => false,
        ]);

        Subtask::create([
            'task_id' => $task2->id,
            'title' => 'Configure MySQL Database',
            'is_completed' => false,
        ]);
    }
}
