@extends('layouts.default', ['CurrentPage' => $CurrentPage])

@section('content')
    <div class="content-body">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <div class="mb-4 pb-3">
                        <a href="{{ url('master/kurikulum/create') }}" class="btn btn-success btn-round">
                            <i class="fa-solid fa-plus me-1"></i> Tambah Kurikulum
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
                            @if(session('status'))
                                <div class="alert alert-success alert-dismissible fade show mx-3 mt-3">
                                    {{ session('status') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif
                            @if(session('error'))
                                <div class="alert alert-danger alert-dismissible fade show mx-3 mt-3">
                                    {{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif
                            <div class="card-body pb-4">
                                <table id="order-table" class="table table-striped table-bordered nowrap">
                                    <thead>
                                        <tr>
                                            <th width="5">No</th>
                                            <th>Kode Kurikulum</th>
                                            <th>Program Studi</th>
                                            <th>Tahun Ajar</th>
                                            <th>Angkatan</th>
                                            <th>Status</th>
                                            <th>Log Update</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($kurikulum as $row)
                                            <tr>
                                                <td>{{ $no++ }}</td>
                                                <td>{{ $row->kode_kurikulum }}</td>
                                                <td>{{ $row->nama_jurusan ?? $row->progdi }}</td>
                                                <td>{{ $row->thn_ajar }}</td>
                                                <td>{{ $row->angkatan }}</td>
                                                <td>
                                                    @if($row->status == 1)
                                                        <span class="badge badge-success light">Aktif</span>
                                                    @else
                                                        <span class="badge badge-danger light">Tidak Aktif</span>
                                                    @endif
                                                </td>
                                                <td>{{ $row->log_update ? \Carbon\Carbon::parse($row->log_update)->format('d-m-Y H:i') : '-' }}</td>
                                                <td>
                                                    <a href="{{ url('master/kurikulum/' . str_replace('=','',base64_encode(base64_encode($row->id)))) }}/edit"
                                                        class="btn btn-success btn-sm" title="Edit">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <a href="{{ url('master/kurikulum/delete/' . str_replace('=','',base64_encode(base64_encode($row->id)))) }}"
                                                        onclick="return confirm('Yakin hapus data kurikulum ini?')"
                                                        class="btn btn-danger btn-sm" title="Hapus">
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>No</th>
                                            <th>Kode Kurikulum</th>
                                            <th>Program Studi</th>
                                            <th>Tahun Ajar</th>
                                            <th>Angkatan</th>
                                            <th>Status</th>
                                            <th>Log Update</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </tfoot>
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
            $('#order-table').DataTable({
                responsive: true,
            });
        });
    </script>
@endsection
