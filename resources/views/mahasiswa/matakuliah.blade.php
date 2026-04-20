@extends('layouts.default', ['CurrentPage' => $CurrentPage])

@section('local-css')
<style>
    .matkul-card {
        height: auto !important;
    }

    .matkul-stat {
        border-radius: 14px;
        padding: 18px;
        background: linear-gradient(135deg, #f7fafc 0%, #eef4ff 100%);
        border: 1px solid #e5edf8;
    }

    .matkul-stat h3 {
        margin-bottom: 4px;
        font-size: 28px;
    }

    .nav-pills .nav-link.active {
        background-color: #0d6efd;
    }
</style>
@endsection

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-12">
                <div class="card matkul-card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h4 class="mb-1">Daftar Mata Kuliah</h4>
                        <p class="text-muted mb-2">Menampilkan mata kuliah sesuai program studi mahasiswa yang sedang login, dipisahkan menjadi yang sudah dan belum diambil.</p>
                        <div class="small text-muted">
                            <div>NIM: <span class="fw-semibold">{{ $mahasiswa->nim ?? '-' }}</span></div>
                            <div>Nama: <span class="fw-semibold">{{ $mahasiswa->nama ?? '-' }}</span></div>
                            <div>Program Studi: <span class="fw-semibold">{{ trim(($prodi->jenjang ?? '') . ' ' . ($prodi->nama_jurusan ?? '-')) }}</span></div>
                            <div>Fakultas: <span class="fw-semibold">{{ $prodi->nama_fakultas ?? '-' }}</span></div>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-lg-4 col-md-6 mb-3">
                        <div class="matkul-stat h-100">
                            <div class="text-muted small">Total Mata Kuliah Prodi</div>
                            <h3>{{ $totalMatakuliah }}</h3>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-3">
                        <div class="matkul-stat h-100">
                            <div class="text-muted small">Sudah Diambil</div>
                            <h3>{{ $matakuliahDiambil->count() }}</h3>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-3">
                        <div class="matkul-stat h-100">
                            <div class="text-muted small">Belum Diambil</div>
                            <h3>{{ $matakuliahBelumDiambil->count() }}</h3>
                        </div>
                    </div>
                </div>

                <div class="card matkul-card border-0 shadow-sm">
                    <div class="card-body">
                        <ul class="nav nav-pills mb-3" id="matkul-tab" role="tablist">
                            <li class="nav-item me-2" role="presentation">
                                <button class="nav-link active" id="diambil-tab" data-bs-toggle="pill" data-bs-target="#diambil-pane" type="button" role="tab" aria-controls="diambil-pane" aria-selected="true">
                                    Sudah Diambil
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="belum-tab" data-bs-toggle="pill" data-bs-target="#belum-pane" type="button" role="tab" aria-controls="belum-pane" aria-selected="false">
                                    Belum Diambil
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content" id="matkul-tab-content">
                            <div class="tab-pane fade show active" id="diambil-pane" role="tabpanel" aria-labelledby="diambil-tab" tabindex="0">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th style="width:60px;">No</th>
                                                <th style="width:140px;">Kode Mata Kuliah</th>
                                                <th>Nama Mata Kuliah</th>
                                                <th style="width:90px;">SKS</th>
                                                <th style="width:90px;">Semester</th>
                                                <th style="width:140px;">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($matakuliahDiambil as $idx => $row)
                                                <tr>
                                                    <td>{{ $idx + 1 }}</td>
                                                    <td>{{ $row->kode_mata_kuliah ?? '-' }}</td>
                                                    <td>{{ $row->nama_mata_kuliah ?? '-' }}</td>
                                                    <td class="text-center">{{ (int) ($row->jumlah_sks ?? 0) }}</td>
                                                    <td class="text-center">{{ (int) ($row->semester ?? 0) }}</td>
                                                    <td class="text-center"><span class="badge bg-success">Sudah Diambil</span></td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted">Belum ada mata kuliah yang terdeteksi sudah diambil.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="belum-pane" role="tabpanel" aria-labelledby="belum-tab" tabindex="0">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th style="width:60px;">No</th>
                                                <th style="width:140px;">Kode Mata Kuliah</th>
                                                <th>Nama Mata Kuliah</th>
                                                <th style="width:90px;">SKS</th>
                                                <th style="width:90px;">Semester</th>
                                                <th style="width:140px;">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($matakuliahBelumDiambil as $idx => $row)
                                                <tr>
                                                    <td>{{ $idx + 1 }}</td>
                                                    <td>{{ $row->kode_mata_kuliah ?? '-' }}</td>
                                                    <td>{{ $row->nama_mata_kuliah ?? '-' }}</td>
                                                    <td class="text-center">{{ (int) ($row->jumlah_sks ?? 0) }}</td>
                                                    <td class="text-center">{{ (int) ($row->semester ?? 0) }}</td>
                                                    <td class="text-center"><span class="badge bg-secondary">Belum Diambil</span></td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted">Semua mata kuliah pada program studi ini sudah diambil.</td>
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
    </div>
</div>
@endsection
