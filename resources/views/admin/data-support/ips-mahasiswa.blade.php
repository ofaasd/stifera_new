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
                            <i class="fa-solid fa-chart-line me-2"></i>{{ $title }}
                        </div>
                        <div class="tools">
                            <a href="javascript:void(0);" class="expand handle"><i class="fal fa-angle-down"></i></a>
                        </div>
                    </div>
                    <div class="cm-content-body">
                        <div class="card-body">
                            <form method="GET" action="{{ route('data-support.ips-mahasiswa') }}">
                                <div class="mb-3">
                                    <label class="form-label">Tahun Angkatan :</label>
                                    <select class="form-select" name="angkatan">
                                        <option value="0">Semua Angkatan</option>
                                        @foreach($angkatanList as $angkatan)
                                            <option value="{{ $angkatan }}" {{ (int) $selectedAngkatan === (int) $angkatan ? 'selected' : '' }}>{{ $angkatan }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Program Studi :</label>
                                    <select class="form-select" name="id_program_studi">
                                        <option value="0">Semua Program Studi</option>
                                        @foreach($programStudiList as $prodi)
                                            <option value="{{ $prodi->id }}" {{ (int) $selectedProgramStudi === (int) $prodi->id ? 'selected' : '' }}>{{ $prodi->nama_jurusan }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Tahun Ajaran :</label>
                                    <select class="form-select" name="id_tahun_ajaran">
                                        @foreach($tahunAjaranList as $tahun)
                                            @php
                                                $jenisLabel = (int) $tahun->jenis === 1 ? 'Ganjil' : ((int) $tahun->jenis === 2 ? 'Genap' : 'Tidak Diketahui');
                                                $tipeLabel = (int) $tahun->tipe_mhs === 1 ? 'Reguler' : ((int) $tahun->tipe_mhs === 2 ? 'RPL' : 'Umum');
                                            @endphp
                                            <option value="{{ $tahun->id }}" {{ (int) $selectedTahunAjaran === (int) $tahun->id ? 'selected' : '' }}>
                                                {{ $tahun->awal }}-{{ $tahun->akhir }} {{ $jenisLabel }} {{ $tipeLabel }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <button type="submit" name="preview" value="1" class="btn btn-info text-white">Preview</button>
                                </div>
                            </form>

                            @if($isPreview)
                                <div class="mb-3 d-flex gap-2">
                                    <button type="button" id="btn-download-excel" class="btn btn-success">
                                        <i class="fa fa-file-excel me-1"></i> Download Excel
                                    </button>
                                    <button type="button" id="btn-download-pdf" class="btn btn-danger">
                                        <i class="fa fa-file-pdf me-1"></i> Download PDF
                                    </button>
                                </div>
                            @endif

                            @if($isPreview)
                                <div class="table-responsive">
                                    <table id="table-ips-mahasiswa" class="display table table-bordered table-striped w-100">
                                        <thead>
                                            <tr>
                                                <th>NIM</th>
                                                <th>Nama</th>
                                                <th>IPS</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($rows as $row)
                                                <tr>
                                                    <td>{{ $row->nim }}</td>
                                                    <td>{{ $row->nama }}</td>
                                                    <td>{{ number_format((float) $row->ips, 2, '.', '') }}</td>
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
        background: #11b5b8;
        border: 1px solid #11b5b8;
        color: #fff;
        border-radius: 4px;
        padding: 6px 12px;
        margin-right: 6px;
        font-size: 13px;
    }

    .dt-button:hover {
        background: #0ea4a7;
        border-color: #0c9497;
    }
</style>
@endsection

@section('local-js')
<script src="{{ asset('vendor/datatables/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/js/buttons.html5.min.js') }}"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="{{ asset('vendor/datatables/js/jszip.min.js') }}"></script>
<script src="{{ asset('vendor/amcharts/plugins/export/libs/pdfmake/pdfmake.min.js') }}"></script>
<script src="{{ asset('vendor/amcharts/plugins/export/libs/pdfmake/vfs_fonts.js') }}"></script>
<script>
    $(function () {
        if ($('#table-ips-mahasiswa').length) {
            const buttons = [
                { extend: 'copy', title: 'IPS Mahasiswa' },
                { extend: 'csv', title: 'IPS Mahasiswa' },
                { extend: 'excel', title: 'IPS Mahasiswa' }
            ];

            if (typeof pdfMake !== 'undefined') {
                buttons.push({ extend: 'pdf', title: 'IPS Mahasiswa' });
            }

            if ($.fn.dataTable.ext.buttons.print) {
                buttons.push({ extend: 'print', title: 'IPS Mahasiswa' });
            }

            const table = $('#table-ips-mahasiswa').DataTable({
                pageLength: 25,
                dom: 'Bfrtip',
                buttons: buttons
            });

            $('#btn-download-excel').on('click', function () {
                const excelBtn = table.button('.buttons-excel');
                if (excelBtn.any()) {
                    excelBtn.trigger();
                }
            });

            $('#btn-download-pdf').on('click', function () {
                const pdfBtn = table.button('.buttons-pdf');
                if (pdfBtn.any()) {
                    pdfBtn.trigger();
                }
            });
        }
    });
</script>
@endsection
