@extends('layouts.default', ['CurrentPage' => $CurrentPage])

@section('local-css')
<style>
    .card { height: auto !important; }
    .kode-input {
        font-size: 2rem;
        letter-spacing: 0.5rem;
        text-align: center;
        text-transform: uppercase;
        font-weight: 700;
        font-family: monospace;
    }
</style>
@endsection

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-xl-5 col-lg-6 col-md-8">

                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <h4 class="mb-1">Absen Online</h4>
                        <p class="text-muted mb-0 small">
                            Masukkan kode 6 karakter yang diberikan oleh dosen pengampu untuk mencatat kehadiran Anda.
                        </p>
                    </div>
                </div>

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('status'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3"
                                style="width:72px;height:72px;">
                                <i class="fa fa-key fa-2x text-primary"></i>
                            </div>
                            <h5 class="mb-1">Masukkan Kode Absen</h5>
                            <p class="text-muted small mb-0">Kode diberikan oleh dosen saat perkuliahan dimulai</p>
                        </div>

                        <form action="{{ route('mahasiswa.absen.verifikasi') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <input
                                    type="text"
                                    name="kode_kelas"
                                    class="form-control kode-input @error('kode_kelas') is-invalid @enderror"
                                    placeholder="_ _ _ _ _ _"
                                    maxlength="6"
                                    autocomplete="off"
                                    autofocus
                                    value="{{ strtoupper(old('kode_kelas', $prefillKode ?? '')) }}"
                                >
                                @error('kode_kelas')
                                    <div class="invalid-feedback text-center">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fa fa-arrow-right me-1"></i> Lanjutkan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@section('local-js')
<script>
    // Auto-uppercase the input
    document.querySelector('input[name="kode_kelas"]').addEventListener('input', function () {
        this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
    });
</script>
@endsection
