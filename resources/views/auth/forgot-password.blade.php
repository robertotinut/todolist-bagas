@extends('partials.layouts.master_auth')

@section('title', 'Lupa Password | SLADA')

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
                            <img src="{{ asset('assets/' . 'images/logo-dark-2.png') }}" alt="Logo Dark" height="30"
                                class="mb-4 mx-auto d-block">
                            <h6 class="mb-2 fw-medium text-center">Lupa Password?</h6>
                            <p class="text-muted text-center fs-13 mb-6">Masukkan alamat email yang terdaftar. Kami akan mengirimkan link untuk mereset kata sandi Anda.</p>

                            @if (session('status'))
                                <div class="alert alert-success mb-4 border-0 shadow-sm">
                                    <i class="bi bi-check-circle-fill me-1"></i> {{ session('status') }}
                                </div>
                            @endif

                            @if ($errors->any())
                                <div class="alert alert-danger mb-4">
                                    <ul class="mb-0 list-unstyled">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('password.email') }}">
                                @csrf
                                <div class="row g-4">
                                    <div class="col-12">
                                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" id="email" name="email"
                                            value="{{ old('email') }}" placeholder="Masukkan email terdaftar" required autofocus>
                                    </div>
                                    <div class="col-12 mt-6">
                                        <button type="submit" class="btn btn-primary w-full mb-4">Kirim Link Reset <i class="bi bi-send ms-1"></i></button>
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
