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

                @if(isset($isKursiRuangLengkap) && !$isKursiRuangLengkap)
                    <div class="alert alert-warning alert-dismissible fade show">
                        Data no kursi dan/atau ruang ujian belum tersimpan lengkap di database untuk jadwal ini. Silakan lengkapi lalu simpan.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="mb-3 d-flex gap-2">
                    <a href="{{ url('master/pengaturan-ujian') }}" class="btn btn-secondary" title="Kembali ke list">
                        <i class="fa fa-arrow-left"></i>
                    </a>
                    <a href="{{ url('master/pengaturan-ujian/detail/' . $jadwal->id) }}" class="btn btn-primary" title="Kembali ke detail pengaturan ujian">
                        Kembali
                    </a>
                </div>

                <div class="filter cm-content-box box-primary">
                    <div class="content-title SlideToolHeader">
                        <div class="cpa">
                            <i class="fa-solid fa-chair me-1"></i>{{ $title }}
                        </div>
                        <div class="tools">
                            <a href="javascript:void(0);" class="expand handle"><i class="fal fa-angle-down"></i></a>
                        </div>
                    </div>

                    <div class="cm-content-body form excerpt">
                        <div class="card-body pb-4">
                            <div class="table-responsive mb-3">
                                <table class="table table-borderless w-auto" style="min-width: 620px;">
                                    <tbody>
                                        <tr>
                                            <td class="fw-bold" style="width:180px;">Kode Mata Kuliah</td>
                                            <td class="px-2">:</td>
                                            <td>{{ $jadwal->kode_mata_kuliah ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Nama Mata Kuliah</td>
                                            <td class="px-2">:</td>
                                            <td>{{ $jadwal->nama_mata_kuliah ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Tahun Ajaran</td>
                                            <td class="px-2">:</td>
                                            <td>{{ ($jadwal->id_tahun ?? '-') }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <form method="POST" action="{{ url('master/pengaturan-ujian/kursi/' . $jadwal->id) }}">
                                @csrf

                                <div class="row g-3 align-items-end mb-3">
                                    <div class="col-md-12">
                                        <label class="form-label fw-bold">Nama Ruang</label>
                                        <select name="ruang_ujian" class="form-control" required>
                                            <option value="">-- Pilih Ruang --</option>
                                            @foreach($ruangList as $ruang)
                                                <option value="{{ $ruang->nama_ruang }}" {{ (string) old('ruang_ujian', $selectedRuang ?? '') === (string) $ruang->nama_ruang ? 'selected' : '' }}>
                                                    {{ $ruang->nama_ruang }}
                                                </option>
                                            @endforeach
                                            @if(!empty($selectedRuang) && !$ruangList->pluck('nama_ruang')->contains($selectedRuang))
                                                <option value="{{ $selectedRuang }}" selected>{{ $selectedRuang }}</option>
                                            @endif
                                        </select>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover align-middle">
                                        <thead>
                                            <tr>
                                                <th style="width: 60px;">#</th>
                                                <th style="width: 160px;">NIM</th>
                                                <th>NAMA MAHASISWA</th>
                                                <th style="width: 180px;">NOMOR KURSI</th>
                                                <th style="width: 140px;">RUANG</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($peserta as $index => $row)
                                                @php
                                                    $nim = (string) ($row->nim ?? '');
                                                    $defaultNo = $index + 1;
                                                    $selectedNo = (int) old('no_kursi.' . $nim, $row->no_kursi ?? $defaultNo);
                                                @endphp
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $nim }}</td>
                                                    <td>{{ $row->nama_mahasiswa ?? '-' }}</td>
                                                    <td>
                                                        <select name="no_kursi[{{ $nim }}]" class="form-control form-control-sm" required>
                                                            @for($n = 1; $n <= $maxKursi; $n++)
                                                                <option value="{{ $n }}" {{ $selectedNo === $n ? 'selected' : '' }}>{{ $n }}</option>
                                                            @endfor
                                                        </select>
                                                    </td>
                                                    <td>{{ old('ruang_ujian', $selectedRuang ?? '-') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="mt-3">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fa fa-save me-1"></i>Simpan Pengaturan Kursi
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
