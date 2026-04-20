@extends('layouts.default', ['CurrentPage' => $CurrentPage])

@section('local-css')
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<style>
    .alert-notification {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 320px;
        animation: slideIn 0.3s ease-out;
    }

    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    .alert-notification.fade-out {
        animation: slideOut 0.3s ease-out forwards;
    }

    @keyframes slideOut {
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }

    .form-section {
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 1px solid #e3e6f0;
    }

    .form-section:last-child {
        border-bottom: none;
    }

    .form-section-title {
        font-size: 16px;
        font-weight: 600;
        color: #0d2e5b;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid #0f4f99;
        display: inline-block;
    }

    .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 15px;
        margin-bottom: 15px;
    }

    .form-group {
        margin-bottom: 0;
    }

    .form-group label {
        font-weight: 500;
        margin-bottom: 8px;
        color: #2c3e50;
        font-size: 14px;
    }

    .form-group .form-control,
    .form-group .form-select {
        border-radius: 4px;
        border: 1px solid #dbe8f8;
        padding: 10px 12px;
        font-size: 14px;
    }

    .form-group .form-control:focus,
    .form-group .form-select:focus {
        border-color: #70a6e8;
        box-shadow: 0 0 0 0.2rem rgba(15, 79, 153, 0.15);
    }

    .form-group textarea.form-control {
        resize: vertical;
        min-height: 80px;
    }

    .education-checkbox {
        display: flex;
        align-items: center;
        margin: 15px 0;
    }

    .education-checkbox input[type="checkbox"] {
        width: 18px;
        height: 18px;
        margin-right: 10px;
        cursor: pointer;
    }

    .education-checkbox label {
        margin: 0;
        cursor: pointer;
        font-weight: 500;
    }

    .education-block {
        background: #f8fafb;
        padding: 15px;
        border-radius: 4px;
        border-left: 3px solid #0f4f99;
        margin-bottom: 15px;
        display: none;
    }

    .education-block.active {
        display: block;
    }

    .btn-group-bottom {
        display: flex;
        gap: 10px;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #e3e6f0;
    }

    .btn {
        padding: 10px 20px;
        font-size: 14px;
        font-weight: 500;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background: #0f4f99;
        color: white;
        border: none;
    }

    .btn-primary:hover {
        background: #0d3d7a;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(15, 79, 153, 0.3);
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
        border: none;
    }

    .btn-secondary:hover {
        background: #5a6268;
    }

    .card {
        border: 1px solid #dbe8f8;
        border-radius: 6px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .card-body {
        padding: 20px;
    }

    .card-footer {
        padding: 15px 20px;
        background: #f8fafb;
        border-top: 1px solid #dbe8f8;
        border-radius: 0 0 6px 6px;
    }

    .photo-panel {
        display: flex;
        gap: 18px;
        align-items: flex-start;
        padding: 16px;
        margin-bottom: 22px;
        border: 1px solid #dbe8f8;
        border-radius: 6px;
        background: #f8fafb;
        flex-wrap: wrap;
    }

    .photo-preview {
        width: 140px;
        height: 170px;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid #cfdff5;
        background: #fff;
    }

    .photo-form {
        min-width: 260px;
        flex: 1;
    }
</style>
@endsection

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
                    <div class="alert alert-notification alert-success alert-dismissible fade show" role="alert">
                        <div style="display: flex; align-items: center;">
                            <i class="fa-solid fa-check-circle me-2" style="font-size: 20px;"></i>
                            <div>
                                <strong>Sukses!</strong><br>
                                {{ session('status') }}
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-notification alert-danger alert-dismissible fade show" role="alert">
                        <div style="display: flex; align-items: center;">
                            <i class="fa-solid fa-exclamation-circle me-2" style="font-size: 20px;"></i>
                            <div>
                                <strong>Gagal!</strong><br>
                                {{ session('error') }}
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="card">
                    <div class="card-body">
                        <h4 class="mb-4"><i class="fa-solid fa-user me-2"></i>Edit Biodata Pegawai</h4>

                        <div class="photo-panel">
                            <img
                                src="{{ asset('assets/foto_pegawai/' . (!empty($query->foto) ? $query->foto : 'logo.png')) }}"
                                alt="Foto Pegawai"
                                class="photo-preview"
                            >

                            <form action="{{ route('pegawai.biodata.upload-photo') }}" method="POST" enctype="multipart/form-data" class="photo-form">
                                @csrf
                                <div class="form-group">
                                    <label>Ubah Foto Profil</label>
                                    <input type="file" class="form-control" name="foto" accept="image/jpeg,image/png" required>
                                    <small class="text-muted">Format JPG/PNG, maksimal 2MB.</small>
                                </div>
                                <button type="submit" class="btn btn-primary mt-2">
                                    <i class="fa-solid fa-image me-1"></i>Upload Foto
                                </button>
                                <button type="button" class="btn btn-secondary mt-2" data-bs-toggle="modal" data-bs-target="#modalUbahPassword">
                                    <i class="fa-solid fa-key me-1"></i>Ubah Password
                                </button>
                                <a href="{{ route('pegawai.biodata.cv') }}" class="btn btn-primary mt-2">
                                    <i class="fa-solid fa-download me-1"></i>Download CV
                                </a>
                                <a href="{{ route('pegawai.biodata.cv-excel') }}" class="btn btn-primary mt-2">
                                    <i class="fa-solid fa-file-excel me-1"></i>Download CV Excel
                                </a>
                            </form>
                        </div>

                        <form action="{{ route('pegawai.biodata.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <input type="hidden" name="id_biodata" value="{{ $query->id_biodata }}">
                            <input type="hidden" name="pegawai_id" value="{{ $query->pegawai_id }}">
                            <input type="hidden" name="id_pegawai" value="{{ $query->id_pegawai }}">

                            <!-- Data Pribadi -->
                            <div class="form-section">
                                <div class="form-section-title">Data Pribadi</div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Nomor Induk Pegawai (NPP)</label>
                                        <input type="text" class="form-control" name="nip" value="{{ $query->npp }}" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label>Nama Lengkap <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="nama_lengkap" value="{{ $query->nama_lengkap }}" required>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Jenis Kelamin <span class="text-danger">*</span></label>
                                        <select class="form-control" name="jenis_kelamin" required>
                                            <option value="">-- Pilih --</option>
                                            @foreach($jenis_kelamin as $key => $val)
                                                <option value="{{ $key }}" {{ $query->jenis_kelamin == $key ? 'selected' : '' }}>
                                                    {{ $val }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Tempat Lahir</label>
                                        <input type="text" class="form-control" name="tempat_lahir" value="{{ $query->tempat_lahir }}">
                                    </div>
                                    <div class="form-group">
                                        <label>Tanggal Lahir</label>
                                        <input type="text" class="form-control datepicker" name="tanggal_lahir"
                                            value="{{ $query->tanggal_lahir ? date('m/d/Y', strtotime($query->tanggal_lahir)) : '' }}">
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Agama</label>
                                        <select class="form-control" name="agama">
                                            <option value="">-- Pilih --</option>
                                            @foreach($agama as $val)
                                                <option value="{{ $val }}" {{ $query->agama == $val ? 'selected' : '' }}>
                                                    {{ $val }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Status Perkawinan</label>
                                        <select class="form-control" name="status_nikah">
                                            <option value="">-- Pilih --</option>
                                            @foreach($status_kawin as $val)
                                                <option value="{{ $val }}" {{ $query->status_nikah == $val ? 'selected' : '' }}>
                                                    {{ $val }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Gelar Depan</label>
                                        <input type="text" class="form-control" name="gelar_depan" value="{{ $query->gelar_depan }}" placeholder="Contoh: Dr., Ir.">
                                    </div>
                                    <div class="form-group">
                                        <label>Gelar Belakang</label>
                                        <input type="text" class="form-control" name="gelar_belakang" value="{{ $query->gelar_belakang }}" placeholder="Contoh: S.Kom, M.Eng">
                                    </div>
                                </div>
                            </div>

                            <!-- Alamat -->
                            <div class="form-section">
                                <div class="form-section-title">Alamat</div>

                                <div class="form-group">
                                    <label>Alamat Tempat Tinggal</label>
                                    <textarea class="form-control" name="alamat">{{ $query->alamat }}</textarea>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Provinsi</label>
                                        <select class="form-control" id="provinsi" name="provinsi">
                                            <option value="">-- Pilih --</option>
                                            @foreach($wilayah as $w)
                                                <option value="{{ $w->id_wil }}" {{ $w->id_wil == $query->provinsi ? 'selected' : '' }}>
                                                    {{ $w->nm_wil }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Kota/Kabupaten</label>
                                        <select class="form-control" id="kotakab" name="kotakab">
                                            <option value="">-- Pilih --</option>
                                            @if(!empty($list_kota))
                                                @foreach($list_kota as $k)
                                                    <option value="{{ $k->id_wil }}" {{ $k->id_wil == $query->kotakab ? 'selected' : '' }}>
                                                        {{ $k->nm_wil }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Kecamatan</label>
                                        <select class="form-control" id="kecamatan" name="kecamatan">
                                            <option value="">-- Pilih --</option>
                                            @if(!empty($list_kecamatan))
                                                @foreach($list_kecamatan as $k)
                                                    <option value="{{ $k->id_wil }}" {{ $k->id_wil == $query->kecamatan ? 'selected' : '' }}>
                                                        {{ $k->nm_wil }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Kelurahan</label>
                                        <input type="text" class="form-control" name="kelurahan" value="{{ $query->kelurahan }}">
                                    </div>
                                </div>
                            </div>

                            <!-- Kontak & Identitas -->
                            <div class="form-section">
                                <div class="form-section-title">Kontak & Identitas</div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="email" class="form-control" name="email1" value="{{ $query->email1 }}">
                                    </div>
                                    <div class="form-group">
                                        <label>No. Telepon</label>
                                        <input type="text" class="form-control" name="notelp" value="{{ $query->notelp }}">
                                    </div>
                                    <div class="form-group">
                                        <label>No. HP</label>
                                        <input type="text" class="form-control" name="nohp" value="{{ $query->nohp }}">
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label>No. KTP</label>
                                        <input type="text" class="form-control" name="no_ktp" value="{{ $query->no_ktp }}">
                                    </div>
                                    <div class="form-group">
                                        <label>No. KK</label>
                                        <input type="text" class="form-control" name="no_kk" value="{{ $query->no_kk }}">
                                    </div>
                                    <div class="form-group">
                                        <label>NIDN</label>
                                        <input type="text" class="form-control" name="nidn" value="{{ $query->nidn }}">
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label>No. BPJS Kesehatan</label>
                                        <input type="text" class="form-control" name="no_bpjs_kesehatan" value="{{ $query->no_bpjs_kesehatan }}">
                                    </div>
                                    <div class="form-group">
                                        <label>No. BPJS Ketenagakerjaan</label>
                                        <input type="text" class="form-control" name="no_bpjs_ketenagakerjaan" value="{{ $query->no_bpjs_ketenagakerjaan }}">
                                    </div>
                                </div>
                            </div>

                            <!-- Data Keluarga -->
                            <div class="form-section">
                                <div class="form-section-title">Data Keluarga</div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Nama Pasangan</label>
                                        <input type="text" class="form-control" name="nama_pasangan" value="{{ $query->nama_pasangan }}">
                                    </div>
                                    <div class="form-group">
                                        <label>Tanggal Lahir Pasangan</label>
                                        <input type="text" class="form-control datepicker" name="tgl_lahir_pasangan"
                                            value="{{ $query->tgl_lahir_pasangan ? date('m/d/Y', strtotime($query->tgl_lahir_pasangan)) : '' }}">
                                    </div>
                                    <div class="form-group">
                                        <label>Pekerjaan Pasangan</label>
                                        <input type="text" class="form-control" name="pekerjaan_pasangan" value="{{ $query->pekerjaan_pasangan }}">
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Jumlah Anak</label>
                                        <input type="number" class="form-control" name="jumlah_anak" value="{{ $query->jumlah_anak }}" min="0">
                                    </div>
                                </div>
                            </div>

                            <!-- Riwayat Pendidikan -->
                            <div class="form-section">
                                <div class="form-section-title">Riwayat Pendidikan</div>

                                <!-- S1 -->
                                <div class="education-block active">
                                    <div class="form-section-title" style="display: block; font-size: 14px; margin-bottom: 12px; border-bottom: none;">
                                        Pendidikan S1
                                    </div>
                                    <input type="hidden" name="jenjang[]" value="S1">
                                    <input type="hidden" name="status_riwayat[]" value="{{ !empty($s1->id) ? $s1->id : '0' }}">

                                    <div class="form-row">
                                        <div class="form-group">
                                            <label>Universitas</label>
                                            <select class="form-control univ-select" name="universitasid[]">
                                                <option value="">-- Pilih --</option>
                                                @foreach($universitas as $val)
                                                    <option value="{{ $val->id }}" {{ (!empty($s1->universitas) && $s1->universitas == $val->id) ? 'selected' : '' }}>
                                                        {{ $val->nama_universitas }}
                                                    </option>
                                                @endforeach
                                                <option value="999999">Lainnya</option>
                                            </select>
                                            <input type="text" class="form-control univ-text mt-2" name="universitas[]" placeholder="Nama universitas lain" style="display:none;">
                                        </div>
                                        <div class="form-group">
                                            <label>Jurusan</label>
                                            <select class="form-control prodi-select" name="jurusanid[]">
                                                <option value="">-- Pilih --</option>
                                                @foreach($master_prodi as $val)
                                                    <option value="{{ $val->id }}" {{ (!empty($s1->jurusan) && $s1->jurusan == $val->id) ? 'selected' : '' }}>
                                                        {{ $val->nama_jurusan }}
                                                    </option>
                                                @endforeach
                                                <option value="999999">Lainnya</option>
                                            </select>
                                            <input type="text" class="form-control prodi-text mt-2" name="jurusan[]" placeholder="Nama jurusan lain" style="display:none;">
                                        </div>
                                    </div>
                                </div>

                                <!-- S2 -->
                                <div class="education-checkbox">
                                    <input type="checkbox" id="pendidikan_s2">
                                    <label for="pendidikan_s2">Tambahkan Pendidikan S2</label>
                                </div>

                                <div class="education-block" id="block_s2">
                                    <div class="form-section-title" style="display: block; font-size: 14px; margin-bottom: 12px; border-bottom: none;">
                                        Pendidikan S2
                                    </div>
                                    <input type="hidden" name="jenjang[]" value="S2">
                                    <input type="hidden" name="status_riwayat[]" value="{{ !empty($s2->id) ? $s2->id : '0' }}">

                                    <div class="form-row">
                                        <div class="form-group">
                                            <label>Universitas</label>
                                            <select class="form-control univ-select" name="universitasid[]">
                                                <option value="">-- Pilih --</option>
                                                @foreach($universitas as $val)
                                                    <option value="{{ $val->id }}" {{ (!empty($s2->universitas) && $s2->universitas == $val->id) ? 'selected' : '' }}>
                                                        {{ $val->nama_universitas }}
                                                    </option>
                                                @endforeach
                                                <option value="999999">Lainnya</option>
                                            </select>
                                            <input type="text" class="form-control univ-text mt-2" name="universitas[]" placeholder="Nama universitas lain" style="display:none;">
                                        </div>
                                        <div class="form-group">
                                            <label>Jurusan</label>
                                            <select class="form-control prodi-select" name="jurusanid[]">
                                                <option value="">-- Pilih --</option>
                                                @foreach($master_prodi as $val)
                                                    <option value="{{ $val->id }}" {{ (!empty($s2->jurusan) && $s2->jurusan == $val->id) ? 'selected' : '' }}>
                                                        {{ $val->nama_jurusan }}
                                                    </option>
                                                @endforeach
                                                <option value="999999">Lainnya</option>
                                            </select>
                                            <input type="text" class="form-control prodi-text mt-2" name="jurusan[]" placeholder="Nama jurusan lain" style="display:none;">
                                        </div>
                                    </div>
                                </div>

                                <!-- S3 -->
                                <div class="education-checkbox">
                                    <input type="checkbox" id="pendidikan_s3">
                                    <label for="pendidikan_s3">Tambahkan Pendidikan S3</label>
                                </div>

                                <div class="education-block" id="block_s3">
                                    <div class="form-section-title" style="display: block; font-size: 14px; margin-bottom: 12px; border-bottom: none;">
                                        Pendidikan S3
                                    </div>
                                    <input type="hidden" name="jenjang[]" value="S3">
                                    <input type="hidden" name="status_riwayat[]" value="{{ !empty($s3->id) ? $s3->id : '0' }}">

                                    <div class="form-row">
                                        <div class="form-group">
                                            <label>Universitas</label>
                                            <select class="form-control univ-select" name="universitasid[]">
                                                <option value="">-- Pilih --</option>
                                                @foreach($universitas as $val)
                                                    <option value="{{ $val->id }}" {{ (!empty($s3->universitas) && $s3->universitas == $val->id) ? 'selected' : '' }}>
                                                        {{ $val->nama_universitas }}
                                                    </option>
                                                @endforeach
                                                <option value="999999">Lainnya</option>
                                            </select>
                                            <input type="text" class="form-control univ-text mt-2" name="universitas[]" placeholder="Nama universitas lain" style="display:none;">
                                        </div>
                                        <div class="form-group">
                                            <label>Jurusan</label>
                                            <select class="form-control prodi-select" name="jurusanid[]">
                                                <option value="">-- Pilih --</option>
                                                @foreach($master_prodi as $val)
                                                    <option value="{{ $val->id }}" {{ (!empty($s3->jurusan) && $s3->jurusan == $val->id) ? 'selected' : '' }}>
                                                        {{ $val->nama_jurusan }}
                                                    </option>
                                                @endforeach
                                                <option value="999999">Lainnya</option>
                                            </select>
                                            <input type="text" class="form-control prodi-text mt-2" name="jurusan[]" placeholder="Nama jurusan lain" style="display:none;">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Kepegawaian -->
                            <div class="form-section">
                                <div class="form-section-title">Data Kepegawaian</div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Homebase</label>
                                        <select class="form-control" name="homebase">
                                            <option value="">-- Pilih --</option>
                                            @foreach($homebase as $row)
                                                <option value="{{ $row->id }}" {{ $row->id == $query->homebase ? 'selected' : '' }}>
                                                    {{ $row->nama_jurusan }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Status Kepegawaian</label>
                                        <select class="form-control" name="status">
                                            <option value="">-- Pilih --</option>
                                            @foreach($status as $val)
                                                <option value="{{ $val }}" {{ $query->status_pegawai == $val ? 'selected' : '' }}>
                                                    {{ ucfirst($val) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Buttons -->
                            <div class="btn-group-bottom">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa-solid fa-save me-1"></i>Simpan Perubahan
                                </button>
                                <a href="{{ route('pegawai.home') }}" class="btn btn-secondary">
                                    <i class="fa-solid fa-times me-1"></i>Batal
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalUbahPassword" tabindex="-1" aria-labelledby="modalUbahPasswordLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('pegawai.biodata.change-password') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="modalUbahPasswordLabel">Ubah Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label>Password Saat Ini</label>
                        <input type="password" class="form-control" name="current_password" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>Password Baru</label>
                        <input type="password" class="form-control" name="password" minlength="8" required>
                        <small class="text-muted">Minimal 8 karakter.</small>
                    </div>
                    <div class="form-group">
                        <label>Konfirmasi Password Baru</label>
                        <input type="password" class="form-control" name="password_confirmation" minlength="8" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Password</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('local-js')
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize datepickers
    $('.datepicker').datepicker({
        dateFormat: 'mm/dd/yy'
    });

    // Auto-dismiss alerts after 5 seconds
    $('.alert-notification').each(function() {
        setTimeout(() => {
            $(this).addClass('fade-out').fadeOut('slow', function() {
                $(this).remove();
            });
        }, 5000);
    });

    // Wilayah cascade loading
    $('#provinsi').on('change', function() {
        loadWilayah($(this).val(), '#kotakab', 'Kota/Kabupaten');
    });

    $('#kotakab').on('change', function() {
        loadWilayah($(this).val(), '#kecamatan', 'Kecamatan');
    });

    function loadWilayah(id, selector, type) {
        if (!id) {
            $(selector).html('<option value="">-- Pilih --</option>');
            return;
        }

        $.ajax({
            url: "{{ url('wilayah/get_kota_kecamatan') }}",
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: { id: id },
            dataType: 'json',
            success: function(data) {
                let html = '<option value="">-- Pilih --</option>';
                data.forEach(item => {
                    html += `<option value="${item.id_wil}">${item.nm_wil}</option>`;
                });
                $(selector).html(html);
            }
        });
    }

    // Education level toggles
    $('#pendidikan_s2').on('change', function() {
        $('#block_s2').toggleClass('active', this.checked);
    });

    $('#pendidikan_s3').on('change', function() {
        $('#block_s3').toggleClass('active', this.checked);
    });

    // Check if S2/S3 data exists and show blocks
    @if(!empty($s2->id))
        $('#pendidikan_s2').prop('checked', true).trigger('change');
    @endif

    @if(!empty($s3->id))
        $('#pendidikan_s3').prop('checked', true).trigger('change');
    @endif

    // Toggle "Lainnya" text input for universitas/jurusan
    $(document).on('change', '.univ-select', function() {
        const parent = $(this).closest('.form-group');
        const textInput = parent.find('.univ-text');
        if ($(this).val() === '999999') {
            textInput.show().find('input').attr('type', 'text');
        } else {
            textInput.hide().find('input').attr('type', 'hidden');
        }
    });

    $(document).on('change', '.prodi-select', function() {
        const parent = $(this).closest('.form-group');
        const textInput = parent.find('.prodi-text');
        if ($(this).val() === '999999') {
            textInput.show().find('input').attr('type', 'text');
        } else {
            textInput.hide().find('input').attr('type', 'hidden');
        }
    });

    // Trigger change event on page load to set initial visibility
    $('.univ-select').trigger('change');
    $('.prodi-select').trigger('change');
});
</script>
@endsection
