<aside class="pe-app-sidebar" id="sidebar">
    <div class="pe-app-sidebar-logo px-6 d-flex align-items-center position-relative">
        <!--begin::Brand Image-->
        <a href="{{ route('dashboard') }}" class="fs-18 fw-semibold">
            <img height="30" class="pe-app-sidebar-logo-default d-none" alt="Logo" src="{{ asset('assets/' . 'images/logo-dark.png') }}">
            <img height="30" class="pe-app-sidebar-logo-light d-none" alt="Logo" src="{{ asset('assets/' . 'images/logo-light.png') }}">
            <img height="30" class="pe-app-sidebar-logo-minimize d-none" alt="Logo" src="{{ asset('assets/' . 'images/logo-md.png') }}">
            <img height="30" class="pe-app-sidebar-logo-minimize-light d-none" alt="Logo" src="{{ asset('assets/' . 'images/logo-md-light.png') }}">
        </a>
        <!--end::Brand Image-->
    </div> 
    <nav class="pe-app-sidebar-menu nav nav-pills" data-simplebar id="sidebar-simplebar">
        <ul class="pe-main-menu list-unstyled">
            <li class="pe-menu-title">
                Main
            </li>
            <li class="pe-slide pe-has-sub">
                <a href="{{ route('dashboard') }}" class="pe-nav-link">
                    <i class="bi bi-speedometer2 pe-nav-icon"></i>
                    <span class="pe-nav-content">Dashboard</span>
                </a>
            </li>
            <li class="pe-menu-title">
                Master Data
            </li>
            <li class="pe-slide pe-has-sub">
                <a href="#collapseMasterData" class="pe-nav-link" data-bs-toggle="collapse" aria-expanded="false" aria-controls="collapseMasterData">
                    <i class="bi bi-database pe-nav-icon"></i>
                    <span class="pe-nav-content">Data Master</span>
                    <i class="ri-arrow-down-s-line pe-nav-arrow"></i>
                </a>
                <ul class="pe-slide-menu collapse" id="collapseMasterData">
                    @php
                        $workspace = Auth::user()->workspaces()->first();
                        $pivot = $workspace ? $workspace->users()->where('users.id', Auth::id())->first()->pivot : null;
                        $role = $pivot ? $pivot->role : 'member';
                    @endphp
                    @if($role === 'owner' || $role === 'admin')
                        <li class="pe-slide-item">
                            <a href="{{ route('master.users') }}" class="pe-nav-link">
                                Master User
                            </a>
                        </li>
                    @endif
                    <li class="pe-slide-item">
                        <a href="{{ route('master.areas') }}" class="pe-nav-link">
                            Master Kategori (Area)
                        </a>
                    </li>
                    <li class="pe-slide-item">
                        <a href="{{ route('master.projects') }}" class="pe-nav-link">
                            Master Project
                        </a>
                    </li>
                </ul>
            </li>
            <li class="pe-menu-title">
                Applications
            </li>
            <li class="pe-slide pe-has-sub">
                <a href="{{ route('tasks') }}" class="pe-nav-link">
                    <i class="bi bi-kanban pe-nav-icon"></i>
                    <span class="pe-nav-content">Kanban Board</span>
                </a>
            </li>
            <li class="pe-slide pe-has-sub">
                <a href="{{ route('calendar') }}" class="pe-nav-link">
                    <i class="bi bi-calendar-week pe-nav-icon"></i>
                    <span class="pe-nav-content">Calendar</span>
                </a>
            </li>
            <li class="pe-slide pe-has-sub">
                <a href="{{ route('reports') }}" class="pe-nav-link">
                    <i class="bi bi-journal-check pe-nav-icon"></i>
                    <span class="pe-nav-content">Jurnal Refleksi</span>
                </a>
            </li>
            <li class="pe-slide pe-has-sub">
                <a href="{{ route('donation') }}" class="pe-nav-link">
                    <i class="bi bi-heart pe-nav-icon text-danger"></i>
                    <span class="pe-nav-content">Donasi</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>
