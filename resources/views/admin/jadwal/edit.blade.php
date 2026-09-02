@extends('layouts.default', ['CurrentPage' => $CurrentPage])

@section('content')
<div class="content-body">
    <div class="container">
        <div class="row">
            <div class="col-xl-12">
                <div class="mb-3">
                    <a href="{{ url('master/jadwal') }}" class="btn btn-primary"><i class="fa fa-arrow-left me-1"></i>Kembali</a>
                </div>

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="filter cm-content-box box-primary">
                    <div class="content-title SlideToolHeader">
                        <div class="cpa">
                            <i class="fa-solid fa-pen-to-square me-1"></i>{{ $title }}
                        </div>
                        <div class="tools">
                            <a href="javascript:void(0);" class="expand handle"><i class="fal fa-angle-down"></i></a>
                        </div>
                    </div>

                    <div class="cm-content-body form excerpt">
                        <div class="card-body">
                            <div class="mb-3">
                                <strong>Mata Kuliah:</strong> {{ $mataKuliah->kode_mata_kuliah ?? $jadwal->kode_mata_kuliah }} - {{ $mataKuliah->nama_mata_kuliah ?? '-' }}<br>
                                <strong>Tahun Ajaran:</strong> {{ $tahun->awal ?? '-' }}/{{ $tahun->akhir ?? '-' }}<br>
                                <strong>Rombel:</strong> {{ ((int) $jadwal->kelas === 3 || (int) $jadwal->tipe_mhs === 2 ? 'RPL' : ((int) $jadwal->kelas === 2 ? 'Karyawan' : 'Reguler')) . ' ' . ($jadwal->rombel ?? '-') }}
                            </div>

                            <form method="POST" action="{{ url('master/jadwal/edit/' . $jadwal->id) }}">
                                @csrf
                                @method('PUT')
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Dosen 1</label>
                                        <select name="id_dosen" class="form-control select2-dosen" required>
                                            <option value="">-- Pilih Dosen 1 --</option>
                                            @foreach($dosenList as $d)
                                                <option value="{{ $d->id }}" {{ (string) old('id_dosen', $jadwal->id_dosen) === (string) $d->id ? 'selected' : '' }}>
                                                    {{ trim(($d->gelar_depan ?? '') . ' ' . ($d->nama_lengkap ?? '') . ' ' . ($d->gelar_belakang ?? '')) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Dosen 2</label>
                                        <select name="id_dosen2" class="form-control select2-dosen">
                                            <option value="">-- Pilih Dosen 2 --</option>
                                            @foreach($dosenList as $d)
                                                <option value="{{ $d->id }}" {{ (string) old('id_dosen2', $jadwal->id_dosen2) === (string) $d->id ? 'selected' : '' }}>
                                                    {{ trim(($d->gelar_depan ?? '') . ' ' . ($d->nama_lengkap ?? '') . ' ' . ($d->gelar_belakang ?? '')) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Hari</label>
                                        <select name="hari" class="form-control" required>
                                            <option value="">-- Pilih Hari --</option>
                                            @foreach($hariList as $h)
                                                <option value="{{ $h->nama_hari }}" {{ old('hari', $jadwal->hari) === $h->nama_hari ? 'selected' : '' }}>{{ $h->nama_hari }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Sesi</label>
                                        <select name="sesi" class="form-control" required>
                                            <option value="">-- Pilih Sesi --</option>
                                            @foreach($sesiList as $s)
                                                @php
                                                    $sesiText = !empty($s->nama_sesi) ? $s->nama_sesi : (substr((string)$s->mulai, 11, 5) . '-' . substr((string)$s->selesai, 11, 5));
                                                @endphp
                                                <option value="{{ $sesiText }}" {{ old('sesi', $jadwal->sesi) === $sesiText ? 'selected' : '' }}>{{ $sesiText }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Ruang</label>
                                        <select name="ruang" class="form-control" required>
                                            <option value="">-- Pilih Ruang --</option>
                                            @foreach($ruangList as $r)
                                                <option value="{{ $r->nama_ruang }}" {{ old('ruang', $jadwal->ruang) === $r->nama_ruang ? 'selected' : '' }}>{{ $r->nama_ruang }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Kuota</label>
                                        <input
                                            type="number"
                                            name="kuota_diambil"
                                            class="form-control"
                                            min="0"
                                            value="{{ old('kuota_diambil', (int) ($jadwal->kuota_diambil ?? 0)) }}"
                                            placeholder="0"
                                        >
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Status</label>
                                        <select name="status" class="form-control" required>
                                            <option value="0" {{ (string) old('status', $jadwal->status) === '0' ? 'selected' : '' }}>TUTUP</option>
                                            <option value="1" {{ (string) old('status', $jadwal->status) === '1' ? 'selected' : '' }}>BUKA</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <button type="submit" class="btn btn-primary">Update</button>
                                    <a href="{{ url('master/jadwal') }}" class="btn btn-light">Batal</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@section('local-js')
<script>
    $(document).ready(function() {
        if ($.fn.select2) {
            $('.select2-dosen').select2({
                placeholder: "--Pilih Dosen--",
                allowClear: true,
                width: '100%'
            });
        }
    });
</script>
@endsection
