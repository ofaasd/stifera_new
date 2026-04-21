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
                                <button type="button" id="btn-refresh-perwalian" class="btn btn-secondary btn-sm">
                                    <i class="fa-solid fa-rotate-right me-1"></i> Refresh
                                </button>
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
@endsection

@section('local-js')
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