@extends('layouts.default', ['CurrentPage' => $CurrentPage])

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-xl-8 col-lg-9">
                @if(session('status'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="card border-0 shadow-sm mb-4" style="height: auto !important;">
                    <div class="card-body">
                        <h4 class="mb-1">Ganti Password</h4>
                        <p class="text-muted mb-0">Ubah password akun mahasiswa Anda secara mandiri.</p>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <form action="{{ route('mahasiswa.password.update') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label" for="current_password">Password Lama</label>
                                <input type="password" class="form-control" id="current_password" name="current_password" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="password">Password Baru</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <small class="text-muted">Minimal 8 karakter, wajib ada huruf besar, huruf kecil, dan angka.</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="password_confirmation">Konfirmasi Password Baru</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">Simpan Password Baru</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
