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
                        <h5 class="mb-0">Tambah Riwayat Mengajar</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('pegawai.riwayat-mengajar.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-2">
                                    <label class="form-label">Tahun</label>
                                    <input type="number" class="form-control" name="tahun" min="1900" max="2099" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Institusi</label>
                                    <input type="text" class="form-control" name="institusi" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Program Studi</label>
                                    <input type="text" class="form-control" name="prodi" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Mata Kuliah</label>
                                    <input type="text" class="form-control" name="mata_kuliah" required>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Rombel</label>
                                    <input type="text" class="form-control" name="rombel">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Kelas</label>
                                    <input type="number" class="form-control" name="kelas" min="0">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">SKS</label>
                                    <input type="number" class="form-control" name="sks" min="0">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Dokumen</label>
                                    <input type="file" class="form-control" name="dokumen" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">SK Mengajar</label>
                                    <input type="file" class="form-control" name="sk_mengajar" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
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
                        <h5 class="mb-0">Data Riwayat Mengajar (CRUD)</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th width="40">No</th>
                                        <th>Tahun</th>
                                        <th>Institusi</th>
                                        <th>Program Studi</th>
                                        <th>Mata Kuliah</th>
                                        <th>Rombel</th>
                                        <th>Kelas</th>
                                        <th>SKS</th>
                                        <th>Dokumen</th>
                                        <th>SK Mengajar</th>
                                        <th width="230">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($riwayat as $i => $row)
                                        <tr>
                                            <td>{{ $i + 1 }}</td>
                                            <td>{{ $row->tahun_awal ?: '-' }}</td>
                                            <td>{{ $row->institusi ?: '-' }}</td>
                                            <td>{{ $row->prodi ?: '-' }}</td>
                                            <td>{{ $row->mata_kuliah ?: '-' }}</td>
                                            <td>{{ $row->rombel ?? '-' }}</td>
                                            <td>{{ (int)($row->kelas ?? 0) === 1 ? 'Reguler' : ((int)($row->kelas ?? 0) === 2 ? 'Karyawan' : ($row->kelas ?? '-')) }}</td>
                                            <td>{{ $row->sks ?: '-' }}</td>
                                            <td>
                                                @if(!empty($row->dokumen_url))
                                                    <a href="{{ $row->dokumen_url }}" class="btn btn-sm btn-outline-primary" target="_blank" download>Download</a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if(!empty($row->sk_mengajar_url))
                                                    <a href="{{ $row->sk_mengajar_url }}" class="btn btn-sm btn-outline-primary" target="_blank" download>Download</a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal{{ $row->id }}">
                                                    Edit
                                                </button>
                                                <form action="{{ route('pegawai.riwayat-mengajar.destroy', $row->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                                </form>
                                            </td>
                                        </tr>

                                        <div class="modal fade" id="editModal{{ $row->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <form action="{{ route('pegawai.riwayat-mengajar.update', $row->id) }}" method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Edit Riwayat Mengajar</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="row g-3">
                                                                <div class="col-md-2">
                                                                    <label class="form-label">Tahun</label>
                                                                    <input type="number" class="form-control" name="tahun" min="1900" max="2099" value="{{ $row->tahun }}" required>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label class="form-label">Institusi</label>
                                                                    <input type="text" class="form-control" name="institusi" value="{{ $row->institusi }}" required>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <label class="form-label">Program Studi</label>
                                                                    <input type="text" class="form-control" name="prodi" value="{{ $row->prodi }}" required>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <label class="form-label">Mata Kuliah</label>
                                                                    <input type="text" class="form-control" name="mata_kuliah" value="{{ $row->mata_kuliah }}" required>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <label class="form-label">Rombel</label>
                                                                    <input type="text" class="form-control" name="rombel" value="{{ $row->rombel }}">
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <label class="form-label">Kelas</label>
                                                                    <input type="number" class="form-control" name="kelas" min="0" value="{{ $row->kelas }}">
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <label class="form-label">SKS</label>
                                                                    <input type="number" class="form-control" name="sks" min="0" value="{{ $row->sks }}">
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <label class="form-label">Dokumen</label>
                                                                    <input type="file" class="form-control" name="dokumen" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                    @if(!empty($row->dokumen))
                                                                        <small class="text-muted">File saat ini: {{ $row->dokumen }}</small>
                                                                    @endif
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <label class="form-label">SK Mengajar</label>
                                                                    <input type="file" class="form-control" name="sk_mengajar" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                    @if(!empty($row->sk_mengajar))
                                                                        <small class="text-muted">File saat ini: {{ $row->sk_mengajar }}</small>
                                                                    @endif
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
                                            <td colspan="11" class="text-center">Belum ada data riwayat mengajar.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card mt-4" style="height:auto !important;">
                    <div class="card-header">
                        <h5 class="mb-0">Data Jadwal Mengajar</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th width="40">No</th>
                                        <th>Nama Institusi</th>
                                        <th>Program Studi</th>
                                        <th>Mata Kuliah</th>
                                        <th>Tahun</th>
                                        <th>Rombel</th>
                                        <th>Kelas</th>
                                        <th>SKS</th>
                                        <th>Dokumen Unggah</th>
                                        <th>SK Mengajar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($riwayatKrs as $i => $row)
                                        @php
                                            $tahunLabel = '-';
                                            if (!empty($row->tahun_awal) && !empty($row->tahun_akhir)) {
                                                $semester = (int)($row->tahun_jenis ?? 0) === 1 ? 'Ganjil' : 'Genap';
                                                $tahunLabel = $row->tahun_awal . '-' . $row->tahun_akhir . ' (' . $semester . ')';
                                            } elseif (!empty($row->id_tahun)) {
                                                $tahunLabel = $row->id_tahun;
                                            }
                                            $mataKuliahLabel = !empty($row->kode_mata_kuliah)
                                                ? $row->kode_mata_kuliah . (!empty($row->nama_mata_kuliah) ? '-' . $row->nama_mata_kuliah : '')
                                                : ($row->nama_mata_kuliah ?? '-');
                                        @endphp
                                        <tr>
                                            <td>{{ $i + 1 }}</td>
                                            <td>STIFERA</td>
                                            <td>{{ $row->nama_prodi ?? '-' }}</td>
                                            <td>{{ $mataKuliahLabel }}</td>
                                            <td>{{ $tahunLabel }}</td>
                                            <td>{{ $row->nama_rombel ?? $row->rombel ?? '-' }}</td>
                                            <td>{{ (int)($row->kelas ?? 0) === 1 ? 'Reguler' : ((int)($row->kelas ?? 0) === 2 ? 'Karyawan' : ($row->kelas ?? '-')) }}</td>
                                            <td>{{ $row->jumlah_sks ?? '-' }}</td>
                                            <td>{{ $row->rps ?? '-' }}</td>
                                            <td>{{ $row->kp ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center">Belum ada data jadwal mengajar untuk dosen ini.</td>
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
