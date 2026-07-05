<div>
    @section('title', 'Master Project | SLADA')
    @section('title-sub', 'Master')
    @section('pagetitle', 'Master Project')

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
                    <h6 class="mb-0 fw-semibold">{{ $isEditing ? 'Ubah Project' : 'Tambah Project Baru' }}</h6>
                    @if($isEditing)
                        <button type="button" wire:click="resetForm" class="btn btn-sm btn-outline-secondary">Batal</button>
                    @endif
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="save">
                        <div class="mb-4">
                            <label for="area_id" class="form-label fw-medium text-muted">Kategori (Area) <span class="text-danger">*</span></label>
                            <select id="area_id" wire:model="area_id" class="form-select @error('area_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($areas as $area)
                                    <option value="{{ $area->id }}">{{ $area->icon }} {{ $area->name }}</option>
                                @endforeach
                            </select>
                            @error('area_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if(count($areas) === 0)
                                <div class="form-text text-danger mt-1">
                                    <i class="bi bi-exclamation-circle"></i> Silakan buat <a href="{{ route('master.areas') }}" class="text-decoration-underline text-danger">Kategori (Area)</a> terlebih dahulu!
                                </div>
                            @endif
                        </div>

                        <div class="mb-4">
                            <label for="name" class="form-label fw-medium text-muted">Nama Project <span class="text-danger">*</span></label>
                            <input type="text" id="name" wire:model="name" class="form-control @error('name') is-invalid @enderror" placeholder="Contoh: Website ERP, Landing Page" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label fw-medium text-muted">Deskripsi Project</label>
                            <textarea id="description" wire:model="description" rows="4" class="form-control @error('description') is-invalid @enderror" placeholder="Tuliskan deskripsi singkat project..."></textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mt-8">
                            <button type="submit" class="btn btn-primary w-full d-flex align-items-center justify-content-center gap-1" {{ count($areas) === 0 ? 'disabled' : '' }}>
                                <i class="bi bi-save"></i> {{ $isEditing ? 'Simpan Perubahan' : 'Tambah Project' }}
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
                    <h6 class="mb-0 fw-semibold">Daftar Project</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Project & Deskripsi</th>
                                    <th>Kategori (Area)</th>
                                    <th>Status</th>
                                    <th class="text-end px-6">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($projects as $project)
                                    <tr class="{{ $project->is_archived ? 'table-light opacity-60' : '' }}">
                                        <td>
                                            <span class="fw-semibold text-dark d-block mb-1">{{ $project->name }}</span>
                                            <small class="text-muted d-block text-truncate" style="max-width: 250px;">{{ $project->description ?: '-' }}</small>
                                        </td>
                                        <td>
                                            @if($project->area)
                                                @php
                                                    $styles = match($project->area->color) {
                                                        'rose' => 'background-color: rgba(244, 63, 94, 0.15); color: #f43f5e;',
                                                        'emerald' => 'background-color: rgba(16, 185, 129, 0.15); color: #10b981;',
                                                        'purple' => 'background-color: rgba(139, 92, 246, 0.15); color: #8b5cf6;',
                                                        'orange' => 'background-color: rgba(249, 115, 22, 0.15); color: #f97316;',
                                                        'dark' => 'background-color: rgba(31, 41, 55, 0.15); color: #1f2937;',
                                                        default => 'background-color: rgba(13, 110, 253, 0.15); color: #0d6efd;'
                                                    };
                                                @endphp
                                                <span class="badge px-3 py-1" style="{{ $styles }}">
                                                    {{ $project->area->icon }} {{ $project->area->name }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($project->is_default)
                                                <span class="badge bg-secondary-subtle text-muted px-3 py-1"><i class="bi bi-lock-fill"></i> Default</span>
                                            @elseif($project->is_archived)
                                                <span class="badge bg-secondary-subtle text-secondary px-3 py-1">Archived</span>
                                            @else
                                                <span class="badge bg-success-subtle text-success px-3 py-1">Active</span>
                                            @endif
                                        </td>
                                        <td class="text-end px-6">
                                            @if($project->is_default && $userRole === 'member')
                                                <span class="text-muted fs-12"><i class="bi bi-lock-fill me-1"></i> Terkunci</span>
                                            @else
                                                <div class="d-inline-flex gap-2">
                                                    <button type="button" wire:click="edit({{ $project->id }})" class="btn btn-sm btn-outline-primary border-0" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button type="button" wire:click="toggleArchive({{ $project->id }})" class="btn btn-sm btn-outline-warning border-0" title="{{ $project->is_archived ? 'Activate' : 'Archive' }}">
                                                        <i class="bi {{ $project->is_archived ? 'bi-archive' : 'bi-archive-fill' }}"></i>
                                                    </button>
                                                     <button type="button" 
                                                             onclick="Swal.fire({
                                                                 title: 'Hapus Project?',
                                                                 text: 'Menghapus project ini juga akan merilis semua tugas di dalamnya!',
                                                                 icon: 'warning',
                                                                 showCancelButton: true,
                                                                 confirmButtonColor: '#ea5455',
                                                                 cancelButtonColor: '#6c757d',
                                                                 confirmButtonText: 'Ya, Hapus!',
                                                                 cancelButtonText: 'Batal'
                                                             }).then((result) => {
                                                                 if (result.isConfirmed) {
                                                                     @this.call('delete', {{ $project->id }})
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
                                        <td colspan="4" class="text-center py-8 text-muted">
                                            <i class="bi bi-inbox fs-32 d-block mb-2"></i> Belum ada project yang ditambahkan.
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
