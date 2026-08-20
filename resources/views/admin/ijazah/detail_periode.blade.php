@extends('layouts.default')

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <div class="row page-titles">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.ijazah.index') }}">Manajemen Ijazah & Transkrip</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Daftar Mahasiswa - {{ $periode->nama_periode }}</a></li>
            </ol>
        </div>

        @if(session('success'))
            <div class="alert alert-success solid alert-dismissible fade show">
                <strong>Sukses!</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header border-0 pb-0">
                        <h4 class="card-title">Daftar Mahasiswa Lulus Yudisium</h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info py-2">
                            <i class="fas fa-info-circle me-1"></i> Data di bawah otomatis sinkron dengan mahasiswa yang telah ditandai "LULUS YUDISIUM" oleh admin.
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="example1">
                                <thead>
                                    <tr>
                                        <th>Mahasiswa</th>
                                        <th>Program Studi</th>
                                        <th>No. Ijazah / PIN</th>
                                        <th>No. Transkrip</th>
                                        <th>Predikat</th>
                                        <th style="text-align: left !important;" class="text-start">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dokumens as $d)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="ms-2">
                                                    <h6 class="mb-0 font-weight-bold">{{ $d->mahasiswa->nama ?? '-' }}</h6>
                                                    <span>{{ $d->mahasiswa->nim ?? '-' }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $d->mahasiswa->id_program_studi == 1 ? 'D-III Farmasi' : 'S-1 Farmasi' }}</td>
                                        <td>
                                            <strong>Ijazah:</strong> {{ $d->no_ijazah ?? 'Belum ada' }}<br>
                                            <strong>PIN Dikti:</strong> {{ $d->pin_dikti ?? 'Belum ada' }}
                                        </td>
                                        <td>{{ $d->no_transkrip ?? 'Belum ada' }}</td>
                                        <td>{{ $d->kategori_kelulusan }}</td>
                                        <td>
                                            <div class="d-flex flex-column gap-2" style="max-width: 180px;">
                                                <a class="btn btn-primary btn-sm shadow" href="{{ route('admin.ijazah.editPenomoran', $d->id) }}"><i class="fas fa-edit me-1"></i> Penomoran</a>
                                                <a class="btn btn-success btn-sm shadow" href="{{ route('admin.ijazah.cetak', $d->id) }}" target="_blank"><i class="fas fa-certificate me-1"></i> Ijazah</a>
                                                <a class="btn btn-warning btn-sm shadow text-dark" href="{{ route('admin.ijazah.transkrip', ['id' => $d->id, 'jenis' => 'depan']) }}" target="_blank"><i class="fas fa-file-alt me-1"></i> T. Depan</a>
                                                <a class="btn btn-warning btn-sm shadow text-dark" href="{{ route('admin.ijazah.transkrip', ['id' => $d->id, 'jenis' => 'belakang']) }}" target="_blank"><i class="fas fa-file-alt me-1"></i> T. Belakang</a>
                                            </div>
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
    $(document).ready(function() {
        if ($.fn.DataTable) {
            $('#example1').DataTable({
                columnDefs: [
                    { targets: -1, orderable: false, className: "text-start" }
                ],
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ hingga _END_ dari _TOTAL_ entri",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: ">",
                        previous: "<"
                    }
                }
            });
        }
    });
</script>
@endsection
