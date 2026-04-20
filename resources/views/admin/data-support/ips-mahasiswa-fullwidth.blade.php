@extends('layouts.fullwidth', ['CurrentPage' => $CurrentPage])

@section('content')
<div class="content-body">
    <div class="container">
        <div class="row">
            <div class="col-xl-12">
                <div class="mb-3 pb-3">
                    <a href="{{ $backUrl }}" class="btn btn-primary">
                        <i class="fa-solid fa-arrow-left me-1"></i> Kembali
                    </a>
                </div>

                <div class="cm-content-box box-primary">
                    <div class="content-title cm-title">
                        <div class="cpa">
                            <i class="fa-solid fa-chart-line me-2"></i>{{ $title }}
                        </div>
                        <div class="tools">
                            <a href="javascript:void(0);" class="expand handle"><i class="fal fa-angle-down"></i></a>
                        </div>
                    </div>
                    <div class="cm-content-body">
                        <div class="card-body">
                            <p class="text-muted mb-4">Download data Indeks Prestasi Semester (IPS) mahasiswa dalam format Excel.</p>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Tahun Ajaran</label>
                                        <select class="form-select" id="filter-tahun-ajaran">
                                            <option value="">-- Pilih Tahun Ajaran --</option>
                                            <option value="2023">2023/2024</option>
                                            <option value="2024">2024/2025</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Program Studi (Opsional)</label>
                                        <select class="form-select" id="filter-progdi">
                                            <option value="">-- Semua Program Studi --</option>
                                            <option value="T001">Teknik Informatika</option>
                                            <option value="T002">Teknik Komputer</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-12">
                                    <button class="btn btn-success" onclick="downloadData()">
                                        <i class="fa-solid fa-download me-1"></i> Download Excel
                                    </button>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="alert alert-info">
                                <i class="fa-solid fa-circle-info me-2"></i>
                                <strong>Info:</strong> Data IPS mahasiswa menampilkan indeks prestasi semester per mahasiswa untuk tahun ajaran yang dipilih.
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
    function downloadData() {
        alert('Fitur download akan diimplementasikan di tahap selanjutnya');
    }
</script>
@endsection
