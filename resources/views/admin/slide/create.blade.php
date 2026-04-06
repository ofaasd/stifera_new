@extends('layouts.default', ['CurrentPage' => $CurrentPage])

@section('content')
    <div class="content-body">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <div class="mb-4 pb-3">
                        <a href="{{ route('slide.index') }}" class="btn btn-primary">
                            <i class="fa-solid fa-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                    <div class="filter cm-content-box box-primary">
                        <div class="content-title SlideToolHeader">
                            <div class="cpa">
                                <i class="fa-solid fa-images me-1"></i>{{ $title }}
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
                                <form action="{{ route('slide.store') }}" method="POST" id="form-slide">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-8 mb-3">
                                            <label class="form-label">Gambar (path/url) <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="gambar" value="{{ old('gambar') }}" required>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Link (opsional)</label>
                                            <input type="text" class="form-control" name="link" value="{{ old('link') }}">
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label">Caption <span class="text-danger">*</span></label>
                                            <textarea class="form-control js-editor" name="caption" rows="6" required>{{ old('caption') }}</textarea>
                                        </div>
                                    </div>

                                    <div class="mt-3">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa-solid fa-save me-1"></i> Simpan
                                        </button>
                                        <a href="{{ route('slide.index') }}" class="btn btn-light ms-2">Batal</a>
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
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    <script>
        let captionEditor;

        document.addEventListener('DOMContentLoaded', () => {
            ClassicEditor
                .create(document.querySelector('.js-editor'))
                .then((editor) => {
                    captionEditor = editor;
                })
                .catch((error) => {
                    console.error(error);
                });

            document.getElementById('form-slide').addEventListener('submit', () => {
                if (captionEditor) {
                    document.querySelector('textarea[name="caption"]').value = captionEditor.getData();
                }
            });
        });
    </script>
@endsection
