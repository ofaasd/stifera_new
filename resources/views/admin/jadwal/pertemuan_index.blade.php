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

                <div class="mb-3">
                    <a href="{{ url('master/jadwal') }}" class="btn btn-light">
                        <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Jadwal
                    </a>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title"><i class="fa-solid fa-list-check me-1"></i>{{ $title }}</h4>
                    </div>
                    <div class="card-body pb-4">
                            <div class="table-responsive">
                                <table id="table-setting-pertemuan" class="table table-bordered table-striped">
                                    <thead class="bg-primary text-white text-center">
                                        <tr>
                                            <th>No</th>
                                            <th>Kode</th>
                                            <th style="max-width: 200px; white-space: normal;">Nama</th>
                                            <th style="max-width: 200px; white-space: normal;">Pengampu</th>
                                            <th style="max-width: 150px; white-space: normal;">Hari, Jam</th>
                                            <th>RPS / KP</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($jadwalList as $idx => $row)
                                            <tr>
                                                <td>{{ $idx + 1 }}</td>
                                                <td>{{ $row->kode_mata_kuliah }}</td>
                                                <td style="white-space: normal; word-break: break-word; max-width: 200px;">{{ $row->nama_mata_kuliah ?? '-' }}</td>
                                                <td style="white-space: normal; word-break: break-word; max-width: 200px;">{{ trim($row->nama_dosen ?? '-') }}</td>
                                                <td style="white-space: normal; word-break: break-word; max-width: 150px;">{{ $row->hari }}, {{ $row->sesi }} {{ $row->ruang }}</td>
                                                <td>
                                                    @if(!empty($row->rps))
                                                        <a href="{{ asset('assets/files/' . $row->rps) }}" class="badge badge-success" target="_blank">Ada</a>
                                                    @else
                                                        <span class="badge badge-danger">Kosong</span>
                                                    @endif

                                                    @if(!empty($row->kp))
                                                        <a href="{{ asset('assets/files/' . $row->kp) }}" class="badge badge-success" target="_blank">Ada</a>
                                                    @else
                                                        <span class="badge badge-danger">Kosong</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ url('master/pertemuan/' . $row->id) }}" class="btn btn-success btn-sm">
                                                        Set Pertemuan
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center">Belum ada data jadwal.</td>
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
            pageLength: 25,
            language: {
                paginate: {
                    next: '>',
                    previous: '<'
                },
                lengthMenu: "Tampilkan _MENU_ data per halaman",
                zeroRecords: "Data tidak ditemukan",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                infoEmpty: "Data kosong",
                infoFiltered: "(disaring dari _MAX_ total entri)",
                search: "Cari:"
            }
        });
    });
</script>
@endsection
