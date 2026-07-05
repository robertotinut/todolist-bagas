<div>
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">Laporan Produktivitas Otomatis</h4>
            <p class="text-muted fs-13">Evaluasi performa harian Anda secara otomatis berdasarkan data penyelesaian tugas dan tenggat waktu.</p>
        </div>
        
        <!-- Days Range Selector -->
        <div class="btn-group btn-group-sm rounded-pill p-0.5 bg-light" style="border: 1px solid #e9ecef;">
            <button type="button" wire:click="setViewDays(7)" class="btn px-3 rounded-pill fw-semibold {{ $viewDays === 7 ? 'btn-white shadow-sm text-primary' : 'btn-light border-0 text-muted' }}" style="{{ $viewDays === 7 ? 'background: white !important;' : '' }}">
                7 Hari
            </button>
            <button type="button" wire:click="setViewDays(14)" class="btn px-3 rounded-pill fw-semibold {{ $viewDays === 14 ? 'btn-white shadow-sm text-primary' : 'btn-light border-0 text-muted' }}" style="{{ $viewDays === 14 ? 'background: white !important;' : '' }}">
                14 Hari
            </button>
            <button type="button" wire:click="setViewDays(30)" class="btn px-3 rounded-pill fw-semibold {{ $viewDays === 30 ? 'btn-white shadow-sm text-primary' : 'btn-light border-0 text-muted' }}" style="{{ $viewDays === 30 ? 'background: white !important;' : '' }}">
                30 Hari
            </button>
        </div>
    </div>

    <!-- Timeline List -->
    <div class="row g-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header border-bottom py-3 bg-transparent">
                    <h5 class="card-title mb-0 fw-bold text-dark">Linimasa Produktivitas Harian</h5>
                </div>
                <div class="card-body p-0">
                    <div class="d-flex flex-column">
                        @foreach($reports as $rep)
                            <div class="p-4 border-bottom last-border-0">
                                <div class="row g-4 align-items-start">
                                    <!-- Date Section -->
                                    <div class="col-xl-2 col-md-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <h5 class="fw-bold text-dark mb-0">{{ $rep->date_formatted }}</h5>
                                            @if($rep->is_today)
                                                <span class="badge bg-primary text-white fs-10 px-2 py-0.5 rounded-pill">Hari Ini</span>
                                            @endif
                                        </div>
                                        <small class="text-muted d-block mt-1">{{ $rep->date->isoFormat('dddd') }}</small>
                                    </div>

                                    <!-- Rating & Auto-Assessment -->
                                    <div class="col-xl-10 col-md-9">
                                        <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                                            @if($rep->rating === 'baik')
                                                <span class="badge bg-success text-white px-2.5 py-1 fs-11">🟢 {{ $rep->status_text }}</span>
                                            @elseif($rep->rating === 'cukup')
                                                <span class="badge bg-warning text-dark px-2.5 py-1 fs-11">🟡 {{ $rep->status_text }}</span>
                                            @else
                                                <span class="badge bg-danger text-white px-2.5 py-1 fs-11">🔴 {{ $rep->status_text }}</span>
                                            @endif
                                        </div>
                                        
                                        <p class="text-muted fs-13 mb-3">{{ $rep->description }}</p>

                                        <!-- Details Grid: Completed, Active, and Overdue Tasks -->
                                        <div class="row g-3">
                                            <!-- Completed Tasks column -->
                                            <div class="col-md-4">
                                                <h6 class="fw-bold text-dark fs-12 mb-2">Tugas Diselesaikan ({{ $rep->completed->count() }})</h6>
                                                @forelse($rep->completed as $task)
                                                    <div class="p-2 border rounded bg-light mb-1.5 last-mb-0 d-flex align-items-center gap-2" style="font-size: 12px;">
                                                        <span>{{ $task->area->icon }}</span>
                                                        <span class="text-dark fw-medium text-truncate" title="{{ $task->title }}">{{ $task->title }}</span>
                                                    </div>
                                                @empty
                                                    <span class="text-muted fs-12 italic d-block">Tidak ada tugas yang diselesaikan.</span>
                                                @endforelse
                                            </div>

                                            <!-- Active Tasks column -->
                                            <div class="col-md-4">
                                                <h6 class="fw-bold text-dark fs-12 mb-2">Tugas Aktif Dikerjakan ({{ $rep->active->count() }})</h6>
                                                @forelse($rep->active as $task)
                                                    <div class="p-2 border rounded bg-light mb-1.5 last-mb-0 d-flex align-items-center gap-2" style="font-size: 12px;">
                                                        <span>{{ $task->area->icon }}</span>
                                                        <span class="text-dark fw-medium text-truncate" title="{{ $task->title }}">{{ $task->title }}</span>
                                                    </div>
                                                @empty
                                                    <span class="text-muted fs-12 italic d-block">Tidak ada aktivitas tugas.</span>
                                                @endforelse
                                            </div>

                                            <!-- Overdue Tasks (Impediments / Obstacles) -->
                                            <div class="col-md-4">
                                                <h6 class="fw-bold text-danger fs-12 mb-2">Kendala / Tugas Terlambat ({{ $rep->overdue->count() }})</h6>
                                                @forelse($rep->overdue as $task)
                                                    <div class="p-2 border border-danger-subtle rounded bg-danger-subtle text-danger mb-1.5 last-mb-0 d-flex align-items-center gap-2" style="font-size: 12px;">
                                                        <i class="bi bi-exclamation-octagon-fill"></i>
                                                        <span class="fw-semibold text-truncate" title="{{ $task->title }}">{{ $task->title }}</span>
                                                    </div>
                                                @empty
                                                    <span class="text-success fs-12 italic d-block"><i class="bi bi-check-circle-fill me-1"></i>Bagus! Semua tenggat terpenuhi.</span>
                                                @endforelse
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
