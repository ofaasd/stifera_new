@extends('layouts.default', ['CurrentPage' => $CurrentPage])

@section('content')
    <style>
    .table td { white-space: normal !important; word-break: break-word !important; }
    .table th { white-space: nowrap !important; }
    .table th:first-child { width: 50px; }
    .table th:last-child { min-width: 120px; }
</style>
<div class="content-body">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <div class="mb-4 pb-3">
							<a href="{{ url('pegawai/MeninggalkanPekerjaan/create') }}" class="btn btn-primary bg-primary">
								<i class="fa-solid fa-plus me-1"></i> Tambah Data
							</a>
						</div>

                    <div class="card mb-3">
                        <div class="card-header border-bottom"><h4 class="card-title">
                                <i class="fa-solid fa-filter me-1"></i>Filter Tanggal
                            </h4></div><div class="card-body p-3">
                            <form method="GET" action="{{ url('pegawai/MeninggalkanPekerjaan') }}" class="row g-2 align-items-end">
                                <div class="col-md-3">
                                    <label class="form-label">Tanggal Awal</label>
                                    <input type="date" class="form-control" name="tanggal_awal" value="{{ $tanggal_awal }}" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Tanggal Akhir</label>
                                    <input type="date" class="form-control" name="tanggal_akhir" value="{{ $tanggal_akhir }}" required>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                                </div>
                                <div class="col-md-2">
                                    <a href="{{ url('pegawai/MeninggalkanPekerjaan') }}" class="btn btn-light w-100">Reset</a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header border-bottom"><h4 class="card-title">
                                <i class="fa-solid fa-file-lines me-1"></i>{{ $title }}
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

                            <div class="table-responsive">
                                <table id="order-table" class="table table-bordered table-striped">
                                    <thead class="bg-primary text-white text-center">
                                        <tr>
                                            <th>No</th>
                                            @if($isPimpinan ?? false)
                                                <th>Pegawai</th>
                                            @endif
                                            <th>Tanggal Mulai</th>
                                            <th>Waktu Mulai</th>
                                            <th>Tanggal Selesai</th>
                                            <th>Waktu Selesai</th>
                                            <th>Keperluan</th>
                                            <th>Izin KA Jenjang</th>
                                            <th>Izin Manager SDM</th>
                                            <th>Kategori</th>
                                            <th>Lampiran</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($izinList as $row)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                @if($isPimpinan ?? false)
                                                    <td>{{ $row->nama_pengirim ?? '-' }}</td>
                                                @endif
                                                <td>{{ $row->tanggal ? \Carbon\Carbon::parse($row->tanggal)->format('Y-m-d') : '-' }}</td>
                                                <td>{{ $row->waktu_mulai ? \Carbon\Carbon::parse($row->waktu_mulai)->format('H:i:s') : '-' }}</td>
                                                <td>{{ $row->tanggal_selesai ? \Carbon\Carbon::parse($row->tanggal_selesai)->format('Y-m-d') : '-' }}</td>
                                                <td>{{ $row->waktu_selesai ? \Carbon\Carbon::parse($row->waktu_selesai)->format('H:i:s') : '-' }}</td>
                                                <td>{{ $row->keperluan }}</td>
                                                <td>{{ (int) $row->izin_ka_jenjang === 1 ? 'Disetujui' : 'Belum Disetujui' }}</td>
                                                <td>{{ (int) $row->izin_mgr_sdm === 1 ? 'Disetujui' : 'Belum Disetujui' }}</td>
                                                <td>{{ $row->kategori_nama ?? '-' }}</td>
                                                <td>
                                                    @if(!empty($row->lampiran))
                                                        <a href="{{ asset('assets/izin_meninggalkan_pekerjaan/' . $row->lampiran) }}" target="_blank" class="btn btn-info btn-sm" title="Lihat Lampiran">
                                                            <i class="fa fa-eye"></i> Lihat
                                                        </a>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td class="d-flex gap-1">
                                                    @if($isPimpinan ?? false)
                                                        <form action="{{ route('pegawai.meninggalkan-pekerjaan.validasi-toggle', $row->id) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="btn {{ $row->izin_ka_jenjang == 1 ? 'btn-warning' : 'btn-success' }} btn-sm" title="{{ $row->izin_ka_jenjang == 1 ? 'Batal Setuju' : 'Setujui' }}">
                                                                <i class="fa {{ $row->izin_ka_jenjang == 1 ? 'fa-times' : 'fa-check' }}"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                    <a href="{{ url('pegawai/MeninggalkanPekerjaan/' . $row->id . '/edit') }}" class="btn btn-success btn-sm" title="Edit">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <form action="{{ url('pegawai/MeninggalkanPekerjaan/' . $row->id) }}" method="POST" onsubmit="return confirm('Yakin hapus data izin ini?')">
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
@endsection

@section('local-js')
    <script>
        $(document).ready(function () {
            $('#order-table').DataTable({
            });
        });
    </script>
@endsection
