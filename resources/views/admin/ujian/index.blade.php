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

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h4 class="card-title mb-0"><i class="fa-solid fa-file-pen me-1"></i>{{ $title }}</h4>
                        <button type="button" class="btn btn-sm btn-outline-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTtdKabak">
                            <i class="fa-solid fa-stamp me-1"></i> Pengaturan TTD & Cap Kepala BAK
                        </button>
                    </div>

                    <div class="card-body pb-4">
                            <ul class="nav nav-tabs mb-3" id="ujianTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="reguler-tab" data-bs-toggle="tab" data-bs-target="#reguler-pane" type="button" role="tab">Reguler</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="rpl-tab" data-bs-toggle="tab" data-bs-target="#rpl-pane" type="button" role="tab">RPL</button>
                                </li>
                            </ul>

                            <div class="tab-content" id="ujianTabContent">
                                <div class="tab-pane fade show active" id="reguler-pane" role="tabpanel">
                                    <div class="mb-2 fw-bold">
                                        Tahun Ajaran Aktif Reguler:
                                        @if($tahunReguler)
                                            {{ $tahunReguler->awal }}/{{ $tahunReguler->akhir }}
                                            ({{ (int) $tahunReguler->jenis === 1 ? 'Ganjil' : ((int) $tahunReguler->jenis === 2 ? 'Genap' : '-') }})
                                        @else
                                            -
                                        @endif
                                    </div>

                                    <div class="table-responsive">
                                        <table id="table-ujian-reguler" class="table table-bordered table-striped">
                                            <thead class="bg-primary text-white text-center">
                                                <tr>
                                                    <th style="width: 50px;">No</th>
                                                    <th>Kode MK</th>
                                                    <th style="min-width: 150px; white-space: normal;">Mata Kuliah</th>
                                                    <th style="max-width: 150px; white-space: normal;">Dosen</th>
                                                    <th style="width: 80px;">Rombel</th>
                                                    <th style="max-width: 150px; white-space: normal;">Jadwal Kuliah</th>
                                                    <th>Status Pengaturan</th>
                                                    <th style="width: 120px;">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($jadwalReguler as $idx => $row)
                                                    <tr>
                                                        <td class="text-center">{{ $idx + 1 }}</td>
                                                        <td>{{ $row->kode_mata_kuliah }}</td>
                                                        <td style="white-space: normal; word-break: break-word; min-width: 150px;">{{ $row->nama_mata_kuliah ?? '-' }}</td>
                                                        <td style="white-space: normal; word-break: break-word; max-width: 150px;">{{ trim($row->nama_dosen ?? '-') }}</td>
                                                        <td class="text-center">{{ $row->rombel ?? '-' }}</td>
                                                        <td style="white-space: normal; word-break: break-word; max-width: 150px;">{{ $row->hari ?? '-' }} / {{ $row->sesi ?? '-' }} / {{ $row->ruang ?? '-' }}</td>
                                                        <td>
                                                            @if((int) $row->sudah_diatur === 1)
                                                                <span class="badge bg-success">Sudah Diatur</span>
                                                            @else
                                                                <span class="badge bg-warning text-dark">Belum Diatur</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div class="d-flex flex-column gap-1">
                                                                <a href="{{ url('master/pengaturan-ujian/detail/' . $row->id) }}" class="btn btn-primary btn-sm py-1" title="Detail Pengaturan Ujian">
                                                                    <i class="fa fa-eye me-1"></i> Detail
                                                                </a>
                                                                <div class="btn-group btn-group-sm w-100" role="group">
                                                                    <button type="button" class="btn btn-outline-info btn-xs py-1 btn-copy-link-pengawas"
                                                                            data-id-jadwal="{{ $row->id }}"
                                                                            data-jenis="uts"
                                                                            data-token="{{ $row->token_pengawas_uts ?? '' }}"
                                                                            title="Salin Link Pengawas UTS">
                                                                        <i class="fa-solid fa-link me-1"></i>UTS
                                                                    </button>
                                                                    <button type="button" class="btn btn-outline-success btn-xs py-1 btn-copy-link-pengawas"
                                                                            data-id-jadwal="{{ $row->id }}"
                                                                            data-jenis="uas"
                                                                            data-token="{{ $row->token_pengawas_uas ?? '' }}"
                                                                            title="Salin Link Pengawas UAS">
                                                                        <i class="fa-solid fa-link me-1"></i>UAS
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr><td colspan="8" class="text-center">Belum ada jadwal untuk Reguler.</td></tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="rpl-pane" role="tabpanel">
                                    <div class="mb-2 fw-bold">
                                        Tahun Ajaran Aktif RPL:
                                        @if($tahunRpl)
                                            {{ $tahunRpl->awal }}/{{ $tahunRpl->akhir }}
                                            ({{ (int) $tahunRpl->jenis === 1 ? 'Ganjil' : ((int) $tahunRpl->jenis === 2 ? 'Genap' : '-') }})
                                        @else
                                            -
                                        @endif
                                    </div>

                                    <div class="table-responsive">
                                        <table id="table-ujian-rpl" class="table table-bordered table-striped">
                                            <thead class="bg-primary text-white text-center">
                                                <tr>
                                                    <th style="width: 50px;">No</th>
                                                    <th>Kode MK</th>
                                                    <th style="min-width: 150px; white-space: normal;">Mata Kuliah</th>
                                                    <th style="max-width: 150px; white-space: normal;">Dosen</th>
                                                    <th style="width: 80px;">Rombel</th>
                                                    <th style="max-width: 150px; white-space: normal;">Jadwal Kuliah</th>
                                                    <th>Status Pengaturan</th>
                                                    <th style="width: 120px;">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($jadwalRpl as $idx => $row)
                                                    <tr>
                                                        <td class="text-center">{{ $idx + 1 }}</td>
                                                        <td>{{ $row->kode_mata_kuliah }}</td>
                                                        <td style="white-space: normal; word-break: break-word; min-width: 150px;">{{ $row->nama_mata_kuliah ?? '-' }}</td>
                                                        <td style="white-space: normal; word-break: break-word; max-width: 150px;">{{ trim($row->nama_dosen ?? '-') }}</td>
                                                        <td class="text-center">{{ $row->rombel ?? '-' }}</td>
                                                        <td style="white-space: normal; word-break: break-word; max-width: 150px;">{{ $row->hari ?? '-' }} / {{ $row->sesi ?? '-' }} / {{ $row->ruang ?? '-' }}</td>
                                                        <td>
                                                            @if((int) $row->sudah_diatur === 1)
                                                                <span class="badge bg-success">Sudah Diatur</span>
                                                            @else
                                                                <span class="badge bg-warning text-dark">Belum Diatur</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div class="d-flex flex-column gap-1">
                                                                <a href="{{ url('master/pengaturan-ujian/detail/' . $row->id) }}" class="btn btn-primary btn-sm py-1" title="Detail Pengaturan Ujian">
                                                                    <i class="fa fa-eye me-1"></i> Detail
                                                                </a>
                                                                <div class="btn-group btn-group-sm w-100" role="group">
                                                                    <button type="button" class="btn btn-outline-info btn-xs py-1 btn-copy-link-pengawas"
                                                                            data-id-jadwal="{{ $row->id }}"
                                                                            data-jenis="uts"
                                                                            data-token="{{ $row->token_pengawas_uts ?? '' }}"
                                                                            title="Salin Link Pengawas UTS">
                                                                        <i class="fa-solid fa-link me-1"></i>UTS
                                                                    </button>
                                                                    <button type="button" class="btn btn-outline-success btn-xs py-1 btn-copy-link-pengawas"
                                                                            data-id-jadwal="{{ $row->id }}"
                                                                            data-jenis="uas"
                                                                            data-token="{{ $row->token_pengawas_uas ?? '' }}"
                                                                            title="Salin Link Pengawas UAS">
                                                                        <i class="fa-solid fa-link me-1"></i>UAS
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr><td colspan="8" class="text-center">Belum ada jadwal untuk RPL.</td></tr>
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
</div>

{{-- Modal Pengaturan TTD & Cap Kepala BAK --}}
<div class="modal fade" id="modalTtdKabak" tabindex="-1" aria-labelledby="modalTtdKabakLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.ujian.upload-ttd-kabak') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTtdKabakLabel">
                        <i class="fa-solid fa-stamp me-2 text-primary"></i>Pengaturan TTD & Cap Kepala BAK
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3 text-center">
                        <label class="form-label fw-bold d-block text-start">Preview TTD & Cap Saat Ini</label>
                        @if($hasTtdKabak)
                            <div class="p-3 border rounded bg-light d-inline-block shadow-sm">
                                <img src="{{ asset('uploads/ttd_kabak.png') }}?v={{ time() }}" alt="TTD & Cap BAK" style="max-height: 100px; max-width: 240px; object-fit: contain;">
                            </div>
                            <div class="small text-success mt-1"><i class="fa-solid fa-circle-check me-1"></i>File TTD & Cap Kepala BAK Aktif</div>
                        @else
                            <div class="p-4 border rounded bg-light text-muted">
                                <i class="fa-regular fa-image fa-2x mb-2 d-block"></i>
                                Belum ada file TTD & Cap Kepala BAK yang diunggah.
                            </div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Kepala BAK / Pejabat</label>
                        <input type="text" name="nama_kabak" class="form-control" value="{{ old('nama_kabak', $settingUjian->nama_kabak ?? 'Kepala Bagian Administrasi Akademik & Keuangan') }}" placeholder="Contoh: Kepala Bagian Administrasi Akademik & Kemahasiswaan">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">NIP / NPP</label>
                        <input type="text" name="nip_kabak" class="form-control" value="{{ old('nip_kabak', $settingUjian->nip_kabak ?? '') }}" placeholder="Contoh: 198501012010121001 (opsional)">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Unggah File TTD & Cap Baru</label>
                        <input type="file" name="ttd_cap" class="form-control" accept="image/png">
                        <div class="form-text text-muted">
                            <i class="fa-solid fa-circle-info me-1"></i>Khusus format <strong>.png transparan</strong>, ukuran maksimal <strong>2MB</strong>.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fa-solid fa-save me-1"></i> Simpan Pengaturan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Info & Salin Link Pengawas --}}
<div class="modal fade" id="modalLinkPengawas" tabindex="-1" aria-labelledby="modalLinkPengawasLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLinkPengawasLabel">
                    <i class="fa-solid fa-link me-2 text-primary"></i><span id="modal-link-title">Link Pengawas Ujian</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-2">
                    Bagikan tautan ini kepada pengawas ujian yang bertugas. Pengawas dapat mengakses daftar absensi dan membubuhkan paraf tanpa perlu login.
                </p>
                <div class="input-group mb-3">
                    <input type="text" id="modal-link-url" class="form-control" readonly>
                    <button type="button" id="btn-copy-modal-url" class="btn btn-primary">
                        <i class="fa-solid fa-copy me-1"></i> Salin
                    </button>
                </div>
                <div class="alert alert-info py-2 px-3 small mb-0">
                    <i class="fa-solid fa-shield-halved me-1"></i>Tautan ini bersifat unik dan otomatis tersimpan untuk mata kuliah ini.
                </div>
            </div>
            <div class="modal-footer">
                <a href="#" id="modal-link-open" target="_blank" class="btn btn-outline-primary btn-sm">
                    <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Buka Halaman
                </a>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('local-js')
<script>
    $(document).ready(function () {
        const dtSettings = {
            pageLength: 25,
            language: {
                paginate: {
                    next: '>',
                    previous: '<'
                }
            }
        };
        const dtReguler = $('#table-ujian-reguler').DataTable(dtSettings);
        const dtRpl = $('#table-ujian-rpl').DataTable(dtSettings);

        $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function () {
            dtReguler.columns.adjust().draw(false);
            dtRpl.columns.adjust().draw(false);
        });

        const activeTab = @json($activeTab ?? null);
        if (activeTab) {
            const tabButton = document.querySelector(`button[data-bs-target="#${activeTab}"]`);
            if (tabButton) {
                const tab = new bootstrap.Tab(tabButton);
                tab.show();
            }
        }

        // Handle Generator & Copy Link Pengawas
        $(document).on('click', '.btn-copy-link-pengawas', function () {
            const btn = $(this);
            const idJadwal = btn.data('id-jadwal');
            const jenis = btn.data('jenis');
            let token = btn.data('token');

            const origHtml = btn.html();
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

            function copyAndShow(url, jenisTitle) {
                btn.prop('disabled', false).html(origHtml);

                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(url).catch(() => {});
                }

                $('#modal-link-url').val(url);
                $('#modal-link-title').text('Link Pengawas ' + jenisTitle);
                $('#modal-link-open').attr('href', url);
                $('#modalLinkPengawas').modal('show');
            }

            if (token && String(token).trim() !== '') {
                const fullUrl = '{{ url("/pengawas/ujian") }}/' + jenis + '/' + token;
                copyAndShow(fullUrl, jenis.toUpperCase());
            } else {
                $.ajax({
                    url: '{{ route("admin.ujian.generate-token") }}',
                    type: 'POST',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}' },
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}',
                        id_jadwal: idJadwal,
                        jenis: jenis
                    },
                    dataType: 'json',
                    success: function (res) {
                        if (res.success) {
                            btn.data('token', res.token);
                            copyAndShow(res.url, res.jenis);
                        } else {
                            alert(res.message || 'Gagal menghasilkan link pengawas.');
                            btn.prop('disabled', false).html(origHtml);
                        }
                    },
                    error: function (xhr) {
                        let msg = 'Terjadi kesalahan saat menghasilkan link pengawas.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg += '\n\nPenyebab: ' + xhr.responseJSON.message;
                        } else if (xhr.status === 419) {
                            msg += '\n\nSesi telah berakhir (CSRF expired). Silakan muat ulang (refresh) halaman ini.';
                        } else if (xhr.responseText) {
                            try {
                                const parsed = JSON.parse(xhr.responseText);
                                if (parsed.message) msg += '\n\nPenyebab: ' + parsed.message;
                            } catch (e) {
                                msg += '\n\nStatus: ' + xhr.status + ' (' + xhr.statusText + ')';
                            }
                        }
                        alert(msg);
                        btn.prop('disabled', false).html(origHtml);
                    }
                });
            }
        });

        $('#btn-copy-modal-url').on('click', function () {
            const input = document.getElementById('modal-link-url');
            input.select();
            input.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(input.value).then(() => {
                const btn = $(this);
                const orig = btn.html();
                btn.html('<i class="fa-solid fa-check me-1"></i> Tersalin!');
                setTimeout(() => btn.html(orig), 2000);
            });
        });
    });
</script>
@endsection
