<!-- Begin Header -->
<header class="app-header" id="appHeader">
    <div class="container-fluid w-100">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <div class="d-inline-flex align-items-center gap-5">
                    <a href="index" class="fs-18 fw-semibold">
                        <img height="42" class="header-sidebar-logo-default d-none" alt="Logo" src="{{ asset('assets/' . 'images/logo-dark-2.png') }}">
                        <img height="42" class="header-sidebar-logo-light d-none" alt="Logo" src="{{ asset('assets/' . 'images/logo-light-2.png') }}">
                        <img height="42" class="header-sidebar-logo-small d-none" alt="Logo" src="{{ asset('assets/' . 'images/logo-md-2.png') }}">
                        <img height="42" class="header-sidebar-logo-small-light d-none" alt="Logo" src="{{ asset('assets/' . 'images/logo-md-light-2.png') }}">
                    </a>
                    <button type="button" class="vertical-toggle btn btn-light-light text-muted icon-btn fs-5 rounded-pill" id="toggleSidebar">
                        <i class="bi bi-arrow-bar-left header-icon"></i>
                    </button>
                    <button type="button" class="horizontal-toggle btn btn-light-light text-muted icon-btn fs-5 rounded-pill d-none" id="toggleHorizontal">
                        <i class="ri-menu-2-line header-icon"></i>
                    </button>
                    <div class="header-dropdown d-flex align-items-center">
                        <!-- About Megamenu -->
                        <div class="dropdown pe-dropdown-mega pe-dropdown-hover">
                            <button class="btn pe-dropdown-button" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                About
                            </button>
                            <div class="dropdown-menu dropdown-mega-xl p-0">
                                <div class="p-4 border-bottom d-flex align-items-center gap-4">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 fs-15">Tentang Platform SLADA</h6>
                                        <p class="mb-0 text-muted">Asisten agenda digital pribadi Anda untuk produktivitas harian.</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <a href="{{ route('about') }}" class="btn btn-sm btn-primary">Info Detail</a>
                                    </div>
                                </div>
                                <div class="p-4">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <p class="mb-1 text-uppercase text-muted fs-12">Fitur Utama</p>
                                            <ul class="list-unstyled mb-0">
                                                <li><a class="dropdown-item" href="{{ route('dashboard') }}">Dashboard</a></li>
                                                <li><a class="dropdown-item" href="{{ route('tasks') }}">Kanban Board</a></li>
                                                <li><a class="dropdown-item" href="{{ route('calendar') }}">Calendar Agenda</a></li>
                                                <li><a class="dropdown-item" href="{{ route('reports') }}">Jurnal Refleksi</a></li>
                                            </ul>
                                        </div>
                                        <div class="col-lg-6">
                                            <p class="mb-1 text-uppercase text-muted fs-12">Data Master</p>
                                            <ul class="list-unstyled mb-0">
                                                @php
                                                    $workspace = Auth::user()->workspaces()->first();
                                                    $pivot = $workspace ? $workspace->users()->where('users.id', Auth::id())->first()->pivot : null;
                                                    $role = $pivot ? $pivot->role : 'member';
                                                @endphp
                                                @if($role === 'owner' || $role === 'admin')
                                                    <li><a class="dropdown-item" href="{{ route('master.users') }}">Master User</a></li>
                                                @endif
                                                <li><a class="dropdown-item" href="{{ route('master.areas') }}">Master Kategori</a></li>
                                                <li><a class="dropdown-item" href="{{ route('master.projects') }}">Master Project</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Authentication & Pages Dropdown -->
                        <div class="dropdown pe-dropdown-mega pe-dropdown-hover">
                            <button class="btn pe-dropdown-button" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Authentication & Pages
                            </button>
                            <div class="dropdown-menu dropdown-mega-lg p-0">
                                <div class="p-4 d-flex align-items-center gap-4 bg-primary text-white">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 fs-15 text-white">Sesi & Akun Pengguna</h6>
                                        <p class="mb-0 text-white-50">Kelola login, logout, dan status otentikasi Anda.</p>
                                    </div>
                                </div>
                                <div class="p-4">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <ul class="list-unstyled mb-0">
                                                <li>
                                                    <a class="dropdown-item d-flex align-items-center gap-2" href="javascript:void(0)" onclick="event.preventDefault(); document.getElementById('logout-form-topbar').submit();">
                                                        <i class="bi bi-box-arrow-right text-danger"></i> Keluar (Logout)
                                                    </a>
                                                    <form id="logout-form-topbar" action="{{ route('logout') }}" method="POST" class="d-none">
                                                        @csrf
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Help Dropdown -->
                        <div class="dropdown pe-dropdown-mega pe-dropdown-hover">
                            <button class="btn pe-dropdown-button" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Help
                            </button>
                            <div class="dropdown-menu p-4" style="min-width: 250px;">
                                <p class="mb-2 text-uppercase text-muted fs-12">Bantuan & Dukungan</p>
                                <ul class="list-unstyled mb-0">
                                    <li><a class="dropdown-item" href="{{ route('help') }}"><i class="bi bi-question-circle me-1"></i> Pusat FAQ / Bantuan</a></li>
                                    <li><a class="dropdown-item" href="{{ route('donation') }}"><i class="bi bi-heart me-1 text-danger"></i> Donasi & Dukungan</a></li>
                                    <li><a class="dropdown-item" href="https://wa.me/6285854543488" target="_blank"><i class="bi bi-whatsapp me-1 text-success"></i> Hubungi Admin</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex-shrink-0 d-flex align-items-center gap-1">
                <!-- Theme Mode Toggle -->
                <div class="dark-mode-btn" id="toggleMode">
                    <button class="btn header-btn active" id="lightModeBtn">
                        <i class="bi bi-brightness-high"></i>
                    </button>
                    <button class="btn header-btn" id="darkModeBtn">
                        <i class="bi bi-moon-stars"></i>
                    </button>
                </div>

                <!-- User Profile Dropdown -->
                <div class="dropdown pe-dropdown-mega d-none d-md-block">
                    <button class="header-profile-btn btn gap-1 text-start" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="header-btn btn position-relative p-0 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                            @if(Auth::user()->avatar)
                                <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar" class="rounded-circle object-fit-cover" style="width: 32px; height: 32px;">
                            @else
                                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=0D6EFD&color=fff&size=32" alt="Avatar" class="rounded-circle" style="width: 32px; height: 32px;">
                            @endif
                        </span>
                        <div class="d-none d-lg-block pe-2">
                            <span class="d-block mb-0 fs-13 fw-semibold text-truncate" style="max-width: 100px;">{{ Auth::user()->name }}</span>
                            <span class="d-block mb-0 fs-12 text-muted text-capitalize">{{ $role }}</span>
                        </div>
                    </button>
                    <div class="dropdown-menu dropdown-mega-sm header-dropdown-menu p-3">
                        <div class="border-bottom pb-2 mb-2 d-flex align-items-center gap-2">
                            @if(Auth::user()->avatar)
                                <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar" class="rounded-circle object-fit-cover" style="width: 40px; height: 40px;">
                            @else
                                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=0D6EFD&color=fff&size=40" alt="Avatar" class="rounded-circle" style="width: 40px; height: 40px;">
                            @endif
                            <div class="text-truncate" style="max-width: 150px;">
                                <h6 class="mb-0 lh-base text-truncate">{{ Auth::user()->name }}</h6>
                                <p class="mb-0 fs-12 text-muted text-truncate">{{ Auth::user()->email }}</p>
                            </div>
                        </div>
                        <ul class="list-unstyled mb-2 border-bottom pb-2">
                            <li><a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('profile') }}"><i class="bi bi-person text-primary"></i> Edit Profil</a></li>
                        </ul>
                        <ul class="list-unstyled mb-0">
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 text-danger" href="javascript:void(0)" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="bi bi-box-arrow-right"></i> Keluar (Logout)
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
<!-- END Header -->

<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 bg-transparent">
            <div class="d-flex justify-content-between align-items-center bg-body">
                <div class="d-flex align-items-center border-0 px-3">
                    <i class="bi bi-search me-2"></i>
                    <input class="d-flex w-full py-3 bg-transparent border-0 focus-ring" placeholder="Search Here.." autocomplete="off" autocorrect="off" spellcheck="false" aria-autocomplete="list" role="combobox" aria-expanded="true" type="text">
                </div>
                <button type="button" class="btn-close pe-3" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-body mt-4">
                <p class="font-normal mb-2">Searching For...</p>
                <span class="badge bg-light-subtle border text-body">Analytics <i class="ri-close-line"></i></span>
                <span class="badge bg-light-subtle border text-body">Project <i class="ri-close-line"></i></span>
                <span class="badge bg-light-subtle border text-body">Eccomerce <i class="ri-close-line"></i></span>
                <span class="badge bg-light-subtle border text-body">CRM <i class="ri-close-line"></i></span>
                <span class="badge bg-light-subtle border text-body">Logistics <i class="ri-close-line"></i></span>
                <span class="badge bg-light-subtle border text-body">Academy <i class="ri-close-line"></i></span>
            </div>
        </div>
    </div>
</div>