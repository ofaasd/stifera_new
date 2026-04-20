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
                            <i class="fa-solid fa-file-spreadsheet me-2"></i>{{ $title }}
                        </div>
                        <div class="tools">
                            <a href="javascript:void(0);" class="expand handle"><i class="fal fa-angle-down"></i></a>
                        </div>
                    </div>
                    <div class="cm-content-body">
                        <div class="card-body">
                            <p class="text-muted mb-4">Pilih tahun ajaran lalu klik Preview untuk menampilkan data jadwal.</p>

                            <form method="GET" action="{{ route('data-support.krs-per-ta') }}">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Tahun Ajaran</label>
                                            <select class="form-select" name="id_tahun_ajaran">
                                                @foreach($tahunAjaranList as $ta)
                                                    @php
                                                        $jenis = ((int) $ta->jenis === 1) ? 'Ganjil' : (((int) $ta->jenis === 2) ? 'Genap' : 'Antara');
                                                    @endphp
                                                    <option value="{{ $ta->id }}" {{ (int) $selectedTahunAjaran === (int) $ta->id ? 'selected' : '' }}>{{ $ta->awal }}/{{ $ta->akhir }} - {{ $jenis }} - ({{($ta->tipe_mhs==1)?'Reguler':'RPL'}})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="col-12">
                                        <button type="submit" name="preview" value="1" class="btn btn-info text-white">
                                            <i class="fa-solid fa-eye me-1"></i> Preview
                                        </button>
                                        <button type="submit" formaction="{{ route('data-support.krs-per-ta.export-excel') }}" class="btn btn-success ms-2">
                                            <i class="fa-solid fa-file-excel me-1"></i> Download Excel
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <hr class="my-4">

                            @if($isPreview)
                                <div class="mb-3" id="table-krs-per-ta-buttons"></div>
                                <div class="table-responsive">
                                    <table id="table-krs-per-ta" class="display table table-bordered table-striped w-100">
                                        <thead>
                                            <tr>
                                                @foreach($tableColumns as $column)
                                                    <th>{{ $column }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($rows as $row)
                                                <tr>
                                                    @foreach($tableColumns as $column)
                                                        <td>{{ $row->{$column} ?? '' }}</td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="fa-solid fa-circle-info me-2"></i>
                                    <strong>Info:</strong> Pilih tahun ajaran dan klik Preview untuk menampilkan jadwal dari master_jadwal dan master_jadwal_temp. Tipe Mahasiswa: 1=Reguler, 2=RPL.
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
        if ($('#table-krs-per-ta').length) {
            const exportButtons = [];

            if ($.fn.dataTable && $.fn.dataTable.Buttons) {
                exportButtons.push('copy', 'csv', 'excel');

                if (typeof pdfMake !== 'undefined') {
                    exportButtons.push({
                        extend: 'pdf',
                        title: 'KRS Per TA',
                        orientation: 'landscape',
                        pageSize: 'A4'
                    });
                }
            }

            const table = $('#table-krs-per-ta').DataTable({
                scrollX: true,
                pageLength: 25,
                dom: exportButtons.length ? 'Bfrtip' : 'frtip',
                buttons: exportButtons
            });

            if (exportButtons.length) {
                table.buttons().container().appendTo('#table-krs-per-ta-buttons');
            } else {
                $('#table-krs-per-ta-buttons').html('<div class="alert alert-warning py-2 mb-0">Plugin export DataTables tidak termuat. Silakan refresh halaman.</div>');
            }
        }
    });
</script>
@endsection
