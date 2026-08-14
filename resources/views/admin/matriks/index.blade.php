@extends('layouts.default')

@section('content')
<style>
    /* Custom Hover Data Baris saja, memastikan thead bebas dari bug white-hover bawaan */
    .custom-hover-table tbody tr.mk-row:hover > td {
        background-color: rgba(0, 0, 0, 0.05) !important;
    }
</style>
<div class="content-body">
    <div class="container-fluid">
        <div class="row page-titles">
            <ol class="breadcrumb">
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Master Data</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0)">Matriks Kurikulum (OBE)</a></li>
            </ol>
        </div>

        @if(session('success'))
            <div class="alert alert-success solid alert-dismissible fade show">
                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" class="me-2"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
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
                            <h4 class="fs-20 font-w700">Matriks Kurikulum (Curriculum Mapping)</h4>
                            <span>Pemetaan Mata Kuliah terhadap Capaian Pembelajaran Lulusan (CPL)</span>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <form action="{{ route('matriks.store') }}" method="POST">
                            @csrf
                            <div class="mb-4 text-end">
                                <button type="submit" class="btn btn-primary shadow"><i class="fas fa-save me-2"></i>Simpan Pemetaan Kurikulum</button>
                            </div>
                            
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
                                        <div class="table-responsive" style="overflow-x: auto;">
                                            <table class="table table-bordered custom-hover-table" style="min-width: 1000px; white-space: nowrap;">
                                                <thead class="text-center bg-primary text-white">
                                                    <tr>
                                                        <th rowspan="2" class="align-middle" width="50">No</th>
                                                        <th rowspan="2" class="align-middle">Nama Mata Kuliah</th>
                                                        <th rowspan="2" class="align-middle" width="70">SKS</th>
                                                        <th colspan="{{ count($cplS1) }}" class="align-middle">Capaian Pembelajaran Lulusan (CPL)</th>
                                                        <th rowspan="2" class="align-middle bg-secondary" width="100">Total CPL</th>
                                                    </tr>
                                                    <tr>
                                                        @foreach($cplS1 as $cpl)
                                                            <th title="{{ $cpl->deskripsi }}" class="p-2" style="vertical-align: middle; min-width: 65px;">
                                                                <div class="d-flex flex-column align-items-center justify-content-center w-100 text-center text-center-force">
                                                                    <span class="mb-1">{{ $cpl->kode_cpl }}</span>
                                                                    @php
                                                                        $badgeClass = match($cpl->kategori_aspek) {
                                                                            'Sikap' => 'badge-success light',
                                                                            'Pengetahuan' => 'badge-info light',
                                                                            'Keterampilan Umum' => 'badge-primary light',
                                                                            'Keterampilan Khusus' => 'badge-warning light',
                                                                            default => 'badge-light'
                                                                        };
                                                                    @endphp
                                                                    <span class="badge {{ $badgeClass }} fs-10" style="padding: 3px 6px;">
                                                                        {{ strtoupper(substr($cpl->kategori_aspek, 0, 1)) }}
                                                                    </span>
                                                                </div>
                                                            </th>
                                                        @endforeach
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php $globalNoS1 = 1; @endphp
                                                    @foreach($mkS1Grouped as $semester => $mks)
                                                        <tr class="bg-light font-weight-bold">
                                                            <td colspan="{{ 4 + count($cplS1) }}" class="text-start text-dark fs-14">
                                                                <i class="fas fa-tag me-2"></i> <strong>Semester {{ $semester }}</strong>
                                                            </td>
                                                        </tr>
                                                        @foreach($mks as $mk)
                                                            <tr class="mk-row">
                                                                <td class="text-center">{{ $globalNoS1++ }}</td>
                                                                <td class="text-wrap" style="min-width: 250px;">
                                                                    <strong>{{ $mk->kode_mata_kuliah }}</strong><br>
                                                                    {{ $mk->nama_mata_kuliah }}
                                                                </td>
                                                                <td class="text-center">{{ $mk->jumlah_sks }}</td>
                                                                @foreach($cplS1 as $cpl)
                                                                    @php $isChecked = isset($mappedData[$mk->id][$cpl->id_cpl]) ? 'checked' : ''; @endphp
                                                                    <td class="text-center">
                                                                        <div class="form-check custom-checkbox custom-control d-inline-block">
                                                                            <input type="checkbox" class="form-check-input chk-cpl" 
                                                                                   name="mapping[{{ $mk->id }}][{{ $cpl->id_cpl }}]" 
                                                                                   value="1" {{ $isChecked }} onchange="calculateTotal(this)">
                                                                        </div>
                                                                    </td>
                                                                @endforeach
                                                                <td class="text-center font-weight-bold fs-16">
                                                                    <span class="badge badge-secondary light badge-total">0</span>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    
                                    <!-- TAB D3 FARMASI -->
                                    <div class="tab-pane fade" id="tab-d3" role="tabpanel">
                                        <div class="table-responsive" style="overflow-x: auto;">
                                            <table class="table table-bordered custom-hover-table" style="min-width: 1000px; white-space: nowrap;">
                                                <thead class="text-center bg-primary text-white">
                                                    <tr>
                                                        <th rowspan="2" class="align-middle" width="50">No</th>
                                                        <th rowspan="2" class="align-middle">Nama Mata Kuliah</th>
                                                        <th rowspan="2" class="align-middle" width="70">SKS</th>
                                                        <th colspan="{{ count($cplD3) }}" class="align-middle">Capaian Pembelajaran Lulusan (CPL)</th>
                                                        <th rowspan="2" class="align-middle bg-secondary" width="100">Total CPL</th>
                                                    </tr>
                                                    <tr>
                                                        @foreach($cplD3 as $cpl)
                                                            <th title="{{ $cpl->deskripsi }}" class="p-2" style="vertical-align: middle; min-width: 65px;">
                                                                <div class="d-flex flex-column align-items-center justify-content-center w-100 text-center text-center-force">
                                                                    <span class="mb-1">{{ $cpl->kode_cpl }}</span>
                                                                    @php
                                                                        $badgeClass = match($cpl->kategori_aspek) {
                                                                            'Sikap' => 'badge-success light',
                                                                            'Pengetahuan' => 'badge-info light',
                                                                            'Keterampilan Umum' => 'badge-primary light',
                                                                            'Keterampilan Khusus' => 'badge-warning light',
                                                                            default => 'badge-light'
                                                                        };
                                                                    @endphp
                                                                    <span class="badge {{ $badgeClass }} fs-10" style="padding: 3px 6px;">
                                                                        {{ strtoupper(substr($cpl->kategori_aspek, 0, 1)) }}
                                                                    </span>
                                                                </div>
                                                            </th>
                                                        @endforeach
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php $globalNoD3 = 1; @endphp
                                                    @foreach($mkD3Grouped as $semester => $mks)
                                                        <tr class="bg-light font-weight-bold">
                                                            <td colspan="{{ 4 + count($cplD3) }}" class="text-start text-dark fs-14">
                                                                <i class="fas fa-tag me-2"></i> <strong>Semester {{ $semester }}</strong>
                                                            </td>
                                                        </tr>
                                                        @foreach($mks as $mk)
                                                            <tr class="mk-row">
                                                                <td class="text-center">{{ $globalNoD3++ }}</td>
                                                                <td class="text-wrap" style="min-width: 250px;">
                                                                    <strong>{{ $mk->kode_mata_kuliah }}</strong><br>
                                                                    {{ $mk->nama_mata_kuliah }}
                                                                </td>
                                                                <td class="text-center">{{ $mk->jumlah_sks }}</td>
                                                                @foreach($cplD3 as $cpl)
                                                                    @php $isChecked = isset($mappedData[$mk->id][$cpl->id_cpl]) ? 'checked' : ''; @endphp
                                                                    <td class="text-center">
                                                                        <div class="form-check custom-checkbox custom-control d-inline-block">
                                                                            <input type="checkbox" class="form-check-input chk-cpl" 
                                                                                   name="mapping[{{ $mk->id }}][{{ $cpl->id_cpl }}]" 
                                                                                   value="1" {{ $isChecked }} onchange="calculateTotal(this)">
                                                                        </div>
                                                                    </td>
                                                                @endforeach
                                                                <td class="text-center font-weight-bold fs-16">
                                                                    <span class="badge badge-secondary light badge-total">0</span>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-4 text-end">
                                <button type="submit" class="btn btn-primary shadow"><i class="fas fa-save me-2"></i>Simpan Pemetaan Kurikulum</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('local-js')
<script>
    function calculateTotal(checkbox) {
        var row = checkbox.closest('.mk-row');
        var checkboxes = row.querySelectorAll('.chk-cpl');
        var total = 0;
        
        checkboxes.forEach(function(chk) {
            if(chk.checked) {
                total++;
            }
        });
        
        var badgeTotal = row.querySelector('.badge-total');
        badgeTotal.innerText = total;
        
        // Ganti warna badge jika ada CPL yang terpilih
        if(total > 0) {
            badgeTotal.classList.remove('badge-secondary', 'light');
            badgeTotal.classList.add('badge-primary');
        } else {
            badgeTotal.classList.remove('badge-primary');
            badgeTotal.classList.add('badge-secondary', 'light');
        }
    }

    // Hitung inisial saat halaman selesai dimuat
    document.addEventListener("DOMContentLoaded", function() {
        var rows = document.querySelectorAll('.mk-row');
        rows.forEach(function(row) {
            // Ambil elemen checkbox pertama sekadar u/ trigger fungsi
            var firstChk = row.querySelector('.chk-cpl');
            if(firstChk) {
                calculateTotal(firstChk);
            }
        });
    });
</script>
@endsection
