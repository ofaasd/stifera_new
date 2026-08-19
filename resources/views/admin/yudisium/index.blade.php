@extends('layouts.default')

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <div class="row page-titles">
            <ol class="breadcrumb">
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Akademik</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0)">Manajemen Yudisium</a></li>
            </ol>
        </div>
        
        @if(session('success'))
            <div class="alert alert-success solid alert-dismissible fade show">
                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                <strong>Sukses!</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header pt-4 pb-0 custom-nav-tab border-bottom-0">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#pendaftar" role="tab">Pendaftar Yudisium Aktif</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#periode" role="tab">Manajemen Periode</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <!-- TAB PENDAFTAR -->
                            <div class="tab-pane fade show active" id="pendaftar" role="tabpanel">
                                @if($activePeriodes == null || count($activePeriodes) == 0)
                                    <div class="alert alert-warning">Belum ada periode yudisium yang aktif. Silahkan tambahkan periode terlebih dahulu.</div>
                                @else
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <h4 class="card-title mb-0">Daftar Pengajuan Yudisium Aktif</h4>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped" id="tablePendaftar">
                                            <thead class="bg-primary text-white text-center">
                                                <tr>
                                                    <th>No</th>
                                                    <th>NIM</th>
                                                    <th>Nama Mahasiswa</th>
                                                    <th>Prodi & Periode</th>
                                                    <th>Tgl Pengajuan</th>
                                                    <th>Status</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($pendaftars as $index => $pd)
                                                <tr>
                                                    <td class="text-center">{{ $index + 1 }}</td>
                                                    <td class="text-center">{{ $pd->mahasiswa->nim ?? '-' }}</td>
                                                    <td>{{ $pd->mahasiswa->nama ?? '-' }}</td>
                                                    <td class="text-center">
                                                        <span class="badge badge-light shadow-sm text-dark">{{ $pd->periode->programStudi->nama_jurusan ?? '-' }}</span><br>
                                                        <small class="text-primary">{{ $pd->periode->nama_periode }}</small>
                                                    </td>
                                                    <td class="text-center">{{ $pd->created_at->format('d/m/Y H:i') }}</td>
                                                    <td class="text-center">
                                                        @if($pd->status_pengajuan == 'menunggu')
                                                            <span class="badge badge-warning">Menunggu</span>
                                                        @elseif($pd->status_pengajuan == 'revisi')
                                                            <span class="badge badge-danger">Revisi</span>
                                                        @elseif($pd->status_pengajuan == 'valid')
                                                            <span class="badge badge-info">Valid</span>
                                                        @elseif($pd->status_pengajuan == 'lulus_yudisium')
                                                            <span class="badge badge-success">Lulus Yudisium</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="{{ route('admin.yudisium.show', $pd->id) }}" class="btn btn-sm btn-primary shadow">
                                                            <i class="fas fa-search me-1"></i> Verifikasi Berkas
                                                        </a>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="7" class="text-center">Belum ada mahasiswa yang mendaftar pada periode aktif saat ini.</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>

                            <!-- TAB PERIODE -->
                            <div class="tab-pane fade" id="periode" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h4 class="card-title mb-0">Daftar Periode Yudisium</h4>
                                    <button class="btn btn-primary shadow btn-sm" data-bs-toggle="modal" data-bs-target="#modalAddPeriode">
                                        <i class="fas fa-plus me-2"></i> Tambah Periode
                                    </button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped" id="tablePeriode">
                                        <thead class="bg-primary text-white text-center">
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Periode</th>
                                                <th>Prodi</th>
                                                <th>Angkatan Izin</th>
                                                <th>Tgl Mulai</th>
                                                <th>Tgl Akhir</th>
                                                <th>Status</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($periodes as $index => $period)
                                            <tr>
                                                <td class="text-center">{{ $index + 1 }}</td>
                                                <td>{{ $period->nama_periode }}</td>
                                                <td class="text-center">{{ $period->programStudi->nama_jurusan ?? '-' }}</td>
                                                <td class="text-center">{{ $period->angkatan_allowed }}</td>
                                                <td class="text-center">{{ \Carbon\Carbon::parse($period->tanggal_mulai)->format('d/m/Y') }}</td>
                                                <td class="text-center">{{ \Carbon\Carbon::parse($period->tanggal_akhir)->format('d/m/Y') }}</td>
                                                <td class="text-center">
                                                    @if($period->is_active)
                                                        <span class="badge badge-success"><i class="fas fa-check me-1"></i> Aktif</span>
                                                    @else
                                                        <span class="badge badge-secondary">Nonaktif</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <form action="{{ route('admin.yudisium.toggleActivePeriode', $period->id) }}" method="POST">
                                                        @csrf
                                                        @if($period->is_active)
                                                            <button type="submit" class="btn btn-warning btn-sm shadow" title="Nonaktifkan"><i class="fas fa-power-off"></i></button>
                                                        @else
                                                            <button type="submit" class="btn btn-success btn-sm shadow" title="Aktifkan"><i class="fas fa-check-circle"></i></button>
                                                        @endif
                                                    </form>
                                                </td>
                                            </tr>
                                            @endforeach
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

<!-- Modal Tambah Periode -->
<div class="modal fade" id="modalAddPeriode" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.yudisium.storePeriode') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Periode Yudisium</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Nama Periode</label>
                        <input type="text" name="nama_periode" class="form-control" placeholder="Contoh: Periode Ganjil 2025/2026" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold">Program Studi</label>
                            <select name="id_program_studi" class="form-control" required>
                                <option value="">-- Pilih Prodi --</option>
                                @foreach($prodi_list as $prd)
                                    <option value="{{ $prd->id }}">{{ $prd->nama_jurusan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold">Angkatan Tujuan</label>
                            <select name="angkatan_allowed[]" class="form-control default-select" multiple required>
                                @foreach($angkatan_list as $akt)
                                    <option value="{{ $akt }}">{{ $akt }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Tahan tombol CTRL (Windows) atau CMD (Mac) untuk memilih lebih dari 1 angkatan</small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold">Tanggal Akhir</label>
                            <input type="date" name="tanggal_akhir" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3 form-check custom-checkbox">
                        <input type="checkbox" name="is_active" class="form-check-input" id="checkActive" value="1" checked>
                        <label class="form-check-label" for="checkActive">Jadikan Periode Aktif</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@section('local-js')
<script>
    $(document).ready(function() {
        if ($.fn.DataTable) {
            $('#tablePendaftar').DataTable();
            $('#tablePeriode').DataTable();
        }
    });
</script>
@endsection
