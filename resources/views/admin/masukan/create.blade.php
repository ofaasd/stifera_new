@extends('layouts.default', ['CurrentPage' => $CurrentPage])

@section('content')
    <div class="content-body">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <div class="mb-4 pb-3">
                        <a href="{{ route('masukan.index') }}" class="btn btn-primary">
                            <i class="fa-solid fa-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                    <div class="filter cm-content-box box-primary">
                        <div class="content-title SlideToolHeader">
                            <div class="cpa">
                                <i class="fa-solid fa-comments me-1"></i>{{ $title }}
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
                                <form action="{{ route('masukan.store') }}" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">NIM <span class="text-danger">*</span></label>
                                            <select class="form-select js-nim-select" name="nim" style="width:100%" required>
                                                <option value="">-- Pilih NIM --</option>
                                                @foreach($mahasiswaList as $mhs)
                                                    <option value="{{ $mhs->nim }}" {{ old('nim') === $mhs->nim ? 'selected' : '' }}>
                                                        {{ $mhs->nim }} - {{ $mhs->nama }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" name="tanggal" value="{{ old('tanggal') }}" required>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Status <span class="text-danger">*</span></label>
                                            <select class="form-select" name="status" required>
                                                <option value="">-- Pilih Status --</option>
                                                <option value="belum" {{ old('status') === 'belum' ? 'selected' : '' }}>Belum</option>
                                                <option value="proses" {{ old('status') === 'proses' ? 'selected' : '' }}>Proses</option>
                                                <option value="selesai" {{ old('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                                            </select>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label">Saran / Masukan <span class="text-danger">*</span></label>
                                            <textarea class="form-control" name="saran" rows="5" required>{{ old('saran') }}</textarea>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label">Tindak Lanjut</label>
                                            <textarea class="form-control" name="tindak_lanjut" rows="4">{{ old('tindak_lanjut') }}</textarea>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Tanggal Tanggapan</label>
                                            <input type="date" class="form-control" name="tanggal_tanggapan" value="{{ old('tanggal_tanggapan') }}">
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa-solid fa-save me-1"></i> Simpan
                                        </button>
                                        <a href="{{ route('masukan.index') }}" class="btn btn-light ms-2">Batal</a>
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

@section('local-js')
    <script>
        $(document).ready(function () {
            $('.js-nim-select').select2({
                placeholder: '-- Pilih NIM --',
                allowClear: true,
            });
        });
    </script>
@endsection
