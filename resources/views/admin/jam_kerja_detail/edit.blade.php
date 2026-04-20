@extends('layouts.default', ['CurrentPage' => $CurrentPage])

@section('content')
    <div class="content-body">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <div class="mb-4 pb-3">
                        <a href="{{ url('simpeg/absensi/jam_kerja_master/' . $master->id . '/detail') }}" class="btn btn-primary">
                            <i class="fa-solid fa-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                    <div class="filter cm-content-box box-primary">
                        <div class="content-title SlideToolHeader">
                            <div class="cpa">
                                <i class="fa-solid fa-file-lines me-1"></i>{{ $title }} - {{ $master->judul }}
                            </div>
                            <div class="tools">
                                <a href="javascript:void(0);" class="expand handle">
                                    <i class="fal fa-angle-down"></i>
                                </a>
                            </div>
                        </div>
                        <div class="cm-content-body form excerpt">
                            @if($errors->any())
                                <div class="alert alert-danger mx-3 mt-3">
                                    <ul class="mb-0">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="card-body pb-4">
                                <form action="{{ url('simpeg/absensi/jam_kerja_master/' . $master->id . '/detail/' . $d->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Pegawai <span class="text-danger">*</span></label>
                                            <select class="form-control" name="id_pegawai" required>
                                                <option value="">Pilih Pegawai</option>
                                                @foreach($pegawaiList as $pegawai)
                                                    <option value="{{ $pegawai->id }}" {{ (string) old('id_pegawai', $d->id_pegawai) === (string) $pegawai->id ? 'selected' : '' }}>
                                                        {{ $pegawai->npp ?? '-' }} - {{ $pegawai->nama ?? '-' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2 mb-3">
                                            <label class="form-label">Status <span class="text-danger">*</span></label>
                                            <select class="form-control" name="status" required>
                                                <option value="1" {{ old('status', (string) $d->status) === '1' ? 'selected' : '' }}>Aktif</option>
                                                <option value="0" {{ old('status', (string) $d->status) === '0' ? 'selected' : '' }}>Tidak Aktif</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="table-responsive mt-3">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Hari</th>
                                                    <th>Jam Mulai</th>
                                                    <th>Jam Selesai</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach(['senin' => 'Senin', 'selasa' => 'Selasa', 'rabu' => 'Rabu', 'kamis' => 'Kamis', 'jumat' => 'Jumat', 'sabtu' => 'Sabtu', 'minggu' => 'Minggu'] as $key => $label)
                                                    <tr>
                                                        <td>{{ $label }} {!! $key === 'minggu' ? '<span class="text-danger">*</span>' : '' !!}</td>
                                                        <td>
                                                            <input type="time" class="form-control" name="jam_{{ $key }}_mulai" value="{{ old('jam_' . $key . '_mulai', data_get($d, 'jam_' . $key . '_mulai') ? \Carbon\Carbon::parse(data_get($d, 'jam_' . $key . '_mulai'))->format('H:i') : '') }}" {{ $key === 'minggu' ? 'required' : '' }}>
                                                        </td>
                                                        <td>
                                                            <input type="time" class="form-control" name="jam_{{ $key }}_selesai" value="{{ old('jam_' . $key . '_selesai', data_get($d, 'jam_' . $key . '_selesai') ? \Carbon\Carbon::parse(data_get($d, 'jam_' . $key . '_selesai'))->format('H:i') : '') }}" {{ $key === 'minggu' ? 'required' : '' }}>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="mt-3">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa-solid fa-save me-1"></i> Update
                                        </button>
                                        <a href="{{ url('simpeg/absensi/jam_kerja_master/' . $master->id . '/detail') }}" class="btn btn-light ms-2">Batal</a>
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
