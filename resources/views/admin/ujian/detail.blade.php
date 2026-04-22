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

                <div class="mb-3">
                    <a href="{{ url('master/pengaturan-ujian') }}" class="btn btn-secondary" title="Kembali ke list">
                        <i class="fa fa-arrow-left"></i>
                    </a>
                    <a href="{{ url('master/pengaturan-ujian/kursi/' . $jadwal->id) }}" class="btn btn-info ms-2" title="Atur nomor kursi ujian">
                        <i class="fa fa-chair"></i> Atur No Kursi
                    </a>
                </div>

                <div class="filter cm-content-box box-primary">
                    <div class="content-title SlideToolHeader">
                        <div class="cpa">
                            <i class="fa-solid fa-gear me-1"></i>{{ $title }}
                        </div>
                        <div class="tools">
                            <a href="javascript:void(0);" class="expand handle"><i class="fal fa-angle-down"></i></a>
                        </div>
                    </div>

                    <div class="cm-content-body form excerpt">
                        <div class="card-body pb-4">
                            <div class="table-responsive mb-4">
                                <table class="table table-borderless w-auto" style="min-width: 620px;">
                                    <tbody>
                                        <tr>
                                            <td class="fw-bold" style="width:200px;">Kode Mata Kuliah</td>
                                            <td class="px-2">:</td>
                                            <td>{{ $jadwal->kode_mata_kuliah ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Nama Mata Kuliah</td>
                                            <td class="px-2">:</td>
                                            <td>{{ $jadwal->nama_mata_kuliah ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Dosen</td>
                                            <td class="px-2">:</td>
                                            <td>{{ trim($jadwal->nama_dosen ?? '-') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Tahun Ajaran</td>
                                            <td class="px-2">:</td>
                                            <td>{{ ($jadwal->awal ?? '-') . '/' . ($jadwal->akhir ?? '-') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Jadwal Kuliah</td>
                                            <td class="px-2">:</td>
                                            <td>{{ $jadwal->hari ?? '-' }} / {{ $jadwal->sesi ?? '-' }} / {{ $jadwal->ruang ?? '-' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <form method="POST" action="{{ url('master/pengaturan-ujian/detail/' . $jadwal->id) }}">
                                @csrf

                                @php
                                    $tp = strtolower(trim((string) ($jadwal->tp ?? '')));
                                    $showTeori = $tp !== 'p';
                                    $showPraktik = $tp !== 't';
                                @endphp

                                <div class="row g-3">
                                    @if($showTeori)
                                        <div class="col-12">
                                            <div class="fw-semibold text-primary mt-1">Jadwal Ujian Teori</div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Tanggal UTS Teori</label>
                                            <input type="date" name="tanggal_uts_t" class="form-control" value="{{ old('tanggal_uts_t', !empty($pengaturan?->tanggal_uts_t) ? \Carbon\Carbon::parse($pengaturan->tanggal_uts_t)->format('Y-m-d') : '') }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Jam UTS Teori</label>
                                            <select name="id_jam_uts_t" class="form-control">
                                                <option value="">-- Pilih Jam --</option>
                                                @foreach($jamList as $jam)
                                                    <option value="{{ $jam->id }}" {{ (string) old('id_jam_uts_t', $pengaturan->id_jam_uts_t ?? '') === (string) $jam->id ? 'selected' : '' }}>
                                                        {{ $jam->nama_sesi }} ({{ \Carbon\Carbon::parse($jam->mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jam->selesai)->format('H:i') }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Tanggal UAS Teori</label>
                                            <input type="date" name="tanggal_uas_t" class="form-control" value="{{ old('tanggal_uas_t', !empty($pengaturan?->tanggal_uas_t) ? \Carbon\Carbon::parse($pengaturan->tanggal_uas_t)->format('Y-m-d') : '') }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Jam UAS Teori</label>
                                            <select name="id_jam_uas_t" class="form-control">
                                                <option value="">-- Pilih Jam --</option>
                                                @foreach($jamList as $jam)
                                                    <option value="{{ $jam->id }}" {{ (string) old('id_jam_uas_t', $pengaturan->id_jam_uas_t ?? '') === (string) $jam->id ? 'selected' : '' }}>
                                                        {{ $jam->nama_sesi }} ({{ \Carbon\Carbon::parse($jam->mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jam->selesai)->format('H:i') }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif

                                    @if($showPraktik)
                                        <div class="col-12">
                                            <div class="fw-semibold text-primary mt-2">Jadwal Ujian Praktik</div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Tanggal UTS Praktik</label>
                                            <input type="date" name="tanggal_uts_p" class="form-control" value="{{ old('tanggal_uts_p', !empty($pengaturan?->tanggal_uts_p) ? \Carbon\Carbon::parse($pengaturan->tanggal_uts_p)->format('Y-m-d') : '') }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Jam UTS Praktik</label>
                                            <select name="id_jam_uts_p" class="form-control">
                                                <option value="">-- Pilih Jam --</option>
                                                @foreach($jamList as $jam)
                                                    <option value="{{ $jam->id }}" {{ (string) old('id_jam_uts_p', $pengaturan->id_jam_uts_p ?? '') === (string) $jam->id ? 'selected' : '' }}>
                                                        {{ $jam->nama_sesi }} ({{ \Carbon\Carbon::parse($jam->mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jam->selesai)->format('H:i') }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Tanggal UAS Praktik</label>
                                            <input type="date" name="tanggal_uas_p" class="form-control" value="{{ old('tanggal_uas_p', !empty($pengaturan?->tanggal_uas_p) ? \Carbon\Carbon::parse($pengaturan->tanggal_uas_p)->format('Y-m-d') : '') }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Jam UAS Praktik</label>
                                            <select name="id_jam_uas_p" class="form-control">
                                                <option value="">-- Pilih Jam --</option>
                                                @foreach($jamList as $jam)
                                                    <option value="{{ $jam->id }}" {{ (string) old('id_jam_uas_p', $pengaturan->id_jam_uas_p ?? '') === (string) $jam->id ? 'selected' : '' }}>
                                                        {{ $jam->nama_sesi }} ({{ \Carbon\Carbon::parse($jam->mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jam->selesai)->format('H:i') }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif
                                </div>

                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary" title="Simpan Pengaturan Ujian">
                                        <i class="fa fa-save"></i>
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
