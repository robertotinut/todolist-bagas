<div>
    @section('title', 'Master Kategori | SLADA')
    @section('title-sub', 'Master')
    @section('pagetitle', 'Master Kategori')

    <div class="row g-4">
        <!-- Toast Notification -->
        @if (session()->has('message'))
            <div class="col-12">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="col-12">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        @endif

        <!-- Form Card (Col 4) -->
        <div class="col-xl-4 col-lg-5">
            <div class="card">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold">{{ $isEditing ? 'Ubah Kategori' : 'Tambah Kategori Baru' }}</h6>
                    @if($isEditing)
                        <button type="button" wire:click="resetForm" class="btn btn-sm btn-outline-secondary">Batal</button>
                    @endif
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="save">
                        <div class="mb-4">
                            <label for="name" class="form-label fw-medium text-muted">Nama Kategori <span class="text-danger">*</span></label>
                            <input type="text" id="name" wire:model="name" class="form-control @error('name') is-invalid @enderror" placeholder="Contoh: Pekerjaan, Pribadi" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="icon" class="form-label fw-medium text-muted">Emoji / Icon</label>
                            <input type="text" id="icon" wire:model="icon" class="form-control @error('icon') is-invalid @enderror" placeholder="Contoh: 💼, ❤️, 🚀">
                            @error('icon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="mt-2 d-flex gap-2 flex-wrap">
                                @foreach(['💼', '❤️', '💰', '🚀', '🏋️', '📚', '🛒', '🎨'] as $suggestedIcon)
                                    <button type="button" wire:click="$set('icon', '{{ $suggestedIcon }}')" class="btn btn-sm btn-outline-light border text-dark fs-18 py-1 px-2">{{ $suggestedIcon }}</button>
                                @endforeach
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-medium text-muted">Warna Kategori</label>
                            <div class="d-flex gap-2 flex-wrap">
                                @foreach([
                                    'blue' => '#0d6efd',
                                    'rose' => '#f43f5e',
                                    'emerald' => '#10b981',
                                    'purple' => '#8b5cf6',
                                    'orange' => '#f97316',
                                    'dark' => '#1f2937'
                                ] as $key => $hexColor)
                                    <button type="button" wire:click="$set('color', '{{ $key }}')" class="d-flex align-items-center justify-content-center rounded-circle border border-2 {{ $color === $key ? 'border-dark shadow-sm' : 'border-transparent' }}" style="background-color: {{ $hexColor }}; width: 32px; height: 32px; transition: all 0.2s; transform: {{ $color === $key ? 'scale(1.2)' : 'scale(1)' }}; cursor: pointer; padding: 0;">
                                        @if($color === $key)
                                            <i class="bi bi-check-lg text-white fs-14"></i>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <div class="mt-8">
                            <button type="submit" class="btn btn-primary w-full d-flex align-items-center justify-content-center gap-1">
                                <i class="bi bi-save"></i> {{ $isEditing ? 'Simpan Perubahan' : 'Tambah Kategori' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Table Card (Col 8) -->
        <div class="col-xl-8 col-lg-7">
            <div class="card h-100">
                <div class="card-header border-bottom">
                    <h6 class="mb-0 fw-semibold">Daftar Kategori</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 80px;" class="text-center">Icon</th>
                                    <th>Nama Kategori</th>
                                    <th>Warna</th>
                                    <th>Status</th>
                                    <th class="text-end px-6">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($areas as $area)
                                    <tr class="{{ $area->is_archived ? 'table-light opacity-60' : '' }}">
                                        <td class="text-center fs-20">{{ $area->icon ?: '📂' }}</td>
                                        <td>
                                            <span class="fw-semibold text-dark">{{ $area->name }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $styles = match($area->color) {
                                                    'rose' => 'background-color: rgba(244, 63, 94, 0.15); color: #f43f5e;',
                                                    'emerald' => 'background-color: rgba(16, 185, 129, 0.15); color: #10b981;',
                                                    'purple' => 'background-color: rgba(139, 92, 246, 0.15); color: #8b5cf6;',
                                                    'orange' => 'background-color: rgba(249, 115, 22, 0.15); color: #f97316;',
                                                    'dark' => 'background-color: rgba(31, 41, 55, 0.15); color: #1f2937;',
                                                    default => 'background-color: rgba(13, 110, 253, 0.15); color: #0d6efd;'
                                                };
                                            @endphp
                                            <span class="badge text-capitalize px-3 py-1" style="{{ $styles }}">{{ $area->color ?: 'blue' }}</span>
                                        </td>
                                        <td>
                                            @if($area->is_default)
                                                <span class="badge bg-secondary-subtle text-muted px-3 py-1"><i class="bi bi-lock-fill"></i> Default</span>
                                            @elseif($area->is_archived)
                                                <span class="badge bg-secondary-subtle text-secondary px-3 py-1">Archived</span>
                                            @else
                                                <span class="badge bg-success-subtle text-success px-3 py-1">Active</span>
                                            @endif
                                        </td>
                                        <td class="text-end px-6">
                                            @if($area->is_default && $userRole === 'member')
                                                <span class="text-muted fs-12"><i class="bi bi-lock-fill me-1"></i> Terkunci</span>
                                            @else
                                                <div class="d-inline-flex gap-2">
                                                    <button type="button" wire:click="edit({{ $area->id }})" class="btn btn-sm btn-outline-primary border-0" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button type="button" wire:click="toggleArchive({{ $area->id }})" class="btn btn-sm btn-outline-warning border-0" title="{{ $area->is_archived ? 'Activate' : 'Archive' }}">
                                                        <i class="bi {{ $area->is_archived ? 'bi-archive' : 'bi-archive-fill' }}"></i>
                                                    </button>
                                                    <button type="button" 
                                                            onclick="Swal.fire({
                                                                title: 'Hapus Kategori?',
                                                                text: 'Menghapus kategori ini juga akan menghapus semua tugas di dalamnya!',
                                                                icon: 'warning',
                                                                showCancelButton: true,
                                                                confirmButtonColor: '#ea5455',
                                                                cancelButtonColor: '#6c757d',
                                                                confirmButtonText: 'Ya, Hapus!',
                                                                cancelButtonText: 'Batal'
                                                            }).then((result) => {
                                                                if (result.isConfirmed) {
                                                                    @this.call('delete', {{ $area->id }})
                                                                }
                                                            })" 
                                                            class="btn btn-sm btn-outline-danger border-0" 
                                                            title="Delete">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-8 text-muted">
                                            <i class="bi bi-inbox fs-32 d-block mb-2"></i> Belum ada kategori yang ditambahkan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
