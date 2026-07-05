<div>
    <!-- Page Header -->
    <div class="mb-4">
        <h4 class="fw-bold text-dark mb-1">Pengaturan Profil</h4>
        <p class="text-muted fs-13">Ubah nama, alamat email, foto profil, atau kata sandi akun Anda.</p>
    </div>

    <!-- Alert Message -->
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Left Column: Avatar Upload -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm" style="border-radius: 8px;">
                <div class="card-body p-4 text-center">
                    <h6 class="fw-bold text-dark mb-3">Foto Profil</h6>
                    
                    <!-- Avatar Preview -->
                    <div class="mb-3 position-relative d-inline-block">
                        @if ($avatar)
                            <img src="{{ $avatar->temporaryUrl() }}" alt="Preview Avatar" class="rounded-circle img-thumbnail shadow-sm object-fit-cover" style="width: 120px; height: 120px;">
                        @elseif ($currentAvatar)
                            <img src="{{ asset('storage/' . $currentAvatar) }}" alt="Avatar" class="rounded-circle img-thumbnail shadow-sm object-fit-cover" style="width: 120px; height: 120px;">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($name) }}&background=0D6EFD&color=fff&size=120" alt="Default Avatar" class="rounded-circle img-thumbnail shadow-sm" style="width: 120px; height: 120px;">
                        @endif
                    </div>

                    <!-- File input -->
                    <div class="mb-3">
                        <label for="avatar" class="form-label fs-12 text-muted fw-semibold d-block">Pilih Foto Baru (Max 2MB)</label>
                        <input type="file" id="avatar" wire:model="avatar" class="form-control form-control-sm @error('avatar') is-invalid @enderror">
                        @error('avatar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        
                        <div wire:loading wire:target="avatar" class="text-primary fs-11 mt-1">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Mengunggah...
                        </div>
                    </div>

                    <p class="text-muted fs-12 mb-0">Format file yang didukung: JPG, JPEG, PNG.</p>
                </div>
            </div>
        </div>

        <!-- Right Column: Personal Info & Password Form -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" style="border-radius: 8px;">
                <div class="card-body p-4">
                    <form wire:submit.prevent="saveProfile">
                        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">Informasi Akun</h6>
                        
                        <div class="row g-3 mb-4">
                            <!-- Name -->
                            <div class="col-md-6">
                                <label for="profile_name" class="form-label fw-semibold text-muted fs-13">Nama Lengkap *</label>
                                <input type="text" id="profile_name" wire:model="name" class="form-control @error('name') is-invalid @enderror" placeholder="Masukkan nama lengkap">
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <!-- Email -->
                            <div class="col-md-6">
                                <label for="profile_email" class="form-label fw-semibold text-muted fs-13">Alamat Email *</label>
                                <input type="email" id="profile_email" wire:model="email" class="form-control @error('email') is-invalid @enderror" placeholder="Masukkan alamat email">
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">Ubah Kata Sandi (Kosongkan jika tidak ingin diubah)</h6>
                        
                        <div class="row g-3 mb-4">
                            <!-- Password -->
                            <div class="col-md-6">
                                <label for="profile_password" class="form-label fw-semibold text-muted fs-13">Kata Sandi Baru</label>
                                <input type="password" id="profile_password" wire:model="password" class="form-control @error('password') is-invalid @enderror" placeholder="Minimal 6 karakter">
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <!-- Password Confirmation -->
                            <div class="col-md-6">
                                <label for="profile_password_confirmation" class="form-label fw-semibold text-muted fs-13">Konfirmasi Kata Sandi Baru</label>
                                <input type="password" id="profile_password_confirmation" wire:model="password_confirmation" class="form-control" placeholder="Ulangi kata sandi baru">
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="border-top pt-3 text-end">
                            <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-1"></i> Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
