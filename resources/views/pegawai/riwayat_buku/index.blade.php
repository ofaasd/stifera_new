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
                        <h5 class="mb-0">Tambah Riwayat Buku</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('pegawai.riwayat-buku.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Judul Buku <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="judul_buku" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Penulis</label>
                                    <input type="text" class="form-control" name="penulis">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Tahun <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="tahun" min="1900" max="2099" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">ISBN</label>
                                    <input type="text" class="form-control" name="isbn">
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label">Dokumen</label>
                                    <input type="file" class="form-control" name="link_dokumen" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
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
                        <h5 class="mb-0">Data Riwayat Buku</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th width="40">No</th>
                                        <th>Judul Buku</th>
                                        <th>Penulis</th>
                                        <th>ISBN</th>
                                        <th>Tahun</th>
                                        <th>Dokumen</th>
                                        <th width="220">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($riwayat as $i => $row)
                                        <tr>
                                            <td>{{ $i + 1 }}</td>
                                            <td>{{ $row->judul_buku }}</td>
                                            <td>{{ $row->penulis ?: '-' }}</td>
                                            <td>{{ $row->isbn ?: '-' }}</td>
                                            <td>{{ $row->tahun ?? '-' }}</td>
                                            <td>
                                                @if(!empty($row->dokumen_url))
                                                    <a href="{{ $row->dokumen_url }}" target="_blank" class="btn btn-sm btn-info">Lihat Dokumen</a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal{{ $row->id }}">Edit</button>
                                                <form action="{{ route('pegawai.riwayat-buku.destroy', $row->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                                </form>
                                            </td>
                                        </tr>

                                        <div class="modal fade" id="editModal{{ $row->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <form action="{{ route('pegawai.riwayat-buku.update', $row->id) }}" method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Edit Riwayat Buku</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="row g-3">
                                                                <div class="col-md-6">
                                                                    <label class="form-label">Judul Buku <span class="text-danger">*</span></label>
                                                                    <input type="text" class="form-control" name="judul_buku" value="{{ $row->judul_buku }}" required>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label class="form-label">Penulis</label>
                                                                    <input type="text" class="form-control" name="penulis" value="{{ $row->penulis }}">
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <label class="form-label">Tahun <span class="text-danger">*</span></label>
                                                                    <input type="number" class="form-control" name="tahun" min="1900" max="2099" value="{{ $row->tahun }}" required>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label class="form-label">ISBN</label>
                                                                    <input type="text" class="form-control" name="isbn" value="{{ $row->isbn }}">
                                                                </div>
                                                                <div class="col-md-8">
                                                                    <label class="form-label">Dokumen</label>
                                                                    @if(!empty($row->dokumen_url))
                                                                        <div class="mb-1">
                                                                            <a href="{{ $row->dokumen_url }}" target="_blank" class="btn btn-sm btn-outline-info">Lihat Dokumen Saat Ini</a>
                                                                        </div>
                                                                    @endif
                                                                    <input type="file" class="form-control" name="link_dokumen" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                    <small class="text-muted">Kosongkan jika tidak ingin mengubah dokumen.</small>
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
                                            <td colspan="7" class="text-center">Belum ada data riwayat buku.</td>
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
