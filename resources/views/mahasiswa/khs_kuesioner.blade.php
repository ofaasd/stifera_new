@extends('layouts.default', ['CurrentPage' => $CurrentPage])

@section('local-css')
<style>
    .kuesioner-card {
        height: auto !important;
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

                <div class="card kuesioner-card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h4 class="mb-1">Kuesioner Perkuliahan</h4>
                        <p class="text-muted mb-3">
                            KHS akan ditampilkan setelah seluruh kuesioner per jadwal dan dosen selesai diisi.
                        </p>

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

                        <div class="mt-3">
                            <span class="badge bg-primary">Progress: {{ $progressDone }} dari {{ $progressTotal }} kuesioner selesai</span>
                        </div>
                    </div>
                </div>

                <div class="card kuesioner-card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="mb-3">Daftar Kuesioner</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 60px;">No</th>
                                        <th>Jadwal dan Dosen</th>
                                        <th style="width: 180px;">Status</th>
                                        <th style="width: 140px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($questionnairePairs as $idx => $pair)
                                        <tr>
                                            <td>{{ $idx + 1 }}</td>
                                            <td>{{ $pair->label ?? '-' }}</td>
                                            <td>
                                                @if((bool) ($pair->is_completed ?? false))
                                                    <span class="badge bg-success">Selesai ({{ (int) ($pair->answered_count ?? 0) }}/{{ (int) ($pair->total_soal ?? 0) }})</span>
                                                @else
                                                    <span class="badge bg-warning text-dark">Belum selesai ({{ (int) ($pair->answered_count ?? 0) }}/{{ (int) ($pair->total_soal ?? 0) }})</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('mahasiswa.khs.kuesioner.form', ['id_jadwal' => (int) ($pair->id_jadwal ?? 0), 'id_dosen' => (int) ($pair->id_dosen ?? 0)]) }}" class="btn btn-sm btn-outline-primary">
                                                    Pilih
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">Tidak ada jadwal untuk kuesioner.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card kuesioner-card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="mb-3">Petunjuk Pengisian</h5>
                        <div class="alert alert-info mb-0">
                            Klik tombol <strong>Pilih</strong> pada baris jadwal-dosen untuk masuk ke halaman pengisian kuesioner.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
