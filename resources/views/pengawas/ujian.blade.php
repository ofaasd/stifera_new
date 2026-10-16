<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Presensi & Paraf Pengawas Ujian {{ strtoupper($jenis) }} | SIAKAD STIFERA</title>
    
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        body {
            background-color: #f4f6f9;
            color: #333;
            font-family: 'Poppins', sans-serif, -apple-system, BlinkMacSystemFont;
        }
        .header-box {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: #fff;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 10px 25px rgba(30, 60, 114, 0.15);
        }
        .stat-card {
            border-radius: 12px;
            transition: all 0.2s ease;
        }
        .stat-card:hover {
            transform: translateY(-2px);
        }
        .table-success {
            background-color: #edf7ee !important;
        }
        .table-danger {
            background-color: #fdf0f0 !important;
        }
        .signature-wrapper {
            border: 2px dashed #b5b5c3;
            border-radius: 10px;
            background: #ffffff;
            position: relative;
            cursor: crosshair;
        }
        .signature-canvas {
            display: block;
            width: 100%;
            border-radius: 8px;
        }
        .badge-exam {
            font-size: 1rem;
            letter-spacing: 0.5px;
            padding: 8px 16px;
            border-radius: 50rem;
        }
        .table td, .table th {
            vertical-align: middle;
        }
        .table-responsive {
            border-radius: 12px;
            overflow: hidden;
        }
    </style>
</head>
<body class="py-4">
    <div class="container-fluid px-3 px-md-5">
        
        {{-- Header Banner --}}
        <div class="header-box mb-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <img src="{{ asset('images/logo/logo.png') }}" alt="Logo" style="height: 52px; background: #fff; border-radius: 8px; padding: 4px;" onerror="this.style.display='none'">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-warning text-dark fw-bold badge-exam">
                                UJIAN {{ strtoupper($jenis) }}
                            </span>
                            <span class="badge bg-white text-primary fw-semibold">
                                TA {{ $jadwal->ta_awal ?? '-' }}/{{ $jadwal->ta_akhir ?? '-' }} 
                                ({{ (int) ($jadwal->ta_jenis ?? 0) === 1 ? 'Ganjil' : ((int) ($jadwal->ta_jenis ?? 0) === 2 ? 'Genap' : '-') }})
                            </span>
                        </div>
                        <h3 class="fw-bold mb-0 text-white">
                            {{ $jadwal->nama_mata_kuliah ?? '-' }} 
                            <small class="fs-6 opacity-75">({{ $jadwal->kode_mata_kuliah ?? '-' }})</small>
                        </h3>
                    </div>
                </div>
                <div class="text-end">
                    <span class="badge bg-info-subtle text-info fs-6 px-3 py-2 border border-info">
                        <i class="fa-solid fa-door-open me-1"></i> Ruang: <strong>{{ $jadwal->ruang ?? '-' }}</strong>
                    </span>
                    <div class="small mt-1 text-light opacity-75">
                        <i class="fa-solid fa-clock me-1"></i>
                        {{ now()->translatedFormat('l, d F Y') }}
                    </div>
                </div>
            </div>

            <hr class="my-3 opacity-25">

            <div class="row g-2 small text-light">
                <div class="col-md-3 col-6">
                    <span class="opacity-75">Dosen Pengampu:</span><br>
                    <strong>{{ $jadwal->nama_dosen ?? '-' }}</strong>
                </div>
                <div class="col-md-2 col-6">
                    <span class="opacity-75">Beban SKS:</span><br>
                    <strong>{{ $jadwal->jumlah_sks ?? 0 }} SKS</strong>
                </div>
                <div class="col-md-3 col-6">
                    <span class="opacity-75">Jadwal Ujian Teori:</span><br>
                    <strong>
                        @if(!empty($tglTeori))
                            {{ \Carbon\Carbon::parse($tglTeori)->translatedFormat('d M Y') }}
                            @if(!empty($jamTeori)) ({{ $jamTeori->nama_sesi }}: {{ \Carbon\Carbon::parse($jamTeori->mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jamTeori->selesai)->format('H:i') }}) @endif
                        @else
                            -
                        @endif
                    </strong>
                </div>
                <div class="col-md-4 col-6">
                    <span class="opacity-75">Jadwal Ujian Praktik:</span><br>
                    <strong>
                        @if(!empty($tglPraktik))
                            {{ \Carbon\Carbon::parse($tglPraktik)->translatedFormat('d M Y') }}
                            @if(!empty($jamPraktik)) ({{ $jamPraktik->nama_sesi }}: {{ \Carbon\Carbon::parse($jamPraktik->mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jamPraktik->selesai)->format('H:i') }}) @endif
                        @else
                            -
                        @endif
                    </strong>
                </div>
            </div>
        </div>

        {{-- Statistics Row --}}
        <div class="row g-3 mb-4">
            <div class="col-md-3 col-6">
                <div class="card border-0 shadow-sm stat-card bg-white p-3">
                    <div class="d-flex align-items-center">
                        <div class="badge bg-primary-subtle text-primary p-3 rounded-circle me-3">
                            <i class="fa-solid fa-users fs-4"></i>
                        </div>
                        <div>
                            <span class="text-muted small">Total Peserta</span>
                            <h4 class="fw-bold mb-0" id="stat-total">{{ $totalPeserta }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card border-0 shadow-sm stat-card bg-white p-3">
                    <div class="d-flex align-items-center">
                        <div class="badge bg-success-subtle text-success p-3 rounded-circle me-3">
                            <i class="fa-solid fa-user-check fs-4"></i>
                        </div>
                        <div>
                            <span class="text-muted small">Bisa Ikut Ujian</span>
                            <h4 class="fw-bold text-success mb-0" id="stat-lolos">{{ $totalLolos }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card border-0 shadow-sm stat-card bg-white p-3">
                    <div class="d-flex align-items-center">
                        <div class="badge bg-danger-subtle text-danger p-3 rounded-circle me-3">
                            <i class="fa-solid fa-user-xmark fs-4"></i>
                        </div>
                        <div>
                            <span class="text-muted small">Tidak Bisa Ikut</span>
                            <h4 class="fw-bold text-danger mb-0" id="stat-tidak-lolos">{{ $totalPeserta - $totalLolos }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card border-0 shadow-sm stat-card bg-white p-3">
                    <div class="d-flex align-items-center">
                        <div class="badge bg-info-subtle text-info p-3 rounded-circle me-3">
                            <i class="fa-solid fa-signature fs-4"></i>
                        </div>
                        <div>
                            <span class="text-muted small">Sudah Diparaf</span>
                            <h4 class="fw-bold text-info mb-0" id="stat-paraf">{{ $totalDiparaf }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Master Paraf Pengawas Card --}}
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px;">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <div class="badge bg-primary p-2 rounded-circle text-white">
                        <i class="fa-solid fa-pen-nib"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0">Master Paraf Pengawas</h5>
                        <p class="text-muted small mb-0">Tanda tangani satu kali di sini, lalu terapkan ke mahasiswa yang hadir dengan satu klik.</p>
                    </div>
                </div>
                <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseMasterParaf">
                    <i class="fa-solid fa-chevron-down me-1"></i> Buka / Tutup Panel
                </button>
            </div>
            <div class="collapse show" id="collapseMasterParaf">
                <div class="card-body bg-light-subtle">
                    <div class="row g-4 align-items-center">
                        <div class="col-lg-5 col-md-6">
                            <label class="form-label fw-bold small text-muted text-uppercase mb-1">
                                Gambar Paraf Anda di Kotak Berikut:
                            </label>
                            <div class="signature-wrapper shadow-sm">
                                <canvas id="master-canvas" class="signature-canvas" height="150"></canvas>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <button type="button" id="btn-clear-master" class="btn btn-outline-secondary btn-sm">
                                    <i class="fa-solid fa-eraser me-1"></i> Bersihkan
                                </button>
                                <button type="button" id="btn-save-master" class="btn btn-primary btn-sm">
                                    <i class="fa-solid fa-check me-1"></i> Simpan Master Paraf
                                </button>
                            </div>
                        </div>

                        <div class="col-lg-7 col-md-6">
                            <div class="p-3 border rounded bg-white shadow-sm">
                                <h6 class="fw-bold mb-2 text-dark">Status Master Paraf Saat Ini:</h6>
                                <div id="master-preview-box" class="d-flex align-items-center gap-3">
                                    <div class="p-2 border rounded bg-light" style="min-width: 140px; min-height: 70px; display: flex; align-items: center; justify-content: center;">
                                        <img id="master-preview-img" src="" alt="Preview Master Paraf" style="max-height: 60px; max-width: 130px; display: none;">
                                        <span id="master-preview-placeholder" class="text-muted small">Belum ada master paraf</span>
                                    </div>
                                    <div>
                                        <div id="master-status-text" class="text-muted small mb-2">
                                            Silakan buat dan simpan master paraf Anda di sebelah kiri.
                                        </div>
                                        <div class="d-flex gap-2">
                                            <button type="button" id="btn-apply-all-master" class="btn btn-success btn-sm disabled" disabled>
                                                <i class="fa-solid fa-wand-magic-sparkles me-1"></i> Terapkan ke Semua Mahasiswa Lolos
                                            </button>
                                            <button type="button" id="btn-delete-master" class="btn btn-outline-danger btn-sm d-none">
                                                <i class="fa-solid fa-trash me-1"></i> Hapus
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

        {{-- Table Peserta Ujian --}}
        <div class="card border-0 shadow-sm" style="border-radius: 14px;">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="fw-bold mb-0">
                    <i class="fa-solid fa-clipboard-user me-2 text-primary"></i>Daftar Mahasiswa Peserta Ujian
                </h5>
                <div class="d-flex align-items-center gap-2">
                    <input type="text" id="search-input" class="form-control form-control-sm" placeholder="Cari NIM atau Nama..." style="max-width: 240px;">
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.location.reload();">
                        <i class="fa-solid fa-rotate-right"></i>
                    </button>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="table-peserta">
                        <thead class="bg-light text-dark fw-bold text-center">
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th style="width: 90px;">No. Kursi</th>
                                <th style="width: 140px;">NIM</th>
                                <th style="min-width: 200px;" class="text-start">Nama Mahasiswa</th>
                                <th style="min-width: 220px;">Status Validasi</th>
                                <th style="width: 220px;">Paraf Pengawas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($peserta as $idx => $p)
                                <tr class="{{ $p->is_lolos ? 'table-success' : 'table-danger' }}" id="row-{{ $p->nim }}" data-nim="{{ $p->nim }}" data-nama="{{ strtolower($p->nama_mahasiswa ?? '') }}">
                                    <td class="text-center fw-semibold">{{ $idx + 1 }}</td>
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
                                        <div class="small text-muted">Ruang: {{ $p->ruang ?? $jadwal->ruang ?? '-' }}</div>
                                    </td>
                                    <td class="text-center">
                                        @if($p->is_lolos)
                                            <span class="badge bg-success px-3 py-2">
                                                <i class="fa-solid fa-circle-check me-1"></i>Bisa Ikut Ujian
                                            </span>
                                            <div class="small text-success mt-1 fw-semibold">
                                                <i class="fa-solid fa-check-double me-1"></i>Dosen Wali & BAK Lengkap
                                            </div>
                                        @else
                                            <span class="badge bg-danger px-3 py-2">
                                                <i class="fa-solid fa-circle-xmark me-1"></i>Tidak Bisa Ikut Ujian
                                            </span>
                                            <div class="small text-danger fw-semibold mt-1">
                                                <i class="fa-solid fa-triangle-exclamation me-1"></i>{{ $p->keterangan }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="text-center" id="col-paraf-{{ $p->nim }}">
                                        @if($p->is_lolos)
                                            @if(!empty($p->paraf))
                                                <div class="d-flex align-items-center justify-content-center gap-2">
                                                    <img src="{{ $p->paraf }}" alt="Paraf" class="border rounded bg-white shadow-sm p-1" style="max-height: 42px; max-width: 90px; object-fit: contain;">
                                                    <div class="text-start">
                                                        <span class="badge bg-success-subtle text-success small d-block">
                                                            Diparaf ✓
                                                        </span>
                                                        <small class="text-muted" style="font-size: 11px;">
                                                            {{ !empty($p->waktu_paraf) ? \Carbon\Carbon::parse($p->waktu_paraf)->translatedFormat('d/m/Y H:i') : '' }}
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
                                                    <button type="button" class="btn btn-primary btn-sm btn-open-modal-paraf shadow-sm" data-nim="{{ $p->nim }}" data-nama="{{ $p->nama_mahasiswa }}">
                                                        <i class="fa-solid fa-signature me-1"></i>Paraf
                                                    </button>
                                                    <button type="button" class="btn btn-outline-success btn-sm btn-apply-master-row d-none shadow-sm" data-nim="{{ $p->nim }}" title="Terapkan Master Paraf">
                                                        <i class="fa-solid fa-stamp"></i>
                                                    </button>
                                                </div>
                                            @endif
                                        @else
                                            <span class="badge bg-light text-muted border py-2 px-3">
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
        </div>

    </div>

    {{-- Modal Paraf Individu --}}
    <div class="modal fade" id="modalParafIndividu" tabindex="-1" aria-labelledby="modalParafIndividuLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <div class="modal-header bg-light border-0">
                    <div>
                        <h5 class="modal-title fw-bold" id="modalParafIndividuLabel">
                            <i class="fa-solid fa-signature me-2 text-primary"></i>Paraf Mahasiswa
                        </h5>
                        <p class="text-muted small mb-0">Bubuhkan paraf pengawas untuk mahasiswa ini:</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="p-3 bg-light rounded mb-3">
                        <div class="fw-bold text-dark fs-6" id="modal-mhs-nama">-</div>
                        <div class="small text-muted font-monospace" id="modal-mhs-nim">-</div>
                    </div>

                    <label class="form-label fw-bold small text-muted text-uppercase mb-1">
                        Area Tanda Tangan / Paraf:
                    </label>
                    <div class="signature-wrapper mb-2">
                        <canvas id="individu-canvas" class="signature-canvas" height="180"></canvas>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <button type="button" id="btn-clear-individu" class="btn btn-outline-secondary btn-sm">
                            <i class="fa-solid fa-eraser me-1"></i> Bersihkan
                        </button>
                        <span class="text-muted small" id="individu-hint">Gunakan mouse atau sentuhan jari</span>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="button" id="btn-submit-individu" class="btn btn-primary btn-sm">
                        <i class="fa-solid fa-check me-1"></i> Simpan Paraf
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Toast Notification --}}
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1080;">
        <div id="toastNotification" class="toast align-items-center text-white bg-success border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
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
            const toast = new bootstrap.Toast(toastEl);
            function showToast(msg, isError = false) {
                $('#toastMessage').html(msg);
                if (isError) {
                    $('#toastNotification').removeClass('bg-success').addClass('bg-danger');
                } else {
                    $('#toastNotification').removeClass('bg-danger').addClass('bg-success');
                }
                toast.show();
            }

            // ── Live Filter Search ────────────────────────────────────────────────
            $('#search-input').on('keyup', function () {
                const query = $(this).val().toLowerCase().trim();
                $('#table-peserta tbody tr').each(function () {
                    const nim = String($(this).data('nim') || '').toLowerCase();
                    const nama = String($(this).data('nama') || '').toLowerCase();
                    if (nim.includes(query) || nama.includes(query)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });

            // ── Master Signature Pad Setup ────────────────────────────────────────
            const masterCanvas = document.getElementById('master-canvas');
            const masterPad = new SignaturePad(masterCanvas, { backgroundColor: 'rgb(255, 255, 255)' });

            function resizeMasterCanvas() {
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                masterCanvas.width = masterCanvas.offsetWidth * ratio;
                masterCanvas.height = masterCanvas.offsetHeight * ratio;
                masterCanvas.getContext('2d').scale(ratio, ratio);
                masterPad.clear();
            }
            window.addEventListener('resize', resizeMasterCanvas);
            setTimeout(resizeMasterCanvas, 200);

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
                    $('#master-status-text').html('<span class="text-success fw-bold"><i class="fa-solid fa-circle-check me-1"></i>Master Paraf Aktif</span>. Anda dapat langsung menerapkannya.');
                    $('#btn-apply-all-master').removeClass('disabled').prop('disabled', false);
                    $('#btn-delete-master').removeClass('d-none');
                    $('.btn-apply-master-row').removeClass('d-none');
                } else {
                    $('#master-preview-img').hide().attr('src', '');
                    $('#master-preview-placeholder').show();
                    $('#master-status-text').text('Silakan buat dan simpan master paraf Anda di sebelah kiri.');
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
                    success: function (res) {
                        if (res.success) {
                            renderParafInRow(res.nim, res.paraf, res.waktu_paraf);
                            updateCounters();
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
                const col = $('#col-paraf-' + nim);
                const html = `
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
                                <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none small btn-open-modal-paraf" data-nim="${nim}" data-nama="${$('#row-' + nim).find('.fw-bold.text-dark').text()}" style="font-size: 11px;">
                                    <i class="fa-solid fa-pen-to-square me-1"></i>Ubah
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                col.html(html);
            }

            function updateCounters() {
                let diparafCount = 0;
                $('#table-peserta tbody tr').each(function () {
                    if ($(this).find('img').length > 0) {
                        diparafCount++;
                    }
                });
                $('#stat-paraf').text(diparafCount);
            }

            // ── Terapkan Master Paraf ke 1 Baris ───────────────────────────────────
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
                        showToast('<i class="fa-solid fa-circle-check me-1"></i> Paraf mahasiswa ' + nim + ' berhasil diperbarui.');
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

                // Kumpulkan mahasiswa yang lolos (table-success)
                const rowsToApply = [];
                $('#table-peserta tbody tr.table-success').each(function () {
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
            const individuPad = new SignaturePad(individuCanvas, { backgroundColor: 'rgb(255, 255, 255)' });
            let currentIndividuNim = null;

            function resizeIndividuCanvas() {
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                individuCanvas.width = individuCanvas.offsetWidth * ratio;
                individuCanvas.height = individuCanvas.offsetHeight * ratio;
                individuCanvas.getContext('2d').scale(ratio, ratio);
                individuPad.clear();
            }

            $('#modalParafIndividu').on('shown.bs.modal', function () {
                resizeIndividuCanvas();
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
                    alert('Silakan tanda tangani canvas terlebih dahulu.');
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
