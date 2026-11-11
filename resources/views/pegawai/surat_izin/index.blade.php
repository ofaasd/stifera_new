@extends('layouts.default', ['CurrentPage' => $CurrentPage])

@section('content')
    <style>
    .table td { white-space: normal !important; word-break: break-word !important; }
    .table th { white-space: normal !important; word-break: break-word !important; }
    .table th:first-child { width: 50px; }
    .table th:last-child { width: 90px; }
</style>
<div class="content-body">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <div class="mb-4 pb-3">
							<a href="{{ url('pegawai/SuratIzin/create2') }}" class="btn btn-primary bg-primary">
								<i class="fa-solid fa-plus me-1"></i> Tambah / Ajukan Surat Izin
							</a>
						</div>

                    <div class="card mb-3 h-auto" style="height: auto;">
                        <div class="card-header border-bottom">
                            <h4 class="card-title">
                                <i class="fa-solid fa-filter me-1"></i>Filter Tanggal
                            </h4>
                        </div>
                        <div class="card-body p-3">
                            <form method="GET" action="{{ url('pegawai/SuratIzin/index2') }}" class="row g-2 align-items-center">
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-text">Tanggal Awal</span>
                                        <input type="date" class="form-control" name="tanggal_awal" value="{{ $tanggal_awal }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-text">Tanggal Akhir</span>
                                        <input type="date" class="form-control" name="tanggal_akhir" value="{{ $tanggal_akhir }}" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                                </div>
                                <div class="col-md-2">
                                    <a href="{{ url('pegawai/SuratIzin/index2') }}" class="btn btn-light w-100">Reset</a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header border-bottom">
                            <h4 class="card-title">
                                <i class="fa-solid fa-file-lines me-1"></i>{{ $title }}
                            </h4>
                        </div>
                        <div class="card-body">
                            @if(session('status'))
                                <div class="alert alert-success alert-dismissible fade show mx-3 mt-3">
                                    {{ session('status') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif
                            @if(session('error'))
                                <div class="alert alert-danger alert-dismissible fade show mx-3 mt-3">
                                    {{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            <div class="table-responsive">
                                <table id="order-table" class="table table-bordered table-striped">
                                    <thead class="bg-primary text-white text-center">
                                        <tr>
                                            <th>No</th>
                                            @if($isPimpinan ?? false)
                                                <th>Pegawai</th>
                                            @endif
                                            <th>Tanggal Surat</th>
                                            <th>Perihal</th>
                                            <th>Keterangan</th>
                                            <th>Izin Puket SDM</th>
                                            <th>Izin Ketua STIFERA</th>
                                            <th>Kategori</th>
                                            <th>File Surat</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($suratList as $row)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                @if($isPimpinan ?? false)
                                                    <td>{{ $row->nama_pengirim ?? '-' }}</td>
                                                @endif
                                                <td>{{ $row->tgl_surat ? \Carbon\Carbon::parse($row->tgl_surat)->format('Y-m-d') : '-' }}</td>
                                                <td>{{ $row->perihal }}</td>
                                                <td>{{ $row->keterangan }}</td>
                                                <td>{{ (int) $row->izin_mgr_sdm === 1 ? 'Disetujui' : 'Belum Disetujui' }}</td>
                                                <td>{{ (int) $row->izin_ka_jenjang === 1 ? 'Disetujui' : 'Belum Disetujui' }}</td>
                                                <td>{{ $row->kategori_nama ?? '-' }}</td>
                                                <td>
                                                    @if(!empty($row->file_surat))
                                                        <div class="d-flex flex-column gap-1">
                                                            <a href="{{ asset('assets/surat_izin/' . $row->file_surat) }}" target="_blank" class="btn btn-info btn-xs py-1 px-2" title="Lihat File Surat">
                                                                <i class="fa fa-eye"></i> Lihat
                                                            </a>
                                                            <button type="button" class="btn btn-outline-primary btn-xs py-1 px-2 text-nowrap" data-bs-toggle="modal" data-bs-target="#modalUploadSurat{{ $row->id }}" title="Ganti File Surat">
                                                                <i class="fa fa-upload"></i> Ganti
                                                            </button>
                                                        </div>
                                                    @else
                                                        <button type="button" class="btn btn-primary btn-xs py-1 px-2 text-nowrap" data-bs-toggle="modal" data-bs-target="#modalUploadSurat{{ $row->id }}" title="Upload File Surat">
                                                            <i class="fa fa-upload me-1"></i> Upload Surat
                                                        </button>
                                                    @endif

                                                    <!-- Modal Upload File Surat -->
                                                    <div class="modal fade" id="modalUploadSurat{{ $row->id }}" tabindex="-1" aria-labelledby="modalUploadSuratLabel{{ $row->id }}" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content">
                                                                <form action="{{ route('pegawai.surat-izin.upload-file', $row->id) }}" method="POST" enctype="multipart/form-data">
                                                                    @csrf
                                                                    <div class="modal-header bg-primary text-white">
                                                                        <h5 class="modal-title text-white" id="modalUploadSuratLabel{{ $row->id }}">
                                                                            <i class="fa fa-upload me-1"></i> Upload File Surat Izin
                                                                        </h5>
                                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body text-start">
                                                                        <div class="mb-3">
                                                                            <label class="form-label fw-bold">Perihal:</label>
                                                                            <p class="text-muted mb-0">{{ $row->perihal }}</p>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label class="form-label fw-bold">Tanggal Surat:</label>
                                                                            <p class="text-muted mb-0">{{ $row->tgl_surat ? \Carbon\Carbon::parse($row->tgl_surat)->format('d-m-Y') : '-' }}</p>
                                                                        </div>
                                                                        @if(!empty($row->file_surat))
                                                                            <div class="alert alert-info py-2 px-3 mb-3">
                                                                                <small><i class="fa fa-info-circle me-1"></i> File saat ini: <strong>{{ $row->file_surat }}</strong></small>
                                                                            </div>
                                                                        @endif
                                                                        <div class="mb-3">
                                                                            <label class="form-label fw-bold">Pilih File Surat <span class="text-danger">*</span></label>
                                                                            <input type="file" name="file_surat" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,application/pdf,image/*" required>
                                                                            <small class="text-muted d-block mt-1">Format: PDF, JPG, JPEG, PNG, DOC, DOCX (Maksimal 10 MB).</small>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                                                        <button type="submit" class="btn btn-primary">
                                                                            <i class="fa fa-upload me-1"></i> Upload Sekarang
                                                                        </button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-wrap gap-1 justify-content-center" style="max-width: 80px; margin: 0 auto;">
                                                    @if($isPimpinan ?? false)
                                                        <form action="{{ route('pegawai.surat-izin.validasi-toggle', $row->id) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="btn {{ $row->izin_ka_jenjang == 1 ? 'btn-warning' : 'btn-success' }} btn-sm" title="{{ $row->izin_ka_jenjang == 1 ? 'Batal Setuju' : 'Setujui' }}">
                                                                <i class="fa {{ $row->izin_ka_jenjang == 1 ? 'fa-times' : 'fa-check' }}"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                    <a href="{{ url('pegawai/SuratIzin/' . $row->id . '/edit2') }}" class="btn btn-success btn-sm" title="Edit">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <form action="{{ url('pegawai/SuratIzin/' . $row->id . '/delete2') }}" method="POST" onsubmit="return confirm('Yakin hapus surat izin ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
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

@section('local-js')
    <script>
        $(document).ready(function () {
            $('#order-table').DataTable({
                responsive: true,
                autoWidth: false
            });
        });
    </script>
@endsection
