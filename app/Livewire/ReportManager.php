<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReportManager extends Component
{
    public $userRole = 'member';
    public $viewDays = 7; // Default: show past 7 days of performance report

    public function mount()
    {
        $workspace = Auth::user()->workspaces()->first();
        if ($workspace) {
            $pivot = $workspace->users()->where('users.id', Auth::id())->first()->pivot;
            $this->userRole = $pivot ? $pivot->role : 'member';
        }
    }

    public function setViewDays($days)
    {
        if (in_array($days, [7, 14, 30])) {
            $this->viewDays = $days;
        }
    }

    public function getDailyReportsProperty()
    {
        $workspace = Auth::user()->workspaces()->first();
        if (!$workspace) return [];

        $reports = [];
        
        // Generate daily reports dynamically for the selected range (e.g. past 7 days)
        for ($i = 0; $i < $this->viewDays; $i++) {
            $date = Carbon::today()->subDays($i);
            $dateStr = $date->format('Y-m-d');

            // 1. Mapped Completed Tasks on this date
            $completedTasksQuery = Task::where('workspace_id', $workspace->id)
                ->where('status', 'done')
                ->whereDate('updated_at', $date);
            if ($this->userRole === 'member') {
                $completedTasksQuery->where('created_by', Auth::id());
            }
            $completed = $completedTasksQuery->with(['area', 'project'])->get();

            // 2. Mapped Active Tasks worked on this date (updated or created but not done)
            $activeTasksQuery = Task::where('workspace_id', $workspace->id)
                ->where('status', '!=', 'done')
                ->where(function($q) use ($date) {
                    $q->whereDate('updated_at', $date)
                      ->orWhereDate('created_at', $date);
                });
            if ($this->userRole === 'member') {
                $activeTasksQuery->where('created_by', Auth::id());
            }
            $active = $activeTasksQuery->with(['area', 'project'])->get();

            // 3. Mapped Overdue Tasks on this date (obstacles/impediments)
            // Task has due_date <= $date AND (is not done OR was completed AFTER $date)
            $overdueTasksQuery = Task::where('workspace_id', $workspace->id)
                ->where(function($q) use ($date) {
                    $q->where('status', '!=', 'done')
                      ->orWhere(function($sub) use ($date) {
                          $sub->where('status', 'done')
                              ->whereDate('updated_at', '>', $date);
                      });
                })
                ->whereDate('due_date', '<=', $date);
            if ($this->userRole === 'member') {
                $overdueTasksQuery->where('created_by', Auth::id());
            }
            $overdue = $overdueTasksQuery->with(['area', 'project'])->get();

            // 4. Rating Algorithm
            $completedCount = $completed->count();
            $overdueCount = $overdue->count();

            if ($completedCount > 0 && $overdueCount === 0) {
                $rating = 'baik';
                $statusText = 'Sangat Produktif';
                $description = 'Hari yang sangat produktif! Anda menyelesaikan tugas tanpa hambatan tenggat waktu.';
                $themeColor = 'success';
            } elseif ($completedCount > 0 && $overdueCount > 0) {
                $rating = 'cukup';
                $statusText = 'Cukup / Normal';
                $description = 'Progres tercapai dengan beberapa tugas selesai, namun ada tugas tertunda yang melewati tenggat.';
                $themeColor = 'warning';
            } elseif ($completedCount === 0 && $overdueCount > 0) {
                $rating = 'kurang';
                $statusText = 'Kurang Produktif';
                $description = 'Hari yang lambat. Tidak ada tugas diselesaikan hari ini dan ada tugas tertunda yang terlambat.';
                $themeColor = 'danger';
            } else {
                // completed === 0 && overdue === 0
                $rating = 'cukup';
                $statusText = 'Cukup / Normal';
                $description = 'Hari kerja normal tanpa tugas jatuh tempo atau penyelesaian baru.';
                $themeColor = 'secondary';
            }

            $reports[] = (object) [
                'date' => $date,
                'date_formatted' => $date->format('d M Y'),
                'is_today' => $date->isToday(),
                'rating' => $rating,
                'status_text' => $statusText,
                'description' => $description,
                'theme_color' => $themeColor,
                'completed' => $completed,
                'active' => $active,
                'overdue' => $overdue,
            ];
        }

        return $reports;
    }

    public function render()
    {
        return view('livewire.report-manager', [
            'reports' => $this->dailyReports
        ])->layout('partials.layouts.master');
    }
}
