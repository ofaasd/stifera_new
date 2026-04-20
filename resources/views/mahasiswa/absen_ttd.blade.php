@extends('layouts.default', ['CurrentPage' => $CurrentPage])

@section('local-css')
<style>
    .card { height: auto !important; }

    #signature-pad-wrapper {
        border: 2px dashed #ced4da;
        border-radius: 8px;
        background: #fff;
        cursor: crosshair;
        touch-action: none;
    }

    #signature-canvas {
        display: block;
        width: 100%;
        height: 220px;
    }

    .ttd-actions {
        gap: 8px;
    }
</style>
@endsection

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-7 col-md-9">

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                {{-- Info Pertemuan --}}
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <h4 class="mb-3">Tanda Tangan Kehadiran</h4>
                        <div class="row small">
                            <div class="col-md-6 mb-1">
                                <span class="text-muted">Mata Kuliah</span>
                                <div class="fw-semibold">{{ $pertemuan->nama_mata_kuliah ?? '-' }}</div>
                            </div>
                            <div class="col-md-6 mb-1">
                                <span class="text-muted">Kode MK</span>
                                <div class="fw-semibold">{{ $pertemuan->kode_mata_kuliah ?? '-' }}</div>
                            </div>
                            <div class="col-md-6 mb-1">
                                <span class="text-muted">Dosen Pengampu</span>
                                <div class="fw-semibold">{{ $pertemuan->nama_dosen ?? '-' }}</div>
                            </div>
                            <div class="col-md-6 mb-1">
                                <span class="text-muted">Pertemuan ke-</span>
                                <div class="fw-semibold">{{ $pertemuan->id_pertemuan ?? '-' }}</div>
                            </div>
                            <div class="col-md-6 mb-1">
                                <span class="text-muted">Tanggal</span>
                                <div class="fw-semibold">
                                    {{ $pertemuan->tgl_pertemuan ? \Carbon\Carbon::parse($pertemuan->tgl_pertemuan)->translatedFormat('d F Y') : '-' }}
                                </div>
                            </div>
                            <div class="col-md-6 mb-1">
                                <span class="text-muted">Jadwal</span>
                                <div class="fw-semibold">{{ ($pertemuan->hari ?? '-') . ' / ' . ($pertemuan->sesi ?? '-') . ' / ' . ($pertemuan->ruang ?? '-') }}</div>
                            </div>
                            <div class="col-md-6 mb-1">
                                <span class="text-muted">NIM</span>
                                <div class="fw-semibold">{{ $mahasiswa->nim ?? '-' }}</div>
                            </div>
                            <div class="col-md-6 mb-1">
                                <span class="text-muted">Nama</span>
                                <div class="fw-semibold">{{ $mahasiswa->nama ?? '-' }}</div>
                            </div>
                        </div>

                        {{-- Countdown timer --}}
                        @php
                            $msLeft = max(0, \Carbon\Carbon::parse($pertemuan->expired_kode)->diffInSeconds(now(), false) * -1 * 1000);
                        @endphp
                        <div class="mt-3">
                            <span class="text-muted small">Kode berakhir dalam: </span>
                            <span id="countdown-timer" class="fw-bold text-danger"></span>
                        </div>
                    </div>
                </div>

                {{-- Signature Form --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="mb-3">Bubuhkan Tanda Tangan</h5>
                        <p class="text-muted small mb-3">Tanda tangani kolom di bawah ini menggunakan mouse atau layar sentuh.</p>

                        <div id="signature-pad-wrapper" class="mb-2">
                            <canvas id="signature-canvas"></canvas>
                        </div>

                        <div class="d-flex ttd-actions mb-4">
                            <button type="button" id="btn-clear" class="btn btn-sm btn-outline-secondary">
                                <i class="fa fa-eraser me-1"></i> Bersihkan
                            </button>
                            <span class="text-muted small align-self-center ms-auto" id="ttd-hint">Belum ada tanda tangan</span>
                        </div>

                        <form action="{{ route('mahasiswa.absen.simpan') }}" method="POST" id="form-absen">
                            @csrf
                            <input type="hidden" name="absen_token" value="{{ $token }}">
                            <input type="hidden" name="ttd" id="ttd-data">

                            @if($errors->has('ttd'))
                                <div class="alert alert-danger">{{ $errors->first('ttd') }}</div>
                            @endif

                            <div class="d-grid">
                                <button type="submit" class="btn btn-success btn-lg" id="btn-submit-absen" disabled>
                                    <i class="fa fa-check me-1"></i> Simpan Kehadiran
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@section('local-js')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
<script>
(function () {
    // ── Signature Pad ──────────────────────────────────────────────────────
    const canvas  = document.getElementById('signature-canvas');
    const wrapper = document.getElementById('signature-pad-wrapper');
    const pad     = new SignaturePad(canvas, { backgroundColor: 'rgb(255,255,255)' });
    const btnSubmit = document.getElementById('btn-submit-absen');
    const ttdInput  = document.getElementById('ttd-data');
    const hint      = document.getElementById('ttd-hint');

    function resizeCanvas() {
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        const w = wrapper.offsetWidth;
        const h = 220;
        canvas.width  = w * ratio;
        canvas.height = h * ratio;
        canvas.getContext('2d').scale(ratio, ratio);
        canvas.style.width  = w + 'px';
        canvas.style.height = h + 'px';
        pad.clear();
        btnSubmit.disabled = true;
        hint.textContent = 'Belum ada tanda tangan';
    }

    window.addEventListener('resize', resizeCanvas);
    resizeCanvas();

    pad.addEventListener('endStroke', function () {
        if (!pad.isEmpty()) {
            ttdInput.value = pad.toDataURL('image/png');
            btnSubmit.disabled = false;
            hint.textContent = 'Tanda tangan sudah ada ✓';
            hint.className = 'text-success small align-self-center ms-auto';
        }
    });

    document.getElementById('btn-clear').addEventListener('click', function () {
        pad.clear();
        ttdInput.value = '';
        btnSubmit.disabled = true;
        hint.textContent = 'Belum ada tanda tangan';
        hint.className = 'text-muted small align-self-center ms-auto';
    });

    // Prevent form submit if no signature
    document.getElementById('form-absen').addEventListener('submit', function (e) {
        if (pad.isEmpty()) {
            e.preventDefault();
            alert('Silakan bubuhkan tanda tangan terlebih dahulu.');
        }
    });

    // ── Countdown Timer ────────────────────────────────────────────────────
    const expiredAt = {{ $msLeft ?? 0 }};
    let remaining   = Math.floor(expiredAt / 1000);
    const timerEl   = document.getElementById('countdown-timer');

    function updateTimer() {
        if (remaining <= 0) {
            timerEl.textContent = 'Kode sudah expired!';
            btnSubmit.disabled = true;
            return;
        }
        const m = Math.floor(remaining / 60);
        const s = remaining % 60;
        timerEl.textContent = m + ':' + String(s).padStart(2, '0');
        remaining--;
        setTimeout(updateTimer, 1000);
    }
    updateTimer();
}());
</script>
@endsection
