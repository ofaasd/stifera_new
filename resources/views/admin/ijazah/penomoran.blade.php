@extends('layouts.default')

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <div class="row page-titles">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.ijazah.index') }}">Manajemen Ijazah</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.ijazah.show', $dokumen->id_periode) }}">Daftar Mahasiswa</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Input Penomoran</a></li>
            </ol>
        </div>

        <div class="row">
            <div class="col-xl-6 col-lg-8">
                <div class="card">
                    <div class="card-header border-0 pb-0">
                        <h4 class="card-title">Penomoran Dokumen: {{ $dokumen->mahasiswa->nama }}</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.ijazah.updatePenomoran', $dokumen->id) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label font-weight-bold">Nomor Ijazah</label>
                                <input type="text" name="no_ijazah" class="form-control" value="{{ $dokumen->no_ijazah }}" placeholder="Masukkan No. Ijazah">
                            </div>
                            <div class="mb-3">
                                <label class="form-label font-weight-bold">PIN Dikti</label>
                                <input type="text" name="pin_dikti" class="form-control" value="{{ $dokumen->pin_dikti }}" placeholder="Masukkan PIN Dikti">
                            </div>
                            <div class="mb-3">
                                <label class="form-label font-weight-bold">Nomor Transkrip</label>
                                <input type="text" name="no_transkrip" class="form-control" value="{{ $dokumen->no_transkrip }}" placeholder="Masukkan No. Transkrip Akademik">
                            </div>
                            <div class="mb-3">
                                <label class="form-label font-weight-bold">Tanggal Terbit</label>
                                <input type="date" name="tanggal_terbit" class="form-control" value="{{ $dokumen->tanggal_terbit }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label font-weight-bold">Kategori Kelulusan / Predikat</label>
                                <select name="kategori_kelulusan" class="form-control" required>
                                    <option value="Memuaskan" {{ $dokumen->kategori_kelulusan == 'Memuaskan' ? 'selected' : '' }}>Memuaskan</option>
                                    <option value="Sangat Memuaskan" {{ $dokumen->kategori_kelulusan == 'Sangat Memuaskan' ? 'selected' : '' }}>Sangat Memuaskan</option>
                                    <option value="Cumlaude" {{ $dokumen->kategori_kelulusan == 'Cumlaude' ? 'selected' : '' }}>Cumlaude (Dengan Pujian)</option>
                                </select>
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('admin.ijazah.show', $dokumen->id_periode) }}" class="btn btn-light me-2">Batal</a>
                                <button type="submit" class="btn btn-primary">Simpan Penomoran</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
