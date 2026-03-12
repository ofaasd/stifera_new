@extends('layouts.default', ['CurrentPage' => $CurrentPage])

@section('content')
		<div class="content-body">
			<div class="container">

				<!-- Row -->
				<div class="row">
					<div class="col-xl-12">
						
						<div class="mb-4 pb-3">
                            <a href="{{ url('pmb')}}" class="btn btn-success"><i class="fa fa-arrow-left"></i> Kembali</a>
                        </div>
						<div class="filter cm-content-box box-primary">
							<div class="content-title SlideToolHeader">
								<div class="cpa">
									<i class="fa-solid fa-file-lines me-1"></i>{{ $title }}
								</div>
								<div class="tools">
									<a href="javascript:void(0);" class="expand handle"><i
											class="fal fa-angle-down"></i></a>
								</div>
							</div>
							<div class="cm-content-body form excerpt">
								<div class="card-body pb-4">
                                    @if(session('success'))
                                        <div class="alert alert-success">{{ session('success') }}</div>
                                    @endif
									<form action="{{ route('pmb.update', $d->id) }}" method="post" enctype="multipart/form-data">
                                        @csrf 
                                        @method('PUT')
                                        <div class="form-group row">
                                            
                                            <div class="col-sm-6">
                                                <div class="col-sm-4">
                                                    <label>No. Pendaftaran : </label>
                                                </div>
                                                <div class="col-sm-8">
                                                    <input type="hidden" name="id" value="{{ $d->id }}">
                                                    <input type="number" class="form-control" readonly name="nopen" value="{{ $d->nopen }}" maxlength="16">
                                                </div>
                                                
                                                <div class="col-sm-4">
                                                    <label>Nomor KTP : </label>
                                                </div>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" name="noktp" value="{{ $d->noktp }}" maxlength="16">
                                                </div>
                                                
                                                <label class="col-sm-4 col-form-label">NISN : </label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" name="nisn" value="{{ $d->nisn }}" maxlength="15">
                                                    <p><small>Tidak Tahu NISN anda? cek <a href="https://nisn.data.kemdikbud.go.id/page/data" target="_blank">DISINI</a></small></p>
                                                </div>
                                                
                                                <label class="col-sm-4 col-form-label">Nama Lengkap : </label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" name="nama" value="{{ $d->nama }}">
                                                </div>
                                                
                                                <label class="col-sm-4 col-form-label">Jenis Kelamin : </label>
                                                <div class="col-sm-8">
                                                    <select name="jk" class="form-control">
                                                        <option selected value="{{ $d->jk }}">
                                                            {{ $d->jk == 1 ? 'Laki - Laki' : 'Perempuan' }}
                                                        </option>
                                                        <option value="1">Laki - Laki</option>
                                                        <option value="2">Perempuan</option>
                                                    </select>
                                                </div>
                                                
                                                <label class="col-sm-4 col-form-label">Agama : </label>
                                                <div class="col-sm-8">
                                                    <select name="agama" class="form-control">
                                                        <option value="{{ $d->agama }}" selected>
                                                            @if($d->agama == 1) Islam
                                                            @elseif($d->agama == 2) Kristen
                                                            @elseif($d->agama == 3) Katolik
                                                            @elseif($d->agama == 4) Hindu
                                                            @elseif($d->agama == 5) Budha
                                                            @elseif($d->agama == 6) Konghucu
                                                            @else Lainnya
                                                            @endif
                                                        </option>
                                                        <option value="1">Islam</option>
                                                        <option value="2">Kristen</option>
                                                        <option value="3">Katolik</option>
                                                        <option value="4">Hindu</option>
                                                        <option value="5">Budha</option>
                                                        <option value="6">Konghucu</option>
                                                        <option value="99">Lainnya</option>
                                                    </select>
                                                </div>
                                                
                                                <label class="col-sm-4 col-form-label">Nama Ibu : </label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" name="nama_ibu" value="{{ $d->nama_ibu }}">
                                                </div>
                                                
                                                <label class="col-sm-4 col-form-label">Nama Ayah : </label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" name="nama_ayah" value="{{ $d->nama_ayah }}">
                                                </div>
                                                
                                                <label class="col-sm-4 col-form-label">Tinggi Badan : </label>
                                                <div class="col-sm-8">
                                                    <input type="number" class="form-control" name="tinggi_badan" value="{{ $d->tinggi_badan }}">
                                                </div>
                                                
                                                <label class="col-sm-4 col-form-label">Berat Badan : </label>
                                                <div class="col-sm-8">
                                                    <input type="number" class="form-control" name="berat_badan" value="{{ $d->berat_badan }}">
                                                </div>
                                                
                                                <label class="col-sm-4 col-form-label">Tempat Lahir : </label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" name="tempat_lahir" value="{{ $d->tempat_lahir }}">
                                                </div>
                                                
                                                <label class="col-sm-4 col-form-label">Tanggal Lahir : </label>
                                                <div class="col-sm-8">
                                                    <input type="date" class="form-control" name="tanggal_lahir" value="{{ $d->tanggal_lahir }}">
                                                </div>
                                                
                                                <label class="col-sm-3 col-form-label">No. HP : </label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" name="hp" value="{{ $d->hp }}">
                                                </div>
                                                
                                                <label class="col-sm-4 col-form-label">No. Telpon :</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" name="telpon" value="{{ $d->telpon }}">
                                                </div>
                                                
                                                <div class="col-sm-4">
                                                    <label>Warga Negara : </label>
                                                </div>
                                                <div class="col-sm-8">
                                                    <select name="warga_negara" id="wn" class="form-control">
                                                        <option selected value="{{ $d->warga_negara }}">{{ $data['nama_negara'] ?? '' }}</option>
                                                        @foreach($warga_negara as $w)
                                                            <option value="{{ $w->id_negara }}" {{($w->id_negara == $d->warga_negara ? 'selected' : '')}}>{{ $w->nm_negara }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                
                                                <label class="col-sm-4 col-form-label">Nama Provinsi : </label>
                                                <div class="col-sm-8">
                                                    <select name="provinsi" id="provinsi" class="form-control">
                                                        <option selected value="{{ $d->provinsi }}">{{ $data['nm_prop'] ?? '' }}</option>
                                                        @foreach($wilayah as $w)
                                                            <option value="{{ $w->id_wil }}" {{($w->id_wil == $d->provinsi ? 'selected' : '')}}>{{ $w->nm_wil }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <div class="col-sm-6">
                                                <label class="col-sm-4 col-form-label">Kota / Kabupaten : </label>
                                                <div class="col-sm-8">
                                                    <select name="kotakab" id="kotakab" class="form-control"> 
                                                        
                                                         @if(!empty($list_kota))
                                                            @foreach($list_kota as $k)
                                                                <option value="{{ $k->id_wil }}" {{($k->id_wil == $d->kotakab ? 'selected' : '')}}>{{ $k->nm_wil }}</option>
                                                            @endforeach
                                                        @else
                                                            <option selected="" disabled="">Pilih Kota/Kabupaten</option>
                                                        @endif
                                                    </select>
                                                </div>
                                                
                                                <label class="col-sm-3 col-form-label">Kecamatan : </label>
                                                <div class="col-sm-8">
                                                    <select name="kecamatan" id="kecamatan" class="form-control">
                                                        @if(!empty($list_kecamatan))
                                                            @foreach($list_kecamatan as $k)
                                                                <option value="{{ $k->id_wil }}" {{($k->id_wil == $d->kecamatan ? 'selected' : '')}}>{{ $k->nm_wil }}</option>
                                                            @endforeach
                                                        @else
                                                            <option selected="" disabled="">Pilih Kecamatan</option>
                                                        @endif
                                                    </select>
                                                </div>
                                                
                                                <label class="col-sm-3 col-form-label">Kode POS : </label>
                                                <div class="col-sm-8">
                                                    <input type="number" class="form-control" name="kode_pos" value="{{ $d->kodepos }}">
                                                </div>
                                                
                                                <label class="col-sm-3 col-form-label">Kelurahan : </label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" name="kelurahan" value="{{ $d->kelurahan }}">
                                                </div>
                                                
                                                <label class="col-sm-4 col-form-label">Alamat : </label>
                                                <div class="col-sm-8">
                                                    <textarea class="form-control" name="alamat" style="resize: none;">{{ $d->alamat }}</textarea>
                                                </div>
                                                
                                                <label class="col-sm-4 col-form-label">RT / RW : </label>
                                                <div class="col-sm-8">
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <input type="text" class="form-control" name="rt" placeholder="RT" value="{{ $d->rt }}">
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <input type="text" class="form-control" name="rw" placeholder="RW" value="{{ $d->rw }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <label class="col-sm-3 col-form-label">Sekolah Asal :</label>
                                                <div class="col-sm-8">
                                                    <select name="asal_sekolah" id="asal_sekolah" class="form-control js-example-basic-single">
                                                        <option selected value="{{ $d->asal_sekolah }}">{{ $nm_sekolah ?? '' }}</option>
                                                    </select>
                                                </div>
                                                
                                                <label class="col-sm-4 col-form-label">Pilih Jenis Kelas :</label>
                                                <div class="col-sm-8">
                                                    <select name="kelas" class="form-control" readonly>
                                                        <option value="{{ $d->kelas }}" selected>
                                                            {{ $d->kelas == 1 ? 'Reguler' : ($d->kelas == 2 ? 'Karyawan' : '') }}
                                                        </option>
                                                    </select>
                                                </div>
                                                
                                                <label class="col-sm-4 col-form-label">Kategori :</label>
                                                <div class="col-sm-8">
                                                    <select name="jalur" class="form-control" readonly>
                                                        <option value="{{ $d->jalur_pendaftaran }}" selected>
                                                            @if($d->jalur_pendaftaran == 1) PMDP
                                                            @elseif($d->jalur_pendaftaran == 2) KERJASAMA
                                                            @elseif($d->jalur_pendaftaran == 3) UMUM
                                                            @endif
                                                        </option>
                                                    </select>
                                                </div>
                                                
                                                <div {{ $d->jalur_pendaftaran != 2 ? 'hidden' : '' }}>
                                                    <label class="col-sm-4 col-form-label">Jenis Kerjasama :</label>
                                                    <div class="col-sm-8">
                                                        <select name="kerjasama" class="form-control" readonly>
                                                            <option value="{{ $d->is_kerjasama }}" selected>
                                                                @if($d->is_kerjasama == 1) MOU AKADEMI FARMASI NUSAPUTERA
                                                                @elseif($d->is_kerjasama == 2) ALUMNI SMK NUSAPUTERA
                                                                @elseif($d->is_kerjasama == 3) PAFI
                                                                @elseif($d->is_kerjasama == 4) KIMIA FARMA
                                                                @endif
                                                            </option>
                                                        </select>
                                                    </div>
                                                    
                                                    <div {{ $d->is_kerjasama != 1 ? 'hidden' : '' }}>
                                                        <label class="col-sm-4 col-form-label">Mitra MOU :</label>
                                                        <div class="col-sm-8">
                                                            <select name="mou" class="form-control" readonly>
                                                                <option value="{{ $d->is_mou }}" selected>{{ $data['mou'] ?? '' }}</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <label class="col-sm-6 col-form-label">Pilih Jenis Pendaftaran :</label>
                                                <div class="col-sm-8">
                                                    <select name="pendaftaran" class="form-control" readonly>
                                                        <option value="{{ $d->jenis_pendaftaran }}" selected>
                                                            @if($d->jenis_pendaftaran == 1) Peserta Didik Baru
                                                            @elseif($d->jenis_pendaftaran == 2) Pindahan
                                                            @elseif($d->jenis_pendaftaran == 11) Alih Jenjang
                                                            @elseif($d->jenis_pendaftaran == 12) Lintas Jalur
                                                            @endif
                                                        </option>
                                                    </select>
                                                </div>
                                                
                                                <label class="col-sm-8 col-form-label">Gelombang Pendaftaran : </label>
                                                <div class="col-sm-8">
                                                    <select name="gelombang" id="gelombang" class="form-control" readonly>
                                                        <option value="{{ $d->gelombang }}" selected>{{ $data['gelombang'] ?? '' }}</option>
                                                    </select>
                                                </div>
                                                
                                                <div {{ $d->jalur_pendaftaran != 1 ? 'hidden' : '' }}>
                                                    <label class="col-sm-4 col-form-label">Total Nilai PMDP : </label>
                                                    <div class="col-sm-8">
                                                        <input type="number" class="form-control" name="pmdp" value="{{ $d->peringkat_pmdp }}">
                                                    </div>
                                                </div>
                                                
                                                <label class="col-sm-6 col-form-label">Pilih Program Studi 1 : </label>
                                                <div class="col-sm-8">
                                                    <select name="pilihan1" class="form-control">
                                                        @foreach($prodi as $p)
                                                            <option value="{{ $p->id }}" {{ $p->id == $d->pilihan1 ? 'selected' : '' }}>
                                                                {{ $p->nama_jurusan }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                
                                                <label class="col-sm-6 col-form-label">Pilih Program Studi 2 : </label>
                                                <div class="col-sm-8">
                                                    <select name="pilihan2" class="form-control">
                                                        @foreach($prodi as $p)
                                                            <option value="{{ $p->id }}" {{ $p->id == $d->pilihan2 ? 'selected' : '' }}>
                                                                {{ $p->nama_jurusan }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                
                                                <label class="col-sm-6 col-form-label">Dapat info PMB darimana?</label>
                                                <div class="col-sm-8">
                                                    <select name="info_pmb" class="form-control">
                                                        <option value="{{ $d->info_pmb }}" selected>
                                                            @if($d->info_pmb == 1) Teman
                                                            @elseif($d->info_pmb == 2) Kerabat / Orang Tua
                                                            @elseif($d->info_pmb == 3) Sosial Media
                                                            @elseif($d->info_pmb == 4) Lainnya
                                                            @endif
                                                        </option>
                                                        <option value="1">Teman</option>
                                                        <option value="2">Kerabat / Orang Tua</option>
                                                        <option value="3">Sosial Media</option>
                                                        <option value="4">Lainnya</option>
                                                    </select>
                                                </div>
                                                
                                                <label class="col-sm-4 col-form-label">Ukuran Seragam : </label>
                                                <div class="col-sm-8">
                                                    <select name="ukuran_seragam" class="form-control" required>
                                                        <option value="S" {{ $d->ukuran_seragam == 'S' ? 'selected' : '' }}>S</option>
                                                        <option value="M" {{ $d->ukuran_seragam == 'M' ? 'selected' : '' }}>M</option>
                                                        <option value="L" {{ $d->ukuran_seragam == 'L' ? 'selected' : '' }}>L</option>
                                                        <option value="XL" {{ $d->ukuran_seragam == 'XL' ? 'selected' : '' }}>XL</option>
                                                    </select>
                                                </div>
                                                
                                                <label class="col-sm-6 col-form-label">Upload Foto : </label>
                                                <div class="col-sm-8">
                                                    <img src="{{ asset('assets/foto_pmb_peserta/' . $d->foto_peserta) }}" style="width: 100px; height: 150px; object-fit: cover; border-radius: 5px;">
                                                    <br><br>
                                                    <input type="file" class="form-control" name="foto">
                                                    <small class="text-muted">Maksimal 500kb dengan background merah.</small>
                                                </div>
                                                
                                                <label class="col-sm-9 col-form-label"></label>
                                                <div class="col-sm-3 mt-4">
                                                    <input type="submit" class="btn btn-success btn-round w-100" value="Update Data">
                                                </div>
                                            </div>
                                            
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