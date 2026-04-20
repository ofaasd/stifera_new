@extends('layouts.default', ['CurrentPage' => $CurrentPage])

@section('content')
    <div class="content-body">
        <div class="container">
            @if(session('status'))
                <div class="alert alert-success alert-dismissible fade show">{{ session('status') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            @endif

            <div class="row">
                <div class="col-xl-12">
                    <div class="filter cm-content-box box-primary">
                        <div class="content-title SlideToolHeader">
                            <div class="cpa">
                                <i class="fa-solid fa-file-lines me-1"></i>{{ $title }}
                            </div>
                            <div class="tools">
                                <a href="javascript:void(0);" class="expand handle"><i class="fal fa-angle-down"></i></a>
                            </div>
                        </div>
                        <div class="cm-content-body form excerpt">
                            <div class="card-body pb-4">
                                <div class="table-responsive datatables">
                                    <table id="example" class="display table">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Mata Kuliah</th>
                                                <th>Pengampu 1</th>
                                                <th>Pengampu 2</th>
                                                <th>Hari / Jam</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($temu as $a)
                                                <tr>
                                                    <td>{{ $no++ }}</td>
                                                    <td>{{ $a->nama_mata_kuliah }}</td>
                                                    <td>{{ trim($a->nama_dosen) ?: '-' }}</td>
                                                    <td>{{ trim($a->nama_dosen2) ?: '-' }}</td>
                                                    <td>{{ $a->hari . ', ' . $a->sesi . '  ' . $a->ruang }}</td>
                                                    <td>
                                                        <a href="{{ url('dosen/pertemuan/' . $a->id) }}" class="btn btn-sm btn-primary" title="Set Pertemuan">
                                                            <i class="fa fa-calendar"></i> Set Pertemuan
                                                        </a>
                                                        <a href="{{ url('dosen/presensi/tanggal/' . $a->id) }}" class="btn btn-sm btn-success" title="Set Presensi">
                                                            <i class="fa fa-check-square"></i> Set Presensi
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
