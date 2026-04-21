@extends('layouts.default', ['CurrentPage' => $CurrentPage])

@section('content')
<div class="content-body">
    <div class="container">
        <div class="row">
            <div class="col-xl-12">
                @if(session('status'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="mb-3 d-flex gap-2">
                    <a href="{{ url('dosen/pertemuan') }}" class="btn btn-light">
                        <i class="fa-solid fa-arrow-left me-1"></i> Kembali
                    </a>
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#uploadDokumenModal">
                        <i class="fa-solid fa-upload me-1"></i> Upload RPS / KP
                    </button>
                </div>

                {{-- Info Jadwal --}}
                <div class="filter cm-content-box box-primary mb-3">
                    <div class="content-title SlideToolHeader">
                        <div class="cpa">
                            <i class="fa-solid fa-book-open-reader me-1"></i> Informasi Jadwal
                        </div>
                    </div>
                    <div class="cm-content-body form excerpt">
                        <div class="card-body pb-2">
                            <div class="row">
                                <div class="col-md-4 mb-2"><strong>Kode MK:</strong> {{ $jadwal->kode_mata_kuliah }}</div>
                                <div class="col-md-8 mb-2"><strong>Mata Kuliah:</strong> {{ $jadwal->nama_mata_kuliah ?? '-' }}</div>
                                <div class="col-md-6 mb-2"><strong>Pengampu:</strong> {{ trim($jadwal->nama_dosen ?? '-') }}</div>
                                <div class="col-md-6 mb-2"><strong>Kelas:</strong> {{ ($jadwal->tipe_mhs == 2 ? 'Karyawan' : 'Reguler') . ' ' . ($jadwal->rombel ?? '-') }}</div>
                                <div class="col-md-6 mb-2"><strong>Hari/Sesi:</strong> {{ $jadwal->hari }} / {{ $jadwal->sesi }}</div>
                                <div class="col-md-6 mb-2"><strong>Ruang:</strong> {{ $jadwal->ruang }}</div>
                                <div class="col-md-6 mb-2">
                                    <strong>RPS:</strong>
                                    @if(!empty($jadwal->rps))
                                        <a href="{{ asset('assets/files/' . $jadwal->rps) }}" target="_blank" class="btn btn-primary btn-sm">Lihat File</a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </div>
                                <div class="col-md-6 mb-2">
                                    <strong>KP:</strong>
                                    @if(!empty($jadwal->kp))
                                        <a href="{{ asset('assets/files/' . $jadwal->kp) }}" target="_blank" class="btn btn-primary btn-sm">Lihat File</a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Form Setting Pertemuan --}}
                <div class="filter cm-content-box box-primary">
                    <div class="content-title SlideToolHeader">
                        <div class="cpa">
                            <i class="fa-solid fa-calendar-check me-1"></i>{{ $title }}
                        </div>
                        <div class="tools">
                            <a href="javascript:void(0);" class="expand handle"><i class="fal fa-angle-down"></i></a>
                        </div>
                    </div>
                    <div class="cm-content-body form excerpt">
                        <div class="card-body pb-4">
                            <form action="{{ route('pegawai.pertemuan.save', $jadwal->id) }}" method="POST">
                                @csrf
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered align-middle">
                                        <thead>
                                            <tr>
                                                <th style="width: 160px;">ID Pertemuan</th>
                                                <th style="width: 200px;">Tanggal Pertemuan</th>
                                                <th>Kode Absen</th>
                                                <th style="width: 160px;">Aksi Absen</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($listPertemuan as $nomor)
                                                @php
                                                    $pRow    = $pertemuanByNomor[$nomor] ?? null;
                                                    $kode    = $pRow?->kode_kelas ?? null;
                                                    $expTime = $pRow?->expired_kode ?? null;
                                                    $isExpired = $expTime ? (\Carbon\Carbon::parse($expTime)->isPast()) : true;
                                                @endphp
                                                <tr>
                                                    <td>Pertemuan {{ $nomor }}</td>
                                                    <td>
                                                        <input
                                                            type="date"
                                                            class="form-control"
                                                            name="tanggal[{{ $nomor }}]"
                                                            value="{{ old('tanggal.' . $nomor, $tanggalByPertemuan[$nomor] ?? '') }}"
                                                        >
                                                    </td>
                                                    <td>
                                                        @if($kode)
                                                            <span class="fw-bold font-monospace fs-5 kode-display-{{ $nomor }}">{{ $kode }}</span>
                                                            <div class="small text-muted kode-exp-{{ $nomor }}">
                                                                Expired: {{ $expTime ? \Carbon\Carbon::parse($expTime)->format('d/m/Y H:i:s') : '-' }}
                                                                @if(!$isExpired)
                                                                    <span class="badge bg-success ms-1">Aktif</span>
                                                                @else
                                                                    <span class="badge bg-secondary ms-1">Expired</span>
                                                                @endif
                                                            </div>
                                                        @else
                                                            <span class="text-muted small kode-display-{{ $nomor }}">Belum dibuat</span>
                                                            <div class="kode-exp-{{ $nomor }}"></div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($pRow)
                                                            <button type="button"
                                                                class="btn btn-sm btn-warning btn-generate-kode"
                                                                data-nomor="{{ $nomor }}"
                                                                data-jadwal="{{ $jadwal->id }}"
                                                                data-has-kode="{{ !empty($kode) ? '1' : '0' }}"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#modalGenerateKode">
                                                                <i class="fa fa-key me-1"></i> Generate Kode
                                                            </button>
                                                        @else
                                                            <span class="text-muted small">Isi tanggal dahulu</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="mt-3">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa-solid fa-save me-1"></i> Simpan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Upload Dokumen --}}
{{-- Modal Generate Kode Absen --}}
<div class="modal fade" id="modalGenerateKode" tabindex="-1" aria-labelledby="modalGenerateKodeLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalGenerateKodeLabel">Generate Kode Absen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-3">Pilih durasi berlaku kode. Kode baru akan digenerate secara otomatis (6 karakter acak).</p>
                <div id="regenerate-warning" class="alert alert-warning d-none" role="alert">
                    <div class="fw-semibold mb-1">Peringatan Generate Ulang</div>
                    <div class="small mb-2">Kode sudah pernah dibuat. Jika Anda generate ulang, seluruh mahasiswa pada pertemuan ini akan di-set ulang menjadi <strong>tidak hadir (status 0)</strong> dan tanda tangan akan direset.</div>
                    <div class="form-check mb-0">
                        <input class="form-check-input" type="checkbox" id="confirm-regenerate">
                        <label class="form-check-label small" for="confirm-regenerate">
                            Saya memahami risiko reset presensi dan tetap ingin generate ulang.
                        </label>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Durasi Berlaku</label>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-primary btn-durasi" data-durasi="5">5 Menit</button>
                        <button type="button" class="btn btn-outline-primary btn-durasi" data-durasi="10">10 Menit</button>
                        <button type="button" class="btn btn-outline-primary btn-durasi" data-durasi="15">15 Menit</button>
                        <button type="button" class="btn btn-outline-primary btn-durasi" data-durasi="30">30 Menit</button>
                    </div>
                    <input type="hidden" id="selected-durasi" value="">
                </div>
                <div id="generate-kode-result" class="d-none">
                    <hr>
                    <div class="text-center">
                        <div class="text-muted small mb-1">Kode Absen</div>
                        <div class="fw-bold font-monospace fs-2 text-primary" id="generated-kode-text">------</div>
                        <div class="text-muted small mt-1" id="generated-kode-exp"></div>
                    </div>
                </div>
                <div id="generate-kode-error" class="alert alert-danger d-none mt-2"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-warning" id="btn-do-generate">
                    <i class="fa fa-key me-1"></i> Generate
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Upload Dokumen --}}
<div class="modal fade" id="uploadDokumenModal" tabindex="-1" aria-labelledby="uploadDokumenModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('pegawai.pertemuan.dokumen', $jadwal->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadDokumenModalLabel">Upload Dokumen RPS / KP</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">File RPS</label>
                        <input type="file" name="rps_file" class="form-control" accept=".pdf,.doc,.docx">
                        <small class="text-muted">Format: PDF, DOC, DOCX. Maks 5 MB.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">File KP</label>
                        <input type="file" name="kp_file" class="form-control" accept=".pdf,.doc,.docx">
                        <small class="text-muted">Format: PDF, DOC, DOCX. Maks 5 MB.</small>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Preview RPS Saat Ini</label>
                        @if(!empty($jadwal->rps))
                            @php
                                $rpsUrl = asset('assets/files/' . $jadwal->rps);
                                $rpsExt = strtolower(pathinfo((string) $jadwal->rps, PATHINFO_EXTENSION));
                            @endphp
                            <div class="mb-2">
                                <a href="{{ $rpsUrl }}" target="_blank">Buka File RPS</a>
                            </div>
                            @if($rpsExt === 'pdf')
                                <iframe src="{{ $rpsUrl }}" width="100%" height="300" style="border:1px solid #ddd;"></iframe>
                            @else
                                <div class="alert alert-light mb-0">
                                    Preview langsung hanya tersedia untuk file PDF. Untuk file {{ strtoupper($rpsExt) }}, silakan buka melalui tautan di atas.
                                </div>
                            @endif
                        @else
                            <div class="text-muted">Belum ada file RPS yang diupload.</div>
                        @endif
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-bold">Preview KP Saat Ini</label>
                        @if(!empty($jadwal->kp))
                            @php
                                $kpUrl = asset('assets/files/' . $jadwal->kp);
                                $kpExt = strtolower(pathinfo((string) $jadwal->kp, PATHINFO_EXTENSION));
                            @endphp
                            <div class="mb-2">
                                <a href="{{ $kpUrl }}" target="_blank">Buka File KP</a>
                            </div>
                            @if($kpExt === 'pdf')
                                <iframe src="{{ $kpUrl }}" width="100%" height="300" style="border:1px solid #ddd;"></iframe>
                            @else
                                <div class="alert alert-light mb-0">
                                    Preview langsung hanya tersedia untuk file PDF. Untuk file {{ strtoupper($kpExt) }}, silakan buka melalui tautan di atas.
                                </div>
                            @endif
                        @else
                            <div class="text-muted">Belum ada file KP yang diupload.</div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-save me-1"></i> Upload
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('local-js')
<script>
(function () {
    let selectedNomor   = null;
    let selectedJadwal  = null;
    let selectedDurasi  = null;
    let selectedHasKode = false;

    document.querySelectorAll('.btn-generate-kode').forEach(function (btn) {
        btn.addEventListener('click', function () {
            selectedNomor  = this.dataset.nomor;
            selectedJadwal = this.dataset.jadwal;
            selectedDurasi = null;
            selectedHasKode = this.dataset.hasKode === '1';
            document.querySelectorAll('.btn-durasi').forEach(b => { b.classList.remove('active','btn-primary'); b.classList.add('btn-outline-primary'); });
            document.getElementById('selected-durasi').value = '';
            document.getElementById('generate-kode-result').classList.add('d-none');
            document.getElementById('generate-kode-error').classList.add('d-none');
            document.getElementById('confirm-regenerate').checked = false;

            const warningEl = document.getElementById('regenerate-warning');
            if (selectedHasKode) {
                warningEl.classList.remove('d-none');
            } else {
                warningEl.classList.add('d-none');
            }
        });
    });

    document.querySelectorAll('.btn-durasi').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.btn-durasi').forEach(b => { b.classList.remove('active','btn-primary'); b.classList.add('btn-outline-primary'); });
            this.classList.remove('btn-outline-primary');
            this.classList.add('active','btn-primary');
            selectedDurasi = this.dataset.durasi;
        });
    });

    document.getElementById('btn-do-generate').addEventListener('click', function () {
        if (!selectedDurasi) {
            const errEl = document.getElementById('generate-kode-error');
            errEl.textContent = 'Silakan pilih durasi terlebih dahulu.';
            errEl.classList.remove('d-none');
            return;
        }

        if (selectedHasKode && !document.getElementById('confirm-regenerate').checked) {
            const errEl = document.getElementById('generate-kode-error');
            errEl.textContent = 'Centang konfirmasi terlebih dahulu untuk generate ulang.';
            errEl.classList.remove('d-none');
            return;
        }

        document.getElementById('generate-kode-error').classList.add('d-none');
        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Proses...';

        const formData = new FormData();
        formData.append('id_pertemuan', selectedNomor);
        formData.append('durasi', selectedDurasi);
        formData.append('confirm_regenerate', selectedHasKode && document.getElementById('confirm-regenerate').checked ? '1' : '0');
        formData.append('_token', '{{ csrf_token() }}');

        fetch('{{ url("dosen/pertemuan") }}/' + selectedJadwal + '/generate-kode', {
            method: 'POST',
            body: formData,
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-key me-1"></i> Generate';
            if (data.success) {
                document.getElementById('generate-kode-result').classList.remove('d-none');
                document.getElementById('generate-kode-error').classList.add('d-none');
                document.getElementById('generated-kode-text').textContent = data.kode;
                const resetInfo = data.is_regenerate ? ' | Presensi direset: ' + (data.peserta_count || 0) + ' mahasiswa' : '';
                document.getElementById('generated-kode-exp').textContent = 'Berlaku sampai: ' + data.expired_full + resetInfo;
                const displayEl = document.querySelector('.kode-display-' + selectedNomor);
                const expEl     = document.querySelector('.kode-exp-' + selectedNomor);
                if (displayEl) { displayEl.className = 'fw-bold font-monospace fs-5 kode-display-' + selectedNomor; displayEl.textContent = data.kode; }
                if (expEl)     { expEl.innerHTML = 'Expired: ' + data.expired_full + ' <span class="badge bg-success ms-1">Aktif</span>'; }

                // after first generate, subsequent action becomes regenerate
                selectedHasKode = true;
                const currentBtn = document.querySelector('.btn-generate-kode[data-nomor="' + selectedNomor + '"]');
                if (currentBtn) {
                    currentBtn.setAttribute('data-has-kode', '1');
                }
            } else {
                const errEl = document.getElementById('generate-kode-error');
                errEl.textContent = data.message || 'Gagal generate kode.';
                errEl.classList.remove('d-none');
                document.getElementById('generate-kode-result').classList.add('d-none');
            }
        })
        .catch(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-key me-1"></i> Generate';
            const errEl = document.getElementById('generate-kode-error');
            errEl.textContent = 'Terjadi kesalahan jaringan. Silakan coba lagi.';
            errEl.classList.remove('d-none');
        });
    });
}());
</script>
@endsection
