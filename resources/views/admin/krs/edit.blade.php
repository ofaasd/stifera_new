@extends('layouts.default', ['CurrentPage' => $CurrentPage])

@section('content')
<div class="content-body">
    <div class="container">
        <div class="row">
            <div class="col-xl-12">

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="filter cm-content-box box-primary">
                    <div class="content-title SlideToolHeader">
                        <div class="cpa">
                            <i class="fa-solid fa-pen-to-square me-1"></i>Edit KRS Mahasiswa
                        </div>
                        <div class="tools">
                            <a href="javascript:void(0);" class="expand handle"><i class="fal fa-angle-down"></i></a>
                        </div>
                    </div>

                    <div class="cm-content-body form excerpt">
                        <div class="card-body pb-4">

                            <div class="mb-3">
                                <a href="{{ url('master/krs/detail/' . $krs->id_tahun . '/' . $krs->nim) }}" class="btn btn-secondary">
                                    <i class="fa fa-arrow-left"></i> Kembali ke Detail KRS
                                </a>
                            </div>

                            {{-- Info Mahasiswa --}}
                            <div class="table-responsive mb-4">
                                <table class="table table-borderless w-auto" style="min-width: 480px;">
                                    <tbody>
                                        <tr>
                                            <td class="fw-bold" style="width:160px;">NIM</td>
                                            <td class="px-2">:</td>
                                            <td>{{ $mahasiswa->nim ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Nama Mahasiswa</td>
                                            <td class="px-2">:</td>
                                            <td>{{ $mahasiswa->nama ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Tahun Ajaran</td>
                                            <td class="px-2">:</td>
                                            <td>
                                                {{ ($tahun->awal ?? '') . ' - ' . ($tahun->akhir ?? '') }}
                                                @if(!empty($jenisTA)) ({{ $jenisTA }}) @endif
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            {{-- KRS saat ini --}}
                            <div class="card mb-4">
                                <div class="card-header fw-bold bg-light">KRS yang Akan Diubah</div>
                                <div class="card-body">
                                    <table class="table table-borderless w-auto mb-0">
                                        <tbody>
                                            <tr>
                                                <td class="fw-bold" style="width:140px;">Mata Kuliah</td>
                                                <td class="px-2">:</td>
                                                <td>{{ $krs->mata_kuliah ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">SKS</td>
                                                <td class="px-2">:</td>
                                                <td>{{ $krs->sks ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">Hari</td>
                                                <td class="px-2">:</td>
                                                <td>{{ $krs->hari ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">Sesi</td>
                                                <td class="px-2">:</td>
                                                <td>{{ $krs->sesi ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">Ruang</td>
                                                <td class="px-2">:</td>
                                                <td>{{ $krs->ruang ?? '-' }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- Form edit --}}
                            <form action="{{ url('master/krs/edit-krs/' . $krs->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label for="id_jadwal" class="form-label fw-bold">
                                        Pilih Jadwal Baru <span class="text-danger">*</span>
                                    </label>
                                    <select name="id_jadwal" id="id_jadwal" class="form-select @error('id_jadwal') is-invalid @enderror" required>
                                        <option value="">-- Pilih Jadwal --</option>
                                        @foreach($jadwalList as $jadwal)
                                            <option value="{{ $jadwal->id }}"
                                                {{ old('id_jadwal') == $jadwal->id ? 'selected' : '' }}>
                                                {{ $jadwal->nama_mata_kuliah ?? $jadwal->kode_mata_kuliah }}
                                                ({{ $jadwal->jumlah_sks ?? '-' }} SKS)
                                                — {{ $jadwal->hari ?? '-' }}, Sesi {{ $jadwal->sesi ?? '-' }},
                                                Ruang {{ $jadwal->ruang ?? '-' }},
                                                Rombel {{ $jadwal->rombel ?? '-' }}
                                                — {{ trim($jadwal->nama_dosen ?? '-') }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_jadwal')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @if($jadwalList->isEmpty())
                                        <div class="form-text text-warning">
                                            <i class="fa fa-exclamation-triangle me-1"></i>
                                            Tidak ada jadwal tersedia untuk tahun ajaran ini yang belum diambil mahasiswa.
                                        </div>
                                    @endif
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary" {{ $jadwalList->isEmpty() ? 'disabled' : '' }}>
                                        <i class="fa fa-save me-1"></i> Simpan Perubahan
                                    </button>
                                    <a href="{{ url('master/krs/detail/' . $krs->id_tahun . '/' . $krs->nim) }}" class="btn btn-secondary">
                                        Batal
                                    </a>
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
