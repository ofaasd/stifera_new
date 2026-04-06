@extends('layouts.default', ['CurrentPage' => $CurrentPage])

@section('content')
    <div class="content-body">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <div class="mb-4 pb-3">
                        <a href="{{ url('master/ruang') }}" class="btn btn-primary">
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
                                <form action="{{ url('master/ruang') }}" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Nama Ruang <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="nama_ruang" value="{{ old('nama_ruang') }}" required>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label">Kapasitas <span class="text-danger">*</span></label>
                                            <input type="number" min="1" class="form-control" name="kapasitas" value="{{ old('kapasitas') }}" required>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label">Luas <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="luas" value="{{ old('luas') }}" placeholder="Contoh: 8x10" required>
                                        </div>
                                    </div>

                                    <div class="mt-3">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa-solid fa-save me-1"></i> Simpan
                                        </button>
                                        <a href="{{ url('master/ruang') }}" class="btn btn-light ms-2">Batal</a>
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
