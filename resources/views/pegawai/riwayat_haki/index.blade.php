@extends('layouts.default', ['CurrentPage' => $CurrentPage])

@section('content')
<div class="content-body">
    <div class="container">
        <div class="row">
            <div class="col-xl-12">
                <div class="mb-4 pb-3">
                    <a href="{{ (Auth::guard('admin')->check() && Auth::guard('pegawai')->check()) ? url('pegawai/' . Auth::guard('pegawai')->id() . '/edit') : route('pegawai.home') }}" class="btn btn-secondary">
                        <i class="fa-solid fa-arrow-left me-1"></i> Kembali
                    </a>
                </div>

                @if(session('status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ $errors->first() }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="card mb-4" style="height:auto !important;">
                    <div class="card-header">
                        <h5 class="mb-0">Tambah Riwayat HaKI</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('pegawai.riwayat-haki.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Judul <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="judul" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Pemilik</label>
                                    <input type="text" class="form-control" name="pemilik">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Tahun <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="tahun_ajaran" min="1900" max="2099" required>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Sertifikat</label>
                                    <input type="file" class="form-control" name="sertifikat" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                </div>
                            </div>
                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa-solid fa-plus me-1"></i>Tambah
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card" style="height:auto !important;">
                    <div class="card-header">
                        <h5 class="mb-0">Data Riwayat HaKI</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th width="40">No</th>
                                        <th>Judul</th>
                                        <th>Pemilik</th>
                                        <th>Tahun</th>
                                        <th>Sertifikat</th>
                                        <th width="220">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($riwayat as $i => $row)
                                        <tr>
                                            <td>{{ $i + 1 }}</td>
                                            <td>{{ $row->judul }}</td>
                                            <td>{{ $row->pemilik ?: '-' }}</td>
                                            <td>{{ $row->tahun_ajaran ?? '-' }}</td>
                                            <td>
                                                @if(!empty($row->sertifikat_url))
                                                    <a href="{{ $row->sertifikat_url }}" target="_blank" class="btn btn-sm btn-info">Lihat Sertifikat</a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal{{ $row->id }}">Edit</button>
                                                <form action="{{ route('pegawai.riwayat-haki.destroy', $row->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                                </form>
                                            </td>
                                        </tr>

                                        <div class="modal fade" id="editModal{{ $row->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <form action="{{ route('pegawai.riwayat-haki.update', $row->id) }}" method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Edit Riwayat HaKI</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="row g-3">
                                                                <div class="col-md-6">
                                                                    <label class="form-label">Judul <span class="text-danger">*</span></label>
                                                                    <input type="text" class="form-control" name="judul" value="{{ $row->judul }}" required>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label class="form-label">Pemilik</label>
                                                                    <input type="text" class="form-control" name="pemilik" value="{{ $row->pemilik }}">
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <label class="form-label">Tahun <span class="text-danger">*</span></label>
                                                                    <input type="number" class="form-control" name="tahun_ajaran" min="1900" max="2099" value="{{ $row->tahun_ajaran }}" required>
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <label class="form-label">Sertifikat</label>
                                                                    @if(!empty($row->sertifikat_url))
                                                                        <div class="mb-1">
                                                                            <a href="{{ $row->sertifikat_url }}" target="_blank" class="btn btn-sm btn-outline-info">Lihat Sertifikat Saat Ini</a>
                                                                        </div>
                                                                    @endif
                                                                    <input type="file" class="form-control" name="sertifikat" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                    <small class="text-muted">Kosongkan jika tidak ingin mengubah sertifikat.</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">Belum ada data riwayat HaKI.</td>
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
