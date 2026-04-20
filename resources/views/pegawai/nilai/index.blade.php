@extends('layouts.default', ['CurrentPage' => $CurrentPage])

@section('content')
    <div class="content-body">
        <div class="container">

            @if(session('status'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fa fa-check-circle me-1"></i> {{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fa fa-exclamation-circle me-1"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

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
                            <div class="card-body pb-4">
                                @forelse($jadwal as $row)
                                    @php
                                        $jenis = match((int)$row->jenis) {
                                            1 => 'Ganjil',
                                            2 => 'Genap',
                                            3 => 'Antara',
                                            default => '-'
                                        };
                                        $dist   = $distribusi[$row->id] ?? [];
                                        $grades = ['A', 'AB', 'B', 'BC', 'C', 'CD', 'D', 'E'];
                                        $gradeSummary = implode('; ', array_map(fn($g) => $g . '=' . ($dist[$g] ?? 0), $grades));
                                        $pertemuan = (int) $row->jumlah_pertemuan;
                                    @endphp
                                    <div class="card mb-3 border">
                                        <div class="card-body">
                                            <div class="row">
                                                {{-- Kolom Kiri: Info Mata Kuliah --}}
                                                <div class="col-lg-6">
                                                    <table class="table table-sm table-borderless mb-0">
                                                        <tr>
                                                            <td style="width:160px;" class="text-muted fw-semibold">Kode Mata Kuliah</td>
                                                            <td>: {{ $row->kode_mata_kuliah }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-muted fw-semibold">Nama Mata Kuliah</td>
                                                            <td>: <span class="text-primary fw-bold">{{ $row->nama_mata_kuliah ?? $row->kode_mata_kuliah }}</span></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-muted fw-semibold">Hari, Sesi</td>
                                                            <td>: {{ $row->hari }}, {{ $row->sesi }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-muted fw-semibold">Ruang</td>
                                                            <td>: {{ $row->ruang }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-muted fw-semibold">Tahun Ajaran</td>
                                                            <td>: {{ $row->awal ?? '-' }}/{{ $row->akhir ?? '-' }} {{ $jenis }}</td>
                                                        </tr>
                                                    </table>
                                                </div>
                                                {{-- Kolom Kanan: Statistik + Tombol --}}
                                                <div class="col-lg-6">
                                                    <table class="table table-sm table-borderless mb-0">
                                                        <tr>
                                                            <td style="width:160px;" class="text-muted fw-semibold">Jumlah Mahasiswa</td>
                                                            <td>: <strong>{{ $row->jumlah_mahasiswa }}</strong></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-muted fw-semibold">Distribusi Nilai</td>
                                                            <td>: <small class="text-secondary">{{ $gradeSummary }}</small></td>
                                                        </tr>
                                                    </table>

                                                    <div class="mt-2 mb-1">
                                                        <small class="text-muted fw-semibold">Pertemuan :</small>
                                                    </div>
                                                    <div class="progress mb-3" style="height: 22px; border-radius: 4px;">
                                                        @php $pct = $pertemuan > 0 ? min(100, round($pertemuan / 16 * 100)) : 0; @endphp
                                                        <div class="progress-bar bg-info fw-bold"
                                                            role="progressbar"
                                                            style="width: {{ $pct }}%"
                                                            aria-valuenow="{{ $pertemuan }}"
                                                            aria-valuemin="0"
                                                            aria-valuemax="16">
                                                            {{ $pertemuan }}/16
                                                        </div>
                                                    </div>

                                                    <a href="{{ url('dosen/ujian/input/' . $row->id) }}"
                                                        class="btn btn-success btn-sm">
                                                        <i class="fa fa-edit me-1"></i> Set Nilai
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center text-muted py-5">
                                        <i class="fa fa-inbox fa-3x mb-3 d-block"></i>
                                        Belum ada jadwal yang Anda ampu.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
