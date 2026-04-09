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
                            <i class="fa-solid fa-square-poll-vertical me-1"></i>{{ $title }}
                        </div>
                        <div class="tools">
                            <a href="javascript:void(0);" class="expand handle"><i class="fal fa-angle-down"></i></a>
                        </div>
                    </div>
                    <div class="cm-content-body form excerpt">
                        <div class="card-body pb-4">
                            <div class="table-responsive">
                                <table id="table-kuesioner" class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Tahun Ajaran</th>
                                            <th>Tipe Mahasiswa</th>
                                            <th>Status</th>
                                            <th>Total Soal</th>
                                            <th>Total Jawaban</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($tahunAjaranList as $idx => $row)
                                            @php
                                                $jenisLabel = (int) $row->jenis === 1 ? 'Ganjil' : ((int) $row->jenis === 2 ? 'Genap' : '-');
                                                $tipeLabel = (int) $row->tipe_mhs === 2 ? 'RPL' : 'Reguler';
                                            @endphp
                                            <tr>
                                                <td>{{ $idx + 1 }}</td>
                                                <td>{{ $row->awal }}/{{ $row->akhir }} ({{ $jenisLabel }})</td>
                                                <td>{{ $tipeLabel }}</td>
                                                <td>
                                                    @if((int) $row->is_aktif === 1)
                                                        <span class="badge bg-success">Aktif</span>
                                                    @else
                                                        <span class="badge bg-secondary">Nonaktif</span>
                                                    @endif
                                                </td>
                                                <td>{{ (int) ($row->total_soal ?? 0) }}</td>
                                                <td>{{ (int) ($row->total_jawaban ?? 0) }}</td>
                                                <td>
                                                    <div class="d-flex flex-wrap gap-1">
                                                        <a href="{{ url('akademik/kuesioner/soal/' . $row->id) }}" class="btn btn-primary btn-sm">List Kuesioner</a>
                                                        <a href="{{ url('akademik/kuesioner/jawaban/' . $row->id) }}" class="btn btn-info btn-sm">Jawaban Kuesioner</a>
                                                        <a href="{{ url('akademik/kuesioner/rekap/' . $row->id) }}" class="btn btn-success btn-sm">Rekap Nilai</a>
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
</div>
@endsection

@section('local-js')
<script>
    $(document).ready(function () {
        $('#table-kuesioner').DataTable({
            responsive: true,
            pageLength: 25,
            order: [[1, 'desc']],
            columnDefs: [
                { orderable: false, targets: [0, 6] }
            ]
        });
    });
</script>
@endsection
