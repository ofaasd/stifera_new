@extends('layouts.default', ['CurrentPage' => $CurrentPage])

@section('content')
    <div class="content-body">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <div class="mb-4 pb-3">
                        <a href="{{ url('master/tahun') }}" class="btn btn-primary">
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
                                <form action="{{ url('master/tahun/' . $d->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="row">
                                        <div class="col-md-2 mb-3">
                                            <label class="form-label">ID Tahun <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" name="id_tahun" value="{{ old('id_tahun', $d->id_tahun) }}" required>
                                        </div>
                                        <div class="col-md-2 mb-3">
                                            <label class="form-label">Awal <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="awal" value="{{ old('awal', $d->awal) }}" required>
                                        </div>
                                        <div class="col-md-2 mb-3">
                                            <label class="form-label">Akhir <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="akhir" value="{{ old('akhir', $d->akhir) }}" required>
                                        </div>
                                        <div class="col-md-2 mb-3">
                                            <label class="form-label">Jenis <span class="text-danger">*</span></label>
                                            <select class="form-control" name="jenis" required>
                                                <option value="1" {{ old('jenis', (string) $d->jenis) === '1' ? 'selected' : '' }}>Ganjil</option>
                                                <option value="2" {{ old('jenis', (string) $d->jenis) === '2' ? 'selected' : '' }}>Genap</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2 mb-3">
                                            <label class="form-label">Aktif <span class="text-danger">*</span></label>
                                            <select class="form-control" name="is_aktif" required>
                                                <option value="1" {{ old('is_aktif', (string) $d->is_aktif) === '1' ? 'selected' : '' }}>Ya</option>
                                                <option value="0" {{ old('is_aktif', (string) $d->is_aktif) === '0' ? 'selected' : '' }}>Tidak</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2 mb-3">
                                            <label class="form-label">Status <span class="text-danger">*</span></label>
                                            <select class="form-control" name="status" required>
                                                <option value="1" {{ old('status', (string) $d->status) === '1' ? 'selected' : '' }}>Aktif</option>
                                                <option value="0" {{ old('status', (string) $d->status) === '0' ? 'selected' : '' }}>Tidak Aktif</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label">Tipe Mahasiswa <span class="text-danger">*</span></label>
                                            <select class="form-control" name="tipe_mhs" required>
                                                <option value="1" {{ old('tipe_mhs', (string) $d->tipe_mhs) === '1' ? 'selected' : '' }}>Reguler</option>
                                                <option value="2" {{ old('tipe_mhs', (string) $d->tipe_mhs) === '2' ? 'selected' : '' }}>RPL</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label">Kuesioner <span class="text-danger">*</span></label>
                                            <select class="form-control" name="kuesioner" required>
                                                <option value="0" {{ old('kuesioner', (string) $d->kuesioner) === '0' ? 'selected' : '' }}>Tidak</option>
                                                <option value="1" {{ old('kuesioner', (string) $d->kuesioner) === '1' ? 'selected' : '' }}>Ya</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label">Is Delete</label>
                                            <input type="text" class="form-control" name="is_delete" value="{{ old('is_delete', $d->is_delete) }}">
                                        </div>
                                    </div>

                                    <div class="mt-3">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa-solid fa-save me-1"></i> Update
                                        </button>
                                        <a href="{{ url('master/tahun') }}" class="btn btn-light ms-2">Batal</a>
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
