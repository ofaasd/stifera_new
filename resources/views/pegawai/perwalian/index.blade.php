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

                <div class="filter cm-content-box box-primary">
                    <div class="content-title SlideToolHeader">
                        <div class="cpa">
                            <i class="fa-solid fa-users me-1"></i>{{ $title }}
                        </div>
                        <div class="tools">
                            <a href="javascript:void(0);" class="expand handle"><i class="fal fa-angle-down"></i></a>
                        </div>
                    </div>
                    <div class="cm-content-body form excerpt">
                        <div class="card-body pb-4">
                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-3">
                                <div>
                                    <h6 class="mb-1">Dosen Wali</h6>
                                    <p class="mb-0 text-muted">
                                        {{ $dosenInfo->nama_lengkap ?? $pegawai->nama ?? '-' }}
                                        @if(!empty($dosenInfo?->nidn))
                                            ({{ $dosenInfo->nidn }})
                                        @endif
                                    </p>
                                    <small class="text-muted">Jumlah mahasiswa: {{ $mahasiswaList->count() }}</small>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <button type="button" id="btn-open-paraf-dosen" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalParafDosen">
                                        <i class="fa-solid fa-signature me-1"></i> Paraf Dosen Wali
                                        <span id="badge-status-paraf-wrapper">
                                            @if(!empty($dosenInfo?->paraf))
                                                <span class="badge bg-success ms-1"><i class="fa-solid fa-check"></i> Sudah Diset</span>
                                            @else
                                                <span class="badge bg-warning ms-1">Belum Diset</span>
                                            @endif
                                        </span>
                                    </button>
                                    <button type="button" id="btn-refresh-perwalian" class="btn btn-secondary btn-sm">
                                        <i class="fa-solid fa-rotate-right me-1"></i> Refresh
                                    </button>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table id="table-perwalian-pegawai" class="table table-striped table-bordered nowrap">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>NIM</th>
                                            <th>Nama Mahasiswa</th>
                                            <th>Program Studi</th>
                                            <th>Dosen Wali</th>
                                            <th>Status KRS</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($mahasiswaList as $idx => $mhs)
                                            <tr>
                                                <td>{{ $idx + 1 }}</td>
                                                <td>{{ $mhs->nim }}</td>
                                                <td>{{ $mhs->nama ?? '-' }}</td>
                                                <td>{{ $mhs->nama_jurusan ?? '-' }}</td>
                                                <td>
                                                    <span class="badge badge-outline-primary fs-6 px-3 py-2 text-start" style="white-space: normal;">
                                                        {{ $mhs->dosen_wali_nama ?? ($dosenInfo->nama_lengkap ?? '-') }}
                                                        @if(!empty($mhs->dosen_wali_nidn))
                                                            ({{ $mhs->dosen_wali_nidn }})
                                                        @elseif(!empty($dosenInfo?->nidn))
                                                            ({{ $dosenInfo->nidn }})
                                                        @endif
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    @if(is_null($mhs->status_krs))
                                                        <span class="badge badge-outline-secondary">Belum KRS</span>
                                                    @elseif($mhs->status_krs == 1)
                                                        <span class="badge badge-outline-success">Disetujui</span>
                                                    @else
                                                        <span class="badge badge-outline-warning">Belum Disetujui</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group" role="group">
                                                        <button
                                                            type="button"
                                                            class="btn btn-sm btn-info btn-lihat-krs"
                                                            data-nim="{{ $mhs->nim }}"
                                                            data-nama="{{ $mhs->nama }}"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#modalLihatKrs"
                                                            title="Lihat KRS"
                                                        >
                                                            <i class="fa-solid fa-list me-1"></i> 
                                                        </button>
                                                        <button
                                                            type="button"
                                                            class="btn btn-sm btn-primary btn-verifikasi-krs"
                                                            data-nim="{{ $mhs->nim }}"
                                                            data-nama="{{ $mhs->nama }}"
                                                            data-status="{{ $mhs->status_krs }}"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#modalVerifikasiKrs"
                                                            title="Verifikasi KRS"
                                                        >
                                                            <i class="fa-solid fa-clipboard-check me-1"></i> 
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center">Belum ada data mahasiswa perwalian untuk dosen yang sedang login.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Lihat KRS --}}
<div class="modal fade" id="modalLihatKrs" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa-solid fa-list me-2"></i>Daftar KRS</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-2">Mahasiswa: <strong id="modal-lihat-nama-mhs">-</strong></p>
                <p class="mb-3">NIM: <code id="modal-lihat-nim-mhs">-</code></p>
                <div id="krs-table-container" style="max-height: 400px; overflow-y: auto;">
                    <div class="text-center py-4">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Verifikasi KRS --}}
<div class="modal fade" id="modalVerifikasiKrs" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa-solid fa-clipboard-check me-2"></i>Verifikasi KRS</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-1">Mahasiswa: <strong id="modal-nama-mhs">-</strong></p>
                <p class="mb-3">NIM: <code id="modal-nim-mhs">-</code></p>
                <p class="mb-2">Pilih tindakan verifikasi KRS untuk mahasiswa ini:</p>
                <div class="d-grid gap-2">
                    <button type="button" id="btn-setujui-krs" class="btn btn-success">
                        <i class="fa-solid fa-check me-1"></i> Setujui KRS
                    </button>
                    <button type="button" id="btn-tolak-krs" class="btn btn-outline-danger">
                        <i class="fa-solid fa-xmark me-1"></i> Batalkan Persetujuan
                    </button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
{{-- Modal Paraf Dosen Wali --}}
<div class="modal fade" id="modalParafDosen" tabindex="-1" aria-labelledby="modalParafDosenLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="modalParafDosenLabel">
                    <i class="fa-solid fa-signature text-primary me-2"></i>Pengaturan Paraf Dosen Wali
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-info py-2 px-3 small mb-3">
                    <i class="fa-solid fa-circle-info me-1"></i>
                    Paraf ini digunakan secara otomatis untuk berbagai kebutuhan perwalian, salah satunya adalah tanda tangan pengesahan pada <strong>Kartu Ujian (UTS & UAS)</strong> mahasiswa bimbingan Anda.
                </div>

                <div class="row g-4">
                    {{-- Kolom Kiri: Preview Paraf Aktif --}}
                    <div class="col-md-5">
                        <label class="form-label fw-bold small text-muted text-uppercase mb-2">Paraf Aktif Saat Ini</label>
                        <div class="card border bg-light text-center p-3" style="min-height: 220px; display: flex; align-items: center; justify-content: center;">
                            <div id="wrapper-preview-aktif" class="w-100">
                                @if(!empty($dosenInfo?->paraf))
                                    <div class="p-2 border rounded bg-white shadow-sm mb-2" style="background-image: repeating-linear-gradient(45deg, #f0f0f0 25%, transparent 25%, transparent 75%, #f0f0f0 75%, #f0f0f0), repeating-linear-gradient(45deg, #f0f0f0 25%, #fff 25%, #fff 75%, #f0f0f0 75%, #f0f0f0); background-position: 0 0, 10px 10px; background-size: 20px 20px;">
                                        <img id="img-paraf-aktif" src="{{ $dosenInfo->paraf }}" alt="Paraf Aktif" style="max-height: 120px; max-width: 100%; object-fit: contain;">
                                    </div>
                                    <div class="badge bg-success-subtle text-success border border-success small mb-2 d-block">
                                        <i class="fa-solid fa-check-circle me-1"></i>Paraf Tersimpan & Aktif
                                    </div>
                                    <button type="button" id="btn-hapus-paraf" class="btn btn-outline-danger btn-xs">
                                        <i class="fa-solid fa-trash-can me-1"></i>Hapus Paraf
                                    </button>
                                @else
                                    <div id="empty-paraf-notice">
                                        <i class="fa-solid fa-signature fa-3x text-muted opacity-50 mb-2"></i>
                                        <p class="text-muted small mb-0">Belum ada paraf yang disimpan.</p>
                                        <small class="text-muted fst-italic">Silakan buat atau unggah paraf di samping.</small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Kolom Kanan: Buat / Unggah Paraf Baru --}}
                    <div class="col-md-7">
                        <ul class="nav nav-pills nav-fill mb-3" id="pills-paraf-tab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active btn-sm" id="tab-canvas-btn" data-bs-toggle="pill" data-bs-target="#tab-canvas" type="button" role="tab">
                                    <i class="fa-solid fa-pen-nib me-1"></i> Gambar Paraf
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link btn-sm" id="tab-upload-btn" data-bs-toggle="pill" data-bs-target="#tab-upload" type="button" role="tab">
                                    <i class="fa-solid fa-cloud-arrow-up me-1"></i> Upload Gambar
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content" id="pills-paraf-tabContent">
                            {{-- Tab 1: Kanvas Signature Pad --}}
                            <div class="tab-pane fade show active" id="tab-canvas" role="tabpanel">
                                <div class="border rounded p-1 bg-white position-relative shadow-sm mb-2" style="background-image: repeating-linear-gradient(45deg, #fafafa 25%, transparent 25%, transparent 75%, #fafafa 75%, #fafafa), repeating-linear-gradient(45deg, #fafafa 25%, #fff 25%, #fff 75%, #fafafa 75%, #fafafa); background-position: 0 0, 10px 10px; background-size: 20px 20px;">
                                    <canvas id="canvas-paraf-dosen" style="width: 100%; height: 180px; display: block; touch-action: none; cursor: crosshair;"></canvas>
                                    <div class="position-absolute bottom-0 end-0 p-2 opacity-75">
                                        <span class="badge bg-light text-dark border small" id="hint-stroke-paraf">Goreskan paraf di sini</span>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted"><i class="fa-solid fa-hand-pointer me-1"></i>Gunakan mouse atau layar sentuh</small>
                                    <button type="button" id="btn-clear-paraf-canvas" class="btn btn-outline-secondary btn-xs">
                                        <i class="fa-solid fa-rotate-left me-1"></i> Bersihkan
                                    </button>
                                </div>
                            </div>

                            {{-- Tab 2: Upload Gambar --}}
                            <div class="tab-pane fade" id="tab-upload" role="tabpanel">
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Pilih Berkas Gambar Paraf (.png transparan dianjurkan, maks 2MB)</label>
                                    <input type="file" id="input-paraf-file" class="form-control form-control-sm" accept="image/png,image/jpeg,image/jpg">
                                </div>
                                <div id="preview-file-upload" class="border rounded p-2 text-center bg-light d-none" style="min-height: 140px; display: flex; align-items: center; justify-content: center;">
                                    <img id="img-preview-upload" src="" alt="Preview Upload" style="max-height: 120px; max-width: 100%; object-fit: contain;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light py-2">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                <button type="button" id="btn-save-paraf-dosen" class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Paraf
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('local-js')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
<script>
    $(document).ready(function () {
        $('#table-perwalian-pegawai').DataTable({
            responsive: true,
            pageLength: 25,
            columnDefs: [
                { targets: 0, width: '5%' },
                { targets: [5, 6], orderable: false }
            ]
        });

        $('#btn-refresh-perwalian').on('click', function () {
            window.location.reload();
        });

        // ─── LOGIKA PARAF DOSEN WALI ─────────────────────────────────────
        var parafCanvas = document.getElementById('canvas-paraf-dosen');
        var parafPad = null;

        if (parafCanvas) {
            parafPad = new SignaturePad(parafCanvas, {
                backgroundColor: 'rgba(255, 255, 255, 0)',
                penColor: 'rgb(0, 0, 0)'
            });

            function resizeParafCanvas() {
                if (!parafCanvas) return;
                var ratio = Math.max(window.devicePixelRatio || 1, 1);
                var rect = parafCanvas.getBoundingClientRect();
                if (rect.width === 0 || rect.height === 0) return;

                var data = parafPad.toData();
                parafCanvas.width = rect.width * ratio;
                parafCanvas.height = rect.height * ratio;
                var ctx = parafCanvas.getContext('2d');
                ctx.scale(ratio, ratio);
                parafPad.clear();
                if (data && data.length > 0) {
                    parafPad.fromData(data);
                }
            }

            $('#modalParafDosen').on('shown.bs.modal', function () {
                setTimeout(resizeParafCanvas, 150);
            });

            $('button[data-bs-toggle="pill"]').on('shown.bs.tab', function (e) {
                if (e.target.id === 'tab-canvas-btn') {
                    setTimeout(resizeParafCanvas, 100);
                }
            });

            parafPad.addEventListener('endStroke', function () {
                if (!parafPad.isEmpty()) {
                    $('#hint-stroke-paraf').text('Paraf siap disimpan ✓').removeClass('bg-light text-dark').addClass('bg-success text-white');
                }
            });

            $('#btn-clear-paraf-canvas').on('click', function () {
                if (parafPad) {
                    parafPad.clear();
                    $('#hint-stroke-paraf').text('Goreskan paraf di sini').removeClass('bg-success text-white').addClass('bg-light text-dark');
                }
            });
        }

        // Preview saat pilih file di tab upload
        $('#input-paraf-file').on('change', function () {
            var file = this.files[0];
            if (file) {
                if (file.size > 2 * 1024 * 1024) {
                    alert('Ukuran file maksimal 2MB.');
                    $(this).val('');
                    $('#preview-file-upload').addClass('d-none');
                    return;
                }
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#img-preview-upload').attr('src', e.target.result);
                    $('#preview-file-upload').removeClass('d-none');
                };
                reader.readAsDataURL(file);
            } else {
                $('#preview-file-upload').addClass('d-none');
            }
        });

        // Simpan paraf via AJAX
        $('#btn-save-paraf-dosen').on('click', function () {
            var activeTab = $('#pills-paraf-tab .nav-link.active').attr('id');
            var formData = new FormData();
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

            var $btn = $(this);

            if (activeTab === 'tab-canvas-btn') {
                if (!parafPad || parafPad.isEmpty()) {
                    alert('Silakan goreskan paraf pada kanvas terlebih dahulu.');
                    return;
                }
                var base64Data = parafPad.toDataURL('image/png');
                formData.append('paraf_base64', base64Data);
            } else {
                var fileInput = document.getElementById('input-paraf-file');
                if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
                    alert('Silakan pilih file gambar paraf terlebih dahulu.');
                    return;
                }
                formData.append('paraf_file', fileInput.files[0]);
            }

            $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin me-1"></i> Menyimpan...');

            $.ajax({
                url: '{{ route("pegawai.perwalian.simpan-paraf") }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (res) {
                    if (res.result == 1) {
                        // Perbarui preview aktif di modal
                        var previewHtml = '<div class="p-2 border rounded bg-white shadow-sm mb-2" style="background-image: repeating-linear-gradient(45deg, #f0f0f0 25%, transparent 25%, transparent 75%, #f0f0f0 75%, #f0f0f0), repeating-linear-gradient(45deg, #f0f0f0 25%, #fff 25%, #fff 75%, #f0f0f0 75%, #f0f0f0); background-position: 0 0, 10px 10px; background-size: 20px 20px;">' +
                            '<img id="img-paraf-aktif" src="' + res.paraf + '" alt="Paraf Aktif" style="max-height: 120px; max-width: 100%; object-fit: contain;">' +
                            '</div>' +
                            '<div class="badge bg-success-subtle text-success border border-success small mb-2 d-block">' +
                            '<i class="fa-solid fa-check-circle me-1"></i>Paraf Tersimpan & Aktif' +
                            '</div>' +
                            '<button type="button" id="btn-hapus-paraf" class="btn btn-outline-danger btn-xs">' +
                            '<i class="fa-solid fa-trash-can me-1"></i>Hapus Paraf' +
                            '</button>';
                        $('#wrapper-preview-aktif').html(previewHtml);

                        // Perbarui badge di samping tombol refresh
                        $('#badge-status-paraf-wrapper').html('<span class="badge bg-success ms-1"><i class="fa-solid fa-check"></i> Sudah Diset</span>');

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: res.message || 'Paraf Dosen Wali berhasil disimpan.',
                                timer: 1500,
                                showConfirmButton: false
                            });
                        } else {
                            alert(res.message || 'Paraf Dosen Wali berhasil disimpan.');
                        }
                    } else {
                        alert(res.message || 'Gagal menyimpan paraf.');
                    }
                },
                error: function (xhr) {
                    var msg = 'Terjadi kesalahan saat menyimpan paraf.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    alert(msg);
                },
                complete: function () {
                    $btn.prop('disabled', false).html('<i class="fa-solid fa-floppy-disk me-1"></i> Simpan Paraf');
                }
            });
        });

        // Hapus paraf via AJAX
        $(document).on('click', '#btn-hapus-paraf', function () {
            if (!confirm('Apakah Anda yakin ingin menghapus paraf Dosen Wali?')) {
                return;
            }

            var $btn = $(this);
            $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin me-1"></i> Menghapus...');

            $.ajax({
                url: '{{ route("pegawai.perwalian.hapus-paraf") }}',
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function (res) {
                    if (res.result == 1) {
                        $('#wrapper-preview-aktif').html(
                            '<div id="empty-paraf-notice">' +
                            '<i class="fa-solid fa-signature fa-3x text-muted opacity-50 mb-2"></i>' +
                            '<p class="text-muted small mb-0">Belum ada paraf yang disimpan.</p>' +
                            '<small class="text-muted fst-italic">Silakan buat atau unggah paraf di samping.</small>' +
                            '</div>'
                        );
                        $('#badge-status-paraf-wrapper').html('<span class="badge bg-warning ms-1">Belum Diset</span>');

                        if (parafPad) {
                            parafPad.clear();
                            $('#hint-stroke-paraf').text('Goreskan paraf di sini').removeClass('bg-success text-white').addClass('bg-light text-dark');
                        }
                        $('#input-paraf-file').val('');
                        $('#preview-file-upload').addClass('d-none');

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Terhapus',
                                text: res.message || 'Paraf Dosen Wali berhasil dihapus.',
                                timer: 1500,
                                showConfirmButton: false
                            });
                        } else {
                            alert(res.message || 'Paraf Dosen Wali berhasil dihapus.');
                        }
                    } else {
                        alert(res.message || 'Gagal menghapus paraf.');
                    }
                },
                error: function () {
                    alert('Terjadi kesalahan saat menghapus paraf.');
                },
                complete: function () {
                    $btn.prop('disabled', false).html('<i class="fa-solid fa-trash-can me-1"></i>Hapus Paraf');
                }
            });
        });

        // Isi modal dengan data baris yang diklik
        $(document).on('click', '.btn-lihat-krs', function () {
            var nim  = $(this).data('nim');
            var nama = $(this).data('nama');
            $('#modal-lihat-nim-mhs').text(nim);
            $('#modal-lihat-nama-mhs').text(nama);
            
            // Fetch KRS data
            $.ajax({
                url: '{{ route("pegawai.perwalian.get-krs") }}',
                type: 'GET',
                dataType: 'json',
                data: { nim: nim },
                success: function (res) {
                    if (res.result == 1 && res.data.length > 0) {
                        var html = '<table class="table table-sm table-bordered mb-0"><thead><tr><th style="width:60px" class="text-center">No</th><th>Kode MK</th><th>Nama Mata Kuliah</th><th>SKS</th><th>Status</th></tr></thead><tbody>';
                        var totalSks = 0;
                        res.data.forEach(function (row, idx) {
                            var statusBadge = row.is_publish == 1 ? '<span class="badge bg-success">Disetujui</span>' : '<span class="badge bg-warning">Belum Disetujui</span>';
                            var kodeMk = row.mata_kuliah ? row.mata_kuliah : '-';
                            var namaMk = row.nama_mata_kuliah ? row.nama_mata_kuliah : kodeMk;
                            var sks = (row.sks !== null && row.sks !== undefined && row.sks !== '') ? parseFloat(row.sks) : 0;
                            totalSks += sks;
                            html += '<tr><td class="text-center">' + (idx + 1) + '</td><td>' + kodeMk + '</td><td>' + namaMk + '</td><td class="text-center">' + sks + '</td><td>' + statusBadge + '</td></tr>';
                        });
                        html += '</tbody><tfoot><tr><th colspan="3" class="text-end">Total SKS</th><th class="text-center">' + totalSks + '</th><th></th></tr></tfoot></table>';
                        $('#krs-table-container').html(html);
                    } else {
                        $('#krs-table-container').html('<div class="alert alert-info">Belum ada KRS yang diambil mahasiswa ini.</div>');
                    }
                },
                error: function () {
                    $('#krs-table-container').html('<div class="alert alert-danger">Terjadi kesalahan saat memuat data KRS.</div>');
                }
            });
        });

        $(document).on('click', '.btn-verifikasi-krs', function () {
            var nim   = $(this).data('nim');
            var nama  = $(this).data('nama');
            $('#modal-nim-mhs').text(nim);
            $('#modal-nama-mhs').text(nama);
        });

        function kirimVerifikasi(nim, statusKrs) {
            $.ajax({
                url: '{{ route("pegawai.perwalian.verifikasi-krs") }}',
                type: 'POST',
                dataType: 'json',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data: { nim: nim, status_krs: statusKrs },
                success: function (res) {
                    if (res.result == 1) {
                        $('.container').prepend(
                            '<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                            res.message +
                            '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>'
                        );
                        setTimeout(function () { window.location.reload(); }, 1200);
                    } else {
                        alert(res.message || 'Gagal memperbarui status KRS.');
                    }
                },
                error: function () {
                    alert('Terjadi kesalahan. Silakan coba lagi.');
                },
                complete: function () {
                    $('#modalVerifikasiKrs').modal('hide');
                }
            });
        }

        $('#btn-setujui-krs').on('click', function () {
            var nim = $('#modal-nim-mhs').text();
            kirimVerifikasi(nim, 1);
        });

        $('#btn-tolak-krs').on('click', function () {
            var nim = $('#modal-nim-mhs').text();
            kirimVerifikasi(nim, 0);
        });
    });
</script>
@endsection