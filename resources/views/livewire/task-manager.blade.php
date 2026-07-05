<div>
    @if($selectedAreaId === null)
        <!-- LANDING VIEW: Category Selector Grid -->
        <div class="mb-4">
            <h4 class="fw-bold text-dark mb-1">Pilih Kategori Kerja</h4>
            <p class="text-muted fs-13">Pilih salah satu kategori di bawah ini untuk melihat papan tugas (Kanban) atau catatan tabel tugas terkait.</p>
        </div>

        <div class="row g-4">
            @foreach($categoryStats as $cat)
                @php
                    $themeColor = match($cat->color) {
                        'rose' => '#f43f5e',
                        'emerald' => '#10b981',
                        'purple' => '#8b5cf6',
                        'orange' => '#f97316',
                        'dark' => '#1f2937',
                        default => '#0d6efd'
                    };
                    $rgbColor = match($cat->color) {
                        'rose' => '244, 63, 94',
                        'emerald' => '16, 185, 129',
                        'purple' => '139, 92, 246',
                        'orange' => '249, 115, 22',
                        'dark' => '31, 41, 55',
                        default => '13, 110, 253'
                    };
                @endphp
                <div class="col-xl-3 col-md-4 col-sm-6">
                    <div class="card border border-light shadow-sm h-100 cursor-pointer card-hover-effect" 
                         wire:click="selectArea({{ $cat->id }})" 
                         style="border-radius: 12px; background: #fff; transition: all 0.2s ease;">
                        <div class="card-body p-4 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-4">
                                    <div class="avatar-md rounded-circle d-flex align-items-center justify-content-center fs-22 shadow-sm" style="width: 44px; height: 44px; background-color: rgba({{ $rgbColor }}, 0.12) !important; color: {{ $themeColor }};">
                                        {{ $cat->icon ?: '📂' }}
                                    </div>
                                    <span class="badge bg-light text-muted border px-2.5 py-1 rounded-pill fs-11" style="font-weight: 500;">
                                        {{ $cat->total }} Tugas
                                    </span>
                                </div>
                                <h5 class="fw-bold text-dark mb-1">{{ $cat->name }}</h5>
                                <p class="text-muted fs-12 mb-4">{{ $cat->completed }} dari {{ $cat->total }} tugas selesai</p>
                            </div>

                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-1 fs-11 text-muted">
                                    <span>Penyelesaian</span>
                                    <span class="fw-semibold text-dark">{{ $cat->rate }}%</span>
                                </div>
                                <div class="progress" style="height: 5px; border-radius: 3px; background-color: #f1f3f7;">
                                    <div class="progress-bar" role="progressbar" style="width: {{ $cat->rate }}%; background-color: {{ $themeColor }};"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <style>
            .card-hover-effect:hover {
                transform: translateY(-4px);
                box-shadow: 0 .5rem 1.5rem rgba(0,0,0,0.08) !important;
            }
        </style>
    @else
        <!-- WORKSPACE VIEW: Category Tasks (Kanban / Table) -->
        @php
            $activeArea = $this->selectedAreaModel;
            $themeColor = match($activeArea->color) {
                'rose' => '#f43f5e',
                'emerald' => '#10b981',
                'purple' => '#8b5cf6',
                'orange' => '#f97316',
                'dark' => '#1f2937',
                default => '#0d6efd'
            };
        @endphp
        
        <!-- Header Toolbar -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
            <div class="card-body py-3">
                <div class="row g-3 align-items-center justify-content-between">
                    <!-- Navigation / Current Context -->
                    <div class="col-xl-6 col-md-5 d-flex align-items-center gap-3">
                        <button type="button" wire:click="backToCategories" class="btn btn-sm btn-outline-light border text-dark px-3 rounded-pill">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </button>
                        <div class="d-flex align-items-center gap-2">
                            <span class="fs-20">{{ $activeArea->icon ?: '📂' }}</span>
                            <h4 class="mb-0 fw-bold text-dark">{{ $activeArea->name }}</h4>
                        </div>
                    </div>

                    <!-- View Toggle Tabs -->
                    <div class="col-xl-4 col-md-4 d-flex justify-content-md-center">
                        <div class="btn-group btn-group-sm p-1 bg-light rounded-pill" style="border: 1px solid #e9ecef;">
                            <button type="button" wire:click="setView('kanban')" class="btn px-4 rounded-pill fw-semibold {{ $currentView === 'kanban' ? 'btn-white shadow-sm text-primary' : 'btn-light border-0 text-muted' }}" style="{{ $currentView === 'kanban' ? 'background: white !important;' : '' }}">
                                <i class="bi bi-kanban me-1"></i> Kanban Board
                            </button>
                            <button type="button" wire:click="setView('table')" class="btn px-4 rounded-pill fw-semibold {{ $currentView === 'table' ? 'btn-white shadow-sm text-primary' : 'btn-light border-0 text-muted' }}" style="{{ $currentView === 'table' ? 'background: white !important;' : '' }}">
                                <i class="bi bi-table me-1"></i> Tabel Tugas
                            </button>
                        </div>
                    </div>

                    <!-- Form Trigger Button -->
                    <div class="col-xl-2 col-md-3 text-end">
                        <button type="button" wire:click="toggleForm" class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1 shadow-sm px-4">
                            @if($showForm)
                                <i class="bi bi-x-circle"></i> Tutup Form
                            @else
                                <i class="bi bi-plus-circle"></i> Tambah Tugas
                            @endif
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Livewire Status / Error Alerts -->
        <div class="row g-2">
            @if (session()->has('message'))
                <div class="col-12">
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i> {{ session('message') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            @endif

            @if (session()->has('error'))
                <div class="col-12">
                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            @endif
        </div>

        <!-- Task Form (Locked Category Context) -->
        @if($showForm)
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                <div class="card-header border-bottom py-3 bg-light" style="border-top-left-radius: 12px; border-top-right-radius: 12px;">
                    <h5 class="card-title mb-0 fw-bold text-dark">{{ $isEditing ? 'Ubah Tugas' : 'Tambah Tugas Baru' }}</h5>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="saveTask">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="title" class="form-label fw-semibold text-muted fs-13">Judul Tugas *</label>
                                    <input type="text" id="title" wire:model="title" class="form-control @error('title') is-invalid @enderror" placeholder="Tulis judul tugas...">
                                    @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="description" class="form-label fw-semibold text-muted fs-13">Deskripsi</label>
                                    <textarea id="description" wire:model="description" rows="4" class="form-control @error('description') is-invalid @enderror" placeholder="Deskripsikan detail tugas..."></textarea>
                                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="formAttachmentFile" class="form-label fw-semibold text-muted fs-13">Lampiran Berkas (Opsional)</label>
                                    <input type="file" id="formAttachmentFile" wire:model="formAttachmentFile" class="form-control form-control-sm @error('formAttachmentFile') is-invalid @enderror">
                                    <div wire:loading wire:target="formAttachmentFile" class="text-muted fs-11 mt-1">
                                        <span class="spinner-border spinner-border-sm text-primary" role="status" style="width: 12px; height: 12px;"></span> Mengunggah berkas sementara...
                                    </div>
                                    @error('formAttachmentFile') <div class="text-danger fs-12 mt-1">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="row g-2">
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                            <label for="area_id" class="form-label fw-semibold text-muted fs-13">Kategori (Area)</label>
                                            <input type="text" class="form-control bg-light text-muted" value="{{ $activeArea->icon }} {{ $activeArea->name }}" disabled>
                                            <input type="hidden" wire:model="area_id">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                            <label for="project_id" class="form-label fw-semibold text-muted fs-13">Project</label>
                                            <select id="project_id" wire:model="project_id" class="form-select @error('project_id') is-invalid @enderror">
                                                <option value="">Tanpa Project</option>
                                                @foreach($this->filteredFormProjects as $proj)
                                                    <option value="{{ $proj->id }}">{{ $proj->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('project_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-2">
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                            <label for="priority" class="form-label fw-semibold text-muted fs-13">Prioritas</label>
                                            <select id="priority" wire:model="priority" class="form-select @error('priority') is-invalid @enderror">
                                                <option value="low">Low</option>
                                                <option value="medium">Medium</option>
                                                <option value="high">High</option>
                                                <option value="critical">Critical</option>
                                            </select>
                                            @error('priority') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                            <label for="estimate_time" class="form-label fw-semibold text-muted fs-13">Estimasi (Menit)</label>
                                            <input type="number" id="estimate_time" wire:model="estimate_time" class="form-control @error('estimate_time') is-invalid @enderror" placeholder="Contoh: 60">
                                            @error('estimate_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="due_date" class="form-label fw-semibold text-muted fs-13">Tenggat Waktu</label>
                                    <input type="datetime-local" id="due_date" wire:model="due_date" class="form-control @error('due_date') is-invalid @enderror">
                                    @error('due_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <!-- Subtasks checklist builder -->
                            <div class="col-12 border-top pt-3 mt-2">
                                <label class="form-label fw-semibold text-muted fs-13">Sub-Tugas (Checklist)</label>
                                <div class="input-group mb-3" style="max-width: 500px;">
                                    <input type="text" wire:model="subtaskTitle" wire:keydown.enter.prevent="addTempSubtask" class="form-control form-control-sm" placeholder="Tulis sub-tugas baru...">
                                    <button type="button" wire:click="addTempSubtask" class="btn btn-sm btn-outline-primary"><i class="bi bi-plus-lg"></i> Tambah</button>
                                </div>

                                @if(count($tempSubtasks) > 0)
                                    <div class="d-flex flex-wrap gap-2 mb-3">
                                        @foreach($tempSubtasks as $idx => $st)
                                            <span class="badge bg-secondary-subtle text-dark border px-3 py-2 d-flex align-items-center gap-2 rounded" style="font-weight: normal;">
                                                <span>{{ $st }}</span>
                                                <button type="button" wire:click="removeTempSubtask({{ $idx }})" class="btn-close" style="width: 8px; height: 8px; font-size: 8px; padding: 0;"></button>
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <!-- Form Actions -->
                            <div class="col-12 border-top pt-3 d-flex gap-2 justify-content-end">
                                <button type="button" wire:click="toggleForm" class="btn btn-sm btn-light px-4">Batal</button>
                                <button type="submit" class="btn btn-sm btn-primary px-4"><i class="bi bi-save"></i> {{ $isEditing ? 'Simpan Perubahan' : 'Tambah Tugas' }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        @if($currentView === 'kanban')
            <!-- KANBAN BOARD VIEW -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                <div class="card-body py-3 bg-light-subtle rounded-3">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-3">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
                                <input type="text" wire:model.live.debounce.300ms="search" class="form-control bg-light border-0" placeholder="Cari tugas...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select wire:model.live="filterProject" class="form-select form-select-sm bg-light border-0 text-muted">
                                <option value="">Semua Project</option>
                                @foreach($this->projects as $proj)
                                    <option value="{{ $proj->id }}">{{ $proj->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select wire:model.live="filterPriority" class="form-select form-select-sm bg-light border-0 text-muted">
                                <option value="">Semua Prioritas</option>
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                                <option value="critical">Critical</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select wire:model.live="filterDueDate" class="form-select form-select-sm bg-light border-0 text-muted">
                                <option value="">Semua Tenggat</option>
                                <option value="today">Hari Ini</option>
                                <option value="week">Minggu Ini</option>
                                <option value="overdue">Terlambat</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kanban Columns -->
            <div class="row g-3">
                <!-- Column To Do -->
                <div class="col-lg-4 col-md-6">
                    <div class="card bg-light border-0" style="border-radius: 12px; min-height: 500px;">
                        <div class="card-header border-0 bg-transparent py-3 d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-2">
                                <span class="h-8px w-8px rounded-circle bg-primary d-inline-block"></span>
                                <h6 class="mb-0 fw-bold text-dark">To Do</h6>
                            </div>
                            <span class="badge bg-primary-subtle text-primary rounded-pill px-2.5">{{ $todoTasks->count() }}</span>
                        </div>
                        <div class="card-body p-2 d-flex flex-column gap-2" style="max-height: 650px; overflow-y: auto;">
                            @forelse($todoTasks as $task)
                                @include('partials.task-card', ['task' => $task, 'column' => 'todo'])
                            @empty
                                <div class="text-center py-8 text-muted border border-dashed rounded-3 bg-white">
                                    <i class="bi bi-inbox fs-24 d-block mb-1 opacity-50"></i>
                                    <span class="fs-12">Belum ada tugas</span>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Column In Progress -->
                <div class="col-lg-4 col-md-6">
                    <div class="card bg-light border-0" style="border-radius: 12px; min-height: 500px;">
                        <div class="card-header border-0 bg-transparent py-3 d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-2">
                                <span class="h-8px w-8px rounded-circle bg-warning d-inline-block"></span>
                                <h6 class="mb-0 fw-bold text-dark">In Progress</h6>
                            </div>
                            <span class="badge bg-warning-subtle text-warning rounded-pill px-2.5">{{ $inProgressTasks->count() }}</span>
                        </div>
                        <div class="card-body p-2 d-flex flex-column gap-2" style="max-height: 650px; overflow-y: auto;">
                            @forelse($inProgressTasks as $task)
                                @include('partials.task-card', ['task' => $task, 'column' => 'in_progress'])
                            @empty
                                <div class="text-center py-8 text-muted border border-dashed rounded-3 bg-white">
                                    <i class="bi bi-activity fs-24 d-block mb-1 opacity-50"></i>
                                    <span class="fs-12">Belum ada tugas berjalan</span>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Column Completed -->
                <div class="col-lg-4 col-md-6">
                    <div class="card bg-light border-0" style="border-radius: 12px; min-height: 500px;">
                        <div class="card-header border-0 bg-transparent py-3 d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-2">
                                <span class="h-8px w-8px rounded-circle bg-success d-inline-block"></span>
                                <h6 class="mb-0 fw-bold text-dark">Completed</h6>
                            </div>
                            <span class="badge bg-success-subtle text-success rounded-pill px-2.5">{{ $doneTasks->count() }}</span>
                        </div>
                        <div class="card-body p-2 d-flex flex-column gap-2" style="max-height: 650px; overflow-y: auto;">
                            @forelse($doneTasks as $task)
                                @include('partials.task-card', ['task' => $task, 'column' => 'done'])
                            @empty
                                <div class="text-center py-8 text-muted border border-dashed rounded-3 bg-white">
                                    <i class="bi bi-check2-all fs-24 d-block mb-1 opacity-50"></i>
                                    <span class="fs-12">Belum ada tugas selesai</span>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- RECORD TASK: TABLE VIEW -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                <!-- Table View Controls -->
                <div class="card-header border-bottom py-3 bg-transparent d-flex flex-wrap gap-3 align-items-center justify-content-between">
                    <!-- Table Search & Filter -->
                    <div class="d-flex align-items-center gap-3">
                        <div class="input-group input-group-sm" style="max-width: 250px;">
                            <span class="input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
                            <input type="text" wire:model.live.debounce.300ms="search" class="form-control bg-light border-0" placeholder="Cari tugas...">
                        </div>
                        <select wire:model.live="filterProject" class="form-select form-select-sm bg-light border-0 text-muted" style="max-width: 200px;">
                            <option value="">Semua Project</option>
                            @foreach($this->projects as $proj)
                                <option value="{{ $proj->id }}">{{ $proj->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Daily Focus Tabs -->
                    <div class="btn-group btn-group-sm rounded-pill p-0.5 bg-light" style="border: 1px solid #e9ecef;">
                        <button type="button" wire:click="setTableFilter('all')" class="btn px-3 rounded-pill fw-semibold {{ $tableFilter === 'all' ? 'btn-white shadow-sm text-primary' : 'btn-light border-0 text-muted' }}" style="{{ $tableFilter === 'all' ? 'background: white !important;' : '' }}">
                            Semua
                        </button>
                        <button type="button" wire:click="setTableFilter('today')" class="btn px-3 rounded-pill fw-semibold {{ $tableFilter === 'today' ? 'btn-white shadow-sm text-primary' : 'btn-light border-0 text-muted' }}" style="{{ $tableFilter === 'today' ? 'background: white !important;' : '' }}">
                            📅 Hari Ini
                        </button>
                        <button type="button" wire:click="setTableFilter('important')" class="btn px-3 rounded-pill fw-semibold {{ $tableFilter === 'important' ? 'btn-white shadow-sm text-primary' : 'btn-light border-0 text-muted' }}" style="{{ $tableFilter === 'important' ? 'background: white !important;' : '' }}">
                            🔥 Penting
                        </button>
                    </div>
                </div>

                <!-- Table Content -->
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Judul Tugas</th>
                                    <th>Project</th>
                                    <th>Prioritas</th>
                                    <th>Tenggat Waktu</th>
                                    <th>Checklist</th>
                                    <th>Status</th>
                                    <th class="text-end px-6">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($allTasks as $task)
                                    @php
                                        $isOverdue = \Carbon\Carbon::parse($task->due_date)->isPast() && $task->status !== 'done';
                                        $subCount = $task->subtasks->count();
                                        $subCompleted = $task->subtasks->where('is_completed', true)->count();
                                        
                                        // Dynamic tag/highlight if task is due today or in progress
                                        $isTodayTask = ($task->due_date && \Carbon\Carbon::parse($task->due_date)->isToday()) || $task->status === 'in_progress';
                                    @endphp
                                    <tr class="{{ $task->status === 'done' ? 'table-light opacity-75' : '' }}">
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                @if($isTodayTask && $task->status !== 'done')
                                                    <span class="badge bg-warning text-dark fs-10 text-uppercase px-1.5 py-0.5 rounded">Hari Ini</span>
                                                @endif
                                                <span wire:click="openTaskDetail({{ $task->id }})" class="fw-semibold text-dark fs-14 cursor-pointer" style="cursor: pointer;">{{ $task->title }}</span>
                                            </div>
                                            @if($task->description)
                                                <small class="text-muted d-block text-truncate" style="max-width: 300px;">{{ $task->description }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($task->project)
                                                <span class="text-dark fs-13"><i class="bi bi-folder text-muted me-1"></i>{{ $task->project->name }}</span>
                                            @else
                                                <span class="text-muted fs-13">-</span>
                                            @endif
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
                                        <td class="{{ $isOverdue ? 'text-danger fw-bold' : 'text-muted' }} fs-13">
                                            @if($task->due_date)
                                                <i class="bi {{ $isOverdue ? 'bi-exclamation-octagon-fill text-danger' : 'bi-calendar-event' }} me-1"></i>
                                                {{ \Carbon\Carbon::parse($task->due_date)->format('d M Y, H:i') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if($subCount > 0)
                                                <div class="d-flex align-items-center gap-2" style="min-width: 120px;">
                                                    <div class="progress flex-grow-1" style="height: 5px;">
                                                        <div class="progress-bar bg-success" style="width: {{ round(($subCompleted / $subCount) * 100) }}%"></div>
                                                    </div>
                                                    <small class="text-muted fs-11">{{ $subCompleted }}/{{ $subCount }}</small>
                                                </div>
                                            @else
                                                <small class="text-muted fs-12">-</small>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $statusClass = match($task->status) {
                                                    'in_progress' => 'bg-warning-subtle text-warning',
                                                    'done' => 'bg-success-subtle text-success',
                                                    default => 'bg-primary-subtle text-primary'
                                                };
                                            @endphp
                                            <span class="badge {{ $statusClass }} text-capitalize px-3 py-1">
                                                {{ $task->status === 'in_progress' ? 'In Progress' : ($task->status === 'done' ? 'Completed' : 'To Do') }}
                                            </span>
                                        </td>
                                        <td class="text-end px-6">
                                            <div class="d-inline-flex gap-2">
                                                <!-- Move column quick-buttons -->
                                                @if($task->status === 'todo')
                                                    <button type="button" wire:click="changeStatus({{ $task->id }}, 'in_progress')" class="btn btn-xs btn-outline-warning py-0.5 px-2 fs-11 rounded" title="Mulai Kerja">Mulai</button>
                                                @elseif($task->status === 'in_progress')
                                                    <button type="button" wire:click="changeStatus({{ $task->id }}, 'done')" class="btn btn-xs btn-success text-white py-0.5 px-2 fs-11 rounded" title="Selesaikan">Selesai</button>
                                                @elseif($task->status === 'done')
                                                    <button type="button" wire:click="changeStatus({{ $task->id }}, 'in_progress')" class="btn btn-xs btn-outline-primary py-0.5 px-2 fs-11 rounded" title="Buka Kembali">Reopen</button>
                                                @endif

                                                <!-- Edit & Delete -->
                                                <button type="button" wire:click="editTask({{ $task->id }})" class="btn btn-sm btn-outline-primary border-0" title="Ubah">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>
                                                <button type="button" 
                                                        onclick="Swal.fire({
                                                            title: 'Hapus Tugas?',
                                                            text: 'Apakah Anda yakin ingin menghapus tugas ini?',
                                                            icon: 'warning',
                                                            showCancelButton: true,
                                                            confirmButtonColor: '#ea5455',
                                                            cancelButtonColor: '#6c757d',
                                                            confirmButtonText: 'Ya, Hapus!',
                                                            cancelButtonText: 'Batal'
                                                        }).then((result) => {
                                                            if (result.isConfirmed) {
                                                                    @this.call('deleteTask', {{ $task->id }})
                                                            }
                                                        })" 
                                                        class="btn btn-sm btn-outline-danger border-0" 
                                                        title="Hapus">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-8 text-muted">
                                            <i class="bi bi-inbox fs-32 d-block mb-2"></i> Belum ada tugas di bawah filter ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    @endif

    <!-- Task Detail Modal -->
    @if($showDetailModal && $detailTask)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5); z-index: 1050; overflow-y: auto;">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow" style="border-radius: 12px;">
                    <!-- Modal Header -->
                    <div class="modal-header border-bottom py-3 bg-light" style="border-top-left-radius: 12px; border-top-right-radius: 12px;">
                        <div class="d-flex align-items-center gap-2">
                            <span class="fs-18">{{ $detailTask->area->icon }}</span>
                            <span class="badge bg-light text-muted border px-2 py-0.5 fs-11">{{ $detailTask->area->name }}</span>
                            <h5 class="modal-title fw-bold text-dark mb-0 ms-2">Detail Tugas</h5>
                        </div>
                        <button type="button" wire:click="closeDetailModal" class="btn-close" aria-label="Close"></button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body p-4">
                        <div class="row g-4">
                            <!-- Left Column: Details, Subtasks, Attachments -->
                            <div class="col-md-7">
                                <!-- Title & Description -->
                                <div class="mb-4">
                                    <h4 class="fw-bold text-dark mb-2">{{ $detailTask->title }}</h4>
                                    <div class="bg-light p-3 rounded" style="min-height: 80px;">
                                        @if($detailTask->description)
                                            <p class="text-muted fs-13 mb-0" style="white-space: pre-wrap; line-height: 1.6;">{{ $detailTask->description }}</p>
                                        @else
                                            <p class="text-muted fs-13 mb-0 italic">Tidak ada deskripsi detail untuk tugas ini.</p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Subtasks Checklist -->
                                <div class="mb-4">
                                    <h6 class="fw-bold text-dark mb-2.5 d-flex justify-content-between align-items-center">
                                        <span>Checklist Sub-Tugas</span>
                                        @php
                                            $subCount = $detailTask->subtasks->count();
                                            $subCompleted = $detailTask->subtasks->where('is_completed', true)->count();
                                            $percent = $subCount > 0 ? round(($subCompleted / $subCount) * 100) : 0;
                                        @endphp
                                        <small class="text-muted fs-11 fw-normal">{{ $subCompleted }}/{{ $subCount }} ({{ $percent }}%)</small>
                                    </h6>
                                    @if($subCount > 0)
                                        <div class="progress mb-3" style="height: 5px;">
                                            <div class="progress-bar bg-success" style="width: {{ $percent }}%"></div>
                                        </div>
                                        <div class="d-flex flex-column gap-2 bg-light p-3 rounded" style="max-height: 180px; overflow-y: auto;">
                                            @foreach($detailTask->subtasks as $sub)
                                                <label class="d-flex align-items-center gap-2 mb-0 cursor-pointer" style="user-select: none;">
                                                    <input type="checkbox" wire:click="toggleSubtask({{ $sub->id }})" {{ $sub->is_completed ? 'checked' : '' }} class="form-check-input" style="width: 15px; height: 15px; margin-top: 0;">
                                                    <span class="fs-13 {{ $sub->is_completed ? 'text-decoration-line-through text-muted' : 'text-dark' }}">{{ $sub->title }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-muted fs-13 mb-0">Tidak ada checklist sub-tugas.</p>
                                    @endif
                                </div>

                                <!-- Attachments List -->
                                <div class="mb-2">
                                    <h6 class="fw-bold text-dark mb-2.5">Berkas Lampiran ({{ $detailTask->attachments->count() }})</h6>
                                    @if($detailTask->attachments->count() > 0)
                                        <div class="d-flex flex-column gap-2 mb-3">
                                            @foreach($detailTask->attachments as $att)
                                                <div class="p-2 border.5 bg-light rounded d-flex align-items-center justify-content-between">
                                                    <div class="d-flex align-items-center gap-2" style="min-width: 0;">
                                                        <i class="bi bi-file-earmark-text text-primary fs-18"></i>
                                                        <div style="min-width: 0;">
                                                            <span class="d-block text-dark fw-medium fs-12 text-truncate" title="{{ $att->file_name }}" style="max-width: 220px;">
                                                                {{ $att->file_name }}
                                                            </span>
                                                            <small class="text-muted fs-10 d-block">
                                                                {{ $att->file_size }} • Oleh {{ $att->uploader ? $att->uploader->name : 'System' }}
                                                            </small>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex gap-1.5 ms-2">
                                                        <a href="{{ asset('storage/' . $att->file_path) }}" target="_blank" class="btn btn-sm btn-outline-secondary py-1 px-2.5 fs-11" download>
                                                            <i class="bi bi-download"></i> Unduh
                                                        </a>
                                                        <button type="button" 
                                                                wire:click="deleteAttachment({{ $att->id }})" 
                                                                class="btn btn-sm btn-outline-danger py-1 px-2.5 fs-11 border-0" 
                                                                title="Hapus Lampiran">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-muted fs-13 mb-3">Belum ada file lampiran.</p>
                                    @endif

                                    <!-- Upload File Area -->
                                    <div class="border-top pt-3">
                                        <label class="form-label fw-bold text-dark fs-12 mb-1">Unggah Lampiran Baru</label>
                                        <div class="input-group">
                                            <input type="file" wire:model="attachmentFile" class="form-control form-control-sm">
                                            <button type="button" wire:click="uploadAttachment" class="btn btn-sm btn-primary" wire:loading.attr="disabled" {{ !$attachmentFile ? 'disabled' : '' }}>
                                                <span wire:loading wire:target="attachmentFile" class="spinner-border spinner-border-sm me-1" role="status"></span>
                                                Unggah
                                            </button>
                                        </div>
                                        @error('attachmentFile') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                                        <small class="text-muted fs-10 mt-1 d-block">Maksimal ukuran berkas: 10MB.</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column: Task Meta Info -->
                            <div class="col-md-5 bg-light p-3 rounded" style="border: 1px solid #e9ecef;">
                                <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">Informasi Tugas</h6>

                                <div class="d-flex flex-column gap-3">
                                    <div>
                                        <span class="d-block text-muted fs-11 fw-semibold text-uppercase">Project</span>
                                        @if($detailTask->project)
                                            <span class="text-dark fw-semibold fs-13"><i class="bi bi-folder me-1"></i>{{ $detailTask->project->name }}</span>
                                        @else
                                            <span class="text-muted fs-13">-</span>
                                        @endif
                                    </div>

                                    <div>
                                        <span class="d-block text-muted fs-11 fw-semibold text-uppercase">Prioritas</span>
                                        @php
                                            $priorityBadge = match($detailTask->priority) {
                                                'critical' => 'bg-danger text-white',
                                                'high' => 'bg-warning text-dark',
                                                'low' => 'bg-success text-white',
                                                default => 'bg-primary text-white'
                                            };
                                        @endphp
                                        <span class="badge {{ $priorityBadge }} text-capitalize text-uppercase fs-10 px-2 py-0.5 rounded">
                                            {{ $detailTask->priority }}
                                        </span>
                                    </div>

                                    <div>
                                        <span class="d-block text-muted fs-11 fw-semibold text-uppercase">Status Kerja</span>
                                        @php
                                            $statusClass = match($detailTask->status) {
                                                'in_progress' => 'bg-warning-subtle text-warning',
                                                'done' => 'bg-success-subtle text-success',
                                                default => 'bg-primary-subtle text-primary'
                                            };
                                            $statusText = match($detailTask->status) {
                                                'in_progress' => 'In Progress',
                                                'done' => 'Completed',
                                                default => 'To Do'
                                            };
                                        @endphp
                                        <span class="badge {{ $statusClass }} text-capitalize px-3 py-1 fs-12">
                                            {{ $statusText }}
                                        </span>
                                    </div>

                                    <div>
                                        <span class="d-block text-muted fs-11 fw-semibold text-uppercase">Tenggat Waktu</span>
                                        @if($detailTask->due_date)
                                            @php
                                                $isOver = \Carbon\Carbon::parse($detailTask->due_date)->isPast() && $detailTask->status !== 'done';
                                            @endphp
                                            <span class="fs-13 fw-semibold {{ $isOver ? 'text-danger' : 'text-dark' }}">
                                                <i class="bi {{ $isOver ? 'bi-exclamation-triangle-fill text-danger' : 'bi-calendar' }} me-1"></i>
                                                {{ $detailTask->due_date->format('d M Y, H:i') }}
                                            </span>
                                        @else
                                            <span class="text-muted fs-13">-</span>
                                        @endif
                                    </div>

                                    @if($detailTask->estimate_time)
                                        <div>
                                            <span class="d-block text-muted fs-11 fw-semibold text-uppercase">Estimasi Waktu Kerja</span>
                                            <span class="text-dark fs-13"><i class="bi bi-hourglass-split me-1"></i>{{ $detailTask->estimate_time }} Menit</span>
                                        </div>
                                    @endif

                                    <div class="border-top pt-3 mt-1">
                                        <span class="d-block text-muted fs-10">Dibuat oleh: <strong>{{ $detailTask->creator ? $detailTask->creator->name : 'System' }}</strong></span>
                                        <span class="d-block text-muted fs-10">Pada tanggal: {{ $detailTask->created_at->format('d M Y, H:i') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
