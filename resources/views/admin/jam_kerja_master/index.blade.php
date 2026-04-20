@extends('layouts.default', ['CurrentPage' => $CurrentPage])

@section('content')
    <div class="content-body">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <div class="mb-4 pb-3">
                        <a href="{{ url('simpeg/absensi/jam_kerja_master/create') }}" class="btn btn-success btn-round">
                            <i class="fa-solid fa-plus me-1"></i> Tambah Jam Kerja Dosen
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
                                            <th>Judul</th>
                                            <th>Tanggal Mulai</th>
                                            <th>Tanggal Selesai</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($jamKerjaList as $row)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $row->judul }}</td>
                                                <td>{{ $row->mulai ? \Carbon\Carbon::parse($row->mulai)->format('d-m-Y') : '-' }}</td>
                                                <td>{{ $row->selesai ? \Carbon\Carbon::parse($row->selesai)->format('d-m-Y') : '-' }}</td>
                                                <td>
                                                    @if((int) $row->status === 1)
                                                        <span class="badge badge-success light">Aktif</span>
                                                    @else
                                                        <span class="badge badge-danger light">Tidak Aktif</span>
                                                    @endif
                                                </td>
                                                <td class="d-flex gap-1">
                                                    <a href="{{ url('simpeg/absensi/jam_kerja_master/' . $row->id . '/detail') }}" class="btn btn-info btn-sm" title="Lihat Detail">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <a href="{{ url('simpeg/absensi/jam_kerja_master/' . $row->id . '/edit') }}" class="btn btn-success btn-sm" title="Edit">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <form action="{{ url('simpeg/absensi/jam_kerja_master/' . $row->id) }}" method="POST" onsubmit="return confirm('Yakin hapus data jam kerja dosen ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>No</th>
                                            <th>Judul</th>
                                            <th>Jam Mulai</th>
                                            <th>Jam Selesai</th>
                                            <th>Status</th>
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