@extends('partials.layouts.master_auth')

@section('title', 'Sign Up | SLADA')

@section('content')

    <!-- START -->
    <div>
        <img src="{{ asset('assets/' . 'images/auth/login_bg.jpg') }}" alt="Auth Background"
            class="auth-bg light w-full h-full opacity-60 position-absolute top-0">
        <img src="{{ asset('assets/' . 'images/auth/auth_bg_dark.jpg') }}" alt="Auth Background" class="auth-bg d-none dark">
        <div class="container">
            <div class="row justify-content-center align-items-center min-vh-100 py-10">
                <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                    <div class="card mx-xxl-8">
                        <div class="card-body py-12 px-8">
                            <img src="{{ asset('assets/' . 'images/logo-dark-2.png') }}" alt="Logo Dark" height="42"
                                class="mb-4 mx-auto d-block">
                            <h6 class="mb-3 mb-8 fw-medium text-center">Buat Akun Baru SLADA</h6>
                            
                            @if ($errors->any())
                                <div class="alert alert-danger mb-4">
                                    <ul class="mb-0 list-unstyled">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('register') }}">
                                @csrf
                                <div class="row g-4">
                                    <div class="col-12">
                                        <label for="name" class="form-label">Nama Lengkap <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="name" name="name"
                                            value="{{ old('name') }}" placeholder="Masukkan nama lengkap Anda" required autofocus>
                                    </div>
                                    <div class="col-12">
                                        <label for="email" class="form-label">Email <span
                                                class="text-danger">*</span></label>
                                        <input type="email" class="form-control" id="email" name="email"
                                            value="{{ old('email') }}" placeholder="Masukkan email Anda" required>
                                    </div>
                                    <div class="col-12">
                                        <label for="password" class="form-label">Password <span
                                                class="text-danger">*</span></label>
                                        <input type="password" class="form-control" id="password" name="password"
                                            placeholder="Buat password baru" required>
                                    </div>
                                    <div class="col-12">
                                        <label for="password_confirmation" class="form-label">Konfirmasi Password <span
                                                class="text-danger">*</span></label>
                                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
                                            placeholder="Ulangi password baru" required>
                                    </div>
                                    <div class="col-12 mt-8">
                                        <button type="submit" class="btn btn-primary w-full mb-4">Daftar Akun <i
                                                class="bi bi-person-plus ms-1 fs-16"></i></button>
                                    </div>
                                </div>
                                <p class="mb-0 fw-semibold position-relative text-center fs-12 text-muted">
                                    Sudah punya akun? <a href="{{ route('login') }}" class="text-decoration-underline text-primary">Login disini</a>
                                </p>
                            </form>
                        </div>
                    </div>
                    <p class="position-relative text-center fs-12 mb-0">© 2025 Fabkin. Crafted with ❤️ by Pixeleyez</p>
                </div>
            </div>
        </div>
    </div>
@endsection
