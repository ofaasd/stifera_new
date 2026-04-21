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

                <div class="filter cm-content-box box-primary">
                    <div class="content-title SlideToolHeader">
                        <div class="cpa">
                            <i class="fa-solid fa-list-check me-1"></i>{{ $title }}
                        </div>
                        <div class="tools">
                            <a href="javascript:void(0);" class="expand handle"><i class="fal fa-angle-down"></i></a>
                        </div>
                    </div>
                    <div class="cm-content-body form excerpt">
                        <div class="card-body pb-4">
                            <div class="table-responsive">
                                <table id="table-setting-pertemuan" class="table table-striped table-bordered nowrap">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Kode</th>
                                            <th>Nama</th>
                                            <th>Pengampu</th>
                                            <th>Hari, Jam</th>
                                            <th>RPS / KP</th>
                                            <th>Aksi</th>
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
                                                    <span class="badge {{ !empty($row->rps) ? 'badge-success' : 'badge-danger' }}">
                                                        RPS: {{ !empty($row->rps) ? 'Ada' : 'Kosong' }}
                                                    </span>
                                                    <span class="badge {{ !empty($row->kp) ? 'badge-success' : 'badge-danger' }}">
                                                        KP: {{ !empty($row->kp) ? 'Ada' : 'Kosong' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="{{ url('dosen/pertemuan/' . $row->id) }}" class="btn btn-success btn-sm" title="Set Pertemuan">
                                                        <i class="fa-solid fa-calendar-check me-1"></i> 
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
</div>
@endsection

@section('local-js')
<script>
    $(document).ready(function () {
        $('#table-setting-pertemuan').DataTable({
            responsive: true,
            pageLength: 25
        });
    });
</script>
@endsection
