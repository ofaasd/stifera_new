@extends('layouts.default', ['CurrentPage' => $CurrentPage])

@section('local-css')
<style>
    .nilai-card {
        height: auto !important;
    }

    .nilai-huruf {
        font-weight: 600;
    }

    .nilai-A, .nilai-AB, .nilai-B {
        color: #198754;
    }

    .nilai-BC, .nilai-C {
        color: #fd7e14;
    }

    .nilai-CD, .nilai-D, .nilai-E {
        color: #dc3545;
    }
</style>
@endsection

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-12">
                <div class="card nilai-card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h4 class="mb-1">Daftar Nilai Mahasiswa</h4>
                        <p class="text-muted mb-2">Halaman ini menampilkan seluruh nilai mata kuliah mahasiswa yang sedang login.</p>
                        <div class="small text-muted">
                            <div>NIM: <span class="fw-semibold">{{ $mahasiswa->nim ?? '-' }}</span></div>
                            <div>Nama: <span class="fw-semibold">{{ $mahasiswa->nama ?? '-' }}</span></div>
                        </div>
                    </div>
                </div>

                <div class="card nilai-card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th style="width:60px;">No</th>
                                        <th style="width:130px;">Kode Mata Kuliah</th>
                                        <th>Nama Mata Kuliah</th>
                                        <th style="width:90px;">SKS</th>
                                        <th style="width:190px;">Tahun Ajaran</th>
                                        <th style="width:110px;">Nilai (Grade)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($nilaiRows as $idx => $row)
                                        @php
                                            $huruf = !empty($row->nhuruf) ? strtoupper(trim((string) $row->nhuruf)) : (!is_null($row->nakhir) && function_exists('nmutu') ? strtoupper((string) nmutu((float) $row->nakhir)) : '-');
                                            $jenis = '-';
                                            if ((int) ($row->jenis ?? 0) === 1) {
                                                $jenis = 'Ganjil';
                                            } elseif ((int) ($row->jenis ?? 0) === 2) {
                                                $jenis = 'Genap';
                                            } elseif ((int) ($row->jenis ?? 0) === 3) {
                                                $jenis = 'Antara Ganjil Genap';
                                            } elseif ((int) ($row->jenis ?? 0) === 4) {
                                                $jenis = 'Antara Genap Ganjil';
                                            }
                                        @endphp
                                        <tr>
                                            <td>{{ $idx + 1 }}</td>
                                            <td>{{ $row->kode_mata_kuliah ?? '-' }}</td>
                                            <td>{{ $row->nama_mata_kuliah ?? '-' }}</td>
                                            <td class="text-center">{{ (int) ($row->jumlah_sks ?? 0) }}</td>
                                            <td>
                                                {{ ($row->awal ?? '-') . '/' . ($row->akhir ?? '-') }}
                                                <span class="text-muted">({{ $jenis }})</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="nilai-huruf nilai-{{ $huruf }}">{{ $huruf !== '' ? $huruf : '-' }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">Belum ada data nilai.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card nilai-card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="small">
                            E = {{ $rekap['count_per_huruf']['E'] ?? 0 }},
                            D = {{ $rekap['count_per_huruf']['D'] ?? 0 }},
                            CD = {{ $rekap['count_per_huruf']['CD'] ?? 0 }},
                            C = {{ $rekap['count_per_huruf']['C'] ?? 0 }},
                            BC = {{ $rekap['count_per_huruf']['BC'] ?? 0 }},
                            B = {{ $rekap['count_per_huruf']['B'] ?? 0 }},
                            AB = {{ $rekap['count_per_huruf']['AB'] ?? 0 }},
                            A = {{ $rekap['count_per_huruf']['A'] ?? 0 }}
                        </div>
                        <div class="small mt-2">
                            Jumlah SKS = {{ (int) ($rekap['total_sks'] ?? 0) }},
                            Total Score = {{ (int) round((float) ($rekap['total_score'] ?? 0)) }}
                        </div>
                        <div class="small mt-2 fw-semibold">
                            Indeks Prestasi Komulatif (IPK) = {{ number_format((float) ($rekap['ipk'] ?? 0), 2) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
