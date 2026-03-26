@extends('layouts.default', ['CurrentPage' => $CurrentPage])

@section('local-css')
<style>
    .jadwal-grid label {
        font-size: 12px;
        margin-bottom: 4px;
        color: #2c2c2c;
    }
    .section-title {
        font-weight: 700;
        margin: 18px 0 10px;
    }
    .rombel-row {
        border-bottom: 1px solid #e5e5e5;
        padding-bottom: 12px;
        margin-bottom: 12px;
    }
</style>
@endsection

@section('content')
<div class="content-body">
    <div class="container">
        <div class="row">
            <div class="col-xl-12">
                <div class="mb-3">
                    <a href="{{ url('master/jadwal') }}" class="btn btn-primary"><i class="fa fa-arrow-left me-1"></i>Kembali</a>
                </div>

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
                            <i class="fa-solid fa-file-lines me-1"></i>{{ $title }}
                            <span class="ms-2 text-muted">{{ $mataKuliah->kode_mata_kuliah }} - {{ $mataKuliah->nama_mata_kuliah }}</span>
                        </div>
                        <div class="tools">
                            <a href="javascript:void(0);" class="expand handle"><i class="fal fa-angle-down"></i></a>
                        </div>
                    </div>

                    <div class="cm-content-body form excerpt">
                        <div class="card-body">
                            <div class="mb-3">
                                <strong>Tahun Ajaran Aktif:</strong>
                                {{ $tahunAktif->awal }}/{{ $tahunAktif->akhir }}
                                @php $jenis = ((int)$tahunAktif->jenis === 1) ? 'Ganjil' : (((int)$tahunAktif->jenis === 2) ? 'Genap' : 'Antara'); @endphp
                                ({{ $jenis }})
                            </div>

                            <form method="POST" action="{{ url('master/jadwal/input/' . $mataKuliah->kode_mata_kuliah) }}">
                                @csrf

                                @php
                                    $sections = ['regular' => 'Kelas Reguler', 'karyawan' => 'Kelas Karyawan'];
                                    $rombels = ['a', 'b', 'c'];
                                @endphp

                                @foreach($sections as $sectionKey => $sectionTitle)
                                    <div class="section-title">{{ $sectionTitle }}</div>

                                    @foreach($rombels as $rombel)
                                        @php
                                            $mapKey = $sectionKey . '_' . $rombel;
                                            $ex = $existingMap[$mapKey] ?? null;
                                        @endphp

                                        <div class="rombel-row">
                                            <div class="row jadwal-grid g-2 align-items-end">
                                                <div class="col-md-1">
                                                    <label>Rombel:</label>
                                                    <select class="form-control" disabled>
                                                        <option>{{ 'Rombel ' . strtoupper($rombel) }}</option>
                                                    </select>
                                                </div>

                                                <div class="col-md-4">
                                                    <label>Dosen:</label>
                                                    <select name="rows[{{ $sectionKey }}][{{ $rombel }}][id_dosen]" class="form-control">
                                                        <option value="">--Pilih Dosen 1--</option>
                                                        @foreach($dosenList as $d)
                                                            <option value="{{ $d->id }}" {{ (string)old('rows.' . $sectionKey . '.' . $rombel . '.id_dosen', $ex->id_dosen ?? '') === (string)$d->id ? 'selected' : '' }}>
                                                                {{ trim(($d->gelar_depan ?? '') . ' ' . ($d->nama_lengkap ?? '') . ' ' . ($d->gelar_belakang ?? '')) }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <select name="rows[{{ $sectionKey }}][{{ $rombel }}][id_dosen2]" class="form-control mt-1">
                                                        <option value="">--Pilih Dosen 2--</option>
                                                        @foreach($dosenList as $d)
                                                            <option value="{{ $d->id }}" {{ (string)old('rows.' . $sectionKey . '.' . $rombel . '.id_dosen2', $ex->id_dosen2 ?? '') === (string)$d->id ? 'selected' : '' }}>
                                                                {{ trim(($d->gelar_depan ?? '') . ' ' . ($d->nama_lengkap ?? '') . ' ' . ($d->gelar_belakang ?? '')) }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-md-1">
                                                    <label>Hari:</label>
                                                    <select name="rows[{{ $sectionKey }}][{{ $rombel }}][hari]" class="form-control">
                                                        <option value="">--Pilih Hari--</option>
                                                        @foreach($hariList as $h)
                                                            <option value="{{ $h->nama_hari }}" {{ old('rows.' . $sectionKey . '.' . $rombel . '.hari', $ex->hari ?? '') === $h->nama_hari ? 'selected' : '' }}>{{ $h->nama_hari }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-md-1">
                                                    <label>Sesi:</label>
                                                    <select name="rows[{{ $sectionKey }}][{{ $rombel }}][sesi]" class="form-control">
                                                        <option value="">--Pilih Sesi--</option>
                                                        @foreach($sesiList as $s)
                                                            @php
                                                                $sesiText = !empty($s->nama_sesi) ? $s->nama_sesi : (substr((string)$s->mulai, 11, 5) . '-' . substr((string)$s->selesai, 11, 5));
                                                            @endphp
                                                            <option value="{{ $sesiText }}" {{ old('rows.' . $sectionKey . '.' . $rombel . '.sesi', $ex->sesi ?? '') === $sesiText ? 'selected' : '' }}>{{ $sesiText }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-md-2">
                                                    <label>Ruang:</label>
                                                    <select name="rows[{{ $sectionKey }}][{{ $rombel }}][ruang]" class="form-control">
                                                        <option value="">--Pilih Ruang--</option>
                                                        @foreach($ruangList as $r)
                                                            <option value="{{ $r->nama_ruang }}" {{ old('rows.' . $sectionKey . '.' . $rombel . '.ruang', $ex->ruang ?? '') === $r->nama_ruang ? 'selected' : '' }}>{{ $r->nama_ruang }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-md-1">
                                                    <label>Kelas:</label>
                                                    <select class="form-control" disabled>
                                                        <option>{{ $sectionKey === 'karyawan' ? 'Karyawan' : 'Reguler' }}</option>
                                                    </select>
                                                </div>

                                                <div class="col-md-1">
                                                    <label>Status:</label>
                                                    <select name="rows[{{ $sectionKey }}][{{ $rombel }}][status]" class="form-control">
                                                        <option value="0" {{ (string)old('rows.' . $sectionKey . '.' . $rombel . '.status', $ex->status ?? '0') === '0' ? 'selected' : '' }}>TUTUP</option>
                                                        <option value="1" {{ (string)old('rows.' . $sectionKey . '.' . $rombel . '.status', $ex->status ?? '0') === '1' ? 'selected' : '' }}>BUKA</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endforeach

                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
