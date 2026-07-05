@extends('partials.layouts.master_auth')

@section('title', 'Reset Password | SLADA')

@section('content')

    <div>
        <img src="{{ asset('assets/' . 'images/auth/login_bg.jpg') }}" alt="Auth Background"
            class="auth-bg light w-full h-full opacity-60 position-absolute top-0">
        <img src="{{ asset('assets/' . 'images/auth/auth_bg_dark.jpg') }}" alt="Auth Background" class="auth-bg d-none dark">
        <div class="container">
            <div class="row justify-content-center align-items-center min-vh-100 py-10">
                <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                    <div class="card mx-xxl-8">
                        <div class="card-body py-12 px-8">
                            <img src="{{ asset('assets/' . 'images/logo-dark.png') }}" alt="Logo Dark" height="30"
                                class="mb-4 mx-auto d-block">
                            <h6 class="mb-2 fw-medium text-center">Reset Kata Sandi</h6>
                            <p class="text-muted text-center fs-13 mb-6">Masukkan password baru untuk akun Anda.</p>

                            @if ($errors->any())
                                <div class="alert alert-danger mb-4">
                                    <ul class="mb-0 list-unstyled">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('password.update') }}">
                                @csrf
                                <input type="hidden" name="token" value="{{ $token }}">
                                <div class="row g-4">
                                    <div class="col-12">
                                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" id="email" name="email"
                                            value="{{ $email ?? old('email') }}" placeholder="Masukkan email terdaftar" required>
                                    </div>
                                    <div class="col-12">
                                        <label for="password" class="form-label">Password Baru <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control" id="password" name="password"
                                            placeholder="Minimal 6 karakter" required>
                                    </div>
                                    <div class="col-12">
                                        <label for="password_confirmation" class="form-label">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
                                            placeholder="Ulangi password baru" required>
                                    </div>
                                    <div class="col-12 mt-6">
                                        <button type="submit" class="btn btn-primary w-full mb-4">Reset Password <i class="bi bi-shield-check ms-1"></i></button>
                                    </div>
                                </div>
                            </form>
                            <p class="mb-0 fw-semibold position-relative text-center fs-12">
                                <a href="{{ route('login') }}" class="text-decoration-underline text-primary">
                                    <i class="bi bi-arrow-left me-1"></i>Kembali ke halaman login
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
