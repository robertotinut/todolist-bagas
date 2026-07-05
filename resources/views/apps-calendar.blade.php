@extends('partials.layouts.master')

@section('title', 'Kalender Tugas | SLADA')
@section('title-sub', 'Applications')
@section('pagetitle', 'Kalender Tugas')
@section('content')

    <!-- begin::App -->
    <div id="layout-wrapper">
        <div class="row g-4">
            <!-- Calendar Card -->
            <div class="col-xl-9 col-lg-8">
                <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                    <div class="card-body p-4">
                        <div id='calendar'></div>
                    </div>
                </div>
            </div>

            <!-- Side Panels -->
            <div class="col-xl-3 col-lg-4">
                <!-- Navigation Shortcut -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                    <div class="card-body p-3">
                        <a href="{{ route('tasks') }}" class="btn btn-primary w-100 d-inline-flex align-items-center justify-content-center gap-2 rounded-pill">
                            <i class="bi bi-kanban"></i> Buka Kanban Board
                        </a>
                    </div>
                </div>

                <!-- Reminders List -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                    <div class="card-header border-bottom py-3 bg-transparent">
                        <h5 class="card-title mb-0 fw-bold text-dark">Pengingat Tenggat</h5>
                    </div>
                    <div class="card-body py-2" style="max-height: 320px; overflow-y: auto;">
                        <ul class="list-unstyled mb-0">
                            @forelse($reminders as $rem)
                                @php
                                    $isOverdue = \Carbon\Carbon::parse($rem->due_date)->isPast();
                                    $prioBadge = match($rem->priority) {
                                        'critical' => 'bg-danger text-white',
                                        'high' => 'bg-warning text-dark',
                                        'low' => 'bg-success text-white',
                                        default => 'bg-primary text-white'
                                    };
                                @endphp
                                <li class="py-2.5 border-bottom-dashed last-border-0">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <h6 class="mb-0 fw-semibold text-dark text-truncate fs-13" style="max-width: 140px;" title="{{ $rem->title }}">
                                            {{ $rem->title }}
                                        </h6>
                                        <span class="badge {{ $prioBadge }} fs-9 px-1.5 py-0.5 rounded text-uppercase" style="font-size: 8px;">
                                            {{ $rem->priority }}
                                        </span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fs-11 {{ $isOverdue ? 'text-danger fw-bold' : 'text-muted' }}">
                                            <i class="bi bi-clock me-1"></i>{{ $rem->due_date->format('d M, H:i') }}
                                        </span>
                                        @if($rem->area)
                                            <span class="badge bg-light text-muted border fs-10 px-2 py-0.5" style="font-weight: normal;">
                                                {{ $rem->area->icon }} {{ $rem->area->name }}
                                            </span>
                                        @endif
                                    </div>
                                </li>
                            @empty
                                <li class="text-center py-6 text-muted">
                                    <i class="bi bi-calendar-check fs-24 d-block mb-1"></i> Tidak ada pengingat terdekat.
                                </li>
                            @endforelse
                        </ul>
                    </div>
                </div>

                <!-- Event Legend Card -->
                <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                    <div class="card-header border-bottom py-3 bg-transparent">
                        <h5 class="card-title mb-0 fw-bold text-dark">Legenda Prioritas</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-column gap-2.5">
                            <div class="d-flex justify-content-between align-items-center fs-12">
                                <span class="badge bg-danger-subtle text-danger px-2.5 py-1">Critical</span>
                                <span class="text-muted">Merah</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center fs-12">
                                <span class="badge bg-warning-subtle text-warning px-2.5 py-1">High</span>
                                <span class="text-muted">Kuning</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center fs-12">
                                <span class="badge bg-primary-subtle text-primary px-2.5 py-1">Medium</span>
                                <span class="text-muted">Biru</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center fs-12">
                                <span class="badge bg-success-subtle text-success px-2.5 py-1">Low</span>
                                <span class="text-muted">Hijau</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Event Details Modal -->
    <div class="modal fade" id="eventDetailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 12px;">
                <div class="modal-header border-bottom py-3 bg-light" style="border-top-left-radius: 12px; border-top-right-radius: 12px;">
                    <h5 class="modal-title fw-bold text-dark"><i class="bi bi-info-circle me-2"></i>Detail Agenda Tugas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <span class="d-block text-muted fs-11 fw-semibold text-uppercase">Nama Tugas</span>
                        <h5 class="fw-bold text-dark mt-1" id="eventDetailsTitle"></h5>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <span class="d-block text-muted fs-11 fw-semibold text-uppercase">Kategori (Area)</span>
                            <span class="badge bg-light text-dark border px-2.5 py-1 mt-1 fs-12" id="eventDetailsCategory"></span>
                        </div>
                        <div class="col-sm-6">
                            <span class="d-block text-muted fs-11 fw-semibold text-uppercase">Project</span>
                            <span class="text-dark fw-semibold fs-13 d-block mt-1" id="eventDetailsProject"></span>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <span class="d-block text-muted fs-11 fw-semibold text-uppercase">Prioritas</span>
                            <span class="badge px-2.5 py-1 mt-1 text-uppercase fs-11" id="eventDetailsPriority"></span>
                        </div>
                        <div class="col-sm-6">
                            <span class="d-block text-muted fs-11 fw-semibold text-uppercase">Tenggat Waktu</span>
                            <span class="text-dark fw-semibold fs-13 d-block mt-1" id="eventDetailsStart"></span>
                        </div>
                    </div>

                    <div class="border-top pt-3">
                        <span class="d-block text-muted fs-11 fw-semibold text-uppercase mb-2">Deskripsi</span>
                        <div class="bg-light p-3 rounded text-muted fs-13" id="eventDetailsDescription" style="white-space: pre-wrap; line-height: 1.5; min-height: 60px;"></div>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light py-2" style="border-bottom-left-radius: 12px; border-bottom-right-radius: 12px;">
                    <button type="button" class="btn btn-sm btn-secondary px-3" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
    <!-- FullCalendar CSS and JS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/main.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var eventsData = {!! $eventsJson !!};

            var calendar = new FullCalendar.Calendar(calendarEl, {
                locale: 'id',
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                themeSystem: 'bootstrap5',
                events: eventsData,
                eventClick: function(info) {
                    var eventObj = info.event;
                    
                    document.getElementById('eventDetailsTitle').innerText = eventObj.title;
                    document.getElementById('eventDetailsCategory').innerText = eventObj.extendedProps.category || 'General';
                    document.getElementById('eventDetailsProject').innerText = eventObj.extendedProps.project || 'General';
                    
                    // Priority style
                    var priorityVal = eventObj.extendedProps.priority || 'Medium';
                    var badgeEl = document.getElementById('eventDetailsPriority');
                    badgeEl.innerText = priorityVal;
                    badgeEl.className = 'badge px-2.5 py-1 mt-1 text-uppercase fs-11';
                    if (priorityVal === 'Critical') {
                        badgeEl.classList.add('bg-danger', 'text-white');
                    } else if (priorityVal === 'High') {
                        badgeEl.classList.add('bg-warning', 'text-dark');
                    } else if (priorityVal === 'Low') {
                        badgeEl.classList.add('bg-success', 'text-white');
                    } else {
                        badgeEl.classList.add('bg-primary', 'text-white');
                    }

                    // Format Date
                    var dateStr = eventObj.start ? new Date(eventObj.start).toLocaleString('id-ID', {
                        day: 'numeric',
                        month: 'short',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    }) : '-';
                    document.getElementById('eventDetailsStart').innerText = dateStr;
                    document.getElementById('eventDetailsDescription').innerText = eventObj.extendedProps.description || 'Tidak ada deskripsi detail.';
                    
                    var modal = new bootstrap.Modal(document.getElementById('eventDetailsModal'));
                    modal.show();
                }
            });
            calendar.render();
        });
    </script>
@endsection
