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
                        <div class="small text-muted mt-2 pt-2 border-top">
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <div>NIM: <span class="fw-semibold text-dark">{{ $mahasiswa->nim ?? '-' }}</span></div>
                                    <div>Nama: <span class="fw-semibold text-dark">{{ $mahasiswa->nama ?? '-' }}</span></div>
                                    <div>
                                        Tahun Ajaran:
                                        <span class="fw-semibold text-dark">
                                            @if($tahunAktif)
                                                {{ ($tahunAktif->awal ?? '-') . '/' . ($tahunAktif->akhir ?? '-') }}
                                                @if(!empty($jenisTA)) ({{ $jenisTA }}) @endif
                                            @else
                                                -
                                            @endif
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="d-flex flex-wrap gap-2 align-items-center">
                                        <div>
                                            <span class="text-muted d-block small">Status Validasi Dosen Wali:</span>
                                            @if(!empty($isDosenWaliValid))
                                                <span class="badge bg-success-subtle text-success border border-success py-1 px-2">
                                                    <i class="fa fa-check-circle me-1"></i>Sudah Divalidasi Dosen Wali
                                                    @if(!empty($mahasiswaProfil->dosen_wali_paraf))
                                                        <i class="fa fa-signature ms-1 text-primary" title="Paraf digital Dosen Wali aktif"></i>
                                                    @endif
                                                </span>
                                            @else
                                                <span class="badge bg-danger-subtle text-danger border border-danger py-1 px-2">
                                                    <i class="fa fa-times-circle me-1"></i>Belum Divalidasi Dosen Wali
                                                </span>
                                            @endif
                                        </div>
                                        <div>
                                            <span class="text-muted d-block small">Status Keuangan / BAK (UTS):</span>
                                            @if($isUtsDiizinkan)
                                                <span class="badge bg-success-subtle text-success border border-success py-1 px-2">
                                                    <i class="fa fa-check-circle me-1"></i>Aktif (Diizinkan)
                                                </span>
                                            @else
                                                <span class="badge bg-warning-subtle text-warning border border-warning py-1 px-2">
                                                    <i class="fa fa-clock me-1"></i>Belum Aktif
                                                </span>
                                            @endif
                                        </div>
                                        <div>
                                            <span class="text-muted d-block small">Status Keuangan / BAK (UAS):</span>
                                            @if($isUasDiizinkan)
                                                <span class="badge bg-success-subtle text-success border border-success py-1 px-2">
                                                    <i class="fa fa-check-circle me-1"></i>Aktif (Diizinkan)
                                                </span>
                                            @else
                                                <span class="badge bg-warning-subtle text-warning border border-warning py-1 px-2">
                                                    <i class="fa fa-clock me-1"></i>Belum Aktif
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
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
                                <div class="alert alert-warning alert-dismissible fade show">
                                    @if(!$isUtsDiizinkan && !$isUasDiizinkan)
                                        <i class="fa fa-circle-exclamation me-1"></i>Jadwal UTS dan UAS belum diizinkan pada data keuangan mahasiswa.
                                    @elseif(!$isUtsDiizinkan)
                                        <i class="fa fa-circle-exclamation me-1"></i>Jadwal UTS belum diizinkan pada data keuangan mahasiswa.
                                    @else
                                        <i class="fa fa-circle-exclamation me-1"></i>Jadwal UAS belum diizinkan pada data keuangan mahasiswa.
                                    @endif
                                </div>
                            @endif

                            <div class="table-responsive">
                                <table class="table table-bordered table-hover table-ujian mb-0">
                                    <thead>
                                        <tr>
                                            <th rowspan="2" style="width:50px;">No</th>
                                            <th rowspan="2" style="width:100px;">Kode</th>
                                            <th rowspan="2">Nama Matakuliah</th>
                                            <th rowspan="2" style="width:70px;">SKS</th>
                                            <th rowspan="2" style="width:80px;">Ruang</th>
                                            <th rowspan="2" style="width:85px;">Kursi</th>
                                            <th colspan="3">Ujian UTS</th>
                                            <th colspan="3">Ujian UAS</th>
                                        </tr>
                                        <tr>
                                            <th style="width:100px;">Tanggal</th>
                                            <th style="width:140px;">Waktu</th>
                                            <th style="width:120px;">Paraf Pengawas</th>
                                            <th style="width:100px;">Tanggal</th>
                                            <th style="width:140px;">Waktu</th>
                                            <th style="width:120px;">Paraf Pengawas</th>
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
                                                <td class="text-center">{{ $idx + 1 }}</td>
                                                <td class="text-center fw-semibold font-monospace">{{ $row->kode_mata_kuliah ?? '-' }}</td>
                                                <td>{{ $row->nama_mata_kuliah ?? '-' }}</td>
                                                <td class="text-center">{{ (int) ($row->jumlah_sks ?? 0) }}</td>
                                                <td class="text-center">{{ $row->ruang ?? '-' }}</td>
                                                <td class="text-center">{{ $row->no_kursi ?? '-' }}</td>
                                                
                                                {{-- UTS --}}
                                                <td class="text-center">{{ $utsTanggal }}</td>
                                                <td class="text-center small">{{ $utsWaktu }}</td>
                                                <td class="text-center">
                                                    @if(!empty($row->paraf_pengawas_uts))
                                                        <div class="d-flex flex-column align-items-center">
                                                            <img src="{{ $row->paraf_pengawas_uts }}" alt="Paraf UTS" style="max-height: 36px; max-width: 75px; background: #fff; border: 1px solid #198754; border-radius: 4px; padding: 2px;">
                                                            <small class="text-success fw-semibold mt-1" style="font-size: 10px;">
                                                                {{ !empty($row->waktu_paraf_uts) ? \Carbon\Carbon::parse($row->waktu_paraf_uts)->format('d/m/Y H:i') : '' }}
                                                            </small>
                                                        </div>
                                                    @else
                                                        <span class="badge bg-light text-muted border">Belum Paraf</span>
                                                    @endif
                                                </td>

                                                {{-- UAS --}}
                                                <td class="text-center">{{ $uasTanggal }}</td>
                                                <td class="text-center small">{{ $uasWaktu }}</td>
                                                <td class="text-center">
                                                    @if(!empty($row->paraf_pengawas_uas))
                                                        <div class="d-flex flex-column align-items-center">
                                                            <img src="{{ $row->paraf_pengawas_uas }}" alt="Paraf UAS" style="max-height: 36px; max-width: 75px; background: #fff; border: 1px solid #198754; border-radius: 4px; padding: 2px;">
                                                            <small class="text-success fw-semibold mt-1" style="font-size: 10px;">
                                                                {{ !empty($row->waktu_paraf_uas) ? \Carbon\Carbon::parse($row->waktu_paraf_uas)->format('d/m/Y H:i') : '' }}
                                                            </small>
                                                        </div>
                                                    @else
                                                        <span class="badge bg-light text-muted border">Belum Paraf</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="12" class="text-center text-muted py-4">Belum ada data kartu ujian untuk semester ini.</td>
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
