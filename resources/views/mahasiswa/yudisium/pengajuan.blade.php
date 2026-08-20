@extends('layouts.default')

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <div class="row page-titles">
            <ol class="breadcrumb">
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Akademik</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0)">Pengajuan Yudisium</a></li>
            </ol>
        </div>

        @if(session('success'))
            <div class="alert alert-success solid alert-dismissible fade show">
                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                <strong>Sukses!</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger solid alert-dismissible fade show">
                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2"><polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"></polygon><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                <strong>Error!</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>
            </div>
        @endif

        @if(!$periode_aktif)
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-lock text-muted mb-3" style="font-size: 50px;"></i>
                            <h3 class="text-muted">Pendaftaran Yudisium Belum Dibuka</h3>
                            <p>Silahkan cek secara berkala atau hubungi pihak akademik untuk informasi detail periode Yudisium selanjutnya.</p>
                        </div>
                    </div>
                </div>
            </div>
        @else

            
            @php
                $isDraft = $pendaftaran && $pendaftaran->status_pengajuan == 'draft';
                $uploadedMandatory = [];
                $uploadedSertifikatCount = 0;
                if ($pendaftaran) {
                    foreach($pendaftaran->berkas as $b) {
                        if ($b->jenis_berkas === 'sertifikat_kegiatan') $uploadedSertifikatCount++;
                        else $uploadedMandatory[] = $b->jenis_berkas;
                    }
                }
            @endphp
            @if(!$pendaftaran || $isDraft)
                <!-- FORM PERTAMA KALI MENDAFTAR -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-primary">
                                <h4 class="card-title text-white mb-0">Formulir Pengajuan Yudisium (Periode: {{ $activePeriode->nama_periode }})</h4>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info solid border-0">
                                    <i class="fas fa-info-circle me-2"></i> Lengkapi ke-8 persyaratan wajib di bawah ini. Harap diunggah menggunakan format file <strong>PDF, JPG, atau PNG</strong> dengan ukuran maksimal <strong>25MB</strong> per berkas.
                                </div>
                                <form action="{{ route('mahasiswa.yudisium.store') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        @foreach($mandatoryFiles as $key => $label)
                                        @php
                                            $isUploaded = in_array($key, $uploadedMandatory);
                                        @endphp
                                        <div class="col-md-6 mb-4">
                                            <label class="form-label font-weight-bold">{{ $label }} @if(!$isUploaded) <span class="text-danger">*</span> @else <span class="badge badge-success light ms-1"><i class="fas fa-check"></i> Tersimpan</span> @endif</label>
                                            <input type="file" name="{{ $key }}" class="form-control" accept=".pdf,.jpg,.jpeg,.png" @if(!$isUploaded) required @endif>
                                            @if($isUploaded)
                                                <small class="text-success d-block mt-1">Berkas ini sudah tersimpan di draft. Abaikan jika tidak ingin mengganti.</small>
                                            @endif
                                        </div>
                                        @endforeach
                                    </div>

                                    <hr class="my-4">

                                    <div class="row">
                                        <div class="col-12 mb-3">
                                            <h4 class="text-primary font-weight-bold">Sertifikat Kegiatan</h4>
                                            <p class="text-muted" style="font-size: 14px;">Anda diwajibkan untuk mengunggah <strong>minimal {{ $jumlah_sertifikat }} Sertifikat Kegiatan Kegiatan</strong>. Anda dapat terus menambah file tambahan jika ada.</p>
                                        </div>
                                        <div class="col-12">
                                            <div class="row" id="sertifikat-dynamic-zone">
                                                @php
                                                    $sisaSertif = $jumlah_sertifikat - $uploadedSertifikatCount;
                                                    if ($sisaSertif < 0) $sisaSertif = 0;
                                                @endphp
                                                
                                                @if($pendaftaran)
                                                    @php
                                                        $uploadedSertifData = $pendaftaran->berkas->where('jenis_berkas', 'sertifikat_kegiatan');
                                                        $indexSertif = 1;
                                                    @endphp
                                                    @foreach($uploadedSertifData as $sertifX)
                                                    <div class="col-md-6 mb-3 set-dynamic">
                                                        <label class="form-label font-weight-bold">Sertifikat Kegiatan #{{ $indexSertif++ }} <span class="badge badge-success light ms-1"><i class="fas fa-check"></i> Tersimpan</span></label>
                                                        <div class="d-flex">
                                                            <input type="file" name="sertifikat_kegiatan_update[{{ $sertifX->id }}]" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                                                        </div>
                                                        <small class="text-success mt-1 d-block">Sudah ada file. Upload form ini hanya jika ingin mengganti.</small>
                                                    </div>
                                                    @endforeach
                                                @endif

                                                @for($i = 1; $i <= $sisaSertif; $i++)
                                                <div class="col-md-6 mb-3 set-dynamic">
                                                    <label class="form-label font-weight-bold">Sertifikat Kegiatan #{{ isset($indexSertif) ? ($indexSertif - 1 + $i) : $i }} <span class="text-danger">*</span></label>
                                                    <div class="d-flex">
                                                        <input type="file" name="sertifikat_kegiatan[]" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                                                    </div>
                                                </div>
                                                @endfor
                                            </div>
                                        </div>
                                        <div class="col-12 mt-2">
                                            <button type="button" class="btn btn-outline-primary btn-sm" id="btn-add-sertifikat">
                                                <i class="fas fa-plus"></i> Tambah Sertifikat Lainnya
                                            </button>
                                        </div>
                                    </div>

                                    <div class="mt-5 text-end d-flex justify-content-end">
                                        <button type="submit" name="action" value="draft" class="btn btn-secondary shadow px-4 fs-16 me-3" formnovalidate><i class="fas fa-save me-2"></i> Simpan sebagai Draft</button>
                                        <button type="submit" name="action" value="submit" class="btn btn-primary shadow px-5 fs-16" onclick="return confirm('Apakah Anda yakin formulir sudah final dan siap dicek Akademik?');"><i class="fas fa-paper-plane me-2"></i> Ajukan Yudisium Final</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- DASHBOARD STATUS PENDAFTARAN -->
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card overflow-hidden">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h4 class="card-title mb-0">Status Pengajuan Yudisium (Periode: {{ $pendaftaran->periode->nama_periode }})</h4>
                                <div>
                                    @if($pendaftaran->status_pengajuan == 'menunggu')
                                        <span class="badge badge-warning fs-14"><i class="fas fa-clock fs-14 me-1"></i> Sedang Ditinjau Admin</span>
                                    @elseif($pendaftaran->status_pengajuan == 'revisi')
                                        <span class="badge badge-danger fs-14"><i class="fas fa-exclamation-triangle fs-14 me-1"></i> Ada Berkas Ditolak / Revisi</span>
                                    @elseif($pendaftaran->status_pengajuan == 'valid')
                                        <span class="badge badge-info fs-14"><i class="fas fa-clipboard-check fs-14 me-1"></i> Berkas Valid Menunggu Keputusan</span>
                                    @elseif($pendaftaran->status_pengajuan == 'lulus_yudisium')
                                        <span class="badge badge-success fs-14"><i class="fas fa-award fs-14 me-1"></i> Lulus Yudisium</span>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Peringatan Hardcopy -->
                            <div class="card-body pb-0">
                                @php
                                    $isD3 = Auth::guard('mahasiswa')->user()->id_program_studi == 1;
                                    $labelPK = $isD3 ? 'PKL' : 'PKF';
                                    $labelTA = $isD3 ? 'KTI' : 'Skripsi';
                                @endphp
                                @if(!$pendaftaran->is_hardcopy_pkf || !$pendaftaran->is_hardcopy_skripsi)
                                    <div class="alert alert-danger shadow border-0 solid mb-4">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h4 class="text-white"><i class="fas fa-bullhorn me-2"></i> PRASYARAT HARDCOPY BELUM DITERIMA!</h4>
                                                <p class="mb-0 text-white">Harap segera menyerahkan <strong>fisik/cetakan</strong> berkas berikut ke loket akademik kampus agar proses kelulusan Yudisium dapat ditetapkan:</p>
                                                <ul class="list-unstyled mt-2 mb-0">
                                                    @if(!$pendaftaran->is_hardcopy_pkf)
                                                        <li><i class="fas fa-times-circle text-white me-2"></i> Hardcopy Laporan {{ $labelPK }}</li>
                                                    @endif
                                                    @if(!$pendaftaran->is_hardcopy_skripsi)
                                                        <li><i class="fas fa-times-circle text-white me-2"></i> Hardcopy {{ $labelTA }}</li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-success shadow border-0 solid mb-4">
                                        <h4 class="mb-0 text-white"><i class="fas fa-check-circle me-2"></i> Seluruh Persyaratan Fisik/Hardcopy Telah Diterima Akademik.</h4>
                                    </div>
                                @endif
                                
                                @if($pendaftaran->status_pengajuan == 'lulus_yudisium')
                                    <div class="alert alert-success shadow border-0 solid mb-4 text-center">
                                        <h3 class="text-white mb-2"><i class="fas fa-award fa-2x mb-3"></i><br>SELAMAT! ANDA DINYATAKAN LULUS YUDISIUM</h3>
                                        <p class="text-white text-xl mb-0">No SK Yudisium: <strong>{{ $pendaftaran->no_sk_yudisium }}</strong></p>
                                    </div>
                                @endif
                            </div>

                            <div class="card-body">
                                <h4 class="text-primary font-weight-bold mb-3 border-bottom pb-2">Daftar Berkas Terunggah</h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead class="bg-primary text-white text-center">
                                            <tr>
                                                <th>Jenis Syarat Berkas</th>
                                                <th>Status Verifikasi</th>
                                                <th>Aksi / Kelengkapan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                // Grouping to display separately or dynamically loop
                                                $berkasList = $pendaftaran->berkas;
                                            @endphp
                                            
                                            <!-- Berkas Mandatory -->
                                            @foreach($mandatoryFiles as $key => $label)
                                                @php
                                                    $item = $berkasList->where('jenis_berkas', $key)->first();
                                                @endphp
                                                <tr>
                                                    <td class="font-weight-bold">{{ $label }}</td>
                                                    <td class="text-center align-middle">
                                                        @if($item)
                                                            @if($item->status_berkas == 'valid')
                                                                <span class="badge badge-success">Valid</span>
                                                            @elseif($item->status_berkas == 'menunggu')
                                                                <span class="badge badge-warning">Menunggu</span>
                                                            @elseif($item->status_berkas == 'tolak')
                                                                <span class="badge badge-danger">Ditolak / Revisi</span>
                                                            @endif
                                                        @else
                                                            <span class="badge badge-dark">Tidak Ada</span>
                                                        @endif
                                                    </td>
                                                    <td class="align-middle">
                                                        @if($item)
                                                            <a href="{{ asset($item->file_path) }}" target="_blank" class="btn btn-info btn-sm shadow me-2"><i class="fas fa-file-pdf me-1"></i> Lihat / Download</a>
                                                            
                                                            @if($item->status_berkas == 'tolak')
                                                                <!-- Menampilkan Form Ganti File jika Ditolak -->
                                                                <div class="mt-3 p-3 bg-light rounded shadow-sm border border-danger">
                                                                    <div class="text-danger font-weight-bold mb-2">
                                                                        <i class="fas fa-exclamation-triangle"></i> Catatan Revisi Admin: 
                                                                        <span class="d-block text-dark fw-normal mt-1">{{ $item->catatan_revisi }}</span>
                                                                    </div>
                                                                    <form action="{{ route('mahasiswa.yudisium.updateRevisi') }}" method="POST" enctype="multipart/form-data" class="d-flex align-items-center mt-3">
                                                                        @csrf
                                                                        <input type="hidden" name="id_berkas" value="{{ $item->id }}">
                                                                        <input type="file" name="file_revisi" class="form-control form-control-sm me-2" required>
                                                                        <button type="submit" class="btn btn-sm btn-danger white-space-nowrap shadow px-3">Upload Revisi</button>
                                                                    </form>
                                                                </div>
                                                            @endif
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach

                                            <!-- Berkas Tambahan (Wajib 11+) -->
                                            @php
                                                $tambahan = $berkasList->where('jenis_berkas', 'sertifikat_kegiatan');
                                                $tidx = 1;
                                            @endphp
                                            @foreach($tambahan as $item_tbh)
                                                <tr>
                                                    <td class="font-weight-bold text-muted">Sertifikat Kegiatan #{{ $tidx++ }}</td>
                                                    <td class="text-center align-middle">
                                                        @if($item_tbh->status_berkas == 'valid')
                                                            <span class="badge badge-success">Valid</span>
                                                        @elseif($item_tbh->status_berkas == 'menunggu')
                                                            <span class="badge badge-warning">Menunggu</span>
                                                        @elseif($item_tbh->status_berkas == 'tolak')
                                                            <span class="badge badge-danger">Ditolak / Revisi</span>
                                                        @endif
                                                    </td>
                                                    <td class="align-middle">
                                                        <a href="{{ asset($item_tbh->file_path) }}" target="_blank" class="btn btn-info btn-sm shadow me-2"><i class="fas fa-file-pdf me-1"></i> Lihat / Download</a>

                                                        @if($item_tbh->status_berkas == 'tolak')
                                                            <div class="mt-3 p-3 bg-light rounded shadow-sm border border-danger">
                                                                <div class="text-danger font-weight-bold mb-2">
                                                                    <i class="fas fa-exclamation-triangle"></i> Catatan Revisi Admin: 
                                                                    <span class="d-block text-dark fw-normal mt-1">{{ $item_tbh->catatan_revisi }}</span>
                                                                </div>
                                                                <form action="{{ route('mahasiswa.yudisium.updateRevisi') }}" method="POST" enctype="multipart/form-data" class="d-flex align-items-center mt-3">
                                                                    @csrf
                                                                    <input type="hidden" name="id_berkas" value="{{ $item_tbh->id }}">
                                                                    <input type="file" name="file_revisi" class="form-control form-control-sm me-2" required>
                                                                    <button type="submit" class="btn btn-sm btn-danger white-space-nowrap shadow px-3">Upload Revisi</button>
                                                                </form>
                                                            </div>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        @endif
    </div>
</div>
@endsection

@section('local-js')
<script>
    $(document).ready(function() {
        var currentFields = $('.set-dynamic').length;
        
        $('#btn-add-sertifikat').click(function(e) {
            e.preventDefault();
            currentFields++;
            var html = `
                <div class="col-md-6 mb-3 set-dynamic">
                    <label class="form-label font-weight-bold">Sertifikat Kegiatan #${currentFields}</label>
                    <div class="d-flex align-items-center">
                        <input type="file" name="sertifikat_kegiatan[]" class="form-control me-2" accept=".pdf,.jpg,.jpeg,.png" required>
                        <button type="button" class="btn btn-danger btn-sm btn-remove-set px-3"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
            `;
            $('#sertifikat-dynamic-zone').append(html);
        });

        // Event listener u/ remove
        $('#sertifikat-dynamic-zone').on('click', '.btn-remove-set', function() {
            $(this).closest('.set-dynamic').remove();
            // Rekalkulasi penomoran sertifikat ekstra supaya urut (opsional)
        });
    });
</script>
@endsection
