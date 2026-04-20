@extends('layouts.default', ['CurrentPage' => $CurrentPage])

@section('content')
<div class="content-body">
    <div class="container">
        <div class="row">
            <div class="col-xl-12">
                <div class="mb-4 pb-3">
                    <a href="{{ route('pegawai.home') }}" class="btn btn-secondary">
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
                        <h5 class="mb-0">Tambah Riwayat Karya Ilmiah</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('pegawai.riwayat-karya-ilmiah.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Judul</label>
                                    <input type="text" class="form-control" name="judul" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Nama Majalah/Jurnal</label>
                                    <input type="text" class="form-control" name="nama_majalah" required>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Bulan</label>
                                    <input type="text" class="form-control" name="bulan" required>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Volume</label>
                                    <input type="number" class="form-control" name="volume" min="0" required>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Nomor</label>
                                    <input type="number" class="form-control" name="nomor" min="0" required>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Tahun</label>
                                    <input type="number" class="form-control" name="tahun" min="1900" max="2099" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Link URL</label>
                                    <input type="text" class="form-control" name="link_url" placeholder="https://...">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">File</label>
                                    <input type="file" class="form-control" name="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
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
                        <h5 class="mb-0">Data Riwayat Karya Ilmiah</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th width="40">No</th>
                                        <th>Judul</th>
                                        <th>Majalah/Jurnal</th>
                                        <th>Vol/No</th>
                                        <th>Bulan</th>
                                        <th>Tahun</th>
                                        <th>Link</th>
                                        <th>File</th>
                                        <th width="220">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($riwayat as $i => $row)
                                        <tr>
                                            <td>{{ $i + 1 }}</td>
                                            <td>{{ $row->judul }}</td>
                                            <td>{{ $row->nama_majalah }}</td>
                                            <td>{{ $row->volume }}/{{ $row->nomor }}</td>
                                            <td>{{ $row->bulan }}</td>
                                            <td>{{ $row->tahun ?? '-' }}</td>
                                            <td>
                                                @if(!empty($row->link_url))
                                                    <a href="{{ $row->link_url }}" target="_blank">Buka Link</a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if(!empty($row->file_url))
                                                    <a href="{{ $row->file_url }}" target="_blank" class="btn btn-sm btn-info">Lihat File</a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal{{ $row->id }}">Edit</button>
                                                <form action="{{ route('pegawai.riwayat-karya-ilmiah.destroy', $row->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                                </form>
                                            </td>
                                        </tr>

                                        <div class="modal fade" id="editModal{{ $row->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <form action="{{ route('pegawai.riwayat-karya-ilmiah.update', $row->id) }}" method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Edit Riwayat Karya Ilmiah</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="row g-3">
                                                                <div class="col-md-6">
                                                                    <label class="form-label">Judul</label>
                                                                    <input type="text" class="form-control" name="judul" value="{{ $row->judul }}" required>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label class="form-label">Nama Majalah/Jurnal</label>
                                                                    <input type="text" class="form-control" name="nama_majalah" value="{{ $row->nama_majalah }}" required>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <label class="form-label">Bulan</label>
                                                                    <input type="text" class="form-control" name="bulan" value="{{ $row->bulan }}" required>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <label class="form-label">Volume</label>
                                                                    <input type="number" class="form-control" name="volume" min="0" value="{{ $row->volume }}" required>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <label class="form-label">Nomor</label>
                                                                    <input type="number" class="form-control" name="nomor" min="0" value="{{ $row->nomor }}" required>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <label class="form-label">Tahun</label>
                                                                    <input type="number" class="form-control" name="tahun" min="1900" max="2099" value="{{ $row->tahun ? date('Y', strtotime($row->tahun)) : '' }}" required>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label class="form-label">Link URL</label>
                                                                    <input type="text" class="form-control" name="link_url" value="{{ $row->link_url }}">
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label class="form-label">File</label>
                                                                    @if(!empty($row->file_url))
                                                                        <div class="mb-1">
                                                                            <a href="{{ $row->file_url }}" target="_blank" class="btn btn-sm btn-outline-info">Lihat File</a>
                                                                        </div>
                                                                    @endif
                                                                    <input type="file" class="form-control" name="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
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
                                            <td colspan="9" class="text-center">Belum ada data riwayat karya ilmiah.</td>
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
