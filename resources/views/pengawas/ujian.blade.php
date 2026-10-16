<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Presensi & Paraf Pengawas Ujian {{ strtoupper($jenis) }} | SIAKAD STIFERA</title>
    
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            --header-border-radius: 16px;
            --card-border-radius: 14px;
        }

        body {
            background-color: #f0f2f5;
            color: #2c3e50;
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif;
            -webkit-tap-highlight-color: transparent;
        }

        /* ── Header Styling ── */
        .header-box {
            background: var(--primary-gradient);
            color: #fff;
            border-radius: var(--header-border-radius);
            padding: 24px;
            box-shadow: 0 10px 25px rgba(30, 60, 114, 0.18);
            position: relative;
            overflow: hidden;
        }
        .header-box::after {
            content: "";
            position: absolute;
            top: -50px;
            right: -50px;
            width: 150px;
            height: 150px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            pointer-events: none;
        }

        /* ── Stat Cards ── */
        .stat-card {
            border-radius: var(--card-border-radius);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            border: 1px solid rgba(0,0,0,0.04);
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.06) !important;
        }

        /* ── Signature Canvas Wrapper (Mobile & Touch Ready) ── */
        .signature-wrapper {
            border: 2px dashed #b5b5c3;
            border-radius: 12px;
            background: #ffffff;
            position: relative;
            cursor: crosshair;
            touch-action: none !important;
            -webkit-user-select: none;
            user-select: none;
            background-image: repeating-linear-gradient(45deg, #fafafa 25%, transparent 25%, transparent 75%, #fafafa 75%, #fafafa), repeating-linear-gradient(45deg, #fafafa 25%, #fff 25%, #fff 75%, #fafafa 75%, #fafafa);
            background-position: 0 0, 10px 10px;
            background-size: 20px 20px;
        }
        .signature-canvas {
            display: block;
            width: 100%;
            border-radius: 10px;
            touch-action: none !important;
        }

        /* ── Table Row Colors ── */
        .table-success-custom {
            background-color: #f1f9f2 !important;
        }
        .table-danger-custom {
            background-color: #fdf2f2 !important;
        }
        .table-hover tbody tr:hover {
            filter: brightness(0.98);
        }

        /* ── Mobile Student Card ── */
        .student-mobile-card {
            border-radius: var(--card-border-radius);
            transition: all 0.2s ease;
            position: relative;
            border-left-width: 5px !important;
        }
        .student-mobile-card.card-lolos {
            border-left-color: #198754 !important;
            background: #ffffff;
        }
        .student-mobile-card.card-tidak-lolos {
            border-left-color: #dc3545 !important;
            background: #fffafa;
        }
        .student-mobile-card.card-diparaf {
            border-left-color: #0d6efd !important;
        }

        /* ── Pill Filters ── */
        .filter-pill {
            border-radius: 50rem;
            font-size: 0.82rem;
            padding: 6px 14px;
            font-weight: 500;
            transition: all 0.15s ease;
            white-space: nowrap;
        }
        .filter-pill.active {
            box-shadow: 0 4px 10px rgba(0,0,0,0.12);
        }

        /* ── Mobile Optimization Rules ── */
        @media (max-width: 575.98px) {
            body {
                padding-top: 10px !important;
                padding-bottom: 70px !important;
            }
            .header-box {
                padding: 16px;
                border-radius: 12px;
            }
            .header-box h3 {
                font-size: 1.2rem;
            }
            .header-logo {
                height: 42px !important;
            }
            .stat-card {
                padding: 12px !important;
            }
            .stat-card .badge {
                padding: 10px !important;
                margin-right: 8px !important;
            }
            .stat-card h4 {
                font-size: 1.25rem !important;
            }
            .stat-card .small {
                font-size: 0.75rem !important;
            }
            .filter-pill {
                font-size: 0.75rem;
                padding: 5px 10px;
            }
        }

        @media (max-width: 767.98px) {
            .mobile-full-btn {
                width: 100%;
            }
        }

        /* ── Sticky Mobile Progress Bar ── */
        .sticky-mobile-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(8px);
            border-top: 1px solid #dee2e6;
            padding: 8px 16px;
            z-index: 1020;
            box-shadow: 0 -4px 16px rgba(0, 0, 0, 0.08);
        }

        /* ── Chevron Rotation for Collapse ── */
        [data-bs-toggle="collapse"]:not(.collapsed) .fa-chevron-down {
            transform: rotate(180deg);
        }
    </style>
</head>
<body class="py-3 py-md-4">
    <div class="container-fluid px-2 px-sm-3 px-md-5">
        
        {{-- Header Banner --}}
        <div class="header-box mb-3 mb-md-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div class="d-flex align-items-center gap-2 gap-sm-3">
                    <img src="{{ asset('images/logo/logo.png') }}" alt="Logo" class="header-logo" style="height: 52px; background: #fff; border-radius: 8px; padding: 4px;" onerror="this.style.display='none'">
                    <div>
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                            <span class="badge bg-warning text-dark fw-bold px-3 py-1 rounded-pill" style="letter-spacing: 0.5px;">
                                <i class="fa-solid fa-graduation-cap me-1"></i>UJIAN {{ strtoupper($jenis) }}
                            </span>
                            <span class="badge bg-white text-primary fw-semibold px-2 py-1 rounded-pill small">
                                TA {{ $jadwal->ta_awal ?? '-' }}/{{ $jadwal->ta_akhir ?? '-' }} 
                                ({{ (int) ($jadwal->ta_jenis ?? 0) === 1 ? 'Ganjil' : ((int) ($jadwal->ta_jenis ?? 0) === 2 ? 'Genap' : '-') }})
                            </span>
                        </div>
                        <h3 class="fw-bold mb-0 text-white">
                            {{ $jadwal->nama_mata_kuliah ?? '-' }} 
                            <small class="fs-6 opacity-75 d-block d-sm-inline">({{ $jadwal->kode_mata_kuliah ?? '-' }})</small>
                        </h3>
                    </div>
                </div>

                <div class="d-flex flex-row flex-md-column justify-content-between align-items-md-end w-100 w-md-auto pt-2 pt-md-0 border-top border-md-0 border-white border-opacity-10">
                    <span class="badge bg-info-subtle text-info fs-6 px-3 py-2 border border-info rounded-pill shadow-sm">
                        <i class="fa-solid fa-door-open me-1"></i> Ruang: <strong>{{ $jadwal->ruang ?? '-' }}</strong>
                    </span>
                    <div class="small mt-md-1 text-light opacity-75 d-flex align-items-center">
                        <i class="fa-solid fa-calendar-day me-1"></i>
                        <span>{{ now()->translatedFormat('l, d F Y') }}</span>
                    </div>
                </div>
            </div>

            <hr class="my-3 opacity-25">

            <div class="row g-2 small text-light">
                <div class="col-lg-3 col-md-6 col-6">
                    <span class="opacity-75"><i class="fa-solid fa-user-tie me-1"></i>Dosen Pengampu:</span><br>
                    <strong class="text-truncate d-block" title="{{ $jadwal->nama_dosen ?? '-' }}">{{ $jadwal->nama_dosen ?? '-' }}</strong>
                </div>
                <div class="col-lg-2 col-md-3 col-6">
                    <span class="opacity-75"><i class="fa-solid fa-book-bookmark me-1"></i>Beban SKS:</span><br>
                    <strong>{{ $jadwal->jumlah_sks ?? 0 }} SKS</strong>
                </div>
                <div class="col-lg-3 col-md-6 col-12 mt-1 mt-md-2 mt-lg-0">
                    <span class="opacity-75"><i class="fa-solid fa-clock me-1"></i>Ujian Teori:</span><br>
                    <strong>
                        @if(!empty($tglTeori))
                            {{ \Carbon\Carbon::parse($tglTeori)->translatedFormat('d M Y') }}
                            @if(!empty($jamTeori)) ({{ $jamTeori->nama_sesi }}: {{ \Carbon\Carbon::parse($jamTeori->mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jamTeori->selesai)->format('H:i') }}) @endif
                        @else
                            <span class="opacity-50">-</span>
                        @endif
                    </strong>
                </div>
                <div class="col-lg-4 col-md-6 col-12 mt-1 mt-md-2 mt-lg-0">
                    <span class="opacity-75"><i class="fa-solid fa-flask me-1"></i>Ujian Praktik:</span><br>
                    <strong>
                        @if(!empty($tglPraktik))
                            {{ \Carbon\Carbon::parse($tglPraktik)->translatedFormat('d M Y') }}
                            @if(!empty($jamPraktik)) ({{ $jamPraktik->nama_sesi }}: {{ \Carbon\Carbon::parse($jamPraktik->mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jamPraktik->selesai)->format('H:i') }}) @endif
                        @else
                            <span class="opacity-50">-</span>
                        @endif
                    </strong>
                </div>
            </div>
        </div>

        {{-- Statistics Row --}}
        <div class="row g-2 g-md-3 mb-3 mb-md-4">
            <div class="col-xl-3 col-md-3 col-6">
                <div class="card border-0 shadow-sm stat-card bg-white p-3 h-100">
                    <div class="d-flex align-items-center">
                        <div class="badge bg-primary-subtle text-primary p-3 rounded-circle me-2 me-sm-3">
                            <i class="fa-solid fa-users fs-5 fs-sm-4"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Total Peserta</span>
                            <h4 class="fw-bold mb-0 text-dark" id="stat-total">{{ $totalPeserta }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-3 col-6">
                <div class="card border-0 shadow-sm stat-card bg-white p-3 h-100">
                    <div class="d-flex align-items-center">
                        <div class="badge bg-success-subtle text-success p-3 rounded-circle me-2 me-sm-3">
                            <i class="fa-solid fa-user-check fs-5 fs-sm-4"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Bisa Ikut Ujian</span>
                            <h4 class="fw-bold text-success mb-0" id="stat-lolos">{{ $totalLolos }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-3 col-6">
                <div class="card border-0 shadow-sm stat-card bg-white p-3 h-100">
                    <div class="d-flex align-items-center">
                        <div class="badge bg-danger-subtle text-danger p-3 rounded-circle me-2 me-sm-3">
                            <i class="fa-solid fa-user-xmark fs-5 fs-sm-4"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Tidak Bisa Ikut</span>
                            <h4 class="fw-bold text-danger mb-0" id="stat-tidak-lolos">{{ $totalPeserta - $totalLolos }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-3 col-6">
                <div class="card border-0 shadow-sm stat-card bg-white p-3 h-100">
                    <div class="d-flex align-items-center">
                        <div class="badge bg-info-subtle text-info p-3 rounded-circle me-2 me-sm-3">
                            <i class="fa-solid fa-signature fs-5 fs-sm-4"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Sudah Diparaf</span>
                            <h4 class="fw-bold text-info mb-0" id="stat-paraf">{{ $totalDiparaf }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Master Paraf Pengawas Card --}}
        <div class="card border-0 shadow-sm mb-3 mb-md-4" style="border-radius: var(--card-border-radius);">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <div class="badge bg-primary p-2 rounded-circle text-white">
                        <i class="fa-solid fa-pen-nib"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0">Master Paraf Pengawas</h5>
                        <p class="text-muted small mb-0 d-none d-sm-block">Tanda tangani satu kali di sini, lalu terapkan ke mahasiswa yang hadir dengan satu klik.</p>
                    </div>
                </div>
                <button class="btn btn-sm btn-outline-secondary rounded-pill collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseMasterParaf" aria-expanded="false" aria-controls="collapseMasterParaf">
                    <i class="fa-solid fa-chevron-down me-1" style="transition: transform 0.2s ease;"></i> <span class="d-none d-sm-inline">Buka / Tutup</span> Panel
                </button>
            </div>
            <div class="collapse" id="collapseMasterParaf">
                <div class="card-body bg-light-subtle p-3 p-md-4">
                    <div class="row g-3 g-md-4 align-items-center">
                        <div class="col-lg-5 col-md-6 col-12">
                            <label class="form-label fw-bold small text-muted text-uppercase mb-1">
                                <i class="fa-solid fa-pencil me-1"></i>Gambar Paraf Anda di Kanvas:
                            </label>
                            <div class="signature-wrapper shadow-sm">
                                <canvas id="master-canvas" class="signature-canvas" height="150"></canvas>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <button type="button" id="btn-clear-master" class="btn btn-outline-secondary btn-sm rounded-pill">
                                    <i class="fa-solid fa-eraser me-1"></i> Bersihkan
                                </button>
                                <button type="button" id="btn-save-master" class="btn btn-primary btn-sm rounded-pill shadow-sm">
                                    <i class="fa-solid fa-check me-1"></i> Simpan Master Paraf
                                </button>
                            </div>
                        </div>

                        <div class="col-lg-7 col-md-6 col-12">
                            <div class="p-3 border rounded bg-white shadow-sm">
                                <h6 class="fw-bold mb-2 text-dark"><i class="fa-solid fa-stamp me-1 text-primary"></i>Status Master Paraf Saat Ini:</h6>
                                <div id="master-preview-box" class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center gap-3">
                                    <div class="p-2 border rounded bg-light text-center w-100 w-sm-auto" style="min-width: 140px; min-height: 70px; display: flex; align-items: center; justify-content: center;">
                                        <img id="master-preview-img" src="" alt="Preview Master Paraf" style="max-height: 60px; max-width: 130px; display: none; object-fit: contain;">
                                        <span id="master-preview-placeholder" class="text-muted small">Belum ada master paraf</span>
                                    </div>
                                    <div class="w-100">
                                        <div id="master-status-text" class="text-muted small mb-2">
                                            Silakan gambar dan simpan master paraf Anda pada kanvas di samping.
                                        </div>
                                        <div class="d-flex flex-wrap gap-2">
                                            <button type="button" id="btn-apply-all-master" class="btn btn-success btn-sm rounded-pill disabled shadow-sm" disabled>
                                                <i class="fa-solid fa-wand-magic-sparkles me-1"></i> Terapkan ke Semua Mahasiswa Lolos
                                            </button>
                                            <button type="button" id="btn-delete-master" class="btn btn-outline-danger btn-sm rounded-pill d-none">
                                                <i class="fa-solid fa-trash me-1"></i> Hapus Master
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Peserta Section (Table for Desktop/Tablet, Cards for Mobile) --}}
        <div class="card border-0 shadow-sm mb-4" style="border-radius: var(--card-border-radius);">
            <div class="card-header bg-white py-3">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
                    <h5 class="fw-bold mb-0">
                        <i class="fa-solid fa-clipboard-user me-2 text-primary"></i>Daftar Mahasiswa Peserta Ujian
                    </h5>

                    <div class="d-flex align-items-center gap-2">
                        {{-- Toggle View (Desktop vs Mobile Card) --}}
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" id="btn-view-table" class="btn btn-outline-primary active" title="Tampilan Tabel">
                                <i class="fa-solid fa-table-list me-1"></i><span class="d-none d-sm-inline">Tabel</span>
                            </button>
                            <button type="button" id="btn-view-cards" class="btn btn-outline-primary" title="Tampilan Kartu">
                                <i class="fa-solid fa-grip-vertical me-1"></i><span class="d-none d-sm-inline">Kartu</span>
                            </button>
                        </div>

                        {{-- Search Input --}}
                        <div class="input-group input-group-sm" style="max-width: 250px;">
                            <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                            <input type="text" id="search-input" class="form-control border-start-0" placeholder="Cari NIM / Nama...">
                            <button type="button" id="btn-clear-search" class="btn btn-outline-secondary border-start-0 d-none" title="Reset Pencarian">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>

                        {{-- Refresh Button --}}
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.location.reload();" title="Refresh Halaman">
                            <i class="fa-solid fa-rotate-right"></i>
                        </button>
                    </div>
                </div>

                {{-- Quick Filter Pills --}}
                <div class="d-flex flex-wrap gap-2 align-items-center pt-1 border-top">
                    <span class="small text-muted fw-semibold me-1"><i class="fa-solid fa-filter me-1"></i>Filter:</span>
                    <button type="button" class="btn btn-outline-primary active filter-pill" data-filter="all">
                        Semua (<span class="count-pill-all">{{ $totalPeserta }}</span>)
                    </button>
                    <button type="button" class="btn btn-outline-warning filter-pill" data-filter="belum-paraf">
                        <i class="fa-solid fa-clock me-1"></i>Belum Diparaf (<span class="count-pill-belum">{{ max(0, $totalLolos - $totalDiparaf) }}</span>)
                    </button>
                    <button type="button" class="btn btn-outline-success filter-pill" data-filter="sudah-paraf">
                        <i class="fa-solid fa-check-circle me-1"></i>Sudah Diparaf (<span class="count-pill-sudah">{{ $totalDiparaf }}</span>)
                    </button>
                    <button type="button" class="btn btn-outline-danger filter-pill" data-filter="tidak-lolos">
                        <i class="fa-solid fa-triangle-exclamation me-1"></i>Tidak Bisa Ikut (<span class="count-pill-tidak">{{ $totalPeserta - $totalLolos }}</span>)
                    </button>
                </div>
            </div>

            {{-- 1. TAMPILAN TABEL (Untuk Laptop & Tablet) --}}
            <div id="view-container-table" class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="table-peserta">
                        <thead class="bg-light text-dark fw-bold text-center border-bottom">
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th style="width: 90px;">No. Kursi</th>
                                <th style="width: 130px;">NIM</th>
                                <th style="min-width: 200px;" class="text-start">Nama Mahasiswa</th>
                                <th style="min-width: 200px;">Status Validasi</th>
                                <th style="width: 210px;">Paraf Pengawas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($peserta as $idx => $p)
                                <tr class="peserta-item {{ $p->is_lolos ? 'table-success-custom' : 'table-danger-custom' }}"
                                    id="row-{{ $p->nim }}" 
                                    data-nim="{{ $p->nim }}" 
                                    data-nama="{{ strtolower($p->nama_mahasiswa ?? '') }}"
                                    data-status="{{ $p->is_lolos ? 'lolos' : 'tidak_lolos' }}"
                                    data-paraf="{{ !empty($p->paraf) ? '1' : '0' }}">
                                    
                                    <td class="text-center fw-semibold text-muted">{{ $idx + 1 }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-white text-dark border fs-6 px-2 py-1 shadow-sm">
                                            {{ $p->no_kursi ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-bold font-monospace">{{ $p->nim }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $p->nama_mahasiswa ?? '-' }}</div>
                                        <div class="small text-muted"><i class="fa-solid fa-door-open me-1"></i>Ruang: {{ $p->ruang ?? $jadwal->ruang ?? '-' }}</div>
                                    </td>
                                    <td class="text-center">
                                        @if($p->is_lolos)
                                            <span class="badge bg-success px-3 py-2 rounded-pill">
                                                <i class="fa-solid fa-circle-check me-1"></i>Bisa Ikut Ujian
                                            </span>
                                            <div class="small text-success mt-1 fw-semibold">
                                                <i class="fa-solid fa-check-double me-1"></i>Dosen Wali & BAK Lengkap
                                            </div>
                                        @else
                                            <span class="badge bg-danger px-3 py-2 rounded-pill">
                                                <i class="fa-solid fa-circle-xmark me-1"></i>Tidak Bisa Ikut
                                            </span>
                                            <div class="small text-danger fw-semibold mt-1">
                                                <i class="fa-solid fa-triangle-exclamation me-1"></i>{{ $p->keterangan }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="text-center cell-paraf" id="col-paraf-{{ $p->nim }}">
                                        @if($p->is_lolos)
                                            @if(!empty($p->paraf))
                                                <div class="d-flex align-items-center justify-content-center gap-2">
                                                    <img src="{{ $p->paraf }}" alt="Paraf" class="border rounded bg-white shadow-sm p-1" style="max-height: 42px; max-width: 90px; object-fit: contain;">
                                                    <div class="text-start">
                                                        <span class="badge bg-success-subtle text-success small d-block">
                                                            Diparaf ✓
                                                        </span>
                                                        <small class="text-muted" style="font-size: 11px;">
                                                            {{ !empty($p->waktu_paraf) ? \Carbon\Carbon::parse($p->waktu_paraf)->translatedFormat('d/m/y H:i') : '' }}
                                                        </small>
                                                        <div>
                                                            <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none small btn-open-modal-paraf" data-nim="{{ $p->nim }}" data-nama="{{ $p->nama_mahasiswa }}" style="font-size: 11px;">
                                                                <i class="fa-solid fa-pen-to-square me-1"></i>Ubah
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="d-flex align-items-center justify-content-center gap-1">
                                                    <button type="button" class="btn btn-primary btn-sm btn-open-modal-paraf rounded-pill shadow-sm px-3" data-nim="{{ $p->nim }}" data-nama="{{ $p->nama_mahasiswa }}">
                                                        <i class="fa-solid fa-signature me-1"></i>Paraf
                                                    </button>
                                                    <button type="button" class="btn btn-outline-success btn-sm btn-apply-master-row d-none rounded-pill shadow-sm" data-nim="{{ $p->nim }}" title="Terapkan Master Paraf">
                                                        <i class="fa-solid fa-stamp"></i>
                                                    </button>
                                                </div>
                                            @endif
                                        @else
                                            <span class="badge bg-light text-muted border py-2 px-3 rounded-pill">
                                                <i class="fa-solid fa-lock me-1 text-danger"></i>Terkunci
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        Belum ada mahasiswa yang terdaftar pada jadwal ujian ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 2. TAMPILAN KARTU (Sangat nyaman di HP / Layar Sentuh) --}}
            <div id="view-container-cards" class="card-body p-2 p-sm-3 bg-light d-none">
                <div class="row g-2 g-sm-3">
                    @forelse($peserta as $idx => $p)
                        <div class="col-12 col-md-6 col-lg-4 peserta-item"
                             id="card-item-{{ $p->nim }}"
                             data-nim="{{ $p->nim }}" 
                             data-nama="{{ strtolower($p->nama_mahasiswa ?? '') }}"
                             data-status="{{ $p->is_lolos ? 'lolos' : 'tidak_lolos' }}"
                             data-paraf="{{ !empty($p->paraf) ? '1' : '0' }}">
                            
                            <div class="card h-100 shadow-sm student-mobile-card {{ $p->is_lolos ? (!empty($p->paraf) ? 'card-diparaf' : 'card-lolos') : 'card-tidak-lolos' }}">
                                <div class="card-body p-3">
                                    {{-- Card Top Row: Kursi, NIM & Status Badge --}}
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="d-flex align-items-center gap-1">
                                            <span class="badge bg-primary text-white fs-6 px-2 py-1 shadow-sm">
                                                <i class="fa-solid fa-chair me-1"></i>Kursi {{ $p->no_kursi ?? '-' }}
                                            </span>
                                            <span class="badge bg-light text-dark border font-monospace">
                                                {{ $p->nim }}
                                            </span>
                                        </div>
                                        <div>
                                            @if($p->is_lolos)
                                                <span class="badge bg-success-subtle text-success border border-success rounded-pill small">
                                                    <i class="fa-solid fa-check me-1"></i>Lolos Syarat
                                                </span>
                                            @else
                                                <span class="badge bg-danger-subtle text-danger border border-danger rounded-pill small">
                                                    <i class="fa-solid fa-ban me-1"></i>Tidak Lolos
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Student Name & Room --}}
                                    <h6 class="fw-bold mb-1 text-dark">{{ $p->nama_mahasiswa ?? '-' }}</h6>
                                    <div class="small text-muted mb-2">
                                        <i class="fa-solid fa-door-open me-1"></i>Ruang: <strong>{{ $p->ruang ?? $jadwal->ruang ?? '-' }}</strong>
                                    </div>

                                    {{-- Rejection Notice if not eligible --}}
                                    @if(!$p->is_lolos)
                                        <div class="alert alert-danger py-1 px-2 mb-2 small rounded-3">
                                            <i class="fa-solid fa-triangle-exclamation me-1"></i>{{ $p->keterangan }}
                                        </div>
                                    @endif

                                    {{-- Action Row --}}
                                    <div class="border-top pt-2 mt-2" id="card-action-{{ $p->nim }}">
                                        @if($p->is_lolos)
                                            @if(!empty($p->paraf))
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <img src="{{ $p->paraf }}" alt="Paraf" class="border rounded bg-white shadow-sm p-1" style="max-height: 38px; max-width: 80px; object-fit: contain;">
                                                        <div>
                                                            <span class="badge bg-success small py-1"><i class="fa-solid fa-check me-1"></i>Diparaf</span>
                                                            <small class="text-muted d-block" style="font-size: 10px;">{{ !empty($p->waktu_paraf) ? \Carbon\Carbon::parse($p->waktu_paraf)->translatedFormat('d/m/y H:i') : '' }}</small>
                                                        </div>
                                                    </div>
                                                    <button type="button" class="btn btn-outline-primary btn-sm rounded-pill btn-open-modal-paraf" data-nim="{{ $p->nim }}" data-nama="{{ $p->nama_mahasiswa }}">
                                                        <i class="fa-solid fa-pen-to-square me-1"></i>Ubah
                                                    </button>
                                                </div>
                                            @else
                                                <div class="d-flex gap-2">
                                                    <button type="button" class="btn btn-primary btn-sm rounded-pill flex-grow-1 btn-open-modal-paraf shadow-sm" data-nim="{{ $p->nim }}" data-nama="{{ $p->nama_mahasiswa }}">
                                                        <i class="fa-solid fa-signature me-1"></i>Beri Paraf
                                                    </button>
                                                    <button type="button" class="btn btn-outline-success btn-sm rounded-pill btn-apply-master-row d-none shadow-sm" data-nim="{{ $p->nim }}" title="Terapkan Master Paraf">
                                                        <i class="fa-solid fa-stamp me-1"></i>Terapkan Master
                                                    </button>
                                                </div>
                                            @endif
                                        @else
                                            <div class="text-center py-1">
                                                <span class="badge bg-light text-muted border w-100 py-2 rounded-pill">
                                                    <i class="fa-solid fa-lock me-1 text-danger"></i>Paraf Terkunci (Belum Memenuhi Syarat)
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-4 text-muted">
                            Belum ada mahasiswa yang terdaftar pada jadwal ujian ini.
                        </div>
                    @endforelse
                </div>
            </div>

            <div id="no-filter-results" class="p-4 text-center text-muted d-none">
                <i class="fa-solid fa-user-slash fa-2x mb-2 text-secondary opacity-50 d-block"></i>
                Tidak ditemukan mahasiswa yang sesuai dengan pencarian atau filter yang dipilih.
            </div>
        </div>

    </div>

    {{-- Sticky Mobile Quick Action Bar (Only visible on screens <= 767px) --}}
    <div class="sticky-mobile-bar d-flex justify-content-between align-items-center d-md-none">
        <div class="small">
            <span class="text-muted">Progres: </span>
            <strong class="text-primary"><span id="sticky-paraf-count">{{ $totalDiparaf }}</span>/{{ $totalLolos }}</strong>
            <span class="text-muted">Diparaf</span>
        </div>
        <div class="d-flex gap-1">
            <button type="button" class="btn btn-outline-primary btn-sm rounded-pill py-1 px-2" onclick="window.scrollTo({top: 0, behavior: 'smooth'});">
                <i class="fa-solid fa-arrow-up"></i> Ke Atas
            </button>
        </div>
    </div>

    {{-- Modal Paraf Individu (Fullscreen on Mobile for Maximum Drawing Area) --}}
    <div class="modal fade" id="modalParafIndividu" tabindex="-1" aria-labelledby="modalParafIndividuLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <div class="modal-header bg-light border-0 py-3">
                    <div>
                        <h5 class="modal-title fw-bold" id="modalParafIndividuLabel">
                            <i class="fa-solid fa-signature me-2 text-primary"></i>Paraf Pengawas Ujian
                        </h5>
                        <p class="text-muted small mb-0">Bubuhkan paraf digital untuk mahasiswa berikut:</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3 p-sm-4">
                    <div class="p-3 bg-light rounded-3 mb-3 border">
                        <div class="fw-bold text-dark fs-6" id="modal-mhs-nama">-</div>
                        <div class="small text-muted font-monospace" id="modal-mhs-nim">-</div>
                    </div>

                    <label class="form-label fw-bold small text-muted text-uppercase mb-1">
                        <i class="fa-solid fa-pen-nib me-1"></i>Goreskan Paraf pada Area Berikut:
                    </label>
                    <div class="signature-wrapper mb-2 shadow-sm">
                        <canvas id="individu-canvas" class="signature-canvas" height="200"></canvas>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <button type="button" id="btn-clear-individu" class="btn btn-outline-secondary btn-sm rounded-pill">
                            <i class="fa-solid fa-eraser me-1"></i> Bersihkan
                        </button>
                        <span class="text-muted small" id="individu-hint">
                            <i class="fa-solid fa-hand-pointer me-1"></i>Gunakan jari atau stylus
                        </span>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="button" id="btn-submit-individu" class="btn btn-primary btn-sm rounded-pill px-4 shadow-sm">
                        <i class="fa-solid fa-check me-1"></i> Simpan Paraf
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Toast Notification --}}
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1090; margin-bottom: 50px;">
        <div id="toastNotification" class="toast align-items-center text-white bg-success border-0 shadow-lg rounded-3" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body" id="toastMessage">
                    Paraf berhasil disimpan.
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    {{-- Scripts --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>

    <script>
        $(document).ready(function () {
            const csrfToken = $('meta[name="csrf-token"]').attr('content');
            const postUrl = '{{ route("pengawas.ujian.paraf", ["jenis" => $jenis, "token" => $token]) }}';

            // Toast helper
            const toastEl = document.getElementById('toastNotification');
            const toast = new bootstrap.Toast(toastEl, { delay: 2500 });
            function showToast(msg, isError = false) {
                $('#toastMessage').html(msg);
                if (isError) {
                    $('#toastNotification').removeClass('bg-success').addClass('bg-danger');
                } else {
                    $('#toastNotification').removeClass('bg-danger').addClass('bg-success');
                }
                toast.show();
            }

            // ── View Mode Switcher (Table vs Cards) ────────────────────────────────
            const isMobileScreen = window.innerWidth < 768;
            function setViewMode(mode) {
                if (mode === 'cards') {
                    $('#view-container-table').addClass('d-none');
                    $('#view-container-cards').removeClass('d-none');
                    $('#btn-view-cards').addClass('active');
                    $('#btn-view-table').removeClass('active');
                } else {
                    $('#view-container-cards').addClass('d-none');
                    $('#view-container-table').removeClass('d-none');
                    $('#btn-view-table').addClass('active');
                    $('#btn-view-cards').removeClass('active');
                }
            }

            // Default on mobile is Card mode, on tablet/desktop is Table mode
            if (isMobileScreen) {
                setViewMode('cards');
            } else {
                setViewMode('table');
            }

            $('#btn-view-table').on('click', function () { setViewMode('table'); });
            $('#btn-view-cards').on('click', function () { setViewMode('cards'); });

            // ── Live Filter & Search Logic ─────────────────────────────────────────
            let activeFilter = 'all';
            let activeSearch = '';

            function applyFilters() {
                let visibleCount = 0;

                $('.peserta-item').each(function () {
                    const $item = $(this);
                    const nim = String($item.data('nim') || '').toLowerCase();
                    const nama = String($item.data('nama') || '').toLowerCase();
                    const status = String($item.data('status') || '');
                    const isParaf = String($item.data('paraf') || '0') === '1';

                    // 1. Text Search match
                    const matchSearch = activeSearch === '' || nim.includes(activeSearch) || nama.includes(activeSearch);

                    // 2. Filter Pill match
                    let matchFilter = true;
                    if (activeFilter === 'belum-paraf') {
                        matchFilter = (status === 'lolos' && !isParaf);
                    } else if (activeFilter === 'sudah-paraf') {
                        matchFilter = isParaf;
                    } else if (activeFilter === 'tidak-lolos') {
                        matchFilter = (status === 'tidak_lolos');
                    }

                    if (matchSearch && matchFilter) {
                        $item.show();
                        visibleCount++;
                    } else {
                        $item.hide();
                    }
                });

                if (visibleCount === 0) {
                    $('#no-filter-results').removeClass('d-none');
                } else {
                    $('#no-filter-results').addClass('d-none');
                }
            }

            $('#search-input').on('keyup input', function () {
                activeSearch = $(this).val().toLowerCase().trim();
                if (activeSearch.length > 0) {
                    $('#btn-clear-search').removeClass('d-none');
                } else {
                    $('#btn-clear-search').addClass('d-none');
                }
                applyFilters();
            });

            $('#btn-clear-search').on('click', function () {
                $('#search-input').val('');
                $(this).addClass('d-none');
                activeSearch = '';
                applyFilters();
            });

            $('.filter-pill').on('click', function () {
                $('.filter-pill').removeClass('active');
                $(this).addClass('active');
                activeFilter = $(this).data('filter');
                applyFilters();
            });

            // ── Master Signature Pad Setup ────────────────────────────────────────
            const masterCanvas = document.getElementById('master-canvas');
            const masterPad = new SignaturePad(masterCanvas, { 
                backgroundColor: 'rgba(255, 255, 255, 0)',
                penColor: 'rgb(0, 0, 0)'
            });

            function resizeMasterCanvas() {
                if (!masterCanvas) return;
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                const rect = masterCanvas.getBoundingClientRect();
                if (rect.width === 0) return;

                const data = masterPad.toData();
                masterCanvas.width = rect.width * ratio;
                masterCanvas.height = (rect.height || 150) * ratio;
                const ctx = masterCanvas.getContext('2d');
                ctx.scale(ratio, ratio);
                masterPad.clear();
                if (data && data.length > 0) {
                    masterPad.fromData(data);
                }
            }
            window.addEventListener('resize', resizeMasterCanvas);
            $('#collapseMasterParaf').on('shown.bs.collapse', function () {
                setTimeout(resizeMasterCanvas, 150);
            });
            setTimeout(resizeMasterCanvas, 250);

            $('#btn-clear-master').on('click', function () {
                masterPad.clear();
            });

            // LocalStorage key for this token
            const masterStorageKey = 'master_paraf_' + '{{ $token }}';
            let activeMasterParaf = localStorage.getItem(masterStorageKey) || null;

            function updateMasterUI() {
                if (activeMasterParaf) {
                    $('#master-preview-img').attr('src', activeMasterParaf).show();
                    $('#master-preview-placeholder').hide();
                    $('#master-status-text').html('<span class="text-success fw-bold"><i class="fa-solid fa-circle-check me-1"></i>Master Paraf Aktif</span>. Siap diterapkan ke peserta.');
                    $('#btn-apply-all-master').removeClass('disabled').prop('disabled', false);
                    $('#btn-delete-master').removeClass('d-none');
                    $('.btn-apply-master-row').removeClass('d-none');
                } else {
                    $('#master-preview-img').hide().attr('src', '');
                    $('#master-preview-placeholder').show();
                    $('#master-status-text').text('Silakan gambar dan simpan master paraf Anda pada kanvas di samping.');
                    $('#btn-apply-all-master').addClass('disabled').prop('disabled', true);
                    $('#btn-delete-master').addClass('d-none');
                    $('.btn-apply-master-row').addClass('d-none');
                }
            }
            updateMasterUI();

            $('#btn-save-master').on('click', function () {
                if (masterPad.isEmpty()) {
                    alert('Silakan gambar paraf pada canvas master terlebih dahulu.');
                    return;
                }
                activeMasterParaf = masterPad.toDataURL('image/png');
                localStorage.setItem(masterStorageKey, activeMasterParaf);
                updateMasterUI();
                showToast('<i class="fa-solid fa-circle-check me-1"></i> Master paraf berhasil disimpan!');
            });

            $('#btn-delete-master').on('click', function () {
                if (confirm('Hapus master paraf yang tersimpan?')) {
                    localStorage.removeItem(masterStorageKey);
                    activeMasterParaf = null;
                    masterPad.clear();
                    updateMasterUI();
                    showToast('Master paraf telah dihapus.');
                }
            });

            // ── AJAX Function to Save Paraf ───────────────────────────────────────
            function saveParafMahasiswa(nim, parafData, callback) {
                $.ajax({
                    url: postUrl,
                    type: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                    data: {
                        nim: nim,
                        paraf: parafData
                    },
                    dataType: 'json',
                    success: function (res) {
                        if (res.success) {
                            renderParafInRow(res.nim, res.paraf, res.waktu_paraf);
                            updateCounters();
                            applyFilters();
                            if (callback) callback(true, res);
                        } else {
                            if (callback) callback(false, res.message);
                        }
                    },
                    error: function (xhr) {
                        let errMsg = 'Gagal menyimpan paraf.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errMsg = xhr.responseJSON.message;
                        }
                        if (callback) callback(false, errMsg);
                    }
                });
            }

            function renderParafInRow(nim, parafData, waktuStr) {
                // 1. Update di Tampilan Tabel
                const tableCell = $('#col-paraf-' + nim);
                const tableRow = $('#row-' + nim);
                tableRow.attr('data-paraf', '1');

                const tableHtml = `
                    <div class="d-flex align-items-center justify-content-center gap-2">
                        <img src="${parafData}" alt="Paraf" class="border rounded bg-white shadow-sm p-1" style="max-height: 42px; max-width: 90px; object-fit: contain;">
                        <div class="text-start">
                            <span class="badge bg-success-subtle text-success small d-block">
                                Diparaf ✓
                            </span>
                            <small class="text-muted" style="font-size: 11px;">
                                ${waktuStr}
                            </small>
                            <div>
                                <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none small btn-open-modal-paraf" data-nim="${nim}" data-nama="${tableRow.find('.fw-bold.text-dark').text()}" style="font-size: 11px;">
                                    <i class="fa-solid fa-pen-to-square me-1"></i>Ubah
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                tableCell.html(tableHtml);

                // 2. Update di Tampilan Kartu Mobile
                const cardAction = $('#card-action-' + nim);
                const cardItem = $('#card-item-' + nim);
                cardItem.attr('data-paraf', '1');
                cardItem.find('.student-mobile-card').addClass('card-diparaf');

                const cardHtml = `
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <img src="${parafData}" alt="Paraf" class="border rounded bg-white shadow-sm p-1" style="max-height: 38px; max-width: 80px; object-fit: contain;">
                            <div>
                                <span class="badge bg-success small py-1"><i class="fa-solid fa-check me-1"></i>Diparaf</span>
                                <small class="text-muted d-block" style="font-size: 10px;">${waktuStr}</small>
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm rounded-pill btn-open-modal-paraf" data-nim="${nim}" data-nama="${cardItem.find('h6').text()}">
                            <i class="fa-solid fa-pen-to-square me-1"></i>Ubah
                        </button>
                    </div>
                `;
                cardAction.html(cardHtml);
            }

            function updateCounters() {
                let diparafCount = 0;
                $('#table-peserta tbody tr.peserta-item[data-status="lolos"]').each(function () {
                    if (String($(this).attr('data-paraf')) === '1') {
                        diparafCount++;
                    }
                });
                // Update table counter & stat card
                $('#stat-paraf').text(diparafCount);
                $('#sticky-paraf-count').text(diparafCount);

                const totalLolos = parseInt($('#stat-lolos').text()) || 0;
                const belumDiparaf = Math.max(0, totalLolos - diparafCount);

                $('.count-pill-sudah').text(diparafCount);
                $('.count-pill-belum').text(belumDiparaf);
            }

            // ── Terapkan Master Paraf ke 1 Baris / Kartu ───────────────────────────
            $(document).on('click', '.btn-apply-master-row', function () {
                if (!activeMasterParaf) {
                    alert('Belum ada master paraf yang disimpan.');
                    return;
                }
                const btn = $(this);
                const nim = btn.data('nim');
                const origHtml = btn.html();
                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

                saveParafMahasiswa(nim, activeMasterParaf, function (success, data) {
                    btn.prop('disabled', false).html(origHtml);
                    if (success) {
                        showToast('<i class="fa-solid fa-circle-check me-1"></i> Paraf mahasiswa ' + nim + ' berhasil diterapkan.');
                    } else {
                        showToast(data, true);
                    }
                });
            });

            // ── Terapkan Master Paraf ke Semua Mahasiswa Lolos ─────────────────────
            $('#btn-apply-all-master').on('click', function () {
                if (!activeMasterParaf) {
                    alert('Belum ada master paraf.');
                    return;
                }

                // Kumpulkan mahasiswa yang lolos dan belum diparaf
                const rowsToApply = [];
                $('#table-peserta tbody tr[data-status="lolos"]').each(function () {
                    const nim = $(this).data('nim');
                    if (nim) {
                        rowsToApply.push(nim);
                    }
                });

                if (rowsToApply.length === 0) {
                    alert('Tidak ada mahasiswa yang memenuhi syarat kelayakan ujian.');
                    return;
                }

                if (!confirm('Apakah Anda yakin ingin menerapkan master paraf ke ' + rowsToApply.length + ' mahasiswa yang lolos?')) {
                    return;
                }

                const btn = $(this);
                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Menerapkan...');

                let completed = 0;
                let successCount = 0;

                rowsToApply.forEach(function (nim) {
                    saveParafMahasiswa(nim, activeMasterParaf, function (success) {
                        completed++;
                        if (success) successCount++;

                        if (completed === rowsToApply.length) {
                            btn.prop('disabled', false).html('<i class="fa-solid fa-wand-magic-sparkles me-1"></i> Terapkan ke Semua Mahasiswa Lolos');
                            showToast('<i class="fa-solid fa-check-double me-1"></i> Selesai! Paraf berhasil diterapkan ke ' + successCount + ' mahasiswa.');
                        }
                    });
                });
            });

            // ── Modal Individu Paraf Setup ────────────────────────────────────────
            const individuCanvas = document.getElementById('individu-canvas');
            const individuPad = new SignaturePad(individuCanvas, { 
                backgroundColor: 'rgba(255, 255, 255, 0)',
                penColor: 'rgb(0, 0, 0)'
            });
            let currentIndividuNim = null;

            function resizeIndividuCanvas() {
                if (!individuCanvas) return;
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                const rect = individuCanvas.getBoundingClientRect();
                if (rect.width === 0) return;

                const data = individuPad.toData();
                individuCanvas.width = rect.width * ratio;
                individuCanvas.height = (rect.height || 200) * ratio;
                const ctx = individuCanvas.getContext('2d');
                ctx.scale(ratio, ratio);
                individuPad.clear();
                if (data && data.length > 0) {
                    individuPad.fromData(data);
                }
            }

            $('#modalParafIndividu').on('shown.bs.modal', function () {
                setTimeout(resizeIndividuCanvas, 150);
            });

            $('#btn-clear-individu').on('click', function () {
                individuPad.clear();
            });

            $(document).on('click', '.btn-open-modal-paraf', function () {
                const nim = $(this).data('nim');
                const nama = $(this).data('nama');
                currentIndividuNim = nim;

                $('#modal-mhs-nama').text(nama || '-');
                $('#modal-mhs-nim').text(nim);

                $('#modalParafIndividu').modal('show');
            });

            $('#btn-submit-individu').on('click', function () {
                if (individuPad.isEmpty()) {
                    alert('Silakan goreskan tanda tangan pada canvas terlebih dahulu.');
                    return;
                }
                if (!currentIndividuNim) return;

                const btn = $(this);
                const origHtml = btn.html();
                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...');

                const signatureData = individuPad.toDataURL('image/png');

                saveParafMahasiswa(currentIndividuNim, signatureData, function (success, data) {
                    btn.prop('disabled', false).html(origHtml);
                    if (success) {
                        $('#modalParafIndividu').modal('hide');
                        showToast('<i class="fa-solid fa-circle-check me-1"></i> Paraf mahasiswa ' + currentIndividuNim + ' berhasil disimpan!');
                    } else {
                        alert(data);
                    }
                });
            });

        });
    </script>
</body>
</html>
