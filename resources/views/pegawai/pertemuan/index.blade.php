@extends('layouts.default', ['CurrentPage' => $CurrentPage])

@section('content')
<div class="content-body">
    <div class="container">
        <div class="row">
            <div class="col-xl-12">
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

                <div class="card">
                    <div class="card-header border-0 pb-0">
                        <h4 class="card-title"><i class="fa-solid fa-list-check me-1"></i>{{ $title }}</h4>
                    </div>
                    <div class="card-body pb-4">
                        <style>
                            #table-setting-pertemuan th, #table-setting-pertemuan td {
                                white-space: normal !important;
                                word-break: break-word;
                                vertical-align: top;
                            }
                        </style>
                        <div class="table-responsive">
                            <table id="table-setting-pertemuan" class="table table-striped table-bordered" style="table-layout: fixed; width: 100%;">
                                <thead>
                                    <tr>
                                        <th style="width: 5%">No</th>
                                        <th style="width: 10%">Kode</th>
                                        <th style="width: 25%">Nama</th>
                                        <th style="width: 20%">Pengampu</th>
                                        <th style="width: 15%">Hari, Jam</th>
                                        <th style="width: 15%">RPS / KP</th>
                                        <th style="width: 10%">Aksi</th>
                                    </tr>
                                </thead>
                                    <tbody>
                                        @forelse($jadwalList as $idx => $row)
                                            <tr>
                                                <td>{{ $idx + 1 }}</td>
                                                <td>{{ $row->kode_mata_kuliah }}</td>
                                                <td>{{ $row->nama_mata_kuliah ?? '-' }}</td>
                                                <td>{{ trim($row->nama_dosen ?? '-') }}</td>
                                                <td>{{ $row->hari }}, {{ $row->sesi }} {{ $row->ruang }}</td>
                                                <td>
                                                    @if(!empty($row->rps))
                                                        <a href="{{ asset('assets/files/' . $row->rps) }}" class="badge badge-success d-block w-100 mb-1" target="_blank">RPS: Ada</a>
                                                    @else
                                                        <span class="badge badge-danger d-block w-100 mb-1">RPS: Kosong</span>
                                                    @endif

                                                    @if(!empty($row->kp))
                                                        <a href="{{ asset('assets/files/' . $row->kp) }}" class="badge badge-success d-block w-100" target="_blank">KP: Ada</a>
                                                    @else
                                                        <span class="badge badge-danger d-block w-100">KP: Kosong</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ url('dosen/pertemuan/' . $row->id) }}" class="btn btn-success btn-sm d-block w-100" title="Set Pertemuan">
                                                        <i class="fa-solid fa-calendar-check"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center">Belum ada data jadwal untuk Anda.</td>
                                            </tr>
                                        @endforelse
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

@section('local-js')
<script>
    $(document).ready(function () {
        $('#table-setting-pertemuan').DataTable({
            responsive: true,
            pageLength: 25,
            language: {
                paginate: {
                    next: '<i class="fa-solid fa-angle-right"></i>',
                    previous: '<i class="fa-solid fa-angle-left"></i>' 
                }
            }
        });
    });
</script>
@endsection
