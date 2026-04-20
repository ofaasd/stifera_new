@extends('layouts.default', ['CurrentPage' => $CurrentPage])

@section('content')
<div class="content-body">
    <div class="container">
        <div class="row">
            <div class="col-xl-12">
                <div class="mb-3 pb-3">
                    <a href="{{ $backUrl }}" class="btn btn-primary">
                        <i class="fa-solid fa-arrow-left me-1"></i> Kembali
                    </a>
                </div>

                <div class="cm-content-box box-primary">
                    <div class="content-title cm-title">
                        <div class="cpa">
                            <i class="fa-solid fa-users me-2"></i>{{ $title }}
                        </div>
                        <div class="tools">
                            <a href="javascript:void(0);" class="expand handle"><i class="fal fa-angle-down"></i></a>
                        </div>
                    </div>
                    <div class="cm-content-body">
                        <div class="card-body">
                            <p class="text-muted mb-4">Pilih filter lalu klik Preview untuk menampilkan data mahasiswa lengkap.</p>

                            <form method="GET" action="{{ route('data-support.master-mahasiswa') }}">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Tahun Angkatan</label>
                                            <select class="form-select" name="angkatan">
                                                <option value="0">Semua Angkatan</option>
                                                @foreach($angkatanList as $angkatan)
                                                    <option value="{{ $angkatan }}" {{ (int) $selectedAngkatan === (int) $angkatan ? 'selected' : '' }}>{{ $angkatan }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Status Mahasiswa</label>
                                            <select class="form-select" name="status">
                                                <option value="0">Semua Status</option>
                                                @foreach($statusOptions as $statusValue => $statusLabel)
                                                    <option value="{{ $statusValue }}" {{ (int) $selectedStatus === (int) $statusValue ? 'selected' : '' }}>{{ $statusLabel }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Program Studi</label>
                                            <select class="form-select" name="id_program_studi">
                                                <option value="0">Semua Program Studi</option>
                                                @foreach($programStudiList as $prodi)
                                                    <option value="{{ $prodi->id }}" {{ (int) $selectedProgramStudi === (int) $prodi->id ? 'selected' : '' }}>{{ $prodi->nama_jurusan }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="col-12">
                                        <button type="submit" name="preview" value="1" class="btn btn-info text-white">
                                            Preview
                                        </button>
                                        <button type="submit" formaction="{{ route('data-support.master-mahasiswa.export-excel') }}" class="btn btn-success ms-2">
                                            <i class="fa-solid fa-file-excel me-1"></i> Download Excel
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <hr class="my-4">

                            @if($isPreview)
                                <div class="mb-3" id="table-master-mahasiswa-buttons"></div>
                                <div class="table-responsive">
                                    <table id="table-master-mahasiswa" class="display table table-bordered table-striped w-100">
                                        <thead>
                                            <tr>
                                                @foreach($tableColumns as $column)
                                                    <th>{{ $column }}</th>
                                                @endforeach
                                                <th>nama_program_studi</th>
                                                <th>nama_dosen_wali</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($rows as $row)
                                                <tr>
                                                    @foreach($tableColumns as $column)
                                                        <td>{{ $row->{$column} }}</td>
                                                    @endforeach
                                                    <td>{{ $row->nama_program_studi }}</td>
                                                    <td>{{ $row->nama_dosen_wali }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('local-css')
<style>
    .dt-buttons {
        margin-bottom: 12px;
    }

    .dt-button {
        background: #0d6efd;
        border: 1px solid #0d6efd;
        color: #fff;
        border-radius: 4px;
        padding: 6px 12px;
        margin-right: 6px;
        font-size: 13px;
    }

    .dt-button:hover {
        background: #0b5ed7;
        border-color: #0a58ca;
    }
</style>
@endsection

@section('local-js')
<script src="{{ asset('vendor/datatables/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/js/jszip.min.js') }}"></script>
<script src="{{ asset('vendor/amcharts/plugins/export/libs/pdfmake/pdfmake.min.js') }}"></script>
<script src="{{ asset('vendor/amcharts/plugins/export/libs/pdfmake/vfs_fonts.js') }}"></script>
<script>
    $(function () {
        if ($('#table-master-mahasiswa').length) {
            const exportButtons = [];

            if ($.fn.dataTable && $.fn.dataTable.Buttons) {
                exportButtons.push({
                    extend: 'csv',
                    title: 'Master Mahasiswa'
                });

                exportButtons.push({
                    extend: 'excel',
                    title: 'Master Mahasiswa'
                });

                if (typeof pdfMake !== 'undefined') {
                    exportButtons.push({
                        extend: 'pdf',
                        title: 'Master Mahasiswa',
                        orientation: 'landscape',
                        pageSize: 'A4'
                    });
                }
            }

            const table = $('#table-master-mahasiswa').DataTable({
                scrollX: true,
                pageLength: 25,
                dom: exportButtons.length ? 'Bfrtip' : 'frtip',
                buttons: exportButtons
            });

            if (exportButtons.length) {
                table.buttons().container().appendTo('#table-master-mahasiswa-buttons');
            } else {
                $('#table-master-mahasiswa-buttons').html('<div class="alert alert-warning py-2 mb-0">Plugin export DataTables tidak termuat. Silakan refresh halaman.</div>');
            }
        }
    });
</script>
@endsection
