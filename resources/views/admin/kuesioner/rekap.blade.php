@extends('layouts.default', ['CurrentPage' => $CurrentPage])

@section('content')
<div class="content-body">
    <div class="container">
        <div class="row">
            <div class="col-xl-12">
                @php
                    $jenisLabel = (int) $tahun->jenis === 1 ? 'Ganjil' : ((int) $tahun->jenis === 2 ? 'Genap' : '-');
                    $tipeLabel = (int) $tahun->tipe_mhs === 2 ? 'RPL' : 'Reguler';
                    $exportQuery = http_build_query(array_filter([
                        'id_jadwal' => $selectedJadwalId,
                        'id_dosen' => $selectedDosenId,
                        'tampilkan' => $shouldShowData ? 1 : null,
                    ], fn ($value) => $value !== null && $value !== 0 && $value !== ''));
                    $canExport = $shouldShowData && (int) $selectedJadwalId > 0 && empty($filterError);
                @endphp

                <div class="mb-4 pb-3 d-flex flex-wrap gap-2 justify-content-between align-items-center">
                    <a href="{{ url('akademik/kuesioner') }}" class="btn btn-primary">
                        <i class="fa-solid fa-arrow-left me-1"></i> Kembali
                    </a>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ $canExport ? url('akademik/kuesioner/rekap/' . $tahun->id . '/export-excel') . ($exportQuery !== '' ? ('?' . $exportQuery) : '') : 'javascript:void(0);' }}" class="btn btn-success {{ $canExport ? '' : 'disabled' }}" {{ $canExport ? '' : 'aria-disabled=true tabindex=-1' }}>
                            <i class="fa-solid fa-file-excel me-1"></i> Export Excel
                        </a>
                        <a href="{{ $canExport ? url('akademik/kuesioner/rekap/' . $tahun->id . '/export-pdf') . ($exportQuery !== '' ? ('?' . $exportQuery) : '') : 'javascript:void(0);' }}" class="btn btn-danger {{ $canExport ? '' : 'disabled' }}" {{ $canExport ? '' : 'aria-disabled=true tabindex=-1' }}>
                            <i class="fa-solid fa-file-pdf me-1"></i> Export PDF
                        </a>
                    </div>
                </div>

                <div class="filter cm-content-box box-primary">
                    <div class="content-title SlideToolHeader">
                        <div class="cpa">
                            <i class="fa-solid fa-chart-column me-1"></i>{{ $title }}
                        </div>
                        <div class="tools">
                            <a href="javascript:void(0);" class="expand handle"><i class="fal fa-angle-down"></i></a>
                        </div>
                    </div>
                    <div class="cm-content-body form excerpt">
                        <div class="card-body pb-4">
                            <div class="mb-3">
                                <span class="badge bg-light text-dark">TA: {{ $tahun->awal }}/{{ $tahun->akhir }} ({{ $jenisLabel }}) - {{ $tipeLabel }}</span>
                            </div>

                            <form method="GET" action="{{ url('akademik/kuesioner/rekap/' . $tahun->id) }}" class="mb-4" id="filter-rekap-form">
                                <input type="hidden" name="tampilkan" value="1">
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-6">
                                        <label class="form-label">Jadwal</label>
                                        <select name="id_jadwal" id="rekap-filter-id-jadwal" class="form-select js-select2-jadwal" required>
                                            <option value="">-- Pilih Jadwal --</option>
                                            @foreach($jadwalOptions as $jadwal)
                                                @php
                                                    $kelasInfo = trim((string) ($jadwal->kelas ?? ''));
                                                    $rombelInfo = trim((string) ($jadwal->rombel ?? ''));
                                                @endphp
                                                <option value="{{ $jadwal->id_jadwal }}" {{ (int) $selectedJadwalId === (int) $jadwal->id_jadwal ? 'selected' : '' }}>
                                                    {{ $jadwal->label_jadwal }}
                                                    @if($kelasInfo !== '' || $rombelInfo !== '')
                                                        ({{ trim($kelasInfo . ' ' . $rombelInfo) }})
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-4" id="rekap-filter-dosen-wrap" style="{{ $selectedJadwalId > 0 ? '' : 'display:none;' }}">
                                        <label class="form-label">Nama Dosen</label>
                                        <select name="id_dosen" id="rekap-filter-id-dosen" class="form-select">
                                            <option value="0">Semua Dosen</option>
                                            @foreach($dosenOptions as $dosen)
                                                @php
                                                    $namaDosen = trim((string) ($dosen->nama_dosen ?? ''));
                                                @endphp
                                                <option value="{{ $dosen->id_dosen }}" {{ (int) $selectedDosenId === (int) $dosen->id_dosen ? 'selected' : '' }}>
                                                    {{ $namaDosen !== '' ? $namaDosen : ('Dosen #' . $dosen->id_dosen) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-primary w-100">Tampilkan Data</button>
                                    </div>
                                </div>
                            </form>

                            @if(!empty($filterError))
                                <div class="alert alert-warning">{{ $filterError }}</div>
                            @endif

                            @if(!$shouldShowData)
                                <div class="alert alert-info mb-0">
                                    Silakan pilih jadwal, lalu pilih dosen (opsional), kemudian klik <strong>Tampilkan Data</strong>.
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table id="table-rekap-kuesioner" class="table table-bordered table-hover rekap-kuesioner-table align-middle">
                                        <thead>
                                            <tr>
                                                <th rowspan="2" class="align-middle">Soal</th>
                                                <th colspan="4" class="text-center">Jumlah Jawaban Responden</th>
                                                <th rowspan="2" class="align-middle text-center">Total Jawaban</th>
                                                <th rowspan="2" class="align-middle text-center">Rata-rata</th>
                                            </tr>
                                            <tr>
                                                <th class="text-center">Sangat Tidak Setuju</th>
                                                <th class="text-center">Tidak Setuju</th>
                                                <th class="text-center">Setuju</th>
                                                <th class="text-center">Sangat Setuju</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($rekapGroups as $group)
                                                <tr class="table-secondary fw-semibold">
                                                    <td colspan="7">{{ $group['label'] }}</td>
                                                </tr>
                                                @foreach($group['items'] as $row)
                                                    <tr>
                                                        <td>Q {{ $row->no_soal }}. {{ $row->soal }}</td>
                                                        <td class="text-center">{{ (int) ($row->count_sts ?? 0) }}</td>
                                                        <td class="text-center">{{ (int) ($row->count_ts ?? 0) }}</td>
                                                        <td class="text-center">{{ (int) ($row->count_s ?? 0) }}</td>
                                                        <td class="text-center">{{ (int) ($row->count_ss ?? 0) }}</td>
                                                        <td class="text-center">{{ (int) ($row->total_jawaban ?? 0) }}</td>
                                                        <td class="text-center">{{ number_format((float) ($row->rata_nilai ?? 0), 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="text-center">Data rekap kuesioner tidak ditemukan untuk filter yang dipilih.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                        @if($rekapGroups->isNotEmpty())
                                            <tfoot>
                                                <tr class="fw-semibold">
                                                    <th>Jumlah</th>
                                                    <th class="text-center">{{ (int) ($summary['count_sts'] ?? 0) }}</th>
                                                    <th class="text-center">{{ (int) ($summary['count_ts'] ?? 0) }}</th>
                                                    <th class="text-center">{{ (int) ($summary['count_s'] ?? 0) }}</th>
                                                    <th class="text-center">{{ (int) ($summary['count_ss'] ?? 0) }}</th>
                                                    <th class="text-center">{{ (int) ($summary['total_jawaban'] ?? 0) }}</th>
                                                    <th></th>
                                                </tr>
                                                <tr class="fw-semibold">
                                                    <th>Rata-rata</th>
                                                    <th colspan="5"></th>
                                                    <th class="text-center">{{ number_format((float) ($summary['rata_nilai'] ?? 0), 2) }}</th>
                                                </tr>
                                            </tfoot>
                                        @endif
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('local-css')
<style>
    .rekap-kuesioner-table th,
    .rekap-kuesioner-table td {
        font-size: 13px;
        vertical-align: middle;
    }

    .rekap-kuesioner-table thead th {
        white-space: nowrap;
    }

    .rekap-kuesioner-table td:first-child,
    .rekap-kuesioner-table th:first-child {
        min-width: 420px;
    }

    .rekap-kuesioner-table tfoot th {
        background: #f5f5f5;
    }

    .disabled {
        pointer-events: none;
        opacity: .65;
    }
</style>
@endsection

@section('local-js')
<script>
    $(document).ready(function () {
        const $jadwalSelect = $('#rekap-filter-id-jadwal');

        if ($jadwalSelect.length && $.fn.select2) {
            $jadwalSelect.select2({
                width: '100%',
                placeholder: '-- Pilih Jadwal --'
            });
        }

        $jadwalSelect.on('change', function () {
            const value = $(this).val();
            if (!value) {
                $('#rekap-filter-dosen-wrap').hide();
                $('#rekap-filter-id-dosen').val('0');
                return;
            }

            const url = new URL(window.location.href);
            url.searchParams.set('id_jadwal', value);
            url.searchParams.delete('id_dosen');
            url.searchParams.delete('tampilkan');
            window.location.href = url.toString();
        });
    });
</script>
@endsection
