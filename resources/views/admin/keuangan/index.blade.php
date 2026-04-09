@extends('layouts.default', ['CurrentPage' => $CurrentPage])

@section('local-css')
    <style>
        .keuangan-table-wrap {
            overflow-x: auto;
        }

        .keuangan-toast-wrap {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
            max-width: 360px;
        }

        .keuangan-toast {
            border-radius: 10px;
            color: #fff;
            padding: 12px 14px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.18);
            font-size: 13px;
            line-height: 1.4;
            transform: translateX(10px);
            opacity: 0;
            transition: all 0.2s ease;
        }

        .keuangan-toast.show {
            transform: translateX(0);
            opacity: 1;
        }

        .keuangan-toast.success {
            background: #0e9f6e;
        }

        .keuangan-toast.error {
            background: #dc3545;
        }
    </style>
@endsection

@section('content')
    <div class="content-body">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <div class="filter cm-content-box box-primary mb-4">
                        <div class="content-title SlideToolHeader">
                            <div class="cpa">
                                <i class="fa-sharp fa-solid fa-filter me-2"></i>Filter
                            </div>
                            <div class="tools">
                                <a href="javascript:void(0);" class="expand handle"><i class="fal fa-angle-down"></i></a>
                            </div>
                        </div>
                        <div class="cm-content-body form excerpt">
                            <div class="card-body">
                                <div class="row g-3 align-items-end">
                                    <div class="col-xl-8 col-md-12">
                                        <form method="GET" action="{{ url('master/keuangan') }}" class="row g-3 align-items-end">
                                            <div class="col-xl-4 col-md-6">
                                                <label class="form-label">Tahun Ajaran</label>
                                                <select name="id_tahun_ajaran" class="form-select w-100" required>
                                                    @foreach($tahunAjaranList as $tahun)
                                                        @php
                                                            $labelJenis = (int) $tahun->jenis === 1 ? 'Ganjil' : ((int) $tahun->jenis === 2 ? 'Genap' : '-');
                                                        @endphp
                                                        <option value="{{ $tahun->id }}" {{ (int) $selectedTahunId === (int) $tahun->id ? 'selected' : '' }}>
                                                            {{ $tahun->awal }}/{{ $tahun->akhir }} ({{ $labelJenis }}) ({{($tahun->tipe_mhs == 1)?'Reguler':'RPL'}}) 
                                                            @if((int) $tahun->is_aktif === 1)
                                                                - Aktif 
                                                            @endif
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-xl-4 col-md-6">
                                                <label class="form-label">Program Studi</label>
                                                <select name="id_program_studi" class="form-select w-100">
                                                    <option value="0">Semua Prodi</option>
                                                    @foreach($programStudiList as $prodi)
                                                        <option value="{{ $prodi->id }}" {{ (int) $selectedProdiId === (int) $prodi->id ? 'selected' : '' }}>
                                                            {{ $prodi->nama_jurusan }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-xl-2 col-md-6">
                                                <label class="form-label">Angkatan</label>
                                                <select name="angkatan" class="form-select w-100">
                                                    <option value="0">Semua Angkatan</option>
                                                    @foreach($angkatanList as $angkatan)
                                                        <option value="{{ $angkatan }}" {{ (int) $selectedAngkatan === (int) $angkatan ? 'selected' : '' }}>
                                                            {{ $angkatan }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-xl-2 col-md-6">
                                                <button class="btn btn-primary" type="submit">
                                                    <i class="fa fa-filter me-1"></i>Tampilkan
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="col-xl-4 col-md-12">
                                        <div class="d-flex gap-2 flex-wrap">
                                            <form method="POST" action="{{ url('master/keuangan/generate') }}" class="m-0">
                                                @csrf
                                                <input type="hidden" name="id_tahun_ajaran" value="{{ $selectedTahunId }}">
                                                <button type="submit" class="btn btn-success">
                                                    <i class="fa fa-database me-1"></i>Generate Keuangan Mahasiswa
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ url('master/keuangan/reset') }}" class="m-0" onsubmit="return confirm('Yakin reset semua status KRS, UTS, UAS menjadi Aktif untuk tahun ajaran ini?')">
                                                @csrf
                                                <input type="hidden" name="id_tahun_ajaran" value="{{ $selectedTahunId }}">
                                                <button type="submit" class="btn btn-warning">
                                                    <i class="fa fa-rotate-right me-1"></i>Reset Status Jadi Aktif
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="filter cm-content-box box-primary">
                        <div class="content-title SlideToolHeader">
                            <div class="cpa">
                                <i class="fa-solid fa-file-lines me-1"></i>{{ $title }}
                            </div>
                            <div class="tools">
                                <a href="javascript:void(0);" class="expand handle"><i class="fal fa-angle-down"></i></a>
                            </div>
                        </div>
                        <div class="cm-content-body form excerpt">
                            @if(session('status'))
                                <div class="alert alert-success alert-dismissible fade show mx-3 mt-3">
                                    {{ session('status') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif
                            @if(session('error'))
                                <div class="alert alert-danger alert-dismissible fade show mx-3 mt-3">
                                    {{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif
                            <div class="card-body pb-4">
                                @if(!$hasDataForSelectedYear)
                                    <div class="alert alert-warning mb-0">
                                        Data keuangan mahasiswa untuk tahun ajaran yang dipilih belum tersedia di database. Silakan klik tombol <strong>Generate Keuangan Mahasiswa</strong> terlebih dahulu.
                                    </div>
                                @else
                                    <div class="keuangan-table-wrap">
                                        <table id="table-keuangan" class="table table-bordered table-hover align-middle">
                                            <thead>
                                                <tr>
                                                    <th width="70">No</th>
                                                    <th>NIM</th>
                                                    <th>Nama Mahasiswa</th>
                                                    <th width="180">Status KRS</th>
                                                    <th width="180">Status KHS</th>
                                                    <th width="180">Status UTS</th>
                                                    <th width="180" style="text-align:left !important">Status UAS</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($mahasiswaList as $row)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $row->nim }}</td>
                                                        <td>{{ $row->nama }}</td>
                                                        <td>
                                                            <select class="form-select w-100 js-status-select" data-id-mahasiswa="{{ $row->id }}" data-id-tahun-ajaran="{{ $selectedTahunId }}" data-field="krs">
                                                                @foreach($statusOptions as $val => $label)
                                                                    <option value="{{ $val }}" {{ (int) ($row->krs ?? 0) === (int) $val ? 'selected' : '' }}>{{ $label }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <select class="form-select w-100 js-status-select" data-id-mahasiswa="{{ $row->id }}" data-id-tahun-ajaran="{{ $selectedTahunId }}" data-field="khs">
                                                                @foreach($statusOptions as $val => $label)
                                                                    <option value="{{ $val }}" {{ (int) ($row->khs ?? 0) === (int) $val ? 'selected' : '' }}>{{ $label }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <select class="form-select w-100 js-status-select" data-id-mahasiswa="{{ $row->id }}" data-id-tahun-ajaran="{{ $selectedTahunId }}" data-field="uts">
                                                                @foreach($statusOptions as $val => $label)
                                                                    <option value="{{ $val }}" {{ (int) ($row->uts ?? 0) === (int) $val ? 'selected' : '' }}>{{ $label }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <select class="form-select w-100 js-status-select" data-id-mahasiswa="{{ $row->id }}" data-id-tahun-ajaran="{{ $selectedTahunId }}" data-field="uas">
                                                                @foreach($statusOptions as $val => $label)
                                                                    <option value="{{ $val }}" {{ (int) ($row->uas ?? 0) === (int) $val ? 'selected' : '' }}>{{ $label }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="7" class="text-center">Data mahasiswa tidak ditemukan.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
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
        function showKeuanganToast(message, type = 'success') {
            let $wrap = $('#keuangan-toast-wrap');

            if (!$wrap.length) {
                $('body').append('<div id="keuangan-toast-wrap" class="keuangan-toast-wrap"></div>');
                $wrap = $('#keuangan-toast-wrap');
            }

            const safeType = type === 'error' ? 'error' : 'success';
            const $toast = $('<div class="keuangan-toast ' + safeType + '"></div>').text(message);
            $wrap.append($toast);

            setTimeout(function () {
                $toast.addClass('show');
            }, 10);

            setTimeout(function () {
                $toast.removeClass('show');
                setTimeout(function () {
                    $toast.remove();
                }, 220);
            }, 2500);
        }

        $(document).on('change', '.js-status-select', function () {
            const $el = $(this);
            const originalValue = $el.data('old-value') ?? $el.val();

            $.ajax({
                url: "{{ url('master/keuangan/status') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json'
                },
                data: {
                    id_mahasiswa: $el.data('id-mahasiswa'),
                    id_tahun_ajaran: $el.data('id-tahun-ajaran'),
                    field: $el.data('field'),
                    value: $el.val()
                },
                success: function (res) {
                    $el.data('old-value', $el.val());
                    showKeuanganToast(res.message || 'Status berhasil diperbarui.', 'success');
                },
                error: function () {
                    $el.val(originalValue);
                    showKeuanganToast('Gagal memperbarui status. Coba ulangi.', 'error');
                }
            });
        });

        $(function () {
            if ($('#table-keuangan').length) {
                $('#table-keuangan').DataTable({
                    responsive: true,
                    pageLength: 25,
                    order: [[1, 'asc']],
                    columnDefs: [
                        { orderable: false, targets: [3, 4, 5, 6] }
                    ]
                });
            }

            $('.js-status-select').each(function () {
                $(this).data('old-value', $(this).val());
            });
        });
    </script>
@endsection
