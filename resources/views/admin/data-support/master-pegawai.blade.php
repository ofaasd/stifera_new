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
                            <i class="fa-solid fa-users-gear me-2"></i>{{ $title }}
                        </div>
                        <div class="tools">
                            <a href="javascript:void(0);" class="expand handle"><i class="fal fa-angle-down"></i></a>
                        </div>
                    </div>
                    <div class="cm-content-body">
                        <div class="card-body">
                            <form method="GET" action="{{ route('data-support.master-pegawai') }}">
                                <div class="mb-3">
                                    <label class="form-label">Posisi Pegawai :</label>
                                    <select class="form-select" name="kd_posisi_pegawai">
                                        <option value="">Semua</option>
                                        @foreach($posisiList as $posisi)
                                            <option value="{{ $posisi->kode }}" {{ $selectedPosisi === (string) $posisi->kode ? 'selected' : '' }}>{{ $posisi->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Jabatan Fungsional :</label>
                                    <select class="form-select" name="jabatan_fungsional">
                                        <option value="">Semua</option>
                                        @foreach($jabatanFungsionalList as $jabatanFungsional)
                                            <option value="{{ $jabatanFungsional->jabatan }}" {{ $selectedJabatanFungsional === (string) $jabatanFungsional->jabatan ? 'selected' : '' }}>{{ $jabatanFungsional->jabatan }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Jenis Kelamin :</label>
                                    <select class="form-select" name="jenis_kelamin">
                                        <option value="">Semua</option>
                                        @foreach($jenisKelaminList as $jkKey => $jkLabel)
                                            <option value="{{ $jkKey }}" {{ $selectedJenisKelamin === (string) $jkKey ? 'selected' : '' }}>{{ $jkLabel }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Jenjang :</label>
                                    <select class="form-select" name="jenjang">
                                        <option value="">Semua</option>
                                        @foreach($jenjangList as $jenjang)
                                            <option value="{{ $jenjang }}" {{ $selectedJenjang === (string) $jenjang ? 'selected' : '' }}>{{ $jenjang }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Program Studi :</label>
                                    <select class="form-select" name="id_progdi">
                                        <option value="0">Semua</option>
                                        @foreach($programStudiList as $prodi)
                                            <option value="{{ $prodi->id }}" {{ (int) $selectedProgramStudi === (int) $prodi->id ? 'selected' : '' }}>{{ $prodi->nama_jurusan }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Status :</label>
                                    <select class="form-select" name="status_pegawai">
                                        <option value="">Semua</option>
                                        @foreach($statusList as $status)
                                            <option value="{{ $status }}" {{ $selectedStatus === (string) $status ? 'selected' : '' }}>{{ $status }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <button type="submit" name="preview" value="1" class="btn btn-info text-white">Preview</button>
                                    <button type="submit" formaction="{{ route('data-support.master-pegawai.export-excel') }}" class="btn btn-success ms-2">
                                        <i class="fa-solid fa-file-excel me-1"></i> Download Excel
                                    </button>
                                </div>
                            </form>

                            @if($isPreview)
                                <div class="table-responsive mt-3">
                                    <table id="table-master-pegawai" class="display table table-bordered table-striped w-100">
                                        <thead>
                                            <tr>
                                                @foreach($tableColumns as $column)
                                                    <th>{{ $column }}</th>
                                                @endforeach
                                                <th>nama_pegawai</th>
                                                <th>nama_posisi_pegawai</th>
                                                <th>nama_jenis_pegawai</th>
                                                <th>nama_jabatan_fungsional</th>
                                                <th>nama_jenjang</th>
                                                <th>nama_program_studi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($rows as $row)
                                                <tr>
                                                    @foreach($tableColumns as $column)
                                                        <td>{{ $row->{$column} }}</td>
                                                    @endforeach
                                                    <td>{{ $row->nama_pegawai }}</td>
                                                    <td>{{ $row->nama_posisi_pegawai }}</td>
                                                    <td>{{ $row->nama_jenis_pegawai }}</td>
                                                    <td>{{ $row->nama_jabatan_fungsional }}</td>
                                                    <td>{{ $row->nama_jenjang }}</td>
                                                    <td>{{ $row->nama_program_studi }}</td>
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
        if ($('#table-master-pegawai').length) {
            const buttons = [
                { extend: 'copy', title: 'Master Pegawai' },
                { extend: 'csv', title: 'Master Pegawai' },
                { extend: 'excel', title: 'Master Pegawai' }
            ];

            if (typeof pdfMake !== 'undefined') {
                buttons.push({ extend: 'pdf', title: 'Master Pegawai', orientation: 'landscape', pageSize: 'A4' });
            }

            if ($.fn.dataTable.ext.buttons.print) {
                buttons.push({ extend: 'print', title: 'Master Pegawai' });
            }

            $('#table-master-pegawai').DataTable({
                scrollX: true,
                pageLength: 25,
                dom: 'Bfrtip',
                buttons: buttons
            });
        }
    });
</script>
@endsection
