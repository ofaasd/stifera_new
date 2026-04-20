@extends('layouts.default', ['CurrentPage' => $CurrentPage])

@section('content')
    <div class="content-body">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <div class="mb-4 pb-3 d-flex gap-2">
                        <a href="{{ url('simpeg/absensi/jam_kerja_master') }}" class="btn btn-primary">
                            <i class="fa-solid fa-arrow-left me-1"></i> Kembali
                        </a>
                        <a href="{{ url('simpeg/absensi/jam_kerja_master/' . $master->id . '/detail/create') }}" class="btn btn-success btn-round">
                            <i class="fa-solid fa-plus me-1"></i> Tambah Detail
                        </a>
                    </div>

                    <div class="filter cm-content-box box-primary">
                        <div class="content-title SlideToolHeader">
                            <div class="cpa">
                                <i class="fa-solid fa-file-lines me-1"></i>{{ $title }} - {{ $master->judul }}
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
                                    <table id="order-table" class="table table-striped table-bordered nowrap">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Pegawai</th>
                                                <th>Senin</th>
                                                <th>Selasa</th>
                                                <th>Rabu</th>
                                                <th>Kamis</th>
                                                <th>Jumat</th>
                                                <th>Sabtu</th>
                                                <th>Minggu</th>
                                                <th>Status</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($detailList as $row)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>
                                                        <div><strong>{{ $row->nama }}</strong></div>
                                                        <small>{{ $row->email }}</small>
                                                    </td>
                                                    <td>{{ $row->jam_senin_mulai ? \Carbon\Carbon::parse($row->jam_senin_mulai)->format('H:i') : '-' }} - {{ $row->jam_senin_selesai ? \Carbon\Carbon::parse($row->jam_senin_selesai)->format('H:i') : '-' }}</td>
                                                    <td>{{ $row->jam_selasa_mulai ? \Carbon\Carbon::parse($row->jam_selasa_mulai)->format('H:i') : '-' }} - {{ $row->jam_selasa_selesai ? \Carbon\Carbon::parse($row->jam_selasa_selesai)->format('H:i') : '-' }}</td>
                                                    <td>{{ $row->jam_rabu_mulai ? \Carbon\Carbon::parse($row->jam_rabu_mulai)->format('H:i') : '-' }} - {{ $row->jam_rabu_selesai ? \Carbon\Carbon::parse($row->jam_rabu_selesai)->format('H:i') : '-' }}</td>
                                                    <td>{{ $row->jam_kamis_mulai ? \Carbon\Carbon::parse($row->jam_kamis_mulai)->format('H:i') : '-' }} - {{ $row->jam_kamis_selesai ? \Carbon\Carbon::parse($row->jam_kamis_selesai)->format('H:i') : '-' }}</td>
                                                    <td>{{ $row->jam_jumat_mulai ? \Carbon\Carbon::parse($row->jam_jumat_mulai)->format('H:i') : '-' }} - {{ $row->jam_jumat_selesai ? \Carbon\Carbon::parse($row->jam_jumat_selesai)->format('H:i') : '-' }}</td>
                                                    <td>{{ $row->jam_sabtu_mulai ? \Carbon\Carbon::parse($row->jam_sabtu_mulai)->format('H:i') : '-' }} - {{ $row->jam_sabtu_selesai ? \Carbon\Carbon::parse($row->jam_sabtu_selesai)->format('H:i') : '-' }}</td>
                                                    <td>{{ $row->jam_minggu_mulai ? \Carbon\Carbon::parse($row->jam_minggu_mulai)->format('H:i') : '-' }} - {{ $row->jam_minggu_selesai ? \Carbon\Carbon::parse($row->jam_minggu_selesai)->format('H:i') : '-' }}</td>
                                                    <td>
                                                        @if((int) $row->status === 1)
                                                            <span class="badge badge-success light">Aktif</span>
                                                        @else
                                                            <span class="badge badge-danger light">Tidak Aktif</span>
                                                        @endif
                                                    </td>
                                                    <td class="d-flex gap-1">
                                                        <a href="{{ url('simpeg/absensi/jam_kerja_master/' . $master->id . '/detail/' . $row->id . '/edit') }}" class="btn btn-success btn-sm" title="Edit">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                        <form action="{{ url('simpeg/absensi/jam_kerja_master/' . $master->id . '/detail/' . $row->id) }}" method="POST" onsubmit="return confirm('Yakin hapus detail jam kerja ini?')">
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
            });
        });
    </script>
@endsection
