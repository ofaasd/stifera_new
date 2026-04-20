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
                        <h5 class="mb-0">Tambah Riwayat Jabatan Fungsional</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('pegawai.riwayat-jabatan-fungsional.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Jabatan Fungsional</label>
                                    <select class="form-control" name="jabatan_fungsional_sekarang" required>
                                        <option value="">-- Pilih --</option>
                                        @foreach($jabatanFungsionalList as $jabatan)
                                            <option value="{{ $jabatan->id }}">{{ $jabatan->jabatan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">No. SK Fungsional</label>
                                    <input type="text" class="form-control" name="no_sk_fungsional" required>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Tgl SK Fungsional</label>
                                    <input type="date" class="form-control" name="tgl_sk_fungsional" required>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">TMT SK Fungsional</label>
                                    <input type="date" class="form-control" name="tmt_sk_fungsional">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">KUM</label>
                                    <input type="text" class="form-control" name="kum">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Status</label>
                                    <select class="form-control" name="status" required>
                                        <option value="1">Aktif</option>
                                        <option value="0">Nonaktif</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Upload Dokumen (PDF/JPG/PNG)</label>
                                    <input type="file" class="form-control" name="dokumen">
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
                        <h5 class="mb-0">Data Riwayat Jabatan Fungsional</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th width="40">No</th>
                                        <th>Jabatan Fungsional</th>
                                        <th>No. SK</th>
                                        <th>Tgl SK</th>
                                        <th>TMT SK</th>
                                        <th>KUM</th>
                                        <th>Status</th>
                                        <th>Dokumen</th>
                                        <th width="230">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($riwayat as $i => $row)
                                        <tr>
                                            <td>{{ $i + 1 }}</td>
                                            <td>{{ $row->jabatanFungsional->jabatan ?? '' }}</td>
                                            <td>{{ $row->no_sk_fungsional }}</td>
                                            <td>{{ $row->tgl_sk_fungsional ? date('d-m-Y', strtotime($row->tgl_sk_fungsional)) : '-' }}</td>
                                            <td>{{ $row->tmt_sk_fungsional ? date('d-m-Y', strtotime($row->tmt_sk_fungsional)) : '-' }}</td>
                                            <td>{{ $row->kum ?: '-' }}</td>
                                            <td>{{ (int) $row->status === 1 ? 'Aktif' : 'Nonaktif' }}</td>
                                            <td>
                                                @if(!empty($row->dokumen))
                                                    <a href="{{ asset('assets/dokumen_jabatan_fungsional_pegawai/' . $row->dokumen) }}" target="_blank" class="btn btn-sm btn-info">Lihat</a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal{{ $row->id }}">
                                                    Edit
                                                </button>
                                                <form action="{{ route('pegawai.riwayat-jabatan-fungsional.destroy', $row->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                                </form>
                                            </td>
                                        </tr>

                                        <div class="modal fade" id="editModal{{ $row->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <form action="{{ route('pegawai.riwayat-jabatan-fungsional.update', $row->id) }}" method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Edit Riwayat Jabatan Fungsional</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="row g-3">
                                                                <div class="col-md-4">
                                                                    <label class="form-label">Jabatan Fungsional</label>
                                                                    <select class="form-control" name="jabatan_fungsional_sekarang" required>
                                                                        <option value="">-- Pilih --</option>
                                                                        @foreach($jabatanFungsionalList as $jabatan)
                                                                            <option value="{{ $jabatan->id }}" {{ (int) $row->jabatan_fungsional_sekarang === (int) $jabatan->id ? 'selected' : '' }}>{{ $jabatan->jabatan }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label class="form-label">No. SK Fungsional</label>
                                                                    <input type="text" class="form-control" name="no_sk_fungsional" value="{{ $row->no_sk_fungsional }}" required>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <label class="form-label">Tgl SK Fungsional</label>
                                                                    <input type="date" class="form-control" name="tgl_sk_fungsional" value="{{ $row->tgl_sk_fungsional ? date('Y-m-d', strtotime($row->tgl_sk_fungsional)) : '' }}" required>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <label class="form-label">TMT SK Fungsional</label>
                                                                    <input type="date" class="form-control" name="tmt_sk_fungsional" value="{{ $row->tmt_sk_fungsional ? date('Y-m-d', strtotime($row->tmt_sk_fungsional)) : '' }}">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label class="form-label">KUM</label>
                                                                    <input type="text" class="form-control" name="kum" value="{{ $row->kum }}">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label class="form-label">Status</label>
                                                                    <select class="form-control" name="status" required>
                                                                        <option value="1" {{ (int) $row->status === 1 ? 'selected' : '' }}>Aktif</option>
                                                                        <option value="0" {{ (int) $row->status === 0 ? 'selected' : '' }}>Nonaktif</option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label class="form-label">Upload Dokumen Baru (opsional)</label>
                                                                    <input type="file" class="form-control" name="dokumen">
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
                                            <td colspan="9" class="text-center">Belum ada data riwayat jabatan fungsional.</td>
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
