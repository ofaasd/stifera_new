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
                        <h5 class="mb-0">Tambah Riwayat BKD</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('pegawai.riwayat-bkd.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Periode BKD</label>
                                    <input type="text" class="form-control" name="periode_bkd" placeholder="Contoh: 2025/2026 Ganjil">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Status</label>
                                    <input type="text" class="form-control" name="status" placeholder="Status">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Jabatan Fungsional</label>
                                    <input type="text" class="form-control" name="jabatan_fungsional">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Assesor 1</label>
                                    <input type="text" class="form-control" name="assesor1">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Validasi 1</label>
                                    <select class="form-control" name="status_validasi1">
                                        <option value="">-- Pilih --</option>
                                        <option value="1">Valid</option>
                                        <option value="0">Belum Valid</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Assesor 2</label>
                                    <input type="text" class="form-control" name="assesor2">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Validasi 2</label>
                                    <select class="form-control" name="status_validasi2">
                                        <option value="">-- Pilih --</option>
                                        <option value="1">Valid</option>
                                        <option value="0">Belum Valid</option>
                                    </select>
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label">Lampiran</label>
                                    <input type="file" class="form-control" name="lampiran" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
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
                        <h5 class="mb-0">Data Riwayat BKD</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th width="40">No</th>
                                        <th>Periode BKD</th>
                                        <th>Status</th>
                                        <th>Jabatan Fungsional</th>
                                        <th>Assesor 1</th>
                                        <th>Status Validasi 1</th>
                                        <th>Assesor 2</th>
                                        <th>Status Validasi 2</th>
                                        <th>Lampiran</th>
                                        <th>Dibuat</th>
                                        <th width="220">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($riwayat as $i => $row)
                                        <tr>
                                            <td>{{ $i + 1 }}</td>
                                            <td>{{ $row->periode_bkd ?: '-' }}</td>
                                            <td>{{ $row->status ?: '-' }}</td>
                                            <td>{{ $row->jabatan_fungsional ?: '-' }}</td>
                                            <td>{{ $row->assesor1 ?: '-' }}</td>
                                            <td>{{ $row->status_validasi1 !== null ? "Valid" : 'Belum Valid' }}</td>
                                            <td>{{ $row->assesor2 ?: '-' }}</td>
                                            <td>{{ $row->status_validasi2 !== null ? "Valid" : 'Belum Valid' }}</td>
                                            <td>
                                                @if(!empty($row->lampiran_url))
                                                    <a href="{{ $row->lampiran_url }}" target="_blank" class="btn btn-sm btn-info">Lihat Lampiran</a>
                                                @elseif(!empty($row->lampiran))
                                                    {{ $row->lampiran }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>{{ $row->created_at ? date('d-m-Y H:i', strtotime($row->created_at)) : '-' }}</td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal{{ $row->id }}">Edit</button>
                                                <form action="{{ route('pegawai.riwayat-bkd.destroy', $row->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                                </form>
                                            </td>
                                        </tr>

                                        <div class="modal fade" id="editModal{{ $row->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <form action="{{ route('pegawai.riwayat-bkd.update', $row->id) }}" method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Edit Riwayat BKD</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="row g-3">
                                                                <div class="col-md-3"><label class="form-label">Periode BKD</label><input type="text" class="form-control" name="periode_bkd" value="{{ $row->periode_bkd }}"></div>
                                                                <div class="col-md-2"><label class="form-label">Status</label><input type="text" class="form-control" name="status" value="{{ $row->status }}"></div>
                                                                <div class="col-md-3"><label class="form-label">Jabatan Fungsional</label><input type="text" class="form-control" name="jabatan_fungsional" value="{{ $row->jabatan_fungsional }}"></div>
                                                                <div class="col-md-2"><label class="form-label">Assesor 1</label><input type="text" class="form-control" name="assesor1" value="{{ $row->assesor1 }}"></div>
                                                                <div class="col-md-2">
                                                                    <label class="form-label">Validasi 1</label>
                                                                    <select class="form-control" name="status_validasi1">
                                                                        <option value="" {{ $row->status_validasi1 === null ? 'selected' : '' }}>-- Pilih --</option>
                                                                        <option value="1" {{ (int) $row->status_validasi1 === 1 ? 'selected' : '' }}>Valid</option>
                                                                        <option value="0" {{ (int) $row->status_validasi1 === 0 ? 'selected' : '' }}>Belum Valid</option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-2"><label class="form-label">Assesor 2</label><input type="text" class="form-control" name="assesor2" value="{{ $row->assesor2 }}"></div>
                                                                <div class="col-md-2">
                                                                    <label class="form-label">Validasi 2</label>
                                                                    <select class="form-control" name="status_validasi2">
                                                                        <option value="" {{ $row->status_validasi2 === null ? 'selected' : '' }}>-- Pilih --</option>
                                                                        <option value="1" {{ (int) $row->status_validasi2 === 1 ? 'selected' : '' }}>Valid</option>
                                                                        <option value="0" {{ (int) $row->status_validasi2 === 0 ? 'selected' : '' }}>Belum Valid</option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-10">
                                                                    <label class="form-label">Lampiran</label>
                                                                    @if(!empty($row->lampiran_url))
                                                                        <div class="mb-1"><a href="{{ $row->lampiran_url }}" target="_blank" class="btn btn-sm btn-outline-info"><i class="fa-solid fa-file me-1"></i>Lihat File Saat Ini</a></div>
                                                                    @endif
                                                                    <input type="file" class="form-control" name="lampiran" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                    <small class="text-muted">Kosongkan jika tidak ingin mengubah lampiran.</small>
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
                                            <td colspan="11" class="text-center">Belum ada data riwayat BKD.</td>
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
