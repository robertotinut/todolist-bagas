<div>
    @section('title', 'Master User | SLADA')
    @section('title-sub', 'Master')
    @section('pagetitle', 'Master User')

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
                    <h6 class="mb-0 fw-semibold">{{ $isEditing ? 'Ubah Peran User' : 'Tambah User Baru' }}</h6>
                    @if($isEditing)
                        <button type="button" wire:click="resetForm" class="btn btn-sm btn-outline-secondary">Batal</button>
                    @endif
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="save">
                        <div class="mb-4">
                            <label for="name" class="form-label fw-medium text-muted">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" id="name" wire:model="name" class="form-control @error('name') is-invalid @enderror" placeholder="Nama lengkap user" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="email" class="form-label fw-medium text-muted">Email <span class="text-danger">*</span></label>
                            <input type="email" id="email" wire:model="email" class="form-control @error('email') is-invalid @enderror" placeholder="user@domain.com" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        @if(!$isEditing)
                            <div class="mb-4">
                                <label for="password" class="form-label fw-medium text-muted">Password <span class="text-danger">*</span></label>
                                <input type="password" id="password" wire:model="password" class="form-control @error('password') is-invalid @enderror" placeholder="Min. 6 karakter" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif

                        <div class="mb-4">
                            <label for="role" class="form-label fw-medium text-muted">Peran Workspace <span class="text-danger">*</span></label>
                            <select id="role" wire:model="role" class="form-select @error('role') is-invalid @enderror" required>
                                <option value="member">Member (Dapat melihat & mengelola task)</option>
                                <option value="admin">Admin (Dapat mengelola project & task)</option>
                                <option value="owner">Owner (Akses penuh ke workspace)</option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mt-8">
                            <button type="submit" class="btn btn-primary w-full d-flex align-items-center justify-content-center gap-1">
                                <i class="bi bi-save"></i> {{ $isEditing ? 'Simpan Perubahan' : 'Tambah User' }}
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
                    <h6 class="mb-0 fw-semibold">Daftar Anggota Workspace</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama & Email</th>
                                    <th>Peran</th>
                                    <th class="text-end px-6">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($members as $member)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="avatar-sm h-35px w-35px rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-semibold fs-14">
                                                    {{ strtoupper(substr($member->name, 0, 2)) }}
                                                </div>
                                                <div>
                                                    <span class="fw-semibold text-dark d-block mb-0">{{ $member->name }}</span>
                                                    <small class="text-muted d-block">{{ $member->email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                $roleClass = match($member->pivot->role) {
                                                    'owner' => 'bg-danger-subtle text-danger',
                                                    'admin' => 'bg-warning-subtle text-warning',
                                                    default => 'bg-info-subtle text-info'
                                                };
                                            @endphp
                                            <span class="badge {{ $roleClass }} text-capitalize px-3 py-1">
                                                {{ $member->pivot->role }}
                                            </span>
                                        </td>
                                        <td class="text-end px-6">
                                            <div class="d-inline-flex gap-2">
                                                <button type="button" wire:click="edit({{ $member->id }})" class="btn btn-sm btn-outline-primary border-0" title="Edit">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>
                                                @if(auth()->id() !== $member->id)
                                                    <button type="button" 
                                                            onclick="Swal.fire({
                                                                title: 'Keluarkan User?',
                                                                text: 'Apakah Anda yakin ingin mengeluarkan user ini dari workspace?',
                                                                icon: 'warning',
                                                                showCancelButton: true,
                                                                confirmButtonColor: '#ea5455',
                                                                cancelButtonColor: '#6c757d',
                                                                confirmButtonText: 'Ya, Keluarkan!',
                                                                cancelButtonText: 'Batal'
                                                            }).then((result) => {
                                                                if (result.isConfirmed) {
                                                                    @this.call('remove', {{ $member->id }})
                                                                }
                                                            })" 
                                                            class="btn btn-sm btn-outline-danger border-0" 
                                                            title="Keluarkan">
                                                        <i class="bi bi-person-x"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-8 text-muted">
                                            <i class="bi bi-people fs-32 d-block mb-2"></i> Belum ada user lain di workspace ini.
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
