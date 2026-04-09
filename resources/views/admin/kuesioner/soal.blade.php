@extends('layouts.default', ['CurrentPage' => $CurrentPage])

@section('content')
<div class="content-body">
    <div class="container">
        <div class="row">
            <div class="col-xl-12">
                <div class="mb-4 pb-3 d-flex gap-2 flex-wrap">
                    <a href="{{ url('akademik/kuesioner') }}" class="btn btn-primary">
                        <i class="fa-solid fa-arrow-left me-1"></i> Kembali
                    </a>
                </div>

                @if(session('status'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @php
                    $jenisLabel = (int) $tahun->jenis === 1 ? 'Ganjil' : ((int) $tahun->jenis === 2 ? 'Genap' : '-');
                    $tipeLabel = (int) $tahun->tipe_mhs === 2 ? 'RPL' : 'Reguler';
                @endphp

                <div class="filter cm-content-box box-primary mb-4">
                    <div class="content-title SlideToolHeader">
                        <div class="cpa">
                            <i class="fa-solid fa-plus me-1"></i>Tambah Soal Kuesioner
                        </div>
                        <div class="tools">
                            <a href="javascript:void(0);" class="expand handle"><i class="fal fa-angle-down"></i></a>
                        </div>
                    </div>
                    <div class="cm-content-body form excerpt">
                        <div class="card-body pb-4">
                            <div class="mb-3">
                                <span class="badge bg-light text-dark">TA: {{ $tahun->awal }}/{{ $tahun->akhir }} ({{ $jenisLabel }}) - {{ $tipeLabel }}</span>
                            </div>
                            @if($soalList->isEmpty())
                                <form method="POST" action="{{ url('akademik/kuesioner/soal/' . $tahun->id . '/duplicate') }}" class="mb-3" onsubmit="return confirm('Duplicate soal dari semester sebelumnya?')">
                                    @csrf
                                    <button type="submit" class="btn btn-info">
                                        <i class="fa fa-copy me-1"></i> Duplicate Soal Dari Semester Sebelumnya
                                    </button>
                                </form>
                            @endif
                            <form method="POST" action="{{ url('akademik/kuesioner/soal/' . $tahun->id) }}">
                                @csrf
                                <div class="row">
                                    <div class="col-md-2 mb-3">
                                        <label class="form-label">No Soal</label>
                                        <input type="text" name="no_soal" class="form-control" value="{{ old('no_soal') }}" required>
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label class="form-label">Kategori</label>
                                        <select name="category" class="form-select" required>
                                            @foreach($categoryOptions as $key => $label)
                                                <option value="{{ $key }}" {{ (int) old('category', 1) === (int) $key ? 'selected' : '' }}>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-8 mb-3">
                                        <label class="form-label">Pertanyaan</label>
                                        <input type="text" name="soal" class="form-control" value="{{ old('soal') }}" required>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-success">
                                    <i class="fa fa-save me-1"></i> Simpan Soal
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="filter cm-content-box box-primary">
                    <div class="content-title SlideToolHeader">
                        <div class="cpa">
                            <i class="fa-solid fa-list me-1"></i>{{ $title }}
                        </div>
                        <div class="tools">
                            <a href="javascript:void(0);" class="expand handle"><i class="fal fa-angle-down"></i></a>
                        </div>
                    </div>
                    <div class="cm-content-body form excerpt">
                        <div class="card-body pb-4">
                            <div class="table-responsive">
                                <table id="table-soal-kuesioner" class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>No Soal</th>
                                            <th>Kategori</th>
                                            <th>Pertanyaan</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($soalList as $idx => $row)
                                            <tr>
                                                <td>{{ $idx + 1 }}</td>
                                                <td>{{ $row->no_soal }}</td>
                                                <td>{{ $categoryOptions[(int) $row->category] ?? '-' }}</td>
                                                <td>{{ $row->soal }}</td>
                                                <td>
                                                    <div class="d-flex gap-1">
                                                        <button type="button"
                                                            class="btn btn-warning btn-sm btn-edit-soal"
                                                            data-id="{{ $row->id }}"
                                                            data-no-soal="{{ $row->no_soal }}"
                                                            data-category="{{ $row->category }}"
                                                            data-soal="{{ $row->soal }}"
                                                            data-url="{{ url('akademik/kuesioner/soal/' . $tahun->id . '/' . $row->id) }}">
                                                            <i class="fa fa-edit"></i>
                                                        </button>
                                                        <form method="POST" action="{{ url('akademik/kuesioner/soal/' . $tahun->id . '/' . $row->id) }}" onsubmit="return confirm('Yakin hapus soal ini?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="modal-edit-soal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <form id="form-edit-soal" method="POST" action="#">
                                @csrf
                                @method('PUT')
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Soal Kuesioner</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label">No Soal</label>
                                            <input type="text" id="edit-no-soal" name="no_soal" class="form-control" required>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label">Kategori</label>
                                            <select id="edit-category" name="category" class="form-select" required>
                                                @foreach($categoryOptions as $key => $label)
                                                    <option value="{{ $key }}">{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label">Pertanyaan</label>
                                            <textarea id="edit-soal" name="soal" class="form-control" rows="4" required></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary">Update</button>
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
        $('#table-soal-kuesioner').DataTable({
            responsive: true,
            pageLength: 25,
            order: [[1, 'asc']],
            columnDefs: [{ orderable: false, targets: [0, 4] }]
        });

        $(document).on('click', '.btn-edit-soal', function () {
            const $btn = $(this);
            $('#form-edit-soal').attr('action', $btn.data('url'));
            $('#edit-no-soal').val($btn.data('no-soal'));
            $('#edit-category').val($btn.data('category'));
            $('#edit-soal').val($btn.data('soal'));

            const modal = new bootstrap.Modal(document.getElementById('modal-edit-soal'));
            modal.show();
        });
    });
</script>
@endsection
