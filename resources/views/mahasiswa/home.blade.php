@extends('layouts.default', ['CurrentPage' => $CurrentPage])

@section('content')
{{-- Banner Impersonasi --}}
@if(session('impersonasi_admin') || session('impersonasi_notice'))
<div style="position: fixed; top: 0; left: 0; right: 0; z-index: 9999; background: #c0392b; color: #fff; padding: 10px 20px; font-size: 14px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 2px 8px rgba(0,0,0,0.3);">
    <span>
        <i class="fa fa-user-secret me-2"></i>
        <strong>Mode Impersonasi Admin</strong> &mdash;
        Anda sedang melihat akun mahasiswa ini sebagai admin
        @php $adminInfo = session('impersonasi_admin'); @endphp
        @if($adminInfo) ({{ $adminInfo['usrnm'] }}) @endif.
    </span>
    <form method="POST" action="{{ route('mahasiswa.impersonasi.stop') }}">
        @csrf
        <button type="submit" class="btn btn-sm btn-light text-danger fw-bold">
            <i class="fa fa-sign-out-alt me-1"></i> Akhiri Impersonasi
        </button>
    </form>
</div>
<div style="height: 48px;"></div>
@endif
<div class="content-body">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-9 col-lg-10 col-md-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4 p-lg-5 text-center">
                        <img src="{{ asset('images/logo-full.png') }}" class="mb-4" style="max-height: 52px;" alt="">
                        <h3 class="mb-2">Selamat datang, {{ $mahasiswa->nama ?? $mahasiswa->nim }}</h3>
                        <p class="text-muted mb-4">Anda berhasil login sebagai mahasiswa.</p>

                        <div class="mb-3">
                            <span class="badge bg-primary px-3 py-2">NIM: {{ $mahasiswa->nim }}</span>
                        </div>

                        <p class="text-muted mb-0">Silakan pilih menu di sidebar untuk mengakses fitur mahasiswa.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
