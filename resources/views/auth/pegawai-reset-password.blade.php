@extends('layouts.fullwidth', ['CurrentPage' => $CurrentPage])

@section('content')
<div class="authincation h-100">
    <div class="container-fluid h-100">
        <div class="row h-100">
            <div class="col-lg-6 col-md-12 col-sm-12 mx-auto align-self-center">
                <div class="login-form">
                    @if(session('status'))
                        <div class="alert alert-success">{{ session('status') }}</div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0 ps-3">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="text-center mb-4">
                        <img src="{{ asset('images/logo-full.png') }}" class="mb-3 login-sm-logo mx-auto" alt="">
                        <h3 class="title">Reset Password Pegawai</h3>
                        <p>Gunakan Username dan NPP untuk verifikasi, lalu tentukan password baru.</p>
                    </div>

                    <form action="{{ route('pegawai.password.reset.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="mb-1">Username Pegawai <span class="text-danger">*</span></label>
                            <input type="text" name="usrnm" value="{{ old('usrnm') }}" class="form-control" placeholder="Masukkan username" required>
                        </div>

                        <div class="mb-3">
                            <label class="mb-1">NPP <span class="text-danger">*</span></label>
                            <input type="text" name="npp" value="{{ old('npp') }}" class="form-control" placeholder="Masukkan NPP" required>
                        </div>

                        <div class="mb-3">
                            <label class="mb-1">Password Baru <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" placeholder="Minimal 8 karakter" required>
                        </div>

                        <div class="mb-4">
                            <label class="mb-1">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password baru" required>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Reset Password</button>
                            <a href="{{ route('login') }}" class="btn btn-light border">Kembali ke Login</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-xl-6 col-lg-6">
                <div class="pages-left h-100">
                    <div class="login-content">
                        <a href="{{ url('/') }}"><img src="{{ asset('images/logo-full.png') }}" class="mb-3" alt=""></a>
                        <p>Setelah reset, password tersimpan dengan hash Laravel yang aman dan tidak lagi memakai MD5.</p>
                    </div>
                    <div class="login-media text-center">
                        <img src="{{ asset('images/login.png') }}" alt="">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
