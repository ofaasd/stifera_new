@extends('layouts.default', ['CurrentPage' => $CurrentPage])

@section('local-css')
<style>
    .khs-card {
        height: auto !important;
    }

    .table-responsive .table {
        width: 100%;
        table-layout: fixed;
    }

    .table-responsive .table th,
    .table-responsive .table td {
        white-space: normal;
        word-break: break-word;
        overflow-wrap: anywhere;
    }
</style>
@endsection

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-12">
                <div class="card khs-card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h4 class="mb-1">Kartu Hasil Studi (KHS)</h4>
                        <p class="text-muted mb-2">Menampilkan KHS aktif dan riwayat KHS untuk akun mahasiswa yang sedang login.</p>
                        <div class="small text-muted">
                            <div>NIM: <span class="fw-semibold">{{ $mahasiswa->nim ?? '-' }}</span></div>
                            <div>Nama: <span class="fw-semibold">{{ $mahasiswa->nama ?? '-' }}</span></div>
                            <div>
                                Tahun Ajaran Aktif:
                                <span class="fw-semibold">
                                    @if($tahunAktif)
                                        {{ ($tahunAktif->awal ?? '-') . '/' . ($tahunAktif->akhir ?? '-') }}
                                        @if(!empty($jenisTA)) ({{ $jenisTA }}) @endif
                                    @else
                                        -
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="mt-3">
                            <span class="badge bg-success me-1">IPS Semester Ini: {{ number_format((float) ($ipsSemesterIni ?? 0), 2) }}</span>
                            <span class="badge bg-info text-dark">IPK: {{ number_format((float) ($ipkMahasiswa ?? 0), 2) }}</span>
                        </div>
                    </div>
                </div>

                <div class="card khs-card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Grafik IPS Per Semester</h5>
                            <span class="badge bg-secondary">Jumlah Semester: {{ count($ipsPerSemester['labels'] ?? []) }}</span>
                        </div>

                        @if(empty($ipsPerSemester['labels']))
                            <div class="alert alert-light border text-muted mb-0">
                                Belum ada data IPS per semester untuk ditampilkan.
                            </div>
                        @else
                            <div style="position: relative; height: 300px;">
                                <canvas id="chart-ips-semester"></canvas>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card khs-card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">KHS Aktif</h5>
                            <div class="text-end">
                                <span class="badge bg-primary me-1">Total SKS: {{ $statAktif['total_sks'] ?? 0 }}</span>
                                <span class="badge bg-success">IPS: {{ number_format((float) ($statAktif['ips'] ?? 0), 2) }}</span>
                            </div>
                        </div>

                        @if(!$tahunAktif)
                            <div class="alert alert-warning mb-0">Data tahun ajaran aktif belum tersedia.</div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle">
                                    <thead>
                                        <tr>
                                            <th style="width: 60px;">No</th>
                                            <th>Dosen</th>
                                            <th>Kode MK</th>
                                            <th>Nama Mata Kuliah</th>
                                            <th style="width: 80px;">SKS</th>
                                            <th>Tugas</th>
                                            <th>UTS</th>
                                            <th>UAS</th>
                                            <th>Nilai Akhir</th>
                                            <th>Nilai Huruf</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($khsAktif as $idx => $row)
                                            @php
                                                $vTugas = ((int) ($row->validasi_tugas ?? 0) === 1) ? 'v' : '-';
                                                $vUts = ((int) ($row->validasi_uts ?? 0) === 1) ? 'v' : '-';
                                                $vUas = ((int) ($row->validasi_uas ?? 0) === 1) ? 'v' : '-';

                                                $showTugas = ((int) ($row->publish_tugas ?? 0) === 1) && !is_null($row->ntugas);
                                                $showUts = ((int) ($row->publish_uts ?? 0) === 1) && !is_null($row->nuts);
                                                $showUas = ((int) ($row->publish_uas ?? 0) === 1) && !is_null($row->nuas);
                                                $showAkhir = $showTugas && $showUts && $showUas && !is_null($row->nakhir);
                                                $huruf = !empty($row->nhuruf) ? strtoupper((string) $row->nhuruf) : ($showAkhir && function_exists('nmutu') ? strtoupper((string) nmutu((float) $row->nakhir)) : '');
                                                $showHuruf = $huruf !== '';
                                            @endphp
                                            <tr>
                                                <td>{{ $idx + 1 }}</td>
                                                <td>{{ trim($row->nama_dosen ?? '-') }}</td>
                                                <td>{{ $row->kode_mata_kuliah ?? '-' }}</td>
                                                <td>{{ $row->nama_mata_kuliah ?? '-' }}</td>
                                                <td>{{ (int) ($row->jumlah_sks ?? 0) }}</td>
                                                <td>{{ $showTugas ? round((float) $row->ntugas, 2) : '-' }} | {{ $vTugas }}</td>
                                                <td>{{ $showUts ? round((float) $row->nuts, 2) : '-' }} | {{ $vUts }}</td>
                                                <td>{{ $showUas ? round((float) $row->nuas, 2) : '-' }} | {{ $vUas }}</td>
                                                <td>{{ $showAkhir ? round((float) $row->nakhir, 2) : '-' }} | {{ $vUas }}</td>
                                                <td>
                                                    @if($showHuruf)
                                                        @if($huruf === 'A')
                                                            <span class="fw-semibold text-dark">A</span>
                                                        @elseif(in_array($huruf, ['AB', 'B'], true))
                                                            <span class="fw-semibold text-success">{{ $huruf }}</span>
                                                        @elseif(in_array($huruf, ['BC', 'C'], true))
                                                            <span class="fw-semibold text-warning">{{ $huruf }}</span>
                                                        @else
                                                            <span class="fw-semibold text-danger">{{ $huruf }}</span>
                                                        @endif
                                                    @else
                                                        - | {{ $vUas }}
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="10" class="text-center text-muted">Belum ada data KHS aktif.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card khs-card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="mb-3">Riwayat KHS</h5>

                        @forelse($riwayatKhs as $history)
                            @php
                                $ta = $history['tahun'];
                                $jenis = '-';
                                if ((int) ($ta->jenis ?? 0) === 1) {
                                    $jenis = 'Ganjil';
                                } elseif ((int) ($ta->jenis ?? 0) === 2) {
                                    $jenis = 'Genap';
                                } elseif ((int) ($ta->jenis ?? 0) === 3) {
                                    $jenis = 'Antara Ganjil Genap';
                                } elseif ((int) ($ta->jenis ?? 0) === 4) {
                                    $jenis = 'Antara Genap Ganjil';
                                }
                            @endphp

                            <div class="border rounded p-3 mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0">{{ ($ta->awal ?? '-') . '/' . ($ta->akhir ?? '-') }} ({{ $jenis }})</h6>
                                    <div class="text-end">
                                        <span class="badge bg-primary me-1">Total SKS: {{ $history['stat']['total_sks'] ?? 0 }}</span>
                                        <span class="badge bg-success">IPS: {{ number_format((float) ($history['stat']['ips'] ?? 0), 2) }}</span>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover align-middle table-mhs-khs-history">
                                        <thead>
                                            <tr>
                                                <th style="width: 60px;">No</th>
                                                <th>Dosen</th>
                                                <th>Kode MK</th>
                                                <th>Nama Mata Kuliah</th>
                                                <th style="width: 80px;">SKS</th>
                                                <th>Tugas</th>
                                                <th>UTS</th>
                                                <th>UAS</th>
                                                <th>Nilai Akhir</th>
                                                <th>Nilai Huruf</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($history['rows'] as $idx => $row)
                                                @php
                                                    $huruf = !empty($row->nhuruf) ? strtoupper(trim((string) $row->nhuruf)) : (function_exists('nmutu') && !is_null($row->nakhir) ? strtoupper((string) nmutu((float) $row->nakhir)) : 'E');
                                                @endphp
                                                <tr>
                                                    <td>{{ $idx + 1 }}</td>
                                                    <td>{{ trim($row->nama_dosen ?? '-') }}</td>
                                                    <td>{{ $row->kode_mata_kuliah ?? '-' }}</td>
                                                    <td>{{ $row->nama_mata_kuliah ?? '-' }}</td>
                                                    <td>{{ (int) ($row->jumlah_sks ?? 0) }}</td>
                                                    <td>{{ is_null($row->ntugas) ? '-' : round((float) $row->ntugas, 2) }}</td>
                                                    <td>{{ is_null($row->nuts) ? '-' : round((float) $row->nuts, 2) }}</td>
                                                    <td>{{ is_null($row->nuas) ? '-' : round((float) $row->nuas, 2) }}</td>
                                                    <td>{{ is_null($row->nakhir) ? '-' : round((float) $row->nakhir, 2) }}</td>
                                                    <td>
                                                        @if($huruf === 'A')
                                                            <span class="fw-semibold text-dark">A</span>
                                                        @elseif(in_array($huruf, ['AB', 'B'], true))
                                                            <span class="fw-semibold text-success">{{ $huruf }}</span>
                                                        @elseif(in_array($huruf, ['BC', 'C'], true))
                                                            <span class="fw-semibold text-warning">{{ $huruf }}</span>
                                                        @else
                                                            <span class="fw-semibold text-danger">{{ $huruf }}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="10" class="text-center text-muted">Belum ada data KHS pada semester ini.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @empty
                            <div class="alert alert-light border text-muted mb-0">
                                Riwayat KHS belum tersedia.
                            </div>
                        @endforelse
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
        var ipsLabels = @json($ipsPerSemester['labels'] ?? []);
        var ipsValues = @json($ipsPerSemester['values'] ?? []);

        if (ipsLabels.length > 0 && $('#chart-ips-semester').length) {
            var ctxIps = document.getElementById('chart-ips-semester').getContext('2d');
            new Chart(ctxIps, {
                type: 'line',
                data: {
                    labels: ipsLabels,
                    datasets: [{
                        label: 'IPS',
                        data: ipsValues,
                        borderColor: '#198754',
                        backgroundColor: 'rgba(25, 135, 84, 0.12)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.25,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 4,
                            ticks: {
                                stepSize: 0.5
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true
                        }
                    }
                }
            });
        }

        
    });
</script>
@endsection
