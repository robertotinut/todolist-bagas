@extends('partials.layouts.master')

@section('title', 'Dashboard | SLADA')
@section('title-sub', 'Productivity')
@section('pagetitle', 'SLADA Dashboard')
@section('content')

    <!-- begin::App -->
    <div id="layout-wrapper">

        <!-- Stats Overview Cards -->
        <div class="row g-4 mb-4">
            <div class="col-xl-3 col-sm-6">
                <div class="card overflow-hidden border-0 shadow-sm" style="border-radius: 12px;">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="d-block text-muted mb-1 fs-13 fw-medium">Total Tugas</span>
                                <h3 class="mb-0 fw-bold text-dark">{{ $stats['total'] }}</h3>
                            </div>
                            <div class="avatar-md rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fs-20" style="width: 45px; height: 45px;">
                                <i class="bi bi-list-task"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card overflow-hidden border-0 shadow-sm" style="border-radius: 12px;">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="d-block text-muted mb-1 fs-13 fw-medium">Tugas Pending</span>
                                <h3 class="mb-0 fw-bold text-dark">{{ $stats['pending'] }}</h3>
                            </div>
                            <div class="avatar-md rounded-circle bg-warning-subtle text-warning d-flex align-items-center justify-content-center fs-20" style="width: 45px; height: 45px;">
                                <i class="bi bi-clock-history"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card overflow-hidden border-0 shadow-sm" style="border-radius: 12px;">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="d-block text-muted mb-1 fs-13 fw-medium">Tugas Selesai</span>
                                <h3 class="mb-0 fw-bold text-dark">{{ $stats['completed'] }}</h3>
                            </div>
                            <div class="avatar-md rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center fs-20" style="width: 45px; height: 45px;">
                                <i class="bi bi-check2-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card overflow-hidden border-0 shadow-sm" style="border-radius: 12px;">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div>
                                <span class="d-block text-muted mb-1 fs-13 fw-medium">Total Project</span>
                                <h3 class="mb-0 fw-bold text-dark">{{ $stats['projects'] }}</h3>
                            </div>
                            <div class="avatar-md rounded-circle bg-info-subtle text-info d-flex align-items-center justify-content-center fs-20" style="width: 45px; height: 45px;">
                                <i class="bi bi-folder-fill"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Left Panel (Col 8) -->
            <div class="col-xl-8 col-lg-7">
                <!-- Project Progress Card -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                    <div class="card-header border-bottom py-3 bg-transparent">
                        <h5 class="card-title mb-0 fw-bold text-dark">Progres Penyelesaian Project</h5>
                    </div>
                    <div class="card-body">
                        @forelse($projectsProgress as $proj)
                            <div class="mb-4 last-mb-0">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-semibold text-dark fs-14">{{ $proj->name }}</span>
                                    <span class="text-muted fs-12 fw-medium">{{ $proj->rate }}% ({{ $proj->completed }}/{{ $proj->total }} Tugas)</span>
                                </div>
                                <div class="progress" style="height: 8px; border-radius: 4px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $proj->rate }}%" aria-valuenow="{{ $proj->rate }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-6 text-muted">
                                <i class="bi bi-folder-x fs-32 d-block mb-2"></i> Belum ada aktivitas project saat ini.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Upcoming Tasks Card -->
                <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                    <div class="card-header border-bottom py-3 bg-transparent d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 fw-bold text-dark">Tugas Mendatang (Due Soon)</h5>
                        <a href="{{ route('tasks') }}" class="btn btn-sm btn-outline-primary px-3 rounded-pill">Lihat Semua</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Judul Tugas</th>
                                        <th>Kategori</th>
                                        <th>Prioritas</th>
                                        <th>Tenggat Waktu</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentTasks as $task)
                                        @php
                                            $isOverdue = \Carbon\Carbon::parse($task->due_date)->isPast() && $task->status !== 'done';
                                        @endphp
                                        <tr>
                                            <td>
                                                <span class="fw-semibold text-dark d-block">{{ $task->title }}</span>
                                                @if($task->project)
                                                    <small class="text-muted"><i class="bi bi-folder me-1"></i>{{ $task->project->name }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $areaStyles = match($task->area->color) {
                                                        'rose' => 'background-color: rgba(244, 63, 94, 0.12); color: #f43f5e;',
                                                        'emerald' => 'background-color: rgba(16, 185, 129, 0.12); color: #10b981;',
                                                        'purple' => 'background-color: rgba(139, 92, 246, 0.12); color: #8b5cf6;',
                                                        'orange' => 'background-color: rgba(249, 115, 22, 0.12); color: #f97316;',
                                                        'dark' => 'background-color: rgba(31, 41, 55, 0.12); color: #1f2937;',
                                                        default => 'background-color: rgba(13, 110, 253, 0.12); color: #0d6efd;'
                                                    };
                                                @endphp
                                                <span class="badge px-2.5 py-1 text-capitalize" style="{{ $areaStyles }}">
                                                    {{ $task->area->icon }} {{ $task->area->name }}
                                                </span>
                                            </td>
                                            <td>
                                                @php
                                                    $priorityBadge = match($task->priority) {
                                                        'critical' => 'bg-danger text-white',
                                                        'high' => 'bg-warning text-dark',
                                                        'low' => 'bg-success text-white',
                                                        default => 'bg-primary text-white'
                                                    };
                                                @endphp
                                                <span class="badge {{ $priorityBadge }} text-capitalize text-uppercase fs-10 px-2 py-0.5 rounded">
                                                    {{ $task->priority }}
                                                </span>
                                            </td>
                                            <td class="{{ $isOverdue ? 'text-danger fw-bold' : 'text-muted' }}">
                                                <i class="bi {{ $isOverdue ? 'bi-exclamation-circle-fill' : 'bi-calendar-event' }} me-1"></i>
                                                {{ \Carbon\Carbon::parse($task->due_date)->format('d M Y, H:i') }}
                                            </td>
                                            <td>
                                                @php
                                                    $statusBadge = match($task->status) {
                                                        'in_progress' => 'bg-warning-subtle text-warning',
                                                        'done' => 'bg-success-subtle text-success',
                                                        default => 'bg-primary-subtle text-primary'
                                                    };
                                                @endphp
                                                <span class="badge {{ $statusBadge }} text-capitalize px-3 py-1">
                                                    {{ $task->status === 'in_progress' ? 'In Progress' : ($task->status === 'done' ? 'Completed' : 'To Do') }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-6 text-muted">
                                                <i class="bi bi-inbox fs-32 d-block mb-2"></i> Tidak ada tugas mendasar yang mendekati tenggat.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Panel (Col 4) -->
            <div class="col-xl-4 col-lg-5">
                <!-- Task Category Distribution -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                    <div class="card-header border-bottom py-3 bg-transparent">
                        <h5 class="card-title mb-0 fw-bold text-dark">Distribusi Tugas Kategori</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            @forelse($categoryStats as $cat)
                                @php
                                    $iconStyles = match($cat->color) {
                                        'rose' => 'background-color: rgba(244, 63, 94, 0.12); color: #f43f5e;',
                                        'emerald' => 'background-color: rgba(16, 185, 129, 0.12); color: #10b981;',
                                        'purple' => 'background-color: rgba(139, 92, 246, 0.12); color: #8b5cf6;',
                                        'orange' => 'background-color: rgba(249, 115, 22, 0.12); color: #f97316;',
                                        'dark' => 'background-color: rgba(31, 41, 55, 0.12); color: #1f2937;',
                                        default => 'background-color: rgba(13, 110, 253, 0.12); color: #0d6efd;'
                                    };
                                @endphp
                                <li class="list-group-item d-flex justify-content-between align-items-center py-3 px-0 border-bottom-dashed last-border-0">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-sm rounded-circle d-flex align-items-center justify-content-center fs-18" style="width: 36px; height: 36px; {{ $iconStyles }}">
                                            {{ $cat->icon ?: '📂' }}
                                        </div>
                                        <span class="fw-semibold text-dark fs-14">{{ $cat->name }}</span>
                                    </div>
                                    <span class="badge bg-secondary-subtle text-dark border px-2.5 py-1 rounded-pill">{{ $cat->count }} Tugas</span>
                                </li>
                            @empty
                                <li class="text-center py-6 text-muted list-group-item px-0 border-0">
                                    <i class="bi bi-tag fs-32 d-block mb-2"></i> Belum ada aktivitas tugas per kategori.
                                </li>
                            @endforelse
                        </ul>
                    </div>
                </div>

                <!-- Kanban Board Banner Shortcut -->
                <div class="card border-0 text-white shadow-sm overflow-hidden" style="border-radius: 12px; background: linear-gradient(135deg, #f97316 0%, #ea5455 100%);">
                    <div class="card-body p-4 position-relative z-1">
                        <h5 class="fw-bold mb-1 text-white">Kelola Papan Kanban Anda</h5>
                        <p class="fs-13 text-white-50 mb-4">Masuk ke papan kerja interaktif untuk mulai memindahkan status tugas, checklist subtask, dan prioritas.</p>
                        <a href="{{ route('tasks') }}" class="btn btn-white text-primary btn-sm px-4 rounded-pill shadow-sm fw-semibold" style="background: white !important; color: #ea5455 !important;">
                            Buka Kanban Board <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- end::App -->

@endsection
