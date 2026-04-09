@extends('layouts.default', ['CurrentPage' => $CurrentPage])

@section('content')
<div class="content-body">
    <div class="container">
        <div class="row">
            <div class="col-xl-12">
                <div class="mb-4 pb-3">
                    <a href="{{ url('akademik/kuesioner') }}" class="btn btn-primary">
                        <i class="fa-solid fa-arrow-left me-1"></i> Kembali
                    </a>
                </div>

                @php
                    $jenisLabel = (int) $tahun->jenis === 1 ? 'Ganjil' : ((int) $tahun->jenis === 2 ? 'Genap' : '-');
                    $tipeLabel = (int) $tahun->tipe_mhs === 2 ? 'RPL' : 'Reguler';
                @endphp

                <div class="filter cm-content-box box-primary">
                    <div class="content-title SlideToolHeader">
                        <div class="cpa">
                            <i class="fa-solid fa-comments me-1"></i>{{ $title }}
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

                            <form method="GET" action="{{ url('akademik/kuesioner/jawaban/' . $tahun->id) }}" class="mb-4" id="filter-jawaban-form">
                                <input type="hidden" name="tampilkan" value="1">
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-6">
                                        <label class="form-label">Jadwal</label>
                                        <select name="id_jadwal" id="filter-id-jadwal" class="form-select js-select2-jadwal" required>
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

                                    <div class="col-md-4" id="filter-dosen-wrap" style="{{ $selectedJadwalId > 0 ? '' : 'display:none;' }}">
                                        <label class="form-label">Nama Dosen</label>
                                        <select name="id_dosen" id="filter-id-dosen" class="form-select">
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
                                @if($jawabanMatrix->isEmpty())
                                    <div class="alert alert-warning mb-0">
                                        Data jawaban kuesioner tidak ditemukan untuk filter yang dipilih.
                                    </div>
                                @else
                                    <div class="table-responsive">
                                        <table id="table-jawaban-kuesioner" class="table table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th rowspan="2" class="align-middle">Nama Mahasiswa</th>
                                                    <th colspan="{{ $questionHeaders->count() }}" class="text-center">Jawaban Responden</th>
                                                    <th rowspan="2" class="align-middle text-center">Rata-rata</th>
                                                </tr>
                                                <tr>
                                                    @foreach($questionHeaders as $noSoal)
                                                        <th class="text-center">Q {{ $noSoal }}</th>
                                                    @endforeach
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($jawabanMatrix as $row)
                                                    <tr>
                                                        <td>{{ $row->nama_mahasiswa ?? '-' }}</td>
                                                        @foreach($questionHeaders as $noSoal)
                                                            @php
                                                                $score = $row->scores[(string) $noSoal] ?? null;
                                                            @endphp
                                                            <td class="text-center">{{ $score !== null ? rtrim(rtrim(number_format((float) $score, 2, '.', ''), '0'), '.') : '-' }}</td>
                                                        @endforeach
                                                        <td class="text-center">{{ $row->avg !== null ? number_format((float) $row->avg, 2) : '-' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>Total Rata-rata</th>
                                                    <th colspan="{{ $questionHeaders->count() }}"></th>
                                                    <th class="text-center">{{ $grandAverage !== null ? number_format((float) $grandAverage, 2) : '-' }}</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                @endif
                            @endif
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
        const $jadwalSelect = $('#filter-id-jadwal');

        if ($jadwalSelect.length && $.fn.select2) {
            $jadwalSelect.select2({
                width: '100%',
                placeholder: '-- Pilih Jadwal --'
            });
        }

        $('#filter-id-jadwal').on('change', function () {
            const value = $(this).val();
            if (!value) {
                $('#filter-dosen-wrap').hide();
                $('#filter-id-dosen').val('0');
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
