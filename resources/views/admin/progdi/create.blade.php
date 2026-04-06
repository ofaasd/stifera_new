@extends('layouts.default', ['CurrentPage' => $CurrentPage])

@section('content')
    <div class="content-body">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <div class="mb-4 pb-3">
                        <a href="{{ url('master/progdi') }}" class="btn btn-primary">
                            <i class="fa-solid fa-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                    <div class="filter cm-content-box box-primary">
                        <div class="content-title SlideToolHeader">
                            <div class="cpa">
                                <i class="fa-solid fa-file-lines me-1"></i>{{ $title }}
                            </div>
                            <div class="tools">
                                <a href="javascript:void(0);" class="expand handle">
                                    <i class="fal fa-angle-down"></i>
                                </a>
                            </div>
                        </div>
                        <div class="cm-content-body form excerpt">
                            @if($errors->any())
                                <div class="alert alert-danger mx-3 mt-3">
                                    <ul class="mb-0">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <div class="card-body pb-4">
                                <form action="{{ url('master/progdi') }}" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-2 mb-3">
                                            <label class="form-label">Kode</label>
                                            <input type="text" class="form-control" name="kode" value="{{ old('kode') }}">
                                        </div>
                                        <div class="col-md-2 mb-3">
                                            <label class="form-label">Kode NIM <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="kodenim" value="{{ old('kodenim') }}" required>
                                        </div>
                                        <div class="col-md-2 mb-3">
                                            <label class="form-label">Jenjang</label>
                                            <input type="text" class="form-control" name="jenjang" value="{{ old('jenjang') }}" placeholder="Contoh: D3 / S1">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Nama Jurusan <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="nama_jurusan" value="{{ old('nama_jurusan') }}" required>
                                        </div>
                                        <div class="col-md-2 mb-3">
                                            <label class="form-label">Fakultas</label>
                                            <select class="form-control" name="fakultas">
                                                <option value="">- Pilih -</option>
                                                @foreach($fakultasList as $f)
                                                    <option value="{{ $f->id }}" {{ old('fakultas') == (string) $f->id ? 'selected' : '' }}>{{ $f->nama_fakultas }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-5 mb-3">
                                            <label class="form-label">Nama Ijazah <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="nama_ijazah" value="{{ old('nama_ijazah') }}" required>
                                        </div>
                                        <div class="col-md-5 mb-3">
                                            <label class="form-label">Nama Ijazah (English) <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="nama_ijazah_eng" value="{{ old('nama_ijazah_eng') }}" required>
                                        </div>
                                        <div class="col-md-2 mb-3">
                                            <label class="form-label">Status <span class="text-danger">*</span></label>
                                            <select class="form-control" name="off" required>
                                                <option value="0" {{ old('off', '0') === '0' ? 'selected' : '' }}>Aktif</option>
                                                <option value="1" {{ old('off') === '1' ? 'selected' : '' }}>Tidak Aktif</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="mt-3">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa-solid fa-save me-1"></i> Simpan
                                        </button>
                                        <a href="{{ url('master/progdi') }}" class="btn btn-light ms-2">Batal</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
