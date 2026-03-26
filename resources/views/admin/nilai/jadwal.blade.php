@extends('layouts.default', ['CurrentPage' => $CurrentPage])

@section('content')
    <div class="content-body">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <div class="mb-4 pb-3">
                        <a href="{{ url('master/nilai') }}" class="btn btn-primary">
                            <i class="fa-solid fa-arrow-left me-1"></i> Kembali
                        </a>
                    </div>

                    {{-- Info Dosen --}}
                    <div class="card mb-4" style="height: auto;">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <h5 class="mb-1">
                                        <i class="fa-solid fa-user-tie me-2 text-primary"></i>
                                        {{ trim(($dosen->gelar_depan ?? '') . ' ' . $dosen->nama_lengkap . ' ' . ($dosen->gelar_belakang ?? '')) }}
                                    </h5>
                                    <small class="text-muted">
                                        NIDN: {{ $dosen->nidn ?? '-' }} &nbsp;|&nbsp;
                                        NIP: {{ $dosen->nip_pns ?? '-' }}
                                    </small>
                                </div>
                                <div class="col-md-4 text-end">
                                    <span class="badge badge-info light fs-14">{{ count($jadwal) }} Jadwal</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="filter cm-content-box box-primary">
                        <div class="content-title SlideToolHeader">
                            <div class="cpa">
                                <i class="fa-solid fa-calendar-alt me-1"></i>{{ $title }}
                            </div>
                            <div class="tools">
                                <a href="javascript:void(0);" class="expand handle">
                                    <i class="fal fa-angle-down"></i>
                                </a>
                            </div>
                        </div>
                        <div class="cm-content-body form excerpt">
                            <div class="card-body pb-4">
                                <table id="order-table" class="table table-striped table-bordered nowrap">
                                    <thead>
                                        <tr>
                                            <th width="5">No</th>
                                            <th>Kode Jadwal</th>
                                            <th>Mata Kuliah</th>
                                            <th>SKS</th>
                                            <th>Hari / Sesi</th>
                                            <th>Ruang</th>
                                            <th>Kelas</th>
                                            <th>Tahun Ajaran</th>
                                            <th>Mhs</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($jadwal as $row)
                                            @php
                                                $jenis = match((int)$row->jenis) {
                                                    1 => 'Ganjil',
                                                    2 => 'Genap',
                                                    3 => 'Antara',
                                                    default => '-'
                                                };
                                            @endphp
                                            <tr>
                                                <td>{{ $no++ }}</td>
                                                <td>{{ $row->kode_jadwal }}</td>
                                                <td>
                                                    <strong>{{ $row->nama_mata_kuliah ?? $row->kode_mata_kuliah }}</strong>
                                                    <br><small class="text-muted">{{ $row->kode_mata_kuliah }}</small>
                                                </td>
                                                <td class="text-center">{{ $row->jumlah_sks ?? '-' }}</td>
                                                <td>{{ $row->hari }}<br><small>Sesi {{ $row->sesi }}</small></td>
                                                <td>{{ $row->ruang }}</td>
                                                <td>{{ $row->kelas }} / {{ $row->rombel }}</td>
                                                <td>
                                                    {{ $row->awal ?? '-' }}/{{ $row->akhir ?? '-' }}
                                                    <br><small class="text-muted">{{ $jenis }}</small>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge badge-success light">{{ $row->jumlah_mahasiswa }}</span>
                                                </td>
                                                <td>
                                                    <a href="{{ url('master/nilai/input/' . $row->id) }}"
                                                        class="btn btn-success btn-sm"
                                                        title="Input Nilai">
                                                        <i class="fa fa-edit me-1"></i> 
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
            $('#order-table').DataTable({ responsive: true, pageLength: 25 });
        });
    </script>
@endsection
