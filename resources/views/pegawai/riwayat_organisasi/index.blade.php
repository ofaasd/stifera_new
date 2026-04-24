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
                        <h5 class="mb-0">Tambah Riwayat Organisasi</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('pegawai.riwayat-organisasi.store') }}" method="POST">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Nama Organisasi</label>
                                    <input type="text" class="form-control" name="nama_organisasi" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Jabatan</label>
                                    <input type="text" class="form-control" name="jabatan" required>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Tahun Masuk</label>
                                    <input type="number" class="form-control" name="tahun" min="1900" max="2099" required>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Tahun Keluar</label>
                                    <input type="number" class="form-control" name="tahun_keluar" min="1900" max="2099">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Sekarang</label>
                                    <select class="form-control" name="sekarang" required>
                                        <option value="1">Ya</option>
                                        <option value="0">Tidak</option>
                                    </select>
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
                        <h5 class="mb-0">Data Riwayat Organisasi</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th width="40">No</th>
                                        <th>Nama Organisasi</th>
                                        <th>Jabatan</th>
                                        <th>Tahun Masuk</th>
                                        <th>Tahun Keluar</th>
                                        <th>Sekarang</th>
                                        <th width="230">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($riwayat as $i => $row)
                                        <tr>
                                            <td>{{ $i + 1 }}</td>
                                            <td>{{ $row->nama_organisasi }}</td>
                                            <td>{{ $row->jabatan }}</td>
                                            <td>{{ $row->tahun ?? '-' }}</td>
                                            <td>{{ $row->tahun_keluar ?? '-' }}</td>
                                            <td>{{ (int) $row->sekarang === 1 ? 'Ya' : 'Tidak' }}</td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal{{ $row->id }}">
                                                    Edit
                                                </button>
                                                <form action="{{ route('pegawai.riwayat-organisasi.destroy', $row->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                                </form>
                                            </td>
                                        </tr>

                                        <div class="modal fade" id="editModal{{ $row->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <form action="{{ route('pegawai.riwayat-organisasi.update', $row->id) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Edit Riwayat Organisasi</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="row g-3">
                                                                <div class="col-md-4">
                                                                    <label class="form-label">Nama Organisasi</label>
                                                                    <input type="text" class="form-control" name="nama_organisasi" value="{{ $row->nama_organisasi }}" required>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label class="form-label">Jabatan</label>
                                                                    <input type="text" class="form-control" name="jabatan" value="{{ $row->jabatan }}" required>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <label class="form-label">Tahun Masuk</label>
                                                                    <input type="number" class="form-control" name="tahun" min="1900" max="2099" value="{{ $row->tahun ?? '' }}" required>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <label class="form-label">Tahun Keluar</label>
                                                                    <input type="number" class="form-control" name="tahun_keluar" min="1900" max="2099" value="{{ $row->tahun_keluar ?? '' }}">
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <label class="form-label">Sekarang</label>
                                                                    <select class="form-control" name="sekarang" required>
                                                                        <option value="1" {{ (int) $row->sekarang === 1 ? 'selected' : '' }}>Ya</option>
                                                                        <option value="0" {{ (int) $row->sekarang === 0 ? 'selected' : '' }}>Tidak</option>
                                                                    </select>
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
                                            <td colspan="7" class="text-center">Belum ada data riwayat organisasi.</td>
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
