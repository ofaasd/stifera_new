@extends('layouts.default', ['CurrentPage' => $CurrentPage])

@section('content')
    <div class="content-body">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <div class="mb-4 pb-3">
                        <a href="{{ url('pmb/soal') }}" class="btn btn-primary">
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
                                <form action="{{ url('pmb/soal/' . $d->id) }}" method="POST" id="form-soal-pmb">
                                    @csrf
                                    @method('PUT')

                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label">Soal <span class="text-danger">*</span></label>
                                            <textarea class="form-control js-editor" name="soal" rows="5" required>{{ old('soal', $d->soal) }}</textarea>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Status <span class="text-danger">*</span></label>
                                            <select class="form-control" name="is_aktif" required>
                                                <option value="1" {{ old('is_aktif', (string) $d->is_aktif) === '1' ? 'selected' : '' }}>Aktif</option>
                                                <option value="0" {{ old('is_aktif', (string) $d->is_aktif) === '0' ? 'selected' : '' }}>Tidak Aktif</option>
                                            </select>
                                        </div>
                                    </div>

                                    @php
                                        $pilihanDefault = $d->pilihanSoal->pluck('pilihan')->toArray();
                                        $oldPilihan = old('pilihan', !empty($pilihanDefault) ? $pilihanDefault : ['']);
                                        $existingKunci = strtoupper((string) optional($d->kunciJawaban)->kunci);
                                        $defaultKunciIndex = $existingKunci !== '' ? max(0, ord($existingKunci) - 65) : 0;
                                        $oldKunciIndex = old('kunci_index', $defaultKunciIndex);
                                    @endphp

                                    <div class="d-flex justify-content-between align-items-center mt-2 mb-2">
                                        <label class="form-label mb-0">Pilihan Jawaban <span class="text-danger">*</span></label>
                                        <button type="button" class="btn btn-info btn-sm" id="btn-add-pilihan">
                                            <i class="fa fa-plus me-1"></i> Tambah Pilihan
                                        </button>
                                    </div>

                                    <div id="pilihan-wrapper">
                                        @foreach($oldPilihan as $idx => $value)
                                            <div class="border rounded p-3 mb-3 pilihan-item">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <div class="d-flex align-items-center gap-3">
                                                        <span class="badge badge-primary light pilihan-label">Pilihan</span>
                                                        <div class="form-check mb-0">
                                                            <input class="form-check-input pilihan-kunci" type="radio" name="kunci_index" value="{{ $idx }}" {{ (string) $oldKunciIndex === (string) $idx ? 'checked' : '' }}>
                                                            <label class="form-check-label">Kunci Jawaban</label>
                                                        </div>
                                                    </div>
                                                    <button type="button" class="btn btn-danger btn-xs btn-remove-pilihan">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </div>
                                                <textarea class="form-control js-editor pilihan-input" name="pilihan[]" rows="3" required>{{ $value }}</textarea>
                                            </div>
                                        @endforeach
                                    </div>

                                    @error('kunci_index')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror

                                    <div class="mt-3">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa-solid fa-save me-1"></i> Update
                                        </button>
                                        <a href="{{ url('pmb/soal') }}" class="btn btn-light ms-2">Batal</a>
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
        const editorInstances = new WeakMap();

        function initEditor(element) {
            if (!element || editorInstances.has(element)) {
                return;
            }

            ClassicEditor
                .create(element)
                .then((editor) => {
                    editorInstances.set(element, editor);
                })
                .catch((error) => {
                    console.error(error);
                });
        }

        function destroyEditor(element) {
            const editor = editorInstances.get(element);
            if (!editor) {
                return;
            }

            editor.destroy().finally(() => {
                editorInstances.delete(element);
            });
        }

        function refreshPilihanLabel() {
            document.querySelectorAll('.pilihan-item').forEach((item, index) => {
                const label = item.querySelector('.pilihan-label');
                const kunciRadio = item.querySelector('.pilihan-kunci');
                if (label) {
                    label.textContent = 'Pilihan ' + String.fromCharCode(65 + index);
                }
                if (kunciRadio) {
                    kunciRadio.value = index;
                }
            });

            const removeButtons = document.querySelectorAll('.btn-remove-pilihan');
            const disableRemove = removeButtons.length <= 1;
            removeButtons.forEach((button) => {
                button.disabled = disableRemove;
            });

            const selectedKunci = document.querySelector('.pilihan-kunci:checked');
            if (!selectedKunci) {
                const firstKunci = document.querySelector('.pilihan-kunci');
                if (firstKunci) {
                    firstKunci.checked = true;
                }
            }
        }

        function createPilihanItem(value = '') {
            const wrapper = document.createElement('div');
            wrapper.className = 'border rounded p-3 mb-3 pilihan-item';
            wrapper.innerHTML = `
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="d-flex align-items-center gap-3">
                        <span class="badge badge-primary light pilihan-label">Pilihan</span>
                        <div class="form-check mb-0">
                            <input class="form-check-input pilihan-kunci" type="radio" name="kunci_index" value="0">
                            <label class="form-check-label">Kunci Jawaban</label>
                        </div>
                    </div>
                    <button type="button" class="btn btn-danger btn-xs btn-remove-pilihan">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
                <textarea class="form-control js-editor pilihan-input" name="pilihan[]" rows="3" required></textarea>
            `;

            const textarea = wrapper.querySelector('textarea');
            textarea.value = value;
            return wrapper;
        }

        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.js-editor').forEach((element) => initEditor(element));
            refreshPilihanLabel();

            const pilihanWrapper = document.getElementById('pilihan-wrapper');
            const addButton = document.getElementById('btn-add-pilihan');

            addButton.addEventListener('click', () => {
                const item = createPilihanItem('');
                pilihanWrapper.appendChild(item);
                initEditor(item.querySelector('.js-editor'));
                refreshPilihanLabel();
            });

            pilihanWrapper.addEventListener('click', (event) => {
                const targetButton = event.target.closest('.btn-remove-pilihan');
                if (!targetButton) {
                    return;
                }

                const item = targetButton.closest('.pilihan-item');
                if (!item) {
                    return;
                }

                if (document.querySelectorAll('.pilihan-item').length <= 1) {
                    return;
                }

                const textarea = item.querySelector('.js-editor');
                destroyEditor(textarea);
                item.remove();
                refreshPilihanLabel();
            });

            document.getElementById('form-soal-pmb').addEventListener('submit', () => {
                document.querySelectorAll('.js-editor').forEach((textarea) => {
                    const editor = editorInstances.get(textarea);
                    if (editor) {
                        textarea.value = editor.getData();
                    }
                });
            });
        });
    </script>
@endsection
