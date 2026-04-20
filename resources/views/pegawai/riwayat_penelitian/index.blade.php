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
                        <h5 class="mb-0">Tambah Riwayat Penelitian</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('pegawai.riwayat-penelitian.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-2">
                                    <label class="form-label">Nomor</label>
                                    <input type="text" class="form-control" name="nomor">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Judul</label>
                                    <input type="text" class="form-control" name="judul" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Fakultas</label>
                                    <select class="form-control" name="id_fakultas" required>
                                        <option value="">-- Pilih --</option>
                                        @foreach($fakultasList as $f)
                                            <option value="{{ $f->id }}">{{ $f->nama_fakultas }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Jenis Penelitian</label>
                                    <input type="text" class="form-control" name="jenis_penelitian">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Tahun</label>
                                    <input type="number" class="form-control" name="tahun" min="1900" max="2099" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Sumber Dana</label>
                                    <input type="text" class="form-control" name="sumber_dana">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Dana</label>
                                    <input type="number" class="form-control" name="dana" min="0">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">No Surat</label>
                                    <input type="text" class="form-control" name="no_surat">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Penyelenggara</label>
                                    <input type="text" class="form-control" name="penyelenggara">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Ketua</label>
                                    <input type="text" class="form-control" name="ketua">
                                </div>
                                <div class="col-md-9">
                                    <label class="form-label">Anggota</label>
                                    <input type="text" class="form-control" name="anggota">
                                </div>
                                                <div class="col-12 mt-2 mb-2 p-3 border rounded bg-light">
                                                    <h6 class="mb-3"><i class="fa-solid fa-users me-2"></i>Tim Penelitian</h6>
                                                    <div class="row g-3">
                                                        <div class="col-md-6">
                                                            <label class="form-label fw-bold">Ketua Penelitian</label>
                                                            <select class="form-control form-select select2" name="id_ketua">
                                                                <option value="">-- Pilih Ketua --</option>
                                                                @foreach($pegawaiList as $p)
                                                                    <option value="{{ $p->id }}">{{ $p->nama }} ({{ $p->npp }})</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label fw-bold">Anggota Pegawai</label>
                                                            <select class="form-control form-select select2" name="anggota_pegawai[]" multiple>
                                                                @foreach($pegawaiList as $p)
                                                                    <option value="{{ $p->id }}">{{ $p->nama }} ({{ $p->npp }})</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <label class="form-label fw-bold">Anggota Mahasiswa</label>
                                                            <select class="form-control form-select select2" name="anggota_mahasiswa[]" multiple>
                                                                @foreach($mahasiswaList as $m)
                                                                    <option value="{{ $m->id }}">{{ $m->nama }} ({{ $m->nim }})</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                    <label class="form-label">Dokumen</label>
                                    <input type="file" class="form-control" name="dokumen" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Proposal</label>
                                    <input type="file" class="form-control" name="proposal" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Laporan Kemajuan</label>
                                    <input type="file" class="form-control" name="lap_kemajuan" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Laporan Keuangan</label>
                                    <input type="file" class="form-control" name="lap_keuangan" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Laporan Akhir</label>
                                    <input type="file" class="form-control" name="lap_akhir" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
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
                        <h5 class="mb-0">Data Riwayat Penelitian</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th width="40">No</th>
                                        <th>Nomor</th>
                                        <th>Judul</th>
                                        <th>Fakultas</th>
                                        <th>Tahun</th>
                                        <th>Dana</th>
                                        <th>Ketua</th>
                                        <th>Anggota</th>
                                        <th>File</th>
                                        <th width="230">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($riwayat as $i => $row)
                                        <tr>
                                            <td>{{ $i + 1 }}</td>
                                            <td>{{ $row->nomor ?: '-' }}</td>
                                            <td>{{ $row->judul }}</td>
                                            <td>{{ $row->nama_fakultas ?: '-' }}</td>
                                            <td>{{ $row->tahun ? date('Y', strtotime($row->tahun)) : '-' }}</td>
                                            <td>{{ isset($row->dana) ? number_format((int) $row->dana, 0, ',', '.') : '-' }}</td>
                                            <td>{{ $row->ketua?->nama ?: '-' }}</td>
                                            <td>
                                                @forelse($row->anggota as $anggota)
                                                    <div>
                                                        @if($anggota->jenis_anggota == 1)
                                                            <span class="badge bg-primary me-1">Pegawai</span>{{ $anggota->pegawai?->nama ?? '-' }}
                                                        @else
                                                            <span class="badge bg-success me-1">Mahasiswa</span>{{ $anggota->mahasiswa?->nama ?? '-' }}
                                                        @endif
                                                    </div>
                                                @empty
                                                    -
                                                @endforelse
                                            </td>
                                            <td>
                                                <div class="mt-2 d-flex flex-wrap gap-1">
                                                    @if(!empty($row->dokumen_url))
                                                        <a href="{{ $row->dokumen_url }}" class="btn btn-sm btn-outline-primary" target="_blank" download>Dokumen</a>
                                                    @endif
                                                    @if(!empty($row->proposal_url))
                                                        <a href="{{ $row->proposal_url }}" class="btn btn-sm btn-outline-primary" target="_blank" download>Proposal</a>
                                                    @endif
                                                    @if(!empty($row->lap_kemajuan_url))
                                                        <a href="{{ $row->lap_kemajuan_url }}" class="btn btn-sm btn-outline-primary" target="_blank" download>Kemajuan</a>
                                                    @endif
                                                    @if(!empty($row->lap_keuangan_url))
                                                        <a href="{{ $row->lap_keuangan_url }}" class="btn btn-sm btn-outline-primary" target="_blank" download>Keuangan</a>
                                                    @endif
                                                    @if(!empty($row->lap_akhir_url))
                                                        <a href="{{ $row->lap_akhir_url }}" class="btn btn-sm btn-outline-primary" target="_blank" download>Akhir</a>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal{{ $row->id }}">Edit</button>
                                                <form action="{{ route('pegawai.riwayat-penelitian.destroy', $row->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                                </form>
                                                
                                            </td>
                                        </tr>

                                        <div class="modal fade" id="editModal{{ $row->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <form action="{{ route('pegawai.riwayat-penelitian.update', $row->id) }}" method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Edit Riwayat Penelitian</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="row g-3">
                                                                <div class="col-md-2"><label class="form-label">Nomor</label><input type="text" class="form-control" name="nomor" value="{{ $row->nomor }}"></div>
                                                                <div class="col-md-6"><label class="form-label">Judul</label><input type="text" class="form-control" name="judul" value="{{ $row->judul }}" required></div>
                                                                <div class="col-md-4">
                                                                    <label class="form-label">Fakultas</label>
                                                                    <select class="form-control" name="id_fakultas" required>
                                                                        <option value="">-- Pilih --</option>
                                                                        @foreach($fakultasList as $f)
                                                                            <option value="{{ $f->id }}" {{ (int) $row->id_fakultas === (int) $f->id ? 'selected' : '' }}>{{ $f->nama_fakultas }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-3"><label class="form-label">Jenis Penelitian</label><input type="text" class="form-control" name="jenis_penelitian" value="{{ $row->jenis_penelitian }}"></div>
                                                                <div class="col-md-2"><label class="form-label">Tahun</label><input type="number" class="form-control" name="tahun" min="1900" max="2099" value="{{ $row->tahun ? date('Y', strtotime($row->tahun)) : '' }}" required></div>
                                                                <div class="col-md-3"><label class="form-label">Sumber Dana</label><input type="text" class="form-control" name="sumber_dana" value="{{ $row->sumber_dana }}"></div>
                                                                <div class="col-md-2"><label class="form-label">Dana</label><input type="number" class="form-control" name="dana" min="0" value="{{ $row->dana }}"></div>
                                                                <div class="col-md-2"><label class="form-label">No Surat</label><input type="text" class="form-control" name="no_surat" value="{{ $row->no_surat }}"></div>
                                                                <div class="col-md-4"><label class="form-label">Penyelenggara</label><input type="text" class="form-control" name="penyelenggara" value="{{ $row->penyelenggara }}"></div>
                                                                
                                                                    <div class="col-12 mb-2 p-2 border rounded bg-light">
                                                                        <h6 class="mb-2"><i class="fa-solid fa-users me-2"></i>Tim Penelitian</h6>
                                                                        <div class="row g-2">
                                                                            <div class="col-md-6">
                                                                                <label class="form-label fw-bold">Ketua Penelitian</label>
                                                                                <select class="form-control form-select" name="id_ketua">
                                                                                    <option value="">-- Pilih Ketua --</option>
                                                                                    @foreach($pegawaiList as $p)
                                                                                        <option value="{{ $p->id }}" {{ (int)$row->id_ketua === (int)$p->id ? 'selected' : '' }}>{{ $p->nama }} ({{ $p->npp }})</option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <label class="form-label fw-bold">Anggota Pegawai</label>
                                                                                <select class="form-control form-select" name="anggota_pegawai[]" multiple>
                                                                                    @foreach($pegawaiList as $p)
                                                                                        <option value="{{ $p->id }}" 
                                                                                            @if($row->anggota->where('jenis_anggota', 1)->pluck('id_anggota')->contains((int)$p->id))
                                                                                                selected
                                                                                            @endif
                                                                                        >{{ $p->nama }} ({{ $p->npp }})</option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                            <div class="col-md-12">
                                                                                <label class="form-label fw-bold">Anggota Mahasiswa</label>
                                                                                <select class="form-control form-select" name="anggota_mahasiswa[]" multiple>
                                                                                    @foreach($mahasiswaList as $m)
                                                                                        <option value="{{ $m->id }}"
                                                                                            @if($row->anggota->where('jenis_anggota', 2)->pluck('id_anggota')->contains((int)$m->id))
                                                                                                selected
                                                                                            @endif
                                                                                        >{{ $m->nama }} ({{ $m->nim }})</option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                    <label class="form-label">Dokumen</label>
                                                                    <input type="file" class="form-control" name="dokumen" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                    @if(!empty($row->dokumen))
                                                                        <small class="text-muted">File saat ini: {{ $row->dokumen }}</small>
                                                                    @endif
                                                                    @if(!empty($row->dokumen_url))
                                                                        <div class="mt-1">
                                                                            <a href="{{ $row->dokumen_url }}" class="btn btn-sm btn-outline-primary" target="_blank" download>Download Dokumen</a>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <label class="form-label">Proposal</label>
                                                                    <input type="file" class="form-control" name="proposal" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                    @if(!empty($row->proposal))
                                                                        <small class="text-muted">File saat ini: {{ $row->proposal }}</small>
                                                                    @endif
                                                                    @if(!empty($row->proposal_url))
                                                                        <div class="mt-1">
                                                                            <a href="{{ $row->proposal_url }}" class="btn btn-sm btn-outline-primary" target="_blank" download>Download Proposal</a>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <label class="form-label">Laporan Kemajuan</label>
                                                                    <input type="file" class="form-control" name="lap_kemajuan" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                    @if(!empty($row->lap_kemajuan))
                                                                        <small class="text-muted">File saat ini: {{ $row->lap_kemajuan }}</small>
                                                                    @endif
                                                                    @if(!empty($row->lap_kemajuan_url))
                                                                        <div class="mt-1">
                                                                            <a href="{{ $row->lap_kemajuan_url }}" class="btn btn-sm btn-outline-primary" target="_blank" download>Download Kemajuan</a>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <label class="form-label">Laporan Keuangan</label>
                                                                    <input type="file" class="form-control" name="lap_keuangan" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                    @if(!empty($row->lap_keuangan))
                                                                        <small class="text-muted">File saat ini: {{ $row->lap_keuangan }}</small>
                                                                    @endif
                                                                    @if(!empty($row->lap_keuangan_url))
                                                                        <div class="mt-1">
                                                                            <a href="{{ $row->lap_keuangan_url }}" class="btn btn-sm btn-outline-primary" target="_blank" download>Download Keuangan</a>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <label class="form-label">Laporan Akhir</label>
                                                                    <input type="file" class="form-control" name="lap_akhir" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                    @if(!empty($row->lap_akhir))
                                                                        <small class="text-muted">File saat ini: {{ $row->lap_akhir }}</small>
                                                                    @endif
                                                                    @if(!empty($row->lap_akhir_url))
                                                                        <div class="mt-1">
                                                                            <a href="{{ $row->lap_akhir_url }}" class="btn btn-sm btn-outline-primary" target="_blank" download>Download Laporan Akhir</a>
                                                                        </div>
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
                                            <td colspan="8" class="text-center">Belum ada data riwayat penelitian.</td>
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
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof window.jQuery === 'undefined' || typeof jQuery.fn.select2 === 'undefined') {
            return;
        }

        var $ = window.jQuery;

        // Init select2 untuk elemen di luar modal.
        $('.select2').filter(function() {
            return $(this).closest('.modal').length === 0;
        }).select2({
            placeholder: 'Ketik untuk mencari...',
            allowClear: true,
            width: '100%'
        });

        // Init ulang select2 saat modal dibuka agar dropdown muncul normal.
        $(document).on('shown.bs.modal', '.modal', function() {
            var $modal = $(this);

            $modal.find('.select2').each(function() {
                var $select = $(this);

                if ($select.hasClass('select2-hidden-accessible')) {
                    $select.select2('destroy');
                }

                $select.select2({
                    placeholder: 'Ketik untuk mencari...',
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $modal
                });
            });
        });
    });
    </script>
@endsection
