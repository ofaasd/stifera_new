@extends('layouts.default', ['CurrentPage' => $CurrentPage])

@section('content')
    <div class="content-body">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <div class="mb-4 pb-3">
                        <a href="{{ route('masukan.create') }}" class="btn btn-success btn-round">
                            <i class="fa-solid fa-plus me-1"></i> Tambah Masukan
                        </a>
                    </div>
                    <div class="filter cm-content-box box-primary">
                        <div class="content-title SlideToolHeader">
                            <div class="cpa">
                                <i class="fa-solid fa-comments me-1"></i>{{ $title }}
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
                                <div class="table-responsive">
                                <table id="order-table" class="table table-striped table-bordered" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th width="40">No</th>
                                            <th width="110">NIM</th>
                                            <th>Saran / Masukan</th>
                                            <th width="100">Tanggal</th>
                                            <th width="90">Status</th>
                                            <th>Tindak Lanjut</th>
                                            <th width="120">Tanggal Tanggapan</th>
                                            <th width="90">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($masukanList as $row)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $row->nim }}</td>
                                                <td style="white-space:normal;word-break:break-word;">{{ $row->saran }}</td>
                                                <td>
                                                    {{ $row->tanggal ? \Carbon\Carbon::parse($row->tanggal)->format('d-m-Y') : '-' }}
                                                </td>
                                                <td>
                                                    @if($row->status === 'selesai')
                                                        <span class="badge bg-success">Selesai</span>
                                                    @elseif($row->status === 'proses')
                                                        <span class="badge bg-warning text-dark">Proses</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ $row->status ?? '-' }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ $row->tindak_lanjut ?? '-' }}</td>
                                                <td>
                                                    {{ $row->tanggal_tanggapan ? \Carbon\Carbon::parse($row->tanggal_tanggapan)->format('d-m-Y') : '-' }}
                                                </td>
                                                <td class="d-flex gap-1">
                                                    <a href="{{ route('masukan.edit', $row->id) }}" class="btn btn-success btn-sm" title="Edit">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('masukan.destroy', $row->id) }}" method="POST" onsubmit="return confirm('Yakin hapus masukan ini?')">
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
                                            <th>NIM</th>
                                            <th>Saran / Masukan</th>
                                            <th>Tanggal</th>
                                            <th>Status</th>
                                            <th>Tindak Lanjut</th>
                                            <th>Tanggal Tanggapan</th>
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
    </div>
@endsection

@section('local-js')
    <script>
        $(document).ready(function () {
            $('#order-table').DataTable({
                responsive: true,
                autoWidth: false,
                order: [[3, 'desc']],
                columnDefs: [
                    { targets: [2, 5], className: 'text-wrap' },
                ],
            });
        });
    </script>
@endsection
