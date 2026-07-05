<div class="card border-0 shadow-sm mb-1" style="border-radius: 8px; transition: all 0.2s ease;">
    <div class="card-body p-3">
        <!-- Card Header Tags -->
        <div class="d-flex justify-content-between align-items-start mb-2.5">
            <!-- Category Badge (HEX Styled) -->
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
            <span class="badge px-2.5 py-1 text-capitalize fs-11" style="{{ $areaStyles }}">
                {{ $task->area->icon }} {{ $task->area->name }}
            </span>

            <!-- Priority Badge -->
            @php
                $priorityBadge = match($task->priority) {
                    'critical' => 'bg-danger text-white',
                    'high' => 'bg-warning text-dark',
                    'low' => 'bg-success text-white',
                    default => 'bg-primary text-white'
                };
            @endphp
            <span class="badge {{ $priorityBadge }} fs-10 px-2 py-0.5 rounded text-uppercase" style="font-size: 9px; letter-spacing: 0.5px;">
                {{ $task->priority }}
            </span>
        </div>

        <!-- Task Title & Description -->
        <h6 wire:click="openTaskDetail({{ $task->id }})" class="fw-bold text-dark mb-1 fs-14 cursor-pointer" style="line-height: 1.4; cursor: pointer;">{{ $task->title }}</h6>
        @if($task->description)
            <p class="text-muted fs-12 mb-3 text-truncate-2" style="max-height: 36px; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; white-space: normal;">
                {{ $task->description }}
            </p>
        @endif

        <!-- Project Badge (If linked) -->
        @if($task->project)
            <div class="mb-3">
                <span class="badge bg-light text-muted border px-2.5 py-1 fs-11" style="font-weight: normal;">
                    <i class="bi bi-folder me-1"></i> {{ $task->project->name }}
                </span>
            </div>
        @endif

        <!-- Card Meta: Due date & Estimate -->
        <div class="d-flex align-items-center justify-content-between mb-3 border-top pt-2.5">
            <!-- Due Date -->
            @if($task->due_date)
                @php
                    $isOverdue = \Carbon\Carbon::parse($task->due_date)->isPast() && $task->status !== 'done';
                @endphp
                <div class="d-flex align-items-center gap-1 {{ $isOverdue ? 'text-danger fw-bold' : 'text-muted' }} fs-12">
                    <i class="bi {{ $isOverdue ? 'bi-exclamation-octagon-fill text-danger' : 'bi-calendar-event' }}"></i>
                    <span>{{ \Carbon\Carbon::parse($task->due_date)->format('d M Y, H:i') }}</span>
                </div>
            @else
                <span class="text-muted fs-12">-</span>
            @endif

            <!-- Estimate -->
            @if($task->estimate_time)
                <div class="d-flex align-items-center gap-1 text-muted fs-12" title="Estimasi Waktu Kerja">
                    <i class="bi bi-hourglass-split"></i>
                    <span>{{ $task->estimate_time }} mnt</span>
                </div>
            @endif
        </div>

        <!-- Subtasks Progress Bar & Checklist -->
        @php
            $subtasksCount = $task->subtasks->count();
            $completedSubtasksCount = $task->subtasks->where('is_completed', true)->count();
            $percent = $subtasksCount > 0 ? round(($completedSubtasksCount / $subtasksCount) * 100) : 0;
            $isExpanded = in_array($task->id, $expandedTaskIds);
        @endphp

        @if($subtasksCount > 0)
            <div class="border-top pt-2 mb-3">
                <!-- Progress Header -->
                <button type="button" wire:click="toggleExpand({{ $task->id }})" class="btn p-0 border-0 bg-transparent w-full text-start d-flex justify-content-between align-items-center mb-1">
                    <span class="text-muted fs-11 d-flex align-items-center gap-1">
                        <i class="bi {{ $isExpanded ? 'bi-chevron-down' : 'bi-chevron-right' }} fs-10"></i>
                        Checklist ({{ $completedSubtasksCount }}/{{ $subtasksCount }})
                    </span>
                    <span class="text-muted fs-11 fw-medium">{{ $percent }}%</span>
                </button>
                
                <!-- Progress Line -->
                <div class="progress" style="height: 4px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percent }}%"></div>
                </div>

                <!-- Expanded Checklist List -->
                @if($isExpanded)
                    <div class="d-flex flex-column gap-1.5 mt-2 bg-light p-2 rounded" style="max-height: 180px; overflow-y: auto;">
                        @foreach($task->subtasks as $sub)
                            <label class="d-flex align-items-center gap-2 mb-0 cursor-pointer" style="user-select: none;">
                                <input type="checkbox" wire:click="toggleSubtask({{ $sub->id }})" {{ $sub->is_completed ? 'checked' : '' }} class="form-check-input" style="width: 14px; height: 14px; margin-top: 0;">
                                <span class="fs-12 {{ $sub->is_completed ? 'text-decoration-line-through text-muted' : 'text-dark' }}">{{ $sub->title }}</span>
                            </label>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif

        <!-- Card Footer Actions -->
        <div class="d-flex justify-content-between align-items-center border-top pt-2.5">
            <!-- Move status controls -->
            <div class="d-flex gap-1">
                @if($column === 'todo')
                    <button type="button" wire:click="changeStatus({{ $task->id }}, 'in_progress')" class="btn btn-xs btn-outline-warning py-1 px-2.5 fs-11 d-inline-flex align-items-center gap-1 rounded" style="font-weight: 500;">
                        <i class="bi bi-play-fill"></i> Mulai
                    </button>
                    <button type="button" wire:click="changeStatus({{ $task->id }}, 'done')" class="btn btn-xs btn-outline-success py-1 px-2.5 fs-11 d-inline-flex align-items-center gap-1 rounded" style="font-weight: 500;">
                        <i class="bi bi-check-lg"></i> Selesai
                    </button>
                @elseif($column === 'in_progress')
                    <button type="button" wire:click="changeStatus({{ $task->id }}, 'done')" class="btn btn-xs btn-success text-white py-1 px-2.5 fs-11 d-inline-flex align-items-center gap-1 rounded" style="font-weight: 500;">
                        <i class="bi bi-check-lg"></i> Selesai
                    </button>
                    <button type="button" wire:click="changeStatus({{ $task->id }}, 'todo')" class="btn btn-xs btn-outline-secondary py-1 px-2.5 fs-11 d-inline-flex align-items-center gap-1 rounded" style="font-weight: 500;">
                        <i class="bi bi-arrow-left-short"></i> Undur
                    </button>
                @elseif($column === 'done')
                    <button type="button" wire:click="changeStatus({{ $task->id }}, 'in_progress')" class="btn btn-xs btn-outline-primary py-1 px-2.5 fs-11 d-inline-flex align-items-center gap-1 rounded" style="font-weight: 500;">
                        <i class="bi bi-arrow-counterclockwise"></i> Buka Kembali
                    </button>
                @endif
            </div>

            <!-- Edit & Delete controls -->
            <div class="d-flex gap-1">
                <button type="button" wire:click="editTask({{ $task->id }})" class="btn btn-sm btn-outline-primary border-0 py-1 px-1.5" title="Ubah">
                    <i class="bi bi-pencil-square fs-13"></i>
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
                        class="btn btn-sm btn-outline-danger border-0 py-1 px-1.5" 
                        title="Hapus">
                    <i class="bi bi-trash fs-13"></i>
                </button>
            </div>
        </div>
    </div>
</div>
