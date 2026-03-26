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
                            <div class="d-flex justify-content-end mb-3">
                                <button type="button" id="btn-refresh-perwalian" class="btn btn-secondary btn-sm">
                                    <i class="fa-solid fa-rotate-right me-1"></i> Refresh
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table id="table-perwalian" class="table table-striped table-bordered nowrap">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>NIM</th>
                                            <th>Nama Mahasiswa</th>
                                            <th>Program Studi</th>
                                            <th style="min-width: 300px;">Dosen Wali</th>
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
                                                    <select class="form-select select2-dosen-wali" data-id-mahasiswa="{{ $mhs->id }}" data-row-id="{{ $mhs->id }}" style="width: 100%;">
                                                        <option value="">-- Pilih Dosen Wali --</option>
                                                        @if(!empty($mhs->id_dsn_wali))
                                                            <option value="{{ $mhs->id_dsn_wali }}" selected>
                                                                {{ $mhs->dosen_wali_nama ?? 'Dosen Wali' }}{{ !empty($mhs->dosen_wali_nidn) ? ' (' . $mhs->dosen_wali_nidn . ')' : '' }}
                                                            </option>
                                                        @endif
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
<style>
    .select2-container--default .select2-selection--single {
        border: 1px solid #ced4da;
        min-height: 38px;
        padding: 0.375rem 0.35rem;
        display: flex;
        align-items: center;
    }
    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 1.5;
        padding-left: 0;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
</style>

<script>
    $(document).ready(function () {
        function resolveMahasiswaId($select) {
            var idFromAttr = $select.attr('data-id-mahasiswa');
            if (idFromAttr) {
                return idFromAttr;
            }

            var $row = $select.closest('tr');
            var idFromRow = $row.attr('data-id-mahasiswa');
            if (idFromRow) {
                return idFromRow;
            }

            // Ketika DataTables responsive menaruh elemen ke child row,
            // parent row biasanya berada tepat sebelum child row.
            if ($row.hasClass('child')) {
                var idFromParentRow = $row.prev('.parent').attr('data-id-mahasiswa');
                if (idFromParentRow) {
                    return idFromParentRow;
                }
            }

            return '';
        }

        function initSelect2(scope) {
            const $scope = scope || $(document);

            $scope.find('.select2-dosen-wali').each(function () {
                const $select = $(this);

                if ($select.hasClass('select2-hidden-accessible')) {
                    $select.select2('destroy');
                }

                $select.select2({
                    placeholder: 'Cari dosen...',
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $('body'),
                    ajax: {
                        url: '{{ url("akademik/perwalian/cari-dosen") }}',
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                q: params.term || ''
                            };
                        },
                        processResults: function (data) {
                            return data;
                        },
                        cache: true
                    },
                    minimumInputLength: 1,
                    language: {
                        inputTooShort: function () {
                            return 'Ketik minimal 1 huruf untuk mencari dosen';
                        },
                        noResults: function () {
                            return 'Dosen tidak ditemukan';
                        },
                        searching: function () {
                            return 'Mencari...';
                        }
                    }
                });
            });
        }

        const table = $('#table-perwalian').DataTable({
            responsive: true,
            pageLength: 25,
            columnDefs: [
                { targets: 0, width: '5%' }
            ]
        });

        initSelect2($('#table-perwalian'));

        table.on('draw', function () {
            initSelect2($('#table-perwalian'));
        });

        $('#btn-refresh-perwalian').on('click', function () {
            window.location.reload();
        });

        $(document).on('change', '.select2-dosen-wali', function() {
            var $select = $(this);
            var idMahasiswa = resolveMahasiswaId($select);
            var idDosenWali = $select.val();
            var selectedData = $select.select2('data');
            var selectedText = (selectedData && selectedData.length > 0) ? (selectedData[0].text || '') : '';

            if (!idMahasiswa) {
                console.error('ID mahasiswa tidak ditemukan pada elemen select.', this);
                alert('Gagal membaca data mahasiswa. Silakan refresh halaman.');
                return;
            }

            $select.prop('disabled', true);

            $.ajax({
                url: '{{ url("akademik/perwalian/update-dosen-wali") }}',
                type: 'POST',
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    id_mahasiswa: idMahasiswa,
                    id_dsn_wali: idDosenWali
                },
                success: function(response) {
                    if (response.success) {
                        if (idDosenWali && selectedText) {
                            var $existing = $select.find("option[value='" + idDosenWali + "']");
                            if ($existing.length === 0) {
                                var newOption = new Option(selectedText, idDosenWali, true, true);
                                $select.append(newOption);
                            } else {
                                $existing.text(selectedText).prop('selected', true);
                            }
                            $select.val(idDosenWali);
                        } else if (!idDosenWali) {
                            $select.val('');
                        }

                        var alertHtml = '<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                            response.message +
                            '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                            '</div>';
                        
                        $('.container').prepend(alertHtml);
                        
                        setTimeout(function() {
                            $('.alert:first').fadeOut('slow', function() {
                                $(this).remove();
                            });
                        }, 3000);
                    }
                },
                error: function(xhr) {
                    console.error('Error:', xhr);
                    alert('Gagal mengubah dosen wali. Silakan coba lagi.');
                },
                complete: function() {
                    $select.prop('disabled', false);
                    $select.removeAttr('disabled');
                }
            });
        });
    });
</script>
@endsection
