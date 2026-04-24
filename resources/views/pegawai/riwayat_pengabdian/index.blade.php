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
                        <h5 class="mb-0">Tambah Riwayat Pengabdian</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('pegawai.riwayat-pengabdian.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-5">
                                    <label class="form-label">Nama Kegiatan</label>
                                    <input type="text" class="form-control" name="nama_kegiatan" required>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Tahun</label>
                                    <input type="number" class="form-control" name="tahun" min="1900" max="2099" required>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label">Tempat</label>
                                    <input type="text" class="form-control" name="tempat">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Link URL</label>
                                    <input type="text" class="form-control" name="link_url" placeholder="https://...">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Bukti</label>
                                    <input type="file" class="form-control" name="bukti" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Proposal</label>
                                    <input type="file" class="form-control" name="proposal" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Ketua</label>
                                    <select class="form-control" name="ketua" required>
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
                        <h5 class="mb-0">Data Riwayat Pengabdian</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th width="40">No</th>
                                        <th>Nama Kegiatan</th>
                                        <th>Tahun</th>
                                        <th>Tempat</th>
                                        <th>Link</th>
                                        <th>Bukti</th>
                                        <th>Proposal</th>
                                        <th>Ketua</th>
                                        <th width="220">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($riwayat as $i => $row)
                                        <tr>
                                            <td>{{ $i + 1 }}</td>
                                            <td>{{ $row->nama_kegiatan }}</td>
                                            <td>{{ $row->tahun ?? '-' }}</td>
                                            <td>{{ $row->tempat ?: '-' }}</td>
                                            <td>
                                                @if(!empty($row->link_url))
                                                    <a href="{{ $row->link_url }}" target="_blank">Buka Link</a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if(!empty($row->bukti_url))
                                                    <a href="{{ $row->bukti_url }}" target="_blank" class="btn btn-sm btn-info">Lihat Bukti</a>
                                                @elseif(!empty($row->bukti))
                                                    {{ $row->bukti }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if(!empty($row->proposal_url))
                                                    <a href="{{ $row->proposal_url }}" target="_blank" class="btn btn-sm btn-info">Lihat Proposal</a>
                                                @elseif(!empty($row->proposal))
                                                    {{ $row->proposal }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>{{ (int) $row->ketua === 1 ? 'Ya' : 'Tidak' }}</td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal{{ $row->id }}">Edit</button>
                                                <form action="{{ route('pegawai.riwayat-pengabdian.destroy', $row->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                                </form>
                                            </td>
                                        </tr>

                                        <div class="modal fade" id="editModal{{ $row->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <form action="{{ route('pegawai.riwayat-pengabdian.update', $row->id) }}" method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Edit Riwayat Pengabdian</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="row g-3">
                                                                <div class="col-md-5">
                                                                    <label class="form-label">Nama Kegiatan</label>
                                                                    <input type="text" class="form-control" name="nama_kegiatan" value="{{ $row->nama_kegiatan }}" required>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <label class="form-label">Tahun</label>
                                                                    <input type="number" class="form-control" name="tahun" min="1900" max="2099" value="{{ $row->tahun ? date('Y', strtotime($row->tahun)) : '' }}" required>
                                                                </div>
                                                                <div class="col-md-5">
                                                                    <label class="form-label">Tempat</label>
                                                                    <input type="text" class="form-control" name="tempat" value="{{ $row->tempat }}">
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label class="form-label">Link URL</label>
                                                                    <input type="text" class="form-control" name="link_url" value="{{ $row->link_url }}">
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <label class="form-label">Bukti</label>
                                                                    @if(!empty($row->bukti_url))
                                                                        <div class="mb-1">
                                                                            <a href="{{ $row->bukti_url }}" target="_blank" class="btn btn-sm btn-outline-info">Lihat Bukti</a>
                                                                        </div>
                                                                    @endif
                                                                    <input type="file" class="form-control" name="bukti" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <label class="form-label">Proposal</label>
                                                                    @if(!empty($row->proposal_url))
                                                                        <div class="mb-1">
                                                                            <a href="{{ $row->proposal_url }}" target="_blank" class="btn btn-sm btn-outline-info">Lihat Proposal</a>
                                                                        </div>
                                                                    @endif
                                                                    <input type="file" class="form-control" name="proposal" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <label class="form-label">Ketua</label>
                                                                    <select class="form-control" name="ketua" required>
                                                                        <option value="1" {{ (int) $row->ketua === 1 ? 'selected' : '' }}>Ya</option>
                                                                        <option value="0" {{ (int) $row->ketua === 0 ? 'selected' : '' }}>Tidak</option>
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
                                            <td colspan="9" class="text-center">Belum ada data riwayat pengabdian.</td>
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
