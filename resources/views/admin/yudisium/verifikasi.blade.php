@extends('layouts.default')

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <div class="row page-titles">
            <ol class="breadcrumb">
                <li class="breadcrumb-item active"><a href="{{ route('admin.yudisium.index') }}">Manajemen Yudisium</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0)">Verifikasi Berkas</a></li>
            </ol>
        </div>
        
        @if(session('success'))
            <div class="alert alert-success solid alert-dismissible fade show">
                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                <strong>Sukses!</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>
            </div>
        @endif
        
        @if($errors->any())
            <div class="alert alert-danger solid alert-dismissible fade show">
                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2"><polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"></polygon><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                <strong>Error!</strong> {{ $errors->first() }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-xl-4 col-xxl-4 col-lg-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="card-title text-white mb-0">Identitas Mahasiswa</h4>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <img src="{{ asset('images/profile/pic1.jpg') }}" class="rounded-circle" width="100" alt="">
                            <h4 class="mt-3 mb-1">{{ $pendaftaran->mahasiswa->nama ?? 'Nama Tidak Diketahui' }}</h4>
                            <p class="text-muted">{{ $pendaftaran->mahasiswa->nim ?? '-' }}</p>
                            @if($pendaftaran->status_pengajuan == 'lulus_yudisium')
                                <span class="badge badge-success fs-14">LULUS YUDISIUM</span>
                            @endif
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex px-0 justify-content-between">
                                <strong>Periode Yudisium</strong>
                                <span class="mb-0">{{ $pendaftaran->periode->nama_periode }}</span>
                            </li>
                            <li class="list-group-item d-flex px-0 justify-content-between">
                                <strong>Tanggal Pengajuan</strong>
                                <span class="mb-0">{{ \Carbon\Carbon::parse($pendaftaran->created_at)->format('d F Y') }}</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Hardcopy Checklist -->
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h4 class="card-title text-white mb-0">Verifikasi Hardcopy</h4>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item px-0">
                                <form action="{{ route('admin.yudisium.terimaHardcopy') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="id_pendaftaran" value="{{ $pendaftaran->id }}">
                                    <input type="hidden" name="type" value="pkf">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="form-check custom-checkbox">
                                            <input type="checkbox" class="form-check-input" id="checkPkf" name="is_accepted" value="1" {{ $pendaftaran->is_hardcopy_pkf ? 'checked' : '' }} onchange="this.form.submit()">
                                            <label class="form-check-label font-weight-bold" for="checkPkf">Hardcopy Laporan {{ $labelPK ?? 'PKF' }}</label>
                                        </div>
                                    </div>
                                </form>
                            </li>
                            <li class="list-group-item px-0">
                                <form action="{{ route('admin.yudisium.terimaHardcopy') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="id_pendaftaran" value="{{ $pendaftaran->id }}">
                                    <input type="hidden" name="type" value="skripsi">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="form-check custom-checkbox">
                                            <input type="checkbox" class="form-check-input" id="checkSkripsi" name="is_accepted" value="1" {{ $pendaftaran->is_hardcopy_skripsi ? 'checked' : '' }} onchange="this.form.submit()">
                                            <label class="form-check-label font-weight-bold" for="checkSkripsi">Hardcopy {{ $labelTA ?? 'Skripsi' }}</label>
                                        </div>
                                    </div>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-xl-8 col-xxl-8 col-lg-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="card-title text-white mb-0">Verifikasi Berkas Softfile (Syarat Yudisium)</h4>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped mb-0">
                                <thead class="bg-light text-center">
                                    <tr>
                                        <th>Jenis Berkas</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $allValid = true;
                                        // key file definition mapping from controller
                                        $uploadedFiles = $pendaftaran->berkas->keyBy('jenis_berkas');
                                    @endphp
                                    @foreach($mandatoryFiles as $key => $label)
                                        @php
                                            $berkas = $uploadedFiles->get($key);
                                            $status = $berkas ? $berkas->status_berkas : 'belum_upload';
                                            if ($status !== 'valid') $allValid = false;
                                        @endphp
                                        <tr>
                                            <td class="align-middle font-weight-bold">
                                                {{ $label }}
                                                @if($berkas && $berkas->catatan_revisi)
                                                    <div class="text-danger fs-12 mt-1"><i class="fas fa-exclamation-circle"></i> Catatan: {{ $berkas->catatan_revisi }}</div>
                                                @endif
                                            </td>
                                            <td class="text-center align-middle">
                                                @if($status == 'belum_upload')
                                                    <span class="badge badge-dark">Belum Upload</span>
                                                @elseif($status == 'menunggu')
                                                    <span class="badge badge-warning">Menunggu</span>
                                                @elseif($status == 'valid')
                                                    <span class="badge badge-success">Valid</span>
                                                @else
                                                    <span class="badge badge-danger">Ditolak</span>
                                                @endif
                                            </td>
                                            <td class="text-center align-middle">
                                                @if($berkas)
                                                    <a href="{{ asset($berkas->file_path) }}" target="_blank" class="btn btn-sm btn-info shadow" title="Lihat PDF"><i class="fas fa-file-pdf"></i></a>
                                                    
                                                    @if($pendaftaran->status_pengajuan != 'lulus_yudisium')
                                                    <!-- Form Valid -->
                                                    <form action="{{ route('admin.yudisium.verifikasiBerkas') }}" method="POST" class="d-inline-block">
                                                        @csrf
                                                        <input type="hidden" name="id_berkas" value="{{ $berkas->id }}">
                                                        <input type="hidden" name="status_berkas" value="valid">
                                                        <button type="submit" class="btn btn-sm btn-success shadow" title="Validasi"><i class="fas fa-check"></i></button>
                                                    </form>
                                                    
                                                    <!-- Btn Tolak -->
                                                    <button type="button" class="btn btn-sm btn-danger shadow" title="Tolak & Revisi" onclick="setTolakModal({{ $berkas->id }}, '{{ $label }}')" data-bs-toggle="modal" data-bs-target="#modalTolak">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                    @endif
                                                @else
                                                    <span class="text-muted"><i class="fas fa-ban"></i></span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Final Action -->
                @if($pendaftaran->status_pengajuan != 'lulus_yudisium')
                <div class="card">
                    <div class="card-body">
                        @php
                            $canFinalize = $allValid && $pendaftaran->is_hardcopy_pkf && $pendaftaran->is_hardcopy_skripsi;
                        @endphp
                        
                        <div class="alert {{ $canFinalize ? 'alert-primary' : 'alert-warning' }} solid">
                            @if($canFinalize)
                                <i class="fas fa-check-circle me-2"></i> Semua persyaratan terpenuhi. Anda dapat menetapkan kelulusan yudisium.
                            @else
                                <i class="fas fa-exclamation-triangle me-2"></i> Belum dapat mnetapkan yudisium! Pastikan semua berkas softfile berstatus <strong>Valid</strong> dan semua <strong>Hardcopy diterima</strong>.
                            @endif
                        </div>

                        <form action="{{ route('admin.yudisium.tetapkan') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin menetapkan kelulusan yudisium mahasiswa ini?')">
                            @csrf
                            <input type="hidden" name="id_pendaftaran" value="{{ $pendaftaran->id }}">
                            <div class="mb-3">
                                <label class="form-label font-weight-bold">Nomor SK Yudisium</label>
                                <input type="text" name="no_sk_yudisium" class="form-control" placeholder="Masukkan No. SK Yudisium" required {{ !$canFinalize ? 'disabled' : '' }}>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block text-white font-weight-bold" style="font-size: 16px;" {{ !$canFinalize ? 'disabled' : '' }}>
                                <i class="fas fa-award me-2"></i> TETAPKAN LULUS YUDISIUM
                            </button>
                        </form>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Tolak / Revisi -->
<div class="modal fade" id="modalTolak" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.yudisium.verifikasiBerkas') }}" method="POST">
            @csrf
            <input type="hidden" name="id_berkas" id="modal_id_berkas" value="">
            <input type="hidden" name="status_berkas" value="tolak">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tolak Berkas: <span id="modal_nama_berkas" class="text-primary"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Catatan Revisi <span class="text-danger">*</span></label>
                        <textarea name="catatan_revisi" class="form-control" rows="4" placeholder="Jelaskan alasan penolakan atau perbaikan yang harus dilakukan mahasiswa" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak & Kirim Revisi</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@section('local-js')
<script>
    function setTolakModal(idBerkas, namaBerkas) {
        document.getElementById('modal_id_berkas').value = idBerkas;
        document.getElementById('modal_nama_berkas').innerText = namaBerkas;
    }
</script>
@endsection
