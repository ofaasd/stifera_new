@extends('layouts.default', ['CurrentPage' => $CurrentPage])

@section('local-css')
<style>
    .select2-container--default .select2-selection--single {
        border: 1px solid #ced4da;
        min-height: 38px;
        height: 38px !important;
        display: flex;
        align-items: center;
        padding: 0.375rem 0.75rem;
    }
    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 24px;
        padding-left: 0;
        padding-right: 0;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
    .select2-container {
        width: 100% !important;
    }
    .select2-dropdown {
        z-index: 999999 !important;
    }
    .select2-search--dropdown {
        padding: 6px !important;
    }
    .select2-search--dropdown .select2-search__field {
        padding: 6px 10px !important;
        border: 1px solid #ced4da !important;
        border-radius: 4px !important;
        width: 100% !important;
        box-sizing: border-box !important;
    }
    #table-perwalian td, #table-perwalian th {
        vertical-align: middle;
    }
    .perwalian-table-wrapper {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
</style>
@endsection

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
                            <div class="d-flex justify-content-end mb-3">
                                <button type="button" id="btn-refresh-perwalian" class="btn btn-secondary btn-sm">
                                    <i class="fa-solid fa-rotate-right me-1"></i> Refresh
                                </button>
                            </div>
                            <div class="perwalian-table-wrapper">
                                <table id="table-perwalian" class="table table-striped table-bordered w-100" style="table-layout: fixed;">
                                    <thead>
                                        <tr>
                                            <th style="width: 5%;">No</th>
                                            <th style="width: 15%;">NIM</th>
                                            <th style="width: 25%;">Nama Mahasiswa</th>
                                            <th style="width: 20%;">Program Studi</th>
                                            <th style="width: 35%;">Dosen Wali</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($mahasiswaList as $idx => $mhs)
                                            <tr data-id-mahasiswa="{{ $mhs->id }}">
                                                <td>{{ $idx + 1 }}</td>
                                                <td>{{ $mhs->nim }}</td>
                                                <td>{{ $mhs->nama ?? '-' }}</td>
                                                <td>{{ $mhs->nama_jurusan ?? '-' }}</td>
                                                <td>
                                                    <select class="form-control select2-dosen" data-id-mahasiswa="{{ $mhs->id }}" style="width: 100%;">
                                                        <option value="">-- Pilih Dosen Wali --</option>
                                                        @foreach($dosenList as $dosen)
                                                            @php
                                                                $namaLengkapDosen = trim(($dosen->gelar_depan ?? '') . ' ' . $dosen->nama_lengkap . ' ' . ($dosen->gelar_belakang ?? ''));
                                                                if (!empty($dosen->nidn)) {
                                                                    $namaLengkapDosen .= ' (' . $dosen->nidn . ')';
                                                                }
                                                            @endphp
                                                            <option value="{{ $dosen->id_pegawai }}" {{ ((string)$mhs->id_dsn_wali === (string)$dosen->id_pegawai || (string)$mhs->id_dsn_wali === (string)($dosen->id ?? '')) ? 'selected' : '' }}>{{ $namaLengkapDosen }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">Belum ada data mahasiswa.</td>
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
@endsection

@section('local-js')
<script>
    $(document).ready(function () {
        // Hapus bootstrap-select jika sempat terpasang otomatis oleh template
        if ($.fn.selectpicker) {
            $('#table-perwalian select').selectpicker('destroy');
        }

        // Custom matcher Select2: case-insensitive, mendukung multi-kata (misal cari nama depan/belakang)
        function matchDosen(params, data) {
            if (!params.term || $.trim(params.term) === '') {
                return data;
            }
            if (typeof data.text === 'undefined') {
                return null;
            }

            var term = $.trim(params.term).toLowerCase();
            var text = $.trim(data.text).toLowerCase();

            // Pisahkan kata pencarian (misal "budi santoso")
            var terms = term.split(/\s+/);
            for (var i = 0; i < terms.length; i++) {
                if (terms[i] && text.indexOf(terms[i]) === -1) {
                    return null;
                }
            }

            return data;
        }

        function initSelect2() {
            if ($.fn.select2) {
                $('#table-perwalian .select2-dosen').each(function () {
                    var $select = $(this);
                    if (!$select.hasClass('select2-hidden-accessible')) {
                        $select.select2({
                            placeholder: "-- Pilih Dosen Wali --",
                            allowClear: true,
                            width: '100%',
                            matcher: matchDosen
                        });
                    }
                });
            }
        }

        // Auto-focus ke input search saat dropdown Select2 dibuka
        $(document).on('select2:open', function () {
            window.setTimeout(function () {
                var searchInput = document.querySelector('.select2-container--open .select2-search__field');
                if (searchInput) {
                    searchInput.focus();
                }
            }, 10);
        });

        var table = null;
        if ($.fn.DataTable) {
            table = $('#table-perwalian').DataTable({
                responsive: true,
                pageLength: 25,
                language: {
                    paginate: {
                        next: '>',
                        previous: '<'
                    }
                }
            });

            table.on('draw', function () {
                initSelect2();
            });
        }

        initSelect2();

        $('#btn-refresh-perwalian').on('click', function () {
            window.location.reload();
        });

        $(document).on('change', '.select2-dosen', function () {
            var $select = $(this);
            var idMahasiswa = $select.attr('data-id-mahasiswa') || $select.closest('tr').attr('data-id-mahasiswa');
            var idDosenWali = $select.val();

            if (!idMahasiswa) {
                console.error('ID mahasiswa tidak ditemukan.', this);
                return;
            }

            $.ajax({
                url: '{{ url("akademik/perwalian/update-dosen-wali") }}',
                type: 'POST',
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}'
                },
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}',
                    id_mahasiswa: idMahasiswa,
                    id_dsn_wali: idDosenWali ? idDosenWali : null
                },
                success: function (response) {
                    if (response.success) {
                        $('.perwalian-alert').remove();
                        var alertHtml = '<div class="alert alert-success alert-dismissible fade show perwalian-alert" role="alert">' +
                            (response.message || 'Dosen wali berhasil diperbarui.') +
                            '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                            '</div>';

                        $('.filter.cm-content-box').before(alertHtml);

                        setTimeout(function () {
                            $('.perwalian-alert').fadeOut('slow', function () {
                                $(this).remove();
                            });
                        }, 3000);
                    }
                },
                error: function (xhr) {
                    console.error('Error update dosen wali:', xhr);
                    var errMsg = 'Gagal mengubah dosen wali. Silakan coba lagi.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errMsg += '\n' + xhr.responseJSON.message;
                    }
                    alert(errMsg);
                }
            });
        });
    });
</script>
@endsection
