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
            <div class="col-xl-6 col-lg-6 col-md-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4 p-lg-5">
                        <h3 class="mb-2">Selamat datang, {{ $mahasiswa->nama ?? $mahasiswa->nim }}</h3>
                        <p class="text-muted mb-3">Anda berhasil login sebagai mahasiswa.</p>
                        <span class="badge bg-primary px-3 py-2">NIM: {{ $mahasiswa->nim }}</span>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-3 col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4 d-flex flex-column justify-content-center">
                        <span class="text-black d-block mb-2">IPS</span>
                        <h2 class="mb-0">{{ number_format((float) ($ips ?? 0), 2) }}</h2>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-3 col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4 d-flex flex-column justify-content-center">
                        <span class="text-black d-block mb-2">IPK</span>
                        <h2 class="mb-0">{{ number_format((float) ($ipk ?? 0), 2) }}</h2>
                    </div>
                </div>
            </div>

            <div class="col-12 mt-2">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Kartu Rencana Studi (KRS)</h5>
                            <span class="badge bg-primary">Total SKS: {{ $totalSksKrs ?? 0 }}</span>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 60px;">No</th>
                                        <th>Kode MK</th>
                                        <th>Nama Mata Kuliah</th>
                                        <th style="width: 80px;">SKS</th>
                                        <th>Hari</th>
                                        <th>Sesi</th>
                                        <th>Ruang</th>
                                        <th>Dosen</th>
                                        <th style="width: 110px;">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse(($krsRows ?? collect()) as $idx => $row)
                                        @php
                                            $ruangRaw = (string) ($row->ruang ?? '');
                                            $ruangDecoded = urldecode($ruangRaw);
                                            $ruangClean = preg_replace('/\s+/', ' ', trim($ruangDecoded));
                                        @endphp
                                        <tr>
                                            <td>{{ $idx + 1 }}</td>
                                            <td>{{ $row->mata_kuliah ?? '-' }}</td>
                                            <td>{{ $row->nama_mata_kuliah ?? $row->mata_kuliah ?? '-' }}</td>
                                            <td>{{ (int) ($row->sks ?? 0) }}</td>
                                            <td>{{ $row->hari ?? '-' }}</td>
                                            <td>{{ $row->sesi ?? '-' }}</td>
                                            <td>{{ $ruangClean !== '' ? $ruangClean : '-' }}</td>
                                            <td>{{ trim((string) ($row->nama_dosen ?? '-')) }}</td>
                                            <td>
                                                @if((int) ($row->is_publish ?? 0) === 1)
                                                    <span class="badge bg-success">Disetujui</span>
                                                @else
                                                    <span class="badge bg-warning text-dark">Belum Disetujui</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center text-muted">Belum ada data KRS untuk mahasiswa ini.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
