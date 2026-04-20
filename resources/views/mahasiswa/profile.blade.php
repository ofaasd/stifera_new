@extends('layouts.default', ['CurrentPage' => $CurrentPage])
@section('local-css')
<style>
    .profile-card {
        height: auto !important;
    }

    .select2-container {
        width: 100% !important;
    }

    .select2-container .select2-selection--single {
        height: calc(1.5em + .75rem + 2px) !important;
        border: 1px solid #ced4da;
    }

    .select2-container .select2-selection__rendered {
        line-height: calc(1.5em + .75rem) !important;
        padding-left: .75rem;
    }

    .select2-container .select2-selection__arrow {
        height: calc(1.5em + .75rem + 2px) !important;
    }
</style>
@endsection
@section('content')
<div class="content-body">
    <div class="container-fluid">
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

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="card border-0 shadow-sm mb-4 profile-card">
                    <div class="card-body">
                        <h4 class="mb-1">Profile Mahasiswa</h4>
                        <p class="text-muted mb-2">Perbarui data umum Anda sesuai kebutuhan.</p>
                        <div class="small text-muted">
                            <div>
                                Domisili Wilayah:
                                <span class="fw-semibold">
                                    {{ $selectedWilayahLabel['kecamatan'] ?? '-' }},
                                    {{ $selectedWilayahLabel['kokab'] ?? '-' }},
                                    {{ $selectedWilayahLabel['provinsi'] ?? '-' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-body text-center">
                                @if(!empty($mahasiswa->foto_mhs) && file_exists(public_path('assets/foto_mahasiswa/' . $mahasiswa->foto_mhs)))
                                    <img
                                        src="{{ asset('assets/foto_mahasiswa/' . $mahasiswa->foto_mhs) }}"
                                        alt="Foto Profile"
                                        class="rounded-circle mb-3"
                                        style="width: 160px; height: 160px; object-fit: cover; border: 4px solid #f1f3f5;"
                                    >
                                @else
                                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                         style="width: 160px; height: 160px; background: #f5f5f5; border: 4px solid #f1f3f5; font-size: 48px; font-weight: 700; color: #999;">
                                        {{ strtoupper(substr((string) ($mahasiswa->nama ?? $mahasiswa->nim ?? 'M'), 0, 1)) }}
                                    </div>
                                @endif

                                <div class="small text-muted mb-3">NIM: {{ $mahasiswa->nim ?? '-' }}</div>

                                <form action="{{ route('mahasiswa.profile.upload-photo') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-2 text-start">
                                        <label for="foto" class="form-label">Ubah Foto Profile</label>
                                        <input type="file" id="foto" name="foto" class="form-control" accept=".jpg,.jpeg,.png" required>
                                        <small class="text-muted">Format: JPG/JPEG/PNG, maksimal 2MB.</small>
                                    </div>
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary">Upload Foto</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-body">
                                <form action="{{ route('mahasiswa.profile.update') }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label" for="nama">Nama Lengkap</label>
                                            <input type="text" class="form-control" id="nama" name="nama" value="{{ old('nama', $mahasiswa->nama) }}" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="email">Email</label>
                                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $mahasiswa->email) }}">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label" for="tempat_lahir">Tempat Lahir</label>
                                            <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir" value="{{ old('tempat_lahir', $mahasiswa->tempat_lahir) }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="tgl_lahir">Tanggal Lahir</label>
                                            <input type="date" class="form-control" id="tgl_lahir" name="tgl_lahir" value="{{ old('tgl_lahir', optional($mahasiswa->tgl_lahir)->format('Y-m-d')) }}">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label" for="jk">Jenis Kelamin</label>
                                            <select class="form-select" id="jk" name="jk">
                                                <option value="">Pilih Jenis Kelamin</option>
                                                <option value="1" {{ (string) old('jk', $mahasiswa->jk) === '1' ? 'selected' : '' }}>Laki-laki</option>
                                                <option value="2" {{ (string) old('jk', $mahasiswa->jk) === '2' ? 'selected' : '' }}>Perempuan</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="agama">Agama</label>
                                            <select class="form-select" id="agama" name="agama">
                                                <option value="">Pilih Agama</option>
                                                <option value="1" {{ (string) old('agama', $mahasiswa->agama) === '1' ? 'selected' : '' }}>Islam</option>
                                                <option value="2" {{ (string) old('agama', $mahasiswa->agama) === '2' ? 'selected' : '' }}>Kristen</option>
                                                <option value="3" {{ (string) old('agama', $mahasiswa->agama) === '3' ? 'selected' : '' }}>Katolik</option>
                                                <option value="4" {{ (string) old('agama', $mahasiswa->agama) === '4' ? 'selected' : '' }}>Hindu</option>
                                                <option value="5" {{ (string) old('agama', $mahasiswa->agama) === '5' ? 'selected' : '' }}>Buddha</option>
                                                <option value="6" {{ (string) old('agama', $mahasiswa->agama) === '6' ? 'selected' : '' }}>Konghucu</option>
                                            </select>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label" for="hp">No. HP Mahasiswa</label>
                                            <input type="text" class="form-control" id="hp" name="hp" value="{{ old('hp', $mahasiswa->hp) }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="telp">No. Telepon</label>
                                            <input type="text" class="form-control" id="telp" name="telp" value="{{ old('telp', $mahasiswa->telp) }}">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label" for="nama_ayah">Nama Ayah</label>
                                            <input type="text" class="form-control" id="nama_ayah" name="nama_ayah" value="{{ old('nama_ayah', $mahasiswa->nama_ayah) }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="nama_ibu">Nama Ibu</label>
                                            <input type="text" class="form-control" id="nama_ibu" name="nama_ibu" value="{{ old('nama_ibu', $mahasiswa->nama_ibu) }}">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label" for="hp_ortu">No. HP Orang Tua</label>
                                            <input type="text" class="form-control" id="hp_ortu" name="hp_ortu" value="{{ old('hp_ortu', $mahasiswa->hp_ortu) }}">
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label" for="alamat">Alamat Domisili Asal</label>
                                            <textarea class="form-control" id="alamat" name="alamat" rows="2">{{ old('alamat', $mahasiswa->alamat) }}</textarea>
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label" for="alamat_semarang">Alamat Domisili Sekarang</label>
                                            <textarea class="form-control" id="alamat_semarang" name="alamat_semarang" rows="2">{{ old('alamat_semarang', $mahasiswa->alamat_semarang) }}</textarea>
                                        </div>

                                        <div class="col-md-3">
                                            <label class="form-label" for="rt">RT</label>
                                            <input type="text" class="form-control" id="rt" name="rt" value="{{ old('rt', $mahasiswa->rt) }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label" for="rw">RW</label>
                                            <input type="text" class="form-control" id="rw" name="rw" value="{{ old('rw', $mahasiswa->rw) }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="kelurahan">Kelurahan</label>
                                            <input type="text" class="form-control" id="kelurahan" name="kelurahan" value="{{ old('kelurahan', $mahasiswa->kelurahan) }}">
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label" for="provinsi">Provinsi</label>
                                            <select class="form-select js-select2" id="provinsi" name="provinsi">
                                                <option value="">Pilih Provinsi</option>
                                                @foreach($provinsiList as $prov)
                                                    <option value="{{ $prov->id_wil }}" {{ (string) old('provinsi', $mahasiswa->provinsi) === (string) $prov->id_wil ? 'selected' : '' }}>
                                                        {{ $prov->nm_wil }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label" for="kokab">Kota/Kabupaten</label>
                                            <select class="form-select js-select2" id="kokab" name="kokab">
                                                <option value="">Pilih Kota/Kabupaten</option>
                                                @foreach($kotaList as $kota)
                                                    <option value="{{ $kota->id_wil }}" {{ (string) old('kokab', $mahasiswa->kokab) === (string) $kota->id_wil ? 'selected' : '' }}>
                                                        {{ $kota->nm_wil }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label" for="kecamatan">Kecamatan</label>
                                            <select class="form-select js-select2" id="kecamatan" name="kecamatan">
                                                <option value="">Pilih Kecamatan</option>
                                                @foreach($kecamatanList as $kec)
                                                    <option value="{{ $kec->id_wil }}" {{ (string) old('kecamatan', $mahasiswa->kecamatan) === (string) $kec->id_wil ? 'selected' : '' }}>
                                                        {{ $kec->nm_wil }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="mt-4 d-flex justify-content-end">
                                        <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                                    </div>
                                </form>
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
    $(document).ready(function () {
        $('.js-select2').select2({
            width: '100%'
        });

        function refillSelect($select, items, placeholder) {
            const currentValue = $select.val();
            $select.empty().append(new Option(placeholder, ''));

            items.forEach(function (item) {
                const option = new Option(item.nm_wil, item.id_wil, false, false);
                $select.append(option);
            });

            if (currentValue) {
                $select.val(currentValue).trigger('change.select2');
            } else {
                $select.trigger('change.select2');
            }
        }

        function loadChildren(parentId, $target, placeholder) {
            if (!parentId) {
                refillSelect($target, [], placeholder);
                return $.Deferred().resolve().promise();
            }

            return $.get('{{ route('mahasiswa.wilayah.children') }}', { parent_id: parentId })
                .done(function (res) {
                    refillSelect($target, Array.isArray(res) ? res : [], placeholder);
                })
                .fail(function () {
                    refillSelect($target, [], placeholder);
                });
        }

        $('#provinsi').on('change', function () {
            const provinsiId = $(this).val();
            $('#kokab').val('').trigger('change.select2');
            $('#kecamatan').val('').trigger('change.select2');

            loadChildren(provinsiId, $('#kokab'), 'Pilih Kota/Kabupaten').then(function () {
                refillSelect($('#kecamatan'), [], 'Pilih Kecamatan');
            });
        });

        $('#kokab').on('change', function () {
            const kotaId = $(this).val();
            $('#kecamatan').val('').trigger('change.select2');
            loadChildren(kotaId, $('#kecamatan'), 'Pilih Kecamatan');
        });
    });
</script>
@endsection
