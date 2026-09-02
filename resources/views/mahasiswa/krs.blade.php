@extends('layouts.default', ['CurrentPage' => $CurrentPage])

@section('local-css')
<style>
    .krs-card {
        height: auto !important;
    }

    .dosen-column {
        text-align: left;
    }

    .table-responsive .table {
        width: 100%;
        table-layout: fixed;
    }

    .table-responsive .table th,
    .table-responsive .table td {
        white-space: normal;
        word-break: break-word;
        overflow-wrap: anywhere;
    }

    @media (max-width: 767.98px) {
        .table-responsive .table {
            table-layout: auto;
            min-width: 650px;
        }

        .table-responsive .table th,
        .table-responsive .table td {
            white-space: nowrap;
            word-break: normal;
            overflow-wrap: normal;
        }
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

                <div class="card krs-card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3" >
                            <div>
                                <h4 class="mb-1">Kartu Rencana Studi (KRS)</h4>
                                <p class="text-muted mb-2">Halaman ini hanya menampilkan data KRS untuk akun mahasiswa yang sedang login.</p>
                                <div class="small text-muted">
                                    <div>NIM: <span class="fw-semibold">{{ $mahasiswa->nim ?? '-' }}</span></div>
                                    <div>Nama: <span class="fw-semibold">{{ $mahasiswa->nama ?? '-' }}</span></div>
                                    <div>Tipe Mahasiswa: 
                                        <span class="fw-semibold">
                                            @if(($mahasiswa->tipe_mhs ?? 0) == 1)
                                                Mahasiswa Reguler
                                            @elseif(($mahasiswa->tipe_mhs ?? 0) == 2)
                                                Mahasiswa RPL
                                            @else
                                                -
                                            @endif
                                        </span>
                                    </div>
                                    <div>
                                        Tahun Ajaran Aktif:
                                        <span class="fw-semibold">
                                            @if($tahunAktif)
                                                {{ ($tahunAktif->awal ?? '-') . '/' . ($tahunAktif->akhir ?? '-') }}
                                                @if(!empty($jenisTA)) ({{ $jenisTA }}) (ID: {{ $tahunAktif->id }}) @endif
                                            @else
                                                -
                                            @endif
                                        </span>
                                    </div>
                                    <div>IPS Terakhir: <span class="fw-semibold">{{ number_format((float) ($ipsTerakhir ?? 0), 2) }}</span></div>
                                    <div>Batas SKS: <span class="fw-semibold">{{ (int) ($batasSks ?? 24) }}</span></div>
                                </div>
                            </div>

                            <div>
                                @if($krsRows->isEmpty() || !$isKrsDisetujuiWali)
                                    <button class="btn btn-secondary disabled" title="KRS belum diverifikasi oleh Dosen Wali" aria-disabled="true">
                                        <i class="fa fa-download me-1"></i>Download KRS
                                    </button>
                                @else
                                    <a href="{{ route('mahasiswa.krs.download') }}" class="btn btn-primary">
                                        <i class="fa fa-download me-1"></i>Download KRS
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card krs-card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        @if(!$tahunAktif)
                            <div class="alert alert-warning mb-0">
                                Data tahun ajaran untuk tipe mahasiswa Anda belum tersedia.
                            </div>
                        @elseif($isKrsDiizinkan && !$isKrsDisetujuiWali)
                            <div class="alert alert-success">
                                <i class="fa fa-check-circle me-1"></i>
                                Anda diizinkan admin untuk melakukan input KRS semester berjalan.
                            </div>

                            <form action="{{ route('mahasiswa.krs.store') }}" method="POST" class="row g-2 align-items-end">
                                @csrf
                                <div class="col-lg-9">
                                    <label for="id_jadwal" class="form-label">Pilih Jadwal Mata Kuliah</label>
                                    <select id="id_jadwal" name="id_jadwal" class="form-select" required>
                                        <option value="">-- Pilih Mata Kuliah --</option>
                                        @foreach($jadwalTersedia as $jadwal)
                                            @php
                                                $kuotaPenuh = (int) ($jadwal->kuota_diambil ?? 0) > 0 && (int) ($jadwal->total_diambil ?? 0) >= (int) ($jadwal->kuota_diambil ?? 0);
                                                $ruangJadwal = !empty($jadwal->ruang) ? urldecode((string) $jadwal->ruang) : '-';
                                            @endphp
                                            <option value="{{ $jadwal->id }}" {{ $kuotaPenuh ? 'disabled' : '' }}>
                                                {{ $jadwal->kode_mata_kuliah }} - {{ $jadwal->nama_mata_kuliah ?? '-' }}
                                                | {{ (int) ($jadwal->jumlah_sks ?? 0) }} SKS
                                                | {{ $jadwal->hari ?? '-' }} {{ $jadwal->sesi ?? '-' }}
                                                | Ruang {{ $ruangJadwal }}
                                                | Dosen: {{ trim($jadwal->nama_dosen ?? '-') }}
                                                @if($kuotaPenuh) | KUOTA PENUH @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-3 d-grid">
                                    <button type="submit" class="btn btn-success" {{ $jadwalTersedia->isEmpty() ? 'disabled' : '' }}>
                                        <i class="fa fa-plus me-1"></i>Tambah Ke KRS
                                    </button>
                                </div>
                            </form>

                            @if($jadwalTersedia->isEmpty())
                                <p class="text-muted small mt-2 mb-0">Tidak ada jadwal baru yang dapat diambil untuk saat ini.</p>
                            @endif
                        @elseif($isKrsDisetujuiWali)
                            <div class="alert alert-info mb-0">
                                <i class="fa fa-lock me-1"></i>
                                KRS Anda sudah disetujui dosen wali. Penambahan KRS dinonaktifkan.
                            </div>
                        @else
                            <div class="alert alert-warning mb-0">
                                <i class="fa fa-lock me-1"></i>
                                Input KRS belum diizinkan oleh admin pada data keuangan mahasiswa.
                                Anda hanya dapat melihat KRS yang sudah terinput.
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card krs-card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Daftar KRS Terinput</h5>
                            <span class="badge bg-primary">Total SKS: {{ $totalSks }}</span>
                        </div>

                        <div class="table-responsive">
                            <table id="table-mhs-krs" class="table table-bordered table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th style="width: 60px;">No</th>
                                        <th>Kode MK</th>
                                        <th>Nama Mata Kuliah</th>
                                        <th style="width: 80px;">SKS</th>
                                        <th>Hari</th>
                                        <th>Sesi</th>
                                        <th>Ruang</th>
                                        <th class="dosen-column">Dosen</th>
                                        <th style="width: 80px;" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($krsRows as $idx => $row)
                                        @php
                                            $ruangKrs = !empty($row->ruang) ? urldecode((string) $row->ruang) : '-';
                                        @endphp
                                        <tr>
                                            <td>{{ $idx + 1 }}</td>
                                            <td>{{ $row->mata_kuliah ?? '-' }}</td>
                                            <td>{{ $row->nama_mata_kuliah ?? $row->mata_kuliah ?? '-' }}</td>
                                            <td>{{ $row->sks ?? 0 }}</td>
                                            <td>{{ $row->hari ?? '-' }}</td>
                                            <td>{{ $row->sesi ?? '-' }}</td>
                                            <td>{{ $ruangKrs }}</td>
                                            <td class="dosen-column">{{ trim($row->nama_dosen ?? '-') }}</td>
                                            <td class="text-center">
                                                @if(!$isKrsDisetujuiWali)
                                                    <form method="POST" action="{{ route('mahasiswa.krs.destroy', $row->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan mata kuliah ini?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm shadow-sm" title="Hapus">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center text-muted">Belum ada mata kuliah di KRS Anda.</td>
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

@section('local-js')
<script>
    
</script>
@endsection
