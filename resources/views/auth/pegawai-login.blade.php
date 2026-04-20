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
                        <h3 class="title">Login Pegawai</h3>
                        <p>Masuk menggunakan username pegawai dan password.</p>
                    </div>

                    <form action="{{ route('pegawai.login.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="mb-1">Username Pegawai <span class="text-danger">*</span></label>
                            <input type="text" name="usrnm" value="{{ old('usrnm') }}" class="form-control" placeholder="Masukkan username" required>
                        </div>

                        <div class="mb-4">
                            <label class="mb-1">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                        </div>

                        <div class="d-flex justify-content-between mb-4">
                            <a href="{{ route('pegawai.password.reset.form') }}" class="btn-link text-primary">Lupa password pegawai?</a>
                            <a href="{{ route('admin.login') }}" class="btn-link text-secondary">Login sebagai admin</a>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Login Pegawai</button>
                            <a href="{{ route('login') }}" class="btn btn-outline-secondary">Kembali ke Login Portal</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-xl-6 col-lg-6">
                <div class="pages-left h-100">
                    <div class="login-content">
                        <a href="{{ url('/') }}"><img src="{{ asset('images/logo-full.png') }}" class="mb-3" alt=""></a>
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
