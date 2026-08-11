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
                    <div class="card">
                        <div class="card-header border-0 pb-0">
                            <h4 class="card-title"><i class="fa-solid fa-file-lines me-1"></i>{{ $title }}</h4>
                        </div>
                        <div class="card-body pb-4">
                            <style>
                                #example th, #example td {
                                    white-space: normal !important;
                                    word-break: break-word;
                                    vertical-align: top;
                                }
                            </style>
                            <div class="table-responsive">
                                <table id="example" class="display table" style="table-layout: fixed; width: 100%;">
                                    <thead>
                                        <tr>
                                            <th style="width: 5%">No</th>
                                            <th style="width: 25%">Mata Kuliah</th>
                                            <th style="width: 20%">Pengampu 1</th>
                                            <th style="width: 20%">Pengampu 2</th>
                                            <th style="width: 15%">Hari / Jam</th>
                                            <th style="width: 15%">Aksi</th>
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
                                                        <i class="fa fa-calendar"></i> 
                                                    </a>
                                                    <a href="{{ url('dosen/presensi/tanggal/' . $a->id) }}" class="btn btn-sm btn-success" title="Set Presensi">
                                                        <i class="fa fa-check-square"></i> 
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
@endsection
