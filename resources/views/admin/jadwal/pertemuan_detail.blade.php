@extends('layouts.default', ['CurrentPage' => $CurrentPage])

@section('content')
<div class="content-body">
    <div class="container">
        <div class="row">
            <div class="col-xl-12">
                @if(session('status'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="mb-3 d-flex gap-2">
                    <a href="{{ url('master/pertemuan') }}" class="btn btn-light">
                        <i class="fa-solid fa-arrow-left me-1"></i> Kembali
                    </a>
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#uploadDokumenModal">
                        <i class="fa-solid fa-upload me-1"></i> Upload RPS / KP
                    </button>
                    <a href="{{ url('master/pertemuan/' . $jadwal->id . '/export-pdf') }}" class="btn btn-primary">
                        <i class="fa fa-download me-1"></i> Cetak Absensi PDF
                    </a>
                </div>

                <div class="filter cm-content-box box-primary mb-3">
                    <div class="content-title SlideToolHeader">
                        <div class="cpa">
                            <i class="fa-solid fa-book-open-reader me-1"></i> Informasi Jadwal
                        </div>
                    </div>
                    <div class="cm-content-body form excerpt">
                        <div class="card-body pb-2">
                            <div class="row">
                                <div class="col-md-4 mb-2"><strong>Kode MK:</strong> {{ $jadwal->kode_mata_kuliah }}</div>
                                <div class="col-md-8 mb-2"><strong>Mata Kuliah:</strong> {{ $jadwal->nama_mata_kuliah ?? '-' }}</div>
                                <div class="col-md-6 mb-2"><strong>Pengampu:</strong> {{ trim($jadwal->nama_dosen ?? '-') }}</div>
                                <div class="col-md-6 mb-2"><strong>Kelas:</strong> {{ ($jadwal->tipe_mhs == 2 ? 'Karyawan' : 'Reguler') . ' ' . ($jadwal->rombel ?? '-') }}</div>
                                <div class="col-md-6 mb-2"><strong>Hari/Sesi:</strong> {{ $jadwal->hari }} / {{ $jadwal->sesi }}</div>
                                <div class="col-md-6 mb-2"><strong>Ruang:</strong> {{ $jadwal->ruang }}</div>
                                <div class="col-md-6 mb-2">
                                    <strong>RPS:</strong>
                                    @if(!empty($jadwal->rps))
                                        <a href="{{ asset('assets/files/' . $jadwal->rps) }}" target="_blank" class="btn btn-primary btn-sm">Lihat File</a>
                                    @else
                                        -
                                    @endif
                                </div>
                                <div class="col-md-6 mb-2">
                                    <strong>KP:</strong>
                                    @if(!empty($jadwal->kp))
                                        <a href="{{ asset('assets/files/' . $jadwal->kp) }}" target="_blank" class="btn btn-primary btn-sm">Lihat File</a>
                                    @else
                                        -
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="filter cm-content-box box-primary">
                    <div class="content-title SlideToolHeader">
                        <div class="cpa">
                            <i class="fa-solid fa-calendar-check me-1"></i>{{ $title }}
                        </div>
                        <div class="tools">
                            <a href="javascript:void(0);" class="expand handle"><i class="fal fa-angle-down"></i></a>
                        </div>
                    </div>
                    <div class="cm-content-body form excerpt">
                        <div class="card-body pb-4">
                            <form action="{{ url('master/pertemuan/' . $jadwal->id) }}" method="POST">
                                @csrf
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th style="width: 180px;">ID Pertemuan</th>
                                                <th>Tanggal Pertemuan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($listPertemuan as $nomor)
                                                <tr>
                                                    <td>Pertemuan {{ $nomor }}</td>
                                                    <td>
                                                        <input
                                                            type="date"
                                                            class="form-control"
                                                            name="tanggal[{{ $nomor }}]"
                                                            value="{{ old('tanggal.' . $nomor, $tanggalByPertemuan[$nomor] ?? '') }}"
                                                        >
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="mt-3">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa-solid fa-save me-1"></i> Simpan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="uploadDokumenModal" tabindex="-1" aria-labelledby="uploadDokumenModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ url('master/pertemuan/' . $jadwal->id . '/dokumen') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadDokumenModalLabel">Upload Dokumen RPS / KP</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">File RPS</label>
                        <input type="file" name="rps_file" class="form-control" accept=".pdf,.doc,.docx">
                        <small class="text-muted">Format: PDF, DOC, DOCX. Maks 5MB.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">File KP</label>
                        <input type="file" name="kp_file" class="form-control" accept=".pdf,.doc,.docx">
                        <small class="text-muted">Format: PDF, DOC, DOCX. Maks 5MB.</small>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Preview RPS Saat Ini</label>
                        @if(!empty($jadwal->rps))
                            @php
                                $rpsUrl = asset('assets/files/' . $jadwal->rps);
                                $rpsExt = strtolower(pathinfo((string) $jadwal->rps, PATHINFO_EXTENSION));
                            @endphp
                            <div class="mb-2">
                                <a href="{{ $rpsUrl }}" target="_blank">Buka File RPS</a>
                            </div>
                            @if($rpsExt === 'pdf')
                                <iframe src="{{ $rpsUrl }}" width="100%" height="300" style="border:1px solid #ddd;"></iframe>
                            @else
                                <div class="alert alert-light mb-0">
                                    Preview langsung hanya tersedia untuk file PDF. Untuk file {{ strtoupper($rpsExt) }}, silakan buka file melalui tautan di atas.
                                </div>
                            @endif
                        @else
                            <div class="text-muted">Belum ada file RPS yang diupload.</div>
                        @endif
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-bold">Preview KP Saat Ini</label>
                        @if(!empty($jadwal->kp))
                            @php
                                $kpUrl = asset('assets/files/' . $jadwal->kp);
                                $kpExt = strtolower(pathinfo((string) $jadwal->kp, PATHINFO_EXTENSION));
                            @endphp
                            <div class="mb-2">
                                <a href="{{ $kpUrl }}" target="_blank">Buka File KP</a>
                            </div>
                            @if($kpExt === 'pdf')
                                <iframe src="{{ $kpUrl }}" width="100%" height="300" style="border:1px solid #ddd;"></iframe>
                            @else
                                <div class="alert alert-light mb-0">
                                    Preview langsung hanya tersedia untuk file PDF. Untuk file {{ strtoupper($kpExt) }}, silakan buka file melalui tautan di atas.
                                </div>
                            @endif
                        @else
                            <div class="text-muted">Belum ada file KP yang diupload.</div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-save me-1"></i> Upload
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
