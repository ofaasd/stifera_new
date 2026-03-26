@extends('layouts.default', ['CurrentPage' => $CurrentPage])

@section('content')
    <div class="content-body">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <div class="filter cm-content-box box-primary">
                        <div class="content-title SlideToolHeader">
                            <div class="cpa">
                                <i class="fa-solid fa-graduation-cap me-1"></i>{{ $title }}
                            </div>
                            <div class="tools">
                                <a href="javascript:void(0);" class="expand handle">
                                    <i class="fal fa-angle-down"></i>
                                </a>
                            </div>
                        </div>
                        <div class="cm-content-body form excerpt">
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
                                            <th>Nama Dosen</th>
                                            <th>NIDN</th>
                                            <th>NIP</th>
                                            <th>Jumlah Jadwal</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($dosen as $row)
                                            <tr>
                                                <td>{{ $no++ }}</td>
                                                <td>{{ trim($row->nama_dosen) }}</td>
                                                <td>{{ $row->nidn ?? '-' }}</td>
                                                <td>{{ $row->nip_pns ?? '-' }}</td>
                                                <td>
                                                    <span class="badge badge-primary light">{{ $row->jumlah_jadwal }} Jadwal</span>
                                                </td>
                                                <td>
                                                    <a href="{{ url('master/nilai/jadwal/' . $row->id) }}"
                                                        class="btn btn-info btn-sm"
                                                        title="Lihat Jadwal">
                                                        <i class="fa fa-list me-1"></i> 
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

@section('local-js')
    <script>
        $(document).ready(function () {
            $('#order-table').DataTable({ responsive: true });
        });
    </script>
@endsection
