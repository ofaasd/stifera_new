@extends('layouts.default', ['CurrentPage' => $CurrentPage])

@section('content')
    <div class="content-body">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <div class="mb-4 pb-3">
                        <a href="{{ url('master/waktu') }}" class="btn btn-primary">
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
                                <form action="{{ url('master/waktu/' . $d->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Nama Sesi <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="nama_sesi" value="{{ old('nama_sesi', $d->nama_sesi) }}" required>
                                        </div>
                                        <div class="col-md-2 mb-3">
                                            <label class="form-label">Jam Mulai <span class="text-danger">*</span></label>
                                            <input type="time" class="form-control" name="mulai" value="{{ old('mulai', $d->mulai ? \Carbon\Carbon::parse($d->mulai)->format('H:i') : '') }}" required>
                                        </div>
                                        <div class="col-md-2 mb-3">
                                            <label class="form-label">Jam Selesai <span class="text-danger">*</span></label>
                                            <input type="time" class="form-control" name="selesai" value="{{ old('selesai', $d->selesai ? \Carbon\Carbon::parse($d->selesai)->format('H:i') : '') }}" required>
                                        </div>
                                        <div class="col-md-2 mb-3">
                                            <label class="form-label">SKS <span class="text-danger">*</span></label>
                                            <input type="number" min="1" max="10" class="form-control" name="sks" value="{{ old('sks', $d->sks) }}" required>
                                        </div>
                                        <div class="col-md-2 mb-3">
                                            <label class="form-label">Status <span class="text-danger">*</span></label>
                                            <select class="form-control" name="status" required>
                                                <option value="1" {{ old('status', (string) $d->status) === '1' ? 'selected' : '' }}>Aktif</option>
                                                <option value="0" {{ old('status', (string) $d->status) === '0' ? 'selected' : '' }}>Tidak Aktif</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="mt-3">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa-solid fa-save me-1"></i> Update
                                        </button>
                                        <a href="{{ url('master/waktu') }}" class="btn btn-light ms-2">Batal</a>
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
