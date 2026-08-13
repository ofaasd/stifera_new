@extends('layouts.default')

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <div class="row page-titles">
            <ol class="breadcrumb">
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Master Data</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0)">Capaian Pembelajaran Lulusan (CPL)</a></li>
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
                <strong>Error!</strong> Terdapat kesalahan pada input.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header border-0 pb-0 flex-wrap">
                        <div class="mb-3 mb-md-0">
                            <h4 class="fs-20 font-w700">Data CPL (Master)</h4>
                            <span>Manajemen Capaian Pembelajaran Lulusan Program Studi</span>
                        </div>
                        <div>
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#cplModal" onclick="resetForm()">
                                <i class="fas fa-plus me-2"></i>Tambah Data CPL
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Filter Bagian Atas -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <form action="{{ route('cpl.index') }}" method="GET" id="filterForm">
                                    <label class="form-label font-w600">Filter Aspek Kompetensi</label>
                                    <select class="default-select form-control wide" name="filter_aspek" onchange="document.getElementById('filterForm').submit();">
                                        <option value="">-- Tampilkan Semua Aspek --</option>
                                        @foreach($aspekList as $aspek)
                                            <option value="{{ $aspek }}" {{ request('filter_aspek') == $aspek ? 'selected' : '' }}>{{ $aspek }}</option>
                                        @endforeach
                                    </select>
                                </form>
                            </div>
                        </div>

                        <!-- Tab Layout CPL -->
                        <div class="custom-tab-1">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#tab-s1" role="tab" aria-selected="true">
                                        <i class="la la-graduation-cap me-2"></i> S-1 Farmasi
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#tab-d3" role="tab" aria-selected="false">
                                        <i class="la la-graduation-cap me-2"></i> D-III Farmasi
                                    </a>
                                </li>
                            </ul>
                            
                            <div class="tab-content pt-4">
                                <!-- TAB S1 FARMASI -->
                                <div class="tab-pane fade show active" id="tab-s1" role="tabpanel">
                                    <div class="table-responsive">
                                        <table id="cplTableS1" class="display" style="min-width: 845px">
                                            <thead>
                                                <tr>
                                                    <th>Kode CPL</th>
                                                    <th>Aspek Kompetensi</th>
                                                    <th>Deskripsi Pembelajaran/CPL</th>
                                                    <th>Referensi</th>
                                                    <th>Target Capaian</th>
                                                    <th>Status</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($cplS1 as $cpl)
                                                <tr>
                                                    <td><strong>{{ $cpl->kode_cpl }}</strong></td>
                                                    <td><span class="badge badge-light">{{ $cpl->kategori_aspek }}</span></td>
                                                    <td style="white-space: normal; min-width: 300px;">{{ $cpl->deskripsi }}</td>
                                                    <td>{{ $cpl->referensi ?? '-' }}</td>
                                                    <td>{{ $cpl->target_capaian ?? '-' }}</td>
                                                    <td>
                                                        @if($cpl->is_active)
                                                            <span class="badge badge-success light">Aktif</span>
                                                        @else
                                                            <span class="badge badge-danger light">Tidak Aktif</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="d-flex">
                                                            <button type="button" class="btn btn-primary shadow btn-xs sharp me-1 btn-edit" 
                                                                    onclick="editCpl({{ $cpl->id_cpl }})"><i class="fas fa-pencil-alt"></i></button>
                                                            <form action="{{ route('cpl.destroy', $cpl->id_cpl) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="button" class="btn btn-danger shadow btn-xs sharp btn-delete" onclick="confirmDelete(this)"><i class="fa fa-trash"></i></button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                
                                <!-- TAB D3 FARMASI -->
                                <div class="tab-pane fade" id="tab-d3" role="tabpanel">
                                    <div class="table-responsive">
                                        <table id="cplTableD3" class="display" style="min-width: 845px">
                                            <thead>
                                                <tr>
                                                    <th>Kode CPL</th>
                                                    <th>Aspek Kompetensi</th>
                                                    <th>Deskripsi Pembelajaran/CPL</th>
                                                    <th>Referensi</th>
                                                    <th>Target Capaian</th>
                                                    <th>Status</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($cplD3 as $cpl)
                                                <tr>
                                                    <td><strong>{{ $cpl->kode_cpl }}</strong></td>
                                                    <td><span class="badge badge-light">{{ $cpl->kategori_aspek }}</span></td>
                                                    <td style="white-space: normal; min-width: 300px;">{{ $cpl->deskripsi }}</td>
                                                    <td>{{ $cpl->referensi ?? '-' }}</td>
                                                    <td>{{ $cpl->target_capaian ?? '-' }}</td>
                                                    <td>
                                                        @if($cpl->is_active)
                                                            <span class="badge badge-success light">Aktif</span>
                                                        @else
                                                            <span class="badge badge-danger light">Tidak Aktif</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="d-flex">
                                                            <button type="button" class="btn btn-primary shadow btn-xs sharp me-1 btn-edit" 
                                                                    onclick="editCpl({{ $cpl->id_cpl }})"><i class="fas fa-pencil-alt"></i></button>
                                                            <form action="{{ route('cpl.destroy', $cpl->id_cpl) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="button" class="btn btn-danger shadow btn-xs sharp btn-delete" onclick="confirmDelete(this)"><i class="fa fa-trash"></i></button>
                                                            </form>
                                                        </div>
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
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah/Edit CPL -->
<div class="modal fade" id="cplModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tambah Data CPL</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="cplForm" action="{{ route('cpl.store') }}" method="POST">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label class="form-label font-w600">Program Studi <span class="text-danger">*</span></label>
                            <select class="default-select form-control wide" name="id_prodi" id="id_prodi" required>
                                <option value="">-- Pilih Program Studi --</option>
                                @foreach($prodiList as $prodi)
                                    <option value="{{ $prodi->id }}">{{ $prodi->nama_jurusan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label class="form-label font-w600">Kategori Aspek Kompetensi <span class="text-danger">*</span></label>
                            <select class="default-select form-control wide" name="kategori_aspek" id="kategori_aspek" required>
                                <option value="Sikap">Sikap</option>
                                <option value="Pengetahuan">Pengetahuan</option>
                                <option value="Keterampilan">Keterampilan</option>
                            </select>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label class="form-label font-w600">Kode CPL <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="kode_cpl" id="kode_cpl" placeholder="Contoh: CPL-01" required maxlength="10">
                        </div>
                        <div class="mb-3 col-md-6">
                            <label class="form-label font-w600">Target Capaian (Optional)</label>
                            <input type="number" step="0.01" class="form-control" name="target_capaian" id="target_capaian" placeholder="Contoh: 100">
                        </div>
                        <div class="mb-3 col-md-12">
                            <label class="form-label font-w600">Deskripsi Pembelajaran <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="deskripsi" id="deskripsi" rows="4" required placeholder="Tuliskan deskripsi lengkap CPL disini..."></textarea>
                        </div>
                        <div class="mb-3 col-md-12">
                            <label class="form-label font-w600">Referensi Standar (Optional)</label>
                            <input type="text" class="form-control" name="referensi" id="referensi" placeholder="Contoh: SN-Dikti" maxlength="100">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSubmit">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize DataTables
        $('#cplTableS1').DataTable({
            language: {
                paginate: {
                    next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                    previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>' 
                }
            }
        });
        
        $('#cplTableD3').DataTable({
            language: {
                paginate: {
                    next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                    previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>' 
                }
            }
        });
    });

    function resetForm() {
        document.getElementById('cplForm').reset();
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('cplForm').action = "{{ route('cpl.store') }}";
        document.getElementById('modalTitle').innerText = 'Tambah Data CPL';
        document.getElementById('btnSubmit').innerText = 'Simpan Data';
        
        // Reset selectpicker jika menggunakan bootstrap-select
        if($('.default-select').length > 0) {
            $('.default-select').selectpicker('refresh');
        }
    }

    function editCpl(id) {
        resetForm();
        document.getElementById('modalTitle').innerText = 'Edit Data CPL';
        document.getElementById('btnSubmit').innerText = 'Update Data';
        
        let updateUrl = "{{ url('master/cpl') }}/" + id;
        document.getElementById('formMethod').value = 'PUT';
        document.getElementById('cplForm').action = updateUrl;
        
        $.ajax({
            url: "{{ url('master/cpl') }}/" + id + "/edit",
            type: "GET",
            success: function(response) {
                $('#id_prodi').val(response.id_prodi);
                $('#kategori_aspek').val(response.kategori_aspek);
                $('#kode_cpl').val(response.kode_cpl);
                $('#target_capaian').val(response.target_capaian);
                $('#deskripsi').val(response.deskripsi);
                $('#referensi').val(response.referensi);
                
                if($('.default-select').length > 0) {
                    $('.default-select').selectpicker('refresh');
                }
                
                $('#cplModal').modal('show');
            }
        });
    }

    function confirmDelete(button) {
        if (confirm('Apakah Anda yakin ingin menonaktifkan data CPL ini? (Soft Delete)')) {
            button.closest('form').submit();
        }
    }
</script>
@endpush
