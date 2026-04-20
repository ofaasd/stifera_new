@extends('layouts.default', ['CurrentPage' => $CurrentPage])

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-12">
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Detail Masukan</h4>
                        <a href="{{ route('mahasiswa.masukan.create') }}" class="btn btn-sm btn-outline-secondary">Kembali</a>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label text-muted mb-1">NIM</label>
                                <div class="fw-semibold">{{ $masukan->nim }}</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted mb-1">Tanggal Kirim</label>
                                <div class="fw-semibold">{{ optional($masukan->tanggal)->format('d-m-Y H:i') }}</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted mb-1">Status</label>
                                <div>
                                    @php
                                        $badgeClass = 'secondary';
                                        if ($masukan->status === 'belum') {
                                            $badgeClass = 'warning';
                                        } elseif ($masukan->status === 'proses') {
                                            $badgeClass = 'info';
                                        } elseif ($masukan->status === 'selesai') {
                                            $badgeClass = 'success';
                                        }
                                    @endphp
                                    <span class="badge bg-{{ $badgeClass }}">{{ ucfirst($masukan->status) }}</span>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label text-muted mb-1">Isi Masukan</label>
                                <div class="border rounded p-3 bg-light">{!! nl2br(e($masukan->saran)) !!}</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted mb-1">Tindak Lanjut Admin</label>
                                <div class="border rounded p-3 bg-light">
                                    {!! $masukan->tindak_lanjut ? nl2br(e($masukan->tindak_lanjut)) : '<span class="text-muted">Belum ada tindak lanjut.</span>' !!}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted mb-1">Tanggal Tanggapan</label>
                                <div class="border rounded p-3 bg-light">
                                    {{ optional($masukan->tanggal_tanggapan)->format('d-m-Y H:i') ?? 'Belum ada tanggapan.' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
