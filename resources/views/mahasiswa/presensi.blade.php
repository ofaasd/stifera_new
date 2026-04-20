@extends('layouts.default', ['CurrentPage' => $CurrentPage])

@section('local-css')
<style>
    .card {
        height: auto !important;
    }

    .presensi-badge {
        font-size: 0.82rem;
        padding: 4px 10px;
        border-radius: 20px;
    }

    .presensi-progress-bar {
        height: 8px;
        border-radius: 4px;
    }

    .status-hadir   { background-color: #198754; color: #fff; }
    .status-izin    { background-color: #fd7e14; color: #fff; }
    .status-alfa    { background-color: #dc3545; color: #fff; }
    .status-belum   { background-color: #adb5bd; color: #fff; }

    .course-row td { vertical-align: middle; }

    .presensi-detail-table td,
    .presensi-detail-table th {
        font-size: 0.875rem;
    }

    .progress-text {
        font-size: 0.78rem;
        color: #6c757d;
    }
</style>
@endsection

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-12">

                {{-- Header Card --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h4 class="mb-1">Presensi Perkuliahan</h4>
                        <p class="text-muted mb-2">Status kehadiran per mata kuliah pada semester aktif.</p>
                        @if(session('status'))
                            <div class="alert alert-success py-2 px-3 mb-2">{{ session('status') }}</div>
                        @endif
                        @if(session('error'))
                            <div class="alert alert-danger py-2 px-3 mb-2">{{ session('error') }}</div>
                        @endif
                        <div class="small text-muted">
                            <div>NIM : <span class="fw-semibold">{{ $mahasiswa->nim ?? '-' }}</span></div>
                            <div>Nama : <span class="fw-semibold">{{ $mahasiswa->nama ?? '-' }}</span></div>
                            @if($tahunAktif)
                                <div>Tahun Ajaran : <span class="fw-semibold">{{ ($tahunAktif->awal ?? '-') . '/' . ($tahunAktif->akhir ?? '-') }}</span>
                                    @php
                                        $jenisTa = (int) ($tahunAktif->jenis ?? 0);
                                        $labelJenis = match($jenisTa) { 1 => 'Ganjil', 2 => 'Genap', 3 => 'Antara Ganjil Genap', 4 => 'Antara Genap Ganjil', default => '-' };
                                    @endphp
                                    <span class="text-muted">({{ $labelJenis }})</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                @if(empty($presensiData))
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="alert alert-warning mb-0">
                                Belum ada data KRS aktif atau data presensi untuk semester ini.
                            </div>
                        </div>
                    </div>
                @else

                    {{-- Summary Cards --}}
                    @php
                        $totalMk          = count($presensiData);
                        $totalHadirAll    = array_sum(array_column($presensiData, 'hadir'));
                        $totalIzinAll     = array_sum(array_column($presensiData, 'izin'));
                        $totalAlfaAll     = array_sum(array_column($presensiData, 'alfa'));
                        $totalPertemuanAll = array_sum(array_column($presensiData, 'total_pertemuan'));
                    @endphp
                    <div class="row mb-4">
                        <div class="col-6 col-md-3 mb-3">
                            <div class="card border-0 shadow-sm h-100 text-center">
                                <div class="card-body py-3">
                                    <div class="text-muted small">Mata Kuliah</div>
                                    <h3 class="mb-0">{{ $totalMk }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 mb-3">
                            <div class="card border-0 shadow-sm h-100 text-center">
                                <div class="card-body py-3">
                                    <div class="text-muted small">Total Hadir</div>
                                    <h3 class="mb-0 text-success">{{ $totalHadirAll }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 mb-3">
                            <div class="card border-0 shadow-sm h-100 text-center">
                                <div class="card-body py-3">
                                    <div class="text-muted small">Total Izin</div>
                                    <h3 class="mb-0 text-warning">{{ $totalIzinAll }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 mb-3">
                            <div class="card border-0 shadow-sm h-100 text-center">
                                <div class="card-body py-3">
                                    <div class="text-muted small">Total Alfa</div>
                                    <h3 class="mb-0 text-danger">{{ $totalAlfaAll }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Per-Course Table --}}
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width:50px;">No</th>
                                            <th style="width:120px;">Kode MK</th>
                                            <th>Mata Kuliah</th>
                                            <th style="width:70px;" class="text-center">SKS</th>
                                            <th style="width:150px;">Dosen Pengampu</th>
                                            <th style="width:140px;">Jadwal</th>
                                            <th style="width:90px;" class="text-center">Pertemuan</th>
                                            <th style="width:100px;" class="text-center">Hadir</th>
                                            <th style="width:80px;" class="text-center">Izin</th>
                                            <th style="width:80px;" class="text-center">Alfa</th>
                                            <th style="width:160px;">% Kehadiran</th>
                                            <th style="width:140px;" class="text-center">Aksi Absen</th>
                                            <th style="width:90px;" class="text-center">Detail</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($presensiData as $idx => $item)
                                            @php
                                                $krs    = $item['krs'];
                                                $persen = $item['persen_hadir'];
                                                $barColor = $persen >= 75 ? 'bg-success' : ($persen >= 50 ? 'bg-warning' : 'bg-danger');
                                            @endphp
                                            <tr class="course-row">
                                                <td>{{ $idx + 1 }}</td>
                                                <td>{{ $krs->kode_mata_kuliah ?? '-' }}</td>
                                                <td>{{ $krs->nama_mata_kuliah ?? '-' }}</td>
                                                <td class="text-center">{{ (int)($krs->jumlah_sks ?? 0) }}</td>
                                                <td>{{ $krs->nama_dosen ?? '-' }}</td>
                                                <td>
                                                    {{ $krs->hari ?? '-' }}<br>
                                                    <span class="text-muted small">{{ $krs->sesi ?? '-' }} / {{ $krs->ruang ?? '-' }}</span>
                                                </td>
                                                <td class="text-center">{{ $item['total_pertemuan'] }}</td>
                                                <td class="text-center">
                                                    <span class="badge status-hadir presensi-badge">{{ $item['hadir'] }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge status-izin presensi-badge">{{ $item['izin'] }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge status-alfa presensi-badge">{{ $item['alfa'] }}</span>
                                                </td>
                                                <td>
                                                    <div class="progress presensi-progress-bar mb-1">
                                                        <div class="progress-bar {{ $barColor }}" role="progressbar"
                                                            style="width: {{ $persen }}%"
                                                            aria-valuenow="{{ $persen }}"
                                                            aria-valuemin="0" aria-valuemax="100">
                                                        </div>
                                                    </div>
                                                    <div class="progress-text">{{ $persen }}%
                                                        @if($persen < 75 && $item['total_pertemuan'] > 0)
                                                            <span class="text-danger fw-semibold">&lt; 75%</span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    @if($item['can_absen'])
                                                        <a href="{{ route('mahasiswa.absen.index') }}" class="btn btn-sm btn-success">
                                                            <i class="fa fa-pen-nib me-1"></i> Absen
                                                        </a>
                                                    @elseif($item['sudah_hadir_hari_ini'])
                                                        <span class="badge bg-primary">Sudah Hadir</span>
                                                    @else
                                                        <span class="text-muted small">Belum Ada Sesi</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if(!empty($item['pertemuan']))
                                                        <button type="button"
                                                            class="btn btn-sm btn-outline-primary"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#modalPresensi{{ $idx }}">
                                                            <i class="fa fa-eye"></i>
                                                        </button>
                                                    @else
                                                        <span class="text-muted small">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Modals for detail presensi per course --}}
                    @foreach($presensiData as $idx => $item)
                        @if(!empty($item['pertemuan']))
                            @php $krs = $item['krs']; @endphp
                            <div class="modal fade" id="modalPresensi{{ $idx }}" tabindex="-1"
                                aria-labelledby="modalPresensiLabel{{ $idx }}" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="modalPresensiLabel{{ $idx }}">
                                                Detail Presensi &mdash; {{ $krs->nama_mata_kuliah ?? '-' }}
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3 small text-muted">
                                                <span>Kode: <strong>{{ $krs->kode_mata_kuliah ?? '-' }}</strong></span>
                                                &nbsp;|&nbsp;
                                                <span>Dosen: <strong>{{ $krs->nama_dosen ?? '-' }}</strong></span>
                                                &nbsp;|&nbsp;
                                                <span>Jadwal: <strong>{{ ($krs->hari ?? '-') . ', ' . ($krs->sesi ?? '-') . ' / ' . ($krs->ruang ?? '-') }}</strong></span>
                                            </div>

                                            <div class="d-flex gap-2 mb-3">
                                                <span class="badge status-hadir presensi-badge">Hadir: {{ $item['hadir'] }}</span>
                                                <span class="badge status-izin presensi-badge">Izin: {{ $item['izin'] }}</span>
                                                <span class="badge status-alfa presensi-badge">Alfa: {{ $item['alfa'] }}</span>
                                                <span class="badge bg-secondary presensi-badge">Belum Tercatat: {{ $item['total_pertemuan'] - $item['hadir'] - $item['izin'] - $item['alfa'] }}</span>
                                            </div>

                                            <div class="table-responsive">
                                                <table class="table table-bordered table-sm presensi-detail-table mb-0">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th style="width:60px;" class="text-center">Pertemuan</th>
                                                            <th>Tanggal</th>
                                                            <th style="width:160px;" class="text-center">Status Kehadiran</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($item['pertemuan'] as $p)
                                                            <tr>
                                                                <td class="text-center">{{ $p['pertemuan_ke'] }}</td>
                                                                <td>
                                                                    @if($p['tanggal'])
                                                                        {{ \Carbon\Carbon::parse($p['tanggal'])->translatedFormat('d F Y') }}
                                                                    @else
                                                                        -
                                                                    @endif
                                                                </td>
                                                                <td class="text-center">
                                                                    @if($p['status'] === 1)
                                                                        <span class="badge status-hadir presensi-badge">Hadir</span>
                                                                    @elseif($p['status'] === 2)
                                                                        <span class="badge status-izin presensi-badge">Izin</span>
                                                                    @elseif($p['status'] === 3)
                                                                        <span class="badge status-alfa presensi-badge">Tanpa Keterangan</span>
                                                                    @else
                                                                        <span class="badge status-belum presensi-badge">Belum Tercatat</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach

                @endif

            </div>
        </div>
    </div>
</div>
@endsection
