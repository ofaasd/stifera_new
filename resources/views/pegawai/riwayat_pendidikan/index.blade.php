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
                        <h5 class="mb-0">Tambah Riwayat Pendidikan</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('pegawai.riwayat-pendidikan.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Jenjang</label>
                                    <select class="form-control" name="jenjang" required>
                                        <option value="">-- Pilih --</option>
                                        @foreach($jenjangList as $j)
                                            <option value="{{ $j }}">{{ $j }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Universitas</label>
                                    <select class="form-control" name="universitas" required>
                                        <option value="">-- Pilih --</option>
                                        @foreach($universitas as $u)
                                            <option value="{{ $u->id }}">{{ $u->nama_universitas }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label">Program Studi</label>
                                    <select class="form-control" name="jurusan" required>
                                        <option value="">-- Pilih --</option>
                                        @foreach($prodi as $p)
                                            <option value="{{ $p->id }}">{{ $p->nama_jurusan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Tempat</label>
                                    <input type="text" class="form-control" name="tempat">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">No. Ijazah</label>
                                    <input type="text" class="form-control" name="no_ijazah">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Tanggal Ijazah</label>
                                    <input type="date" class="form-control" name="tanggal_ijazah">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Tahun</label>
                                    <input type="number" class="form-control" name="tahun" min="1900" max="2099">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Jenjang Profesi</label>
                                    <input type="text" class="form-control" name="jenjang_profesi">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Upload Ijazah (PDF/JPG/PNG)</label>
                                    <input type="file" class="form-control" name="ijazah">
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
                        <h5 class="mb-0">Data Riwayat Pendidikan</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th width="40">No</th>
                                        <th>Jenjang</th>
                                        <th>Universitas</th>
                                        <th>Jurusan</th>
                                        <th>Tahun</th>
                                        <th>No Ijazah</th>
                                        <th>File</th>
                                        <th width="230">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($riwayat as $i => $row)
                                        <tr>
                                            <td>{{ $i + 1 }}</td>
                                            <td>{{ $row->jenjang }}</td>
                                            <td>{{ $row->universitas_nama ?? $row->universitas }}</td>
                                            <td>{{ $row->jurusan_nama ?? $row->jurusan }}</td>
                                            <td>{{ $row->tahun ? $row->tahun : '-' }}</td>
                                            <td>{{ $row->no_ijazah ?: '-' }}</td>
                                            <td>
                                                @if(!empty($row->ijazah))
                                                    <a href="{{ asset('assets/ijazah_pegawai/' . $row->ijazah) }}" target="_blank" class="btn btn-sm btn-info">Lihat</a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal{{ $row->id }}">
                                                    Edit
                                                </button>
                                                <form action="{{ route('pegawai.riwayat-pendidikan.destroy', $row->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                                </form>
                                            </td>
                                        </tr>

                                        <div class="modal fade" id="editModal{{ $row->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <form action="{{ route('pegawai.riwayat-pendidikan.update', $row->id) }}" method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Edit Riwayat Pendidikan</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="row g-3">
                                                                <div class="col-md-4">
                                                                    <label class="form-label">Jenjang</label>
                                                                    <select class="form-control" name="jenjang" required>
                                                                        @foreach($jenjangList as $j)
                                                                            <option value="{{ $j }}" {{ $row->jenjang == $j ? 'selected' : '' }}>{{ $j }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label class="form-label">Universitas</label>
                                                                    <select class="form-control" name="universitas" required>
                                                                        @foreach($universitas as $u)
                                                                            <option value="{{ $u->id }}" {{ (string) $row->universitas === (string) $u->id ? 'selected' : '' }}>{{ $u->nama_universitas }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label class="form-label">Program Studi</label>
                                                                    <select class="form-control" name="jurusan" required>
                                                                        @foreach($prodi as $p)
                                                                            <option value="{{ $p->id }}" {{ (string) $row->jurusan === (string) $p->id ? 'selected' : '' }}>{{ $p->nama_jurusan }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label class="form-label">Tempat</label>
                                                                    <input type="text" class="form-control" name="tempat" value="{{ $row->tempat }}">
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label class="form-label">No. Ijazah</label>
                                                                    <input type="text" class="form-control" name="no_ijazah" value="{{ $row->no_ijazah }}">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label class="form-label">Tanggal Ijazah</label>
                                                                    <input type="date" class="form-control" name="tanggal_ijazah" value="{{ $row->tanggal_ijazah ? date('Y-m-d', strtotime($row->tanggal_ijazah)) : '' }}">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label class="form-label">Tahun</label>
                                                                    <input type="number" class="form-control" name="tahun" min="1900" max="2099" value="{{ $row->tahun ? date('Y', strtotime($row->tahun)) : '' }}">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label class="form-label">Jenjang Profesi</label>
                                                                    <input type="text" class="form-control" name="jenjang_profesi" value="{{ $row->jenjang_profesi }}">
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <label class="form-label">Upload Ijazah Baru (opsional)</label>
                                                                    <input type="file" class="form-control" name="ijazah">
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
                                            <td colspan="8" class="text-center">Belum ada data riwayat pendidikan.</td>
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
