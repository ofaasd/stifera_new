@extends('layouts.default', ['CurrentPage' => $CurrentPage])

@section('local-css')
<style>
    .ujian-card {
        height: auto !important;
    }

    .table-ujian thead tr:first-child th {
        background: #f7f9fc;
        vertical-align: middle;
        text-align: center;
    }

    .table-ujian thead tr:nth-child(2) th {
        background: #fbfcfe;
        text-align: center;
        font-weight: 500;
    }

    .table-ujian td {
        vertical-align: middle;
    }
</style>
@endsection

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-12">
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

                <div class="card ujian-card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
                            <div>
                                <h4 class="mb-1">Kartu Ujian Mahasiswa</h4>
                                <p class="text-muted mb-2">Menampilkan jadwal ujian UTS dan UAS untuk mahasiswa yang sedang login.</p>
                            </div>
                            <div class="text-end">
                                <a href="{{ route('mahasiswa.ujian.download.uts') }}" class="btn btn-sm btn-primary me-1 {{ $isUtsDiizinkan ? '' : 'disabled' }}" @if(!$isUtsDiizinkan) aria-disabled="true" @endif>
                                    Download Kartu UTS
                                </a>
                                <a href="{{ route('mahasiswa.ujian.download.uas') }}" class="btn btn-sm btn-success {{ $isUasDiizinkan ? '' : 'disabled' }}" @if(!$isUasDiizinkan) aria-disabled="true" @endif>
                                    Download Kartu UAS
                                </a>
                            </div>
                        </div>
                        <div class="small text-muted">
                            <div>NIM: <span class="fw-semibold">{{ $mahasiswa->nim ?? '-' }}</span></div>
                            <div>Nama: <span class="fw-semibold">{{ $mahasiswa->nama ?? '-' }}</span></div>
                            <div>
                                Tahun Ajaran Aktif:
                                <span class="fw-semibold">
                                    @if($tahunAktif)
                                        {{ ($tahunAktif->awal ?? '-') . '/' . ($tahunAktif->akhir ?? '-') }}
                                        @if(!empty($jenisTA)) ({{ $jenisTA }}) @endif
                                    @else
                                        -
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                @if(!$tahunAktif)
                    <div class="alert alert-warning border-0 shadow-sm">
                        Tahun ajaran aktif untuk tipe mahasiswa Anda belum tersedia.
                    </div>
                @else
                    <div class="card ujian-card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            @if(!$isUtsDiizinkan || !$isUasDiizinkan)
                                <div class="alert alert-warning">
                                    @if(!$isUtsDiizinkan && !$isUasDiizinkan)
                                        Jadwal UTS dan UAS belum diizinkan pada data keuangan mahasiswa.
                                    @elseif(!$isUtsDiizinkan)
                                        Jadwal UTS belum diizinkan pada data keuangan mahasiswa.
                                    @else
                                        Jadwal UAS belum diizinkan pada data keuangan mahasiswa.
                                    @endif
                                </div>
                            @endif

                            <div class="table-responsive">
                                <table class="table table-bordered table-hover table-ujian mb-0">
                                    <thead>
                                        <tr>
                                            <th rowspan="2" style="width:60px;">No</th>
                                            <th rowspan="2" style="width:110px;">Kode</th>
                                            <th rowspan="2">Nama Matakuliah</th>
                                            <th rowspan="2" style="width:90px;">Jml SKS</th>
                                            <th rowspan="2" style="width:90px;">Ruang</th>
                                            <th rowspan="2" style="width:100px;">No. Kursi</th>
                                            <th colspan="2">Tanggal Ujian UTS</th>
                                            <th colspan="2">Tanggal Ujian UAS</th>
                                        </tr>
                                        <tr>
                                            <th style="width:110px;">Tanggal</th>
                                            <th style="width:170px;">Waktu</th>
                                            <th style="width:110px;">Tanggal</th>
                                            <th style="width:170px;">Waktu</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($ujianRows as $idx => $row)
                                            @php
                                                $utsTanggal = $isUtsDiizinkan && !empty($row->tanggal_uts_t)
                                                    ? \Illuminate\Support\Carbon::parse($row->tanggal_uts_t)->format('d-m-Y')
                                                    : '-';
                                                $uasTanggal = $isUasDiizinkan && !empty($row->tanggal_uas_t)
                                                    ? \Illuminate\Support\Carbon::parse($row->tanggal_uas_t)->format('d-m-Y')
                                                    : '-';

                                                $utsWaktu = '-';
                                                if ($isUtsDiizinkan && (!empty($row->mulai_uts_t) || !empty($row->selesai_uts_t))) {
                                                    $mulaiUts = !empty($row->mulai_uts_t) ? \Illuminate\Support\Carbon::parse($row->mulai_uts_t)->format('H:i') : '--:--';
                                                    $selesaiUts = !empty($row->selesai_uts_t) ? \Illuminate\Support\Carbon::parse($row->selesai_uts_t)->format('H:i') : '--:--';
                                                    $utsWaktu = $mulaiUts . ' - ' . $selesaiUts;
                                                }

                                                $uasWaktu = '-';
                                                if ($isUasDiizinkan && (!empty($row->mulai_uas_t) || !empty($row->selesai_uas_t))) {
                                                    $mulaiUas = !empty($row->mulai_uas_t) ? \Illuminate\Support\Carbon::parse($row->mulai_uas_t)->format('H:i') : '--:--';
                                                    $selesaiUas = !empty($row->selesai_uas_t) ? \Illuminate\Support\Carbon::parse($row->selesai_uas_t)->format('H:i') : '--:--';
                                                    $uasWaktu = $mulaiUas . ' - ' . $selesaiUas;
                                                }
                                            @endphp
                                            <tr>
                                                <td>{{ $idx + 1 }}</td>
                                                <td>{{ $row->kode_mata_kuliah ?? '-' }}</td>
                                                <td>{{ $row->nama_mata_kuliah ?? '-' }}</td>
                                                <td class="text-center">{{ (int) ($row->jumlah_sks ?? 0) }}</td>
                                                <td class="text-center">{{ $row->ruang ?? '-' }}</td>
                                                <td class="text-center">{{ $row->no_kursi ?? '-' }}</td>
                                                <td class="text-center">{{ $utsTanggal }}</td>
                                                <td class="text-center">{{ $utsWaktu }}</td>
                                                <td class="text-center">{{ $uasTanggal }}</td>
                                                <td class="text-center">{{ $uasWaktu }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="10" class="text-center text-muted">Belum ada data kartu ujian untuk semester ini.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
