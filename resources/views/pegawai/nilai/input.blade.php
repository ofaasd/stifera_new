@extends('layouts.default', ['CurrentPage' => $CurrentPage])

@section('local-css')
<style>
    .nilai-note {
        color: #dc3545;
        font-style: italic;
        font-size: 20px;
        line-height: 1.1;
    }
    .nilai-warning {
        color: #c80000;
        font-size: 28px;
        font-weight: 700;
        line-height: 1.1;
    }
    .nilai-mini-btn {
        width: 72px;
        margin-bottom: 4px;
    }
    .nilai-grade-summary {
        font-size: 26px;
        font-weight: 700;
        color: #0d2c64;
    }
    .nilai-input-cell {
        background: #e9ecef;
        border: 1px solid #ced4da;
        height: 34px;
        min-width: 90px;
        text-align: center;
    }
    .nilai-table th {
        font-weight: 700;
    }
</style>
@endsection

@section('content')
    <div class="content-body">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <div class="mb-4 pb-3">
                        <a href="{{ url('dosen/ujian') }}" class="btn btn-primary">
                            <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Daftar Matakuliah
                        </a>
                    </div>

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="fa fa-exclamation-circle me-1"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- Kartu Info Jadwal + Kontrol --}}
                    <div class="card mb-4" style="height: auto;">
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <h5 class="fw-bold text-primary mb-1">
                                        {{ $jadwal->nama_mata_kuliah ?? $jadwal->kode_mata_kuliah }}
                                    </h5>
                                    <small class="text-muted">
                                        {{ $jadwal->kode_mata_kuliah }} &nbsp;|&nbsp;
                                        {{ $jadwal->hari }}, {{ $jadwal->sesi }} &nbsp;|&nbsp;
                                        Ruang: {{ $jadwal->ruang }}
                                    </small>
                                </div>
                                <div class="col-md-6 text-end">
                                    <span class="badge badge-info light fs-14">
                                        {{ $jadwal->awal ?? '-' }}/{{ $jadwal->akhir ?? '-' }}
                                        @php
                                            echo match((int)$jadwal->jenis) {
                                                1 => ' – Ganjil', 2 => ' – Genap', 3 => ' – Antara', default => ''
                                            };
                                        @endphp
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                {{-- Kiri: Upload + Persentase --}}
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <div class="nilai-note">Silahkan Atur Persentase Tugas, UTS, dan UAS sebelum Input Nilai</div>
                                        <div class="nilai-warning">UPLOAD NILAI : (FORMAT EXCEL / CSV)</div>
                                    </div>
                                    <form action="{{ url('dosen/ujian/upload') }}" method="POST" enctype="multipart/form-data" class="d-flex align-items-center flex-wrap gap-2 mb-3">
                                        @csrf
                                        <input type="hidden" name="id_jadwal" value="{{ $jadwal->id }}">
                                        <input type="hidden" name="id_tahun" value="{{ $jadwal->id_tahun }}">
                                        <input type="file" name="nilai_file" class="form-control" style="max-width: 280px;" accept=".csv,.txt,.xls,.xlsx" required>
                                        <button type="submit" class="btn btn-success">Upload</button>
                                        <a href="{{ url('dosen/ujian/template') }}" class="btn btn-info">Unduh Format</a>
                                    </form>

                                    @if(!$persentase)
                                        <div class="alert alert-warning">
                                            <i class="fa fa-exclamation-triangle me-1"></i>
                                            Persentase nilai di bawah masih menggunakan nilai default dan belum tersimpan. Silakan simpan terlebih dahulu.
                                        </div>
                                    @endif

                                    <form action="{{ url('dosen/ujian/save-persentase') }}" method="POST" class="border-top pt-3" style="max-width:520px;">
                                        @csrf
                                        <input type="hidden" name="id_jadwal" value="{{ $jadwal->id }}">
                                        <div class="d-flex align-items-center mb-2">
                                            <label class="me-2 mb-0" style="width:220px;">Persentase Nilai Tugas :</label>
                                            <input id="pct_tugas" name="pct_tugas" type="number" class="form-control"
                                                value="{{ $persentase ? $persentase->ntugas : 30 }}" min="0" max="100" step="0.01" required>
                                        </div>
                                        <div class="d-flex align-items-center mb-2">
                                            <label class="me-2 mb-0" style="width:220px;">Persentase Nilai UTS&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</label>
                                            <input id="pct_uts" name="pct_uts" type="number" class="form-control"
                                                value="{{ $persentase ? $persentase->nuts : 35 }}" min="0" max="100" step="0.01" required>
                                        </div>
                                        <div class="d-flex align-items-center mb-3">
                                            <label class="me-2 mb-0" style="width:220px;">Persentase Nilai UAS&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</label>
                                            <input id="pct_uas" name="pct_uas" type="number" class="form-control"
                                                value="{{ $persentase ? $persentase->nuas : 35 }}" min="0" max="100" step="0.01" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Simpan Persentase</button>
                                    </form>
                                </div>

                                {{-- Tengah: Publish --}}
                                <div class="col-lg-3">
                                    <h5 class="fw-bold mb-2">Publish</h5>
                                    @foreach(['tugas' => 'TGS', 'uts' => 'UTS', 'uas' => 'UAS'] as $comp => $label)
                                        <form method="POST" action="{{ url('dosen/ujian/publish-toggle') }}" class="d-inline-block">
                                            @csrf
                                            <input type="hidden" name="id_jadwal" value="{{ $jadwal->id }}">
                                            <input type="hidden" name="id_tahun" value="{{ $jadwal->id_tahun }}">
                                            <input type="hidden" name="component" value="{{ $comp }}">
                                            <button type="submit" class="btn {{ ($publishStatus[$comp] ?? 0) == 1 ? 'btn-danger' : 'btn-success' }} text-white nilai-mini-btn">
                                                {{ $label }}
                                            </button>
                                        </form><br>
                                    @endforeach
                                </div>

                                {{-- Kanan: Validasi --}}
                                <div class="col-lg-3">
                                    <h5 class="fw-bold mb-2">Validasi</h5>
                                    @foreach(['tugas' => 'TGS', 'uts' => 'UTS', 'uas' => 'UAS'] as $comp => $label)
                                        <form method="POST" action="{{ url('dosen/ujian/validasi-toggle') }}" class="d-inline-block">
                                            @csrf
                                            <input type="hidden" name="id_jadwal" value="{{ $jadwal->id }}">
                                            <input type="hidden" name="id_tahun" value="{{ $jadwal->id_tahun }}">
                                            <input type="hidden" name="component" value="{{ $comp }}">
                                            <button type="submit" class="btn {{ ($validasiStatus[$comp] ?? 0) == 1 ? 'btn-danger' : 'btn-success' }} text-white nilai-mini-btn">
                                                {{ $label }}
                                            </button>
                                        </form><br>
                                    @endforeach
                                    <div class="mt-2 small text-muted">
                                        <div>Hijau = Belum di publish/validasi</div>
                                        <div>Merah = Sudah di publish/validasi</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(session('status'))
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="fa fa-check-circle me-1"></i> {{ session('status') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- Tabel Input Nilai --}}
                    <div class="filter cm-content-box box-primary">
                        <div class="content-title SlideToolHeader">
                            <div class="cpa">
                                <i class="fa-solid fa-users me-1"></i>{{ $title }}
                            </div>
                            <div class="tools">
                                <a href="javascript:void(0);" class="expand handle">
                                    <i class="fal fa-angle-down"></i>
                                </a>
                            </div>
                        </div>
                        <div class="cm-content-body form excerpt">
                            <div class="card-body pb-4">
                                <form action="{{ url('dosen/ujian/simpan') }}" method="POST" id="form-nilai">
                                    @csrf
                                    <input type="hidden" name="id_jadwal" value="{{ $jadwal->id }}">
                                    <input type="hidden" name="id_tahun" value="{{ $jadwal->id_tahun }}">

                                    <div class="nilai-grade-summary mb-3" id="grade-summary">
                                        A=0; AB=0; B=0; BC=0; C=0; CD=0; D=0; E=0;
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover align-middle nilai-table">
                                            <thead>
                                                <tr>
                                                    <th>NIM</th>
                                                    <th>Nama Mahasiswa</th>
                                                    <th class="text-center">Tugas</th>
                                                    <th class="text-center">UTS</th>
                                                    <th class="text-center">UAS</th>
                                                    <th class="text-center">Nilai Akhir</th>
                                                    <th class="text-center">Nilai Huruf</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($mahasiswa as $mhs)
                                                    <tr>
                                                        <td>
                                                            <input type="hidden" name="nim[]" value="{{ $mhs->nim }}">
                                                            {{ $mhs->nim }}
                                                        </td>
                                                        <td>{{ $mhs->nama ?? '-' }}</td>
                                                        <td>
                                                            <input type="number" name="ntugas[]"
                                                                class="form-control form-control-sm nilai-input nilai-input-cell"
                                                                min="0" max="100" step="0.01"
                                                                value="{{ $mhs->ntugas }}"
                                                                placeholder="0-100"
                                                                data-row="{{ $loop->index }}"
                                                                data-type="tugas">
                                                        </td>
                                                        <td>
                                                            <input type="number" name="nuts[]"
                                                                class="form-control form-control-sm nilai-input nilai-input-cell"
                                                                min="0" max="100" step="0.01"
                                                                value="{{ $mhs->nuts }}"
                                                                placeholder="0-100"
                                                                data-row="{{ $loop->index }}"
                                                                data-type="uts">
                                                        </td>
                                                        <td>
                                                            <input type="number" name="nuas[]"
                                                                class="form-control form-control-sm nilai-input nilai-input-cell"
                                                                min="0" max="100" step="0.01"
                                                                value="{{ $mhs->nuas }}"
                                                                placeholder="0-100"
                                                                data-row="{{ $loop->index }}"
                                                                data-type="uas">
                                                        </td>
                                                        <td>
                                                            <input type="text" readonly
                                                                class="form-control form-control-sm nilai-input-cell nakhir-display"
                                                                data-row="{{ $loop->index }}"
                                                                value="{{ $mhs->nakhir ?? '' }}">
                                                        </td>
                                                        <td>
                                                            <input type="text" readonly
                                                                class="form-control form-control-sm nilai-input-cell nhuruf-display"
                                                                data-row="{{ $loop->index }}"
                                                                value="{{ $mhs->nhuruf ?? '' }}">
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="7" class="text-center text-muted py-4">
                                                            <i class="fa fa-inbox fa-2x mb-2 d-block"></i>
                                                            Belum ada mahasiswa terdaftar di jadwal ini.
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                    @if(count($mahasiswa) > 0)
                                        <div class="mt-3 d-flex gap-2">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fa fa-save me-1"></i> Simpan Semua Nilai
                                            </button>
                                            <a href="{{ url('dosen/ujian') }}" class="btn btn-light">Batal</a>
                                        </div>
                                    @endif
                                </form>
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
    function hitungHuruf(nilai) {
        if (nilai >= 85) return 'A';
        if (nilai >= 80) return 'AB';
        if (nilai >= 75) return 'B';
        if (nilai >= 70) return 'BC';
        if (nilai >= 60) return 'C';
        if (nilai >= 50) return 'D';
        return 'E';
    }

    function getPersentase() {
        return {
            pctTugas: parseFloat($('#pct_tugas').val()) || 0,
            pctUts:   parseFloat($('#pct_uts').val()) || 0,
            pctUas:   parseFloat($('#pct_uas').val()) || 0,
        };
    }

    function updateGradeSummary() {
        const summary = { A: 0, AB: 0, B: 0, BC: 0, C: 0, CD: 0, D: 0, E: 0 };
        $('.nhuruf-display').each(function () {
            const val = ($(this).val() || '').trim();
            if (summary[val] !== undefined) summary[val]++;
        });
        $('#grade-summary').text(
            `A=${summary.A}; AB=${summary.AB}; B=${summary.B}; BC=${summary.BC}; C=${summary.C}; CD=${summary.CD}; D=${summary.D}; E=${summary.E};`
        );
    }

    function hitungNilaiAkhir(row) {
        const { pctTugas, pctUts, pctUas } = getPersentase();
        const tugas = parseFloat($(`input[name="ntugas[]"][data-row="${row}"]`).val());
        const uts   = parseFloat($(`input[name="nuts[]"][data-row="${row}"]`).val());
        const uas   = parseFloat($(`input[name="nuas[]"][data-row="${row}"]`).val());

        const $akhir = $(`.nakhir-display[data-row="${row}"]`);
        const $huruf = $(`.nhuruf-display[data-row="${row}"]`);

        if (!isNaN(tugas) && !isNaN(uts) && !isNaN(uas)) {
            const akhir = ((tugas * pctTugas / 100) + (uts * pctUts / 100) + (uas * pctUas / 100)).toFixed(2);
            $akhir.val(akhir);
            $huruf.val(hitungHuruf(parseFloat(akhir)));
        } else {
            $akhir.val('');
            $huruf.val('');
        }
        updateGradeSummary();
    }

    $(document).ready(function () {
        $(document).on('input', '.nilai-input', function () {
            let val = parseFloat($(this).val());
            if (val > 100) $(this).val(100);
            if (val < 0)   $(this).val(0);
            hitungNilaiAkhir($(this).data('row'));
        });

        $('#pct_tugas, #pct_uts, #pct_uas').on('input', function () {
            $('.nilai-input[data-type="tugas"]').each(function () {
                hitungNilaiAkhir($(this).data('row'));
            });
        });

        updateGradeSummary();

        $('#form-nilai').on('submit', function (e) {
            if (!confirm('Simpan semua nilai yang telah diinput?')) {
                e.preventDefault();
            }
        });
    });
</script>
@endsection
