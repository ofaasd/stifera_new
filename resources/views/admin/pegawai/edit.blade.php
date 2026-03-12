@extends('layouts.default', ['CurrentPage' => $CurrentPage])
@section('local-css')
	<link href="https://cdn.jsdelivr.net/npm/jquery.steps.full@1.1.0/jquery.steps.min.css" rel="stylesheet">
    <style>
        .wizard > .content {
            overflow-y: scroll;
        }
    </style>
@endsection
@section('content')
		<div class="content-body">
			<div class="container">

				<div class="row">
					<div class="col-xl-12">
                        <div class="mb-4 pb-3">
							<a href="{{ url('pegawai')}}" class="btn btn-primary"><i class="fa-solid fa-arrow-left me-1"></i> Kembali</a>
						</div>
						<div class="filter cm-content-box box-primary">
							<div class="content-title SlideToolHeader">
								<div class="cpa">
									<i class="fa-solid fa-file-lines me-1"></i>Tambah {{ $title }}
								</div>
								<div class="tools">
									<a href="javascript:void(0);" class="expand handle"><i
											class="fal fa-angle-down"></i></a>
								</div>
							</div>
							<div class="cm-content-body form excerpt">
								<div class="card-body pb-4">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <form action="{{ route('pegawai.update', $query->id_pegawai) }}" method="post" enctype="multipart/form-data">
                                                @csrf 
                                                @method('PUT')
                                                <div class="card">
                                                    
                                                    <div class="card-body">
                                                    
                                                    <h4 class="sub-title">Biodata</h4>
                                                    <div class="form-group row">
                                                        <input type="hidden" name="id_biodata" value="{{ $query->id_biodata }}">
                                                        <div class="col-sm-2">
                                                            <img src="{{ asset('assets/foto_pegawai/' . (!empty($query->foto) ? $query->foto : 'logo.png')) }}" width="100%"><br /><br />
                                                            
                                                            @if(isset($simpeg) && $simpeg == true)
                                                                <a href="#" class="btn btn-primary waves-effect" data-toggle="modal" data-target="#ubah_photo"><i class="feather icon-image"></i> Ubah Photo</a><br /><br />
                                                                <a href="#" class="btn btn-primary waves-effect" data-toggle="modal" data-target="#ubah_password"><i class="fa fa-key"></i> Ubah Password</a><br /><br />
                                                            @endif
                                                            
                                                            <a href="{{ url('simpeg/profil/cv/' . $query->id_pegawai) }}" class="btn btn-primary col-md-12"><i class="feather icon-download"></i> CV</a><br /><br />
                                                            <a href="{{ url('simpeg/profil/cv_excel/' . $query->id_pegawai) }}" class="btn btn-primary col-md-12"><i class="feather icon-download"></i> CV Excel</a>
                                                            
                                                        </div>      
                                                        <div class="col-sm-5">
                                                            <input type="hidden" name="pegawai_id" value="{{ $query->pegawai_id }}">
                                                            <label class="col-sm-12 col-form-label">Nomor Induk pegawai(NPP): </label>
                                                            <div class="col-sm-10">
                                                                <input type="text" class="form-control" name="nip" id="nip" value="{{ $query->npp }}">
                                                            </div>
                                                            
                                                            <label class="col-sm-12 col-form-label">Homebase : </label>
                                                            <div class="col-sm-10">
                                                                <select class="form-control" name="homebase">
                                                                    @foreach($homebase as $row)
                                                                        <option value="{{ $row->id }}" {{ $row->id == $query->homebase ? 'selected' : '' }}>
                                                                            {{ $row->nama_jurusan }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <label class="col-sm-12 col-form-label">Nama Lengkap : </label>
                                                            <div class="col-sm-10">
                                                                <input type="text" class="form-control" name="nama_lengkap" value="{{ $query->nama_lengkap }}" >
                                                            </div>
                                                            <label class="col-sm-10 col-form-label">Alamat Tempat Tinggal : </label>
                                                            <div class="col-sm-10">
                                                                <textarea class="form-control" name="alamat">{{ $query->alamat }}</textarea>
                                                            </div>
                                                            
                                                            <label class="col-sm-10 col-form-label">Nama Provinsi :</label>
                                                            <div class="col-sm-10">
                                                                <p>
                                                                    <select name="provinsi" id="provinsi" class="form-control" required="">
                                                                        <option selected="" disabled="">Pilih Provinsi</option>
                                                                        @foreach($wilayah as $w)
                                                                            <option value="{{ $w->id_wil }}" {{($w->id_wil == $query->provinsi ? 'selected' : '')}}>{{ $w->nm_wil }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </p>
                                                            </div>
                                                            
                                                            <label class="col-sm-10 col-form-label">Nama Kota/Kabupaten :</label>
                                                            <div class="col-sm-10">
                                                                <p>
                                                                    <select name="kotakab" id="kotakab" class="form-control" required=""> 
                                                                        @if(!empty($list_kota))
                                                                            @foreach($list_kota as $k)
                                                                                <option value="{{ $k->id_wil }}" {{($k->id_wil == $query->kotakab ? 'selected' : '')}}>{{ $k->nm_wil }}</option>
                                                                            @endforeach
                                                                        @else
                                                                            <option selected="" disabled="">Pilih Kota/Kabupaten</option>
                                                                        @endif
                                                                    </select>
                                                                </p>
                                                            </div>
                                                            
                                                            <label class="col-sm-10 col-form-label">Nama Kecamatan :</label>
                                                            <div class="col-sm-10">
                                                                <p>
                                                                    <select name="kecamatan" id="kecamatan" class="form-control" required="">
                                                                        @if(!empty($list_kecamatan))
                                                                            @foreach($list_kecamatan as $k)
                                                                                <option value="{{ $k->id_wil }}" {{($k->id_wil == $query->kecamatan ? 'selected' : '')}}>{{ $k->nm_wil }}</option>
                                                                            @endforeach
                                                                        @else
                                                                            <option selected="" disabled="">Pilih Kecamatan</option>
                                                                        @endif
                                                                    </select>
                                                                </p>
                                                            </div>
                                                            
                                                            <label class="col-sm-10 col-form-label">Nama Kelurahan :</label>
                                                            <div class="col-sm-10">
                                                                <p><input type="text" class="form-control" placeholder="Nama Kelurahan" id="kelurahan" name="kelurahan" value="{{ $query->kelurahan ?? '' }}" required=""></p>
                                                            </div>
                                                            
                                                            <label class="col-sm-12 col-form-label">Jenis Kelamin : </label>
                                                            <div class="col-sm-10">
                                                                <select class="form-control" name="jenis_kelamin">
                                                                    @foreach($jenis_kelamin as $key => $row)
                                                                        <option value="{{ $key }}" {{ $query->jenis_kelamin == $key ? 'selected' : '' }}>
                                                                            {{ $row }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            
                                                            <label class="col-sm-12 col-form-label">NIDN : </label>
                                                            <div class="col-sm-10">
                                                                <input type="text" class="form-control" name="nidn" value="{{ $query->nidn }}" >
                                                            </div>
                                                            
                                                            <label class="col-sm-12 col-form-label">Status Perkawinan : </label>
                                                            <div class="col-sm-10">
                                                                <select class="form-control" name="status_nikah">
                                                                    @foreach($status_kawin as $row)
                                                                        <option value="{{ $row }}" {{ $query->status_nikah == $row ? 'selected' : '' }}>
                                                                            {{ $row }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            
                                                            <label class="col-sm-10 col-form-label">Status Kepegawaian : </label>
                                                            <div class="col-sm-10">
                                                                <select class="form-control" name="status">
                                                                    @foreach($status as $row)
                                                                        <option value="{{ $row }}" {{ $query->status_pegawai == $row ? 'selected' : '' }}>
                                                                            {{ $row }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            
                                                            <label class="col-sm-12 col-form-label">Tempat, Tgl Lahir : </label>
                                                            <div class="col-sm-10">
                                                                <div class="row">
                                                                    <div class="col-sm-5">
                                                                        <input type="text" class="form-control" name="tempat_lahir" value="{{ $query->tempat_lahir }}" placeholder="Tempat Lahir">
                                                                    </div>
                                                                    <div class="col-sm-7">
                                                                        <input type="text" id="datepicker"  class="form-control" name="tanggal_lahir" placeholder="Tanggal Lahir" value="{{ date('m/d/Y', strtotime($query->tanggal_lahir)) }}">
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <label class="col-sm-12 col-form-label">Gelar Depan : </label>
                                                            <div class="col-sm-5">
                                                                <input type="text" class="form-control" name="gelar_depan" value="{{ $query->gelar_depan }}" placeholder="Ex: Dr., Ir.,">
                                                            </div>
                                                            
                                                            <label class="col-sm-12 col-form-label">Gelar Belakang : </label>
                                                            <div class="col-sm-5">
                                                                <input type="text" class="form-control" name="gelar_belakang" value="{{ $query->gelar_belakang }}" placeholder="Ex: S.ked, S.Farm, M.Farm">
                                                            </div>
                                                            
                                                            <label class="col-sm-12 col-form-label">Agama : </label>
                                                            <div class="col-sm-10">
                                                                <select class="form-control" name="agama">
                                                                    @foreach($agama as $row)
                                                                        <option value="{{ $row }}" {{ $row == $query->agama ? 'selected' : '' }}>
                                                                            {{ $row }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div> 
                                                            
                                                            <label class="col-sm-12 col-form-label">Email : </label>
                                                            <div class="col-sm-10">
                                                                <input type="email" class="form-control" name="email1" value="{{ $query->email1 }}" >
                                                            </div>
                                                            
                                                            <label class="col-sm-12 col-form-label">No. Telp : </label>
                                                            <div class="col-sm-10">
                                                                <input type="text" class="form-control" name="notelp" value="{{ $query->notelp }}" >
                                                            </div>
                                                            
                                                            <label class="col-sm-12 col-form-label">No. HP : </label>
                                                            <div class="col-sm-10">
                                                                <input type="text" class="form-control" name="nohp" value="{{ $query->nohp }}" >
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="col-sm-5">
                                                            <label class="col-sm-12 col-form-label"><p style="margin:0"><b>Pendidikan S1</b></p></label>
                                                            <label class="col-sm-12 col-form-label">Universitas : </label>
                                                            <input type="hidden" name="jenjang[]" value="S1">
                                                            <div class="col-sm-10">
                                                                <select name="universitasid[]" id="univ1" class="js-example-basic-single col-sm-12" placeholder="--Nama Universitas--">
                                                                    @foreach($universitas as $value)
                                                                        <option value="{{ $value->id }}" {{ (!empty($s1->universitas) && $s1->universitas == $value->id) ? 'selected' : '' }}>
                                                                            {{ $value->nama_universitas }}
                                                                        </option>
                                                                    @endforeach
                                                                    <option value='999999'>Lainnya</option>
                                                                </select>
                                                                <input type="hidden" name="status_riwayat[]" value="{{ !empty($s1->id) ? $s1->id : '0' }}">
                                                                <input type="hidden" name="id_pegawai" value="{{ $query->id_pegawai }}">
                                                                <div class="univ1-text">
                                                                    <input type='hidden' class='form-control mt-3' placeholder='Nama Universitas' name='universitas[]'>
                                                                </div>
                                                            </div>
                                                            
                                                            <label class="col-sm-4 col-form-label">Jurusan : </label>
                                                            <div class="col-sm-10">
                                                                <select name="jurusanid[]" id="jurusan1" class="js-example-basic-single col-sm-12" placeholder="--Nama Jurusan--">
                                                                    @foreach($master_prodi as $value)
                                                                        <option value="{{ $value->id }}" {{ (!empty($s1->jurusan) && $s1->jurusan == $value->id) ? 'selected' : '' }}>
                                                                            {{ $value->nama_jurusan }}
                                                                        </option>
                                                                    @endforeach
                                                                    <option value='999999'>Lainnya</option>
                                                                </select>
                                                                <div class="jurusan1-text">
                                                                    <input type='hidden' class='form-control mt-3' placeholder='Nama Jurusan' name='jurusan[]'>
                                                                </div>
                                                            </div>

                                                            <br />
                                                            <div class="form-check col-sm-12">
                                                            <input class="form-check-input" type="checkbox" value="" id="pendidikans2" style="margin-left:0; margin-top:.40rem">
                                                            <label class="form-check-label" for="pendidikans2">
                                                                <p style="margin:0"><b>Pendidikan S2</b></p>
                                                            </label>
                                                            </div>
                                                            
                                                            <label class="col-sm-12 col-form-label">Universitas : </label>
                                                            <input type="hidden" name="jenjang[]" value="S2">
                                                            <div class="col-sm-10">
                                                                <select name="universitasid[]" class="js-example-basic-single col-sm-12" id="univ2" disabled  placeholder="--Nama Universitas--">
                                                                    @foreach($universitas as $value)
                                                                        <option value="{{ $value->id }}" {{ (!empty($s2->universitas) && $s2->universitas == $value->id) ? 'selected' : '' }}>
                                                                            {{ $value->nama_universitas }}
                                                                        </option>
                                                                    @endforeach
                                                                    <option value='999999'>Lainnya</option>
                                                                </select>
                                                                <input type="hidden" name="status_riwayat[]" value="{{ !empty($s2->id) ? $s2->id : '0' }}">
                                                                <div class="univ2-text">
                                                                    <input type='hidden' class='form-control mt-3' placeholder='Nama Universitas' name='universitas[]'>
                                                                </div>
                                                            </div>
                                                            
                                                            <label class="col-sm-4 col-form-label">Jurusan : </label>
                                                            <div class="col-sm-10">
                                                                <select name="jurusanid[]" id="jurusan2" class="js-example-basic-single col-sm-12" disabled  placeholder="--Nama Jurusan--">
                                                                    @foreach($master_prodi as $value)
                                                                        <option value="{{ $value->id }}" {{ (!empty($s2->jurusan) && $s2->jurusan == $value->id) ? 'selected' : '' }}>
                                                                            {{ $value->nama_jurusan }}
                                                                        </option>
                                                                    @endforeach
                                                                    <option value='999999'>Lainnya</option>
                                                                </select>
                                                                <div class="jurusan2-text">
                                                                    <input type='hidden' class='form-control mt-3' placeholder='Nama Jurusan' name='jurusan[]'>
                                                                </div>
                                                            </div>
                                                            
                                                            <br />
                                                            <div class="form-check col-sm-12">
                                                            <input class="form-check-input" type="checkbox" value="" id="pendidikans3" style="margin-left:0; margin-top:.40rem">
                                                            <label class="form-check-label" for="pendidikans3">
                                                                <p style="margin:0"><b>Pendidikan S3</b></p>
                                                            </label>
                                                            </div>
                                                            
                                                            <label class="col-sm-12 col-form-label">Universitas : </label>
                                                            <input type="hidden" name="jenjang[]" value="S3">
                                                            <div class="col-sm-10">
                                                                <select name="universitasid[]" class="js-example-basic-single col-sm-12" id="univ3" disabled  placeholder="--Nama Universitas--">
                                                                    @foreach($universitas as $value)
                                                                        <option value="{{ $value->id }}" {{ (!empty($s3->universitas) && $s3->universitas == $value->id) ? 'selected' : '' }}>
                                                                            {{ $value->nama_universitas }}
                                                                        </option>
                                                                    @endforeach
                                                                    <option value='999999'>Lainnya</option>
                                                                </select>
                                                                <input type="hidden" name="status_riwayat[]" value="{{ !empty($s3->universitas) ? $s3->id_pegawai : '0' }}">
                                                                <div class="univ3-text">
                                                                    <input type='hidden' class='form-control mt-3' placeholder='Nama Universitas' name='universitas[]'>
                                                                </div>
                                                            </div>
                                                            
                                                            <label class="col-sm-4 col-form-label">Jurusan : </label>
                                                            <div class="col-sm-10">
                                                                <select name="jurusanid[]" id="jurusan3" class="js-example-basic-single col-sm-12" disabled  placeholder="--Nama Jurusan--">
                                                                    @foreach($master_prodi as $value)
                                                                        <option value="{{ $value->id }}" {{ (!empty($s3->jurusan) && $s3->jurusan == $value->id) ? 'selected' : '' }}>
                                                                            {{ $value->nama_jurusan }}
                                                                        </option>
                                                                    @endforeach
                                                                    <option value='999999'>Lainnya</option>
                                                                </select>
                                                                <div class="jurusan3-text">
                                                                    <input type='hidden' class='form-control mt-3' placeholder='Nama Jurusan' name='jurusan[]'>
                                                                </div>
                                                            </div>

                                                            <label class="col-sm-12 col-form-label">Nama Pasangan : </label>
                                                            <div class="col-sm-10">
                                                                <input type="text" class="form-control" name="nama_pasangan" value="{{ $query->nama_pasangan }}" >
                                                            </div>

                                                            <label class="col-sm-12 col-form-label">Tgl Lahir Pasangan : </label>
                                                            <div class="col-sm-10">
                                                                <input type="text" class="form-control" name="tgl_lahir_pasangan" value="{{ date('m/d/Y', strtotime($query->tgl_lahir_pasangan)) }}" id="datepicker2">
                                                            </div>

                                                            <label class="col-sm-8 col-form-label">Pekerjaan Pasangan : </label>
                                                            <div class="col-sm-10">
                                                                <input type="text" class="form-control" name="pekerjaan_pasangan" value="{{ $query->pekerjaan_pasangan }}" >
                                                            </div>
                                                            <label class="col-sm-12 col-form-label">Jumlah Anak : </label>
                                                            <div class="col-sm-4">
                                                                <input type="number" class="form-control" name="jumlah_anak" value="{{ $query->jumlah_anak }}" >
                                                            </div>
                                                            
                                                            <label class="col-sm-12 col-form-label">No. KTP : </label>
                                                            <div class="col-sm-10">
                                                                <input type="text" class="form-control" name="no_ktp" value="{{ $query->no_ktp }}" >
                                                            </div>
                                                            <label class="col-sm-12 col-form-label">No. KK : </label>
                                                            <div class="col-sm-10">
                                                                <input type="text" class="form-control" name="no_kk" value="{{ $query->no_kk }}" >
                                                            </div>
                                                            <label class="col-sm-10 col-form-label">No. BPJS Kesehatan : </label>
                                                            <div class="col-sm-10">
                                                                <input type="text" class="form-control" name="no_bpjs_kesehatan" value="{{ $query->no_bpjs_kesehatan }}" >
                                                            </div>
                                                            <label class="col-sm-12 col-form-label">No. BPJS ketenagakerjaan : </label>
                                                            <div class="col-sm-10">
                                                                <input type="text" class="form-control" name="no_bpjs_ketenagakerjaan" value="{{ $query->no_bpjs_ketenagakerjaan }}" >
                                                            </div>
                                                            
                                                        </div>
                                                    </div>
                                                </div>
                                                </div>
                                                
                                                <div class="card">
                                                    
                                                    <div class="card-footer">
                                                        <input type="submit" name="submit" value="Simpan" class="btn btn-primary">
                                                    </div>
                                                </div>

                                            </form>
                                        </div>
                                    </div>

                                    <div class="modal fade" id="ubah_photo" tabindex="-1" role="dialog">
                                        <div class="modal-dialog modal-lg" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title">Ubah Gambar</h4>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form method="POST" action="{{ url('simpeguser/biodata/ubah_photo') }}" enctype="multipart/form-data">
                                                        @csrf <input type="hidden" name="id" value="{{ $query->id_pegawai }}">
                                                        <input type="hidden" name="npp" value="{{ $query->npp }}">
                                                        <label class="col-sm-12 col-form-label">File Gambar : </label>
                                                        <div class="col-sm-10">
                                                            <input type="file" class="form-control" name="foto" value="" >
                                                        </div>
                                                        <div class="col-sm-12 col-form-label">
                                                            <input type="submit" class="btn btn-primary" value="Simpan">
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal fade" id="ubah_password" tabindex="-1" role="dialog">
                                        <div class="modal-dialog modal-lg" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title">Ubah Password</h4>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form method="POST" action="{{ url('simpeguser/biodata/ubah_password') }}">
                                                        @csrf <input type="hidden" name="id" value="{{ $query->id_pegawai }}">
                                                        <input type="hidden" name="npp" value="{{ $query->npp }}">
                                                        <label class="col-sm-12 col-form-label">Password Baru : </label>
                                                        <div class="col-sm-10">
                                                            <input type="password" class="form-control" name="password" value="" >
                                                        </div>
                                                        <div class="col-sm-12 col-form-label">
                                                            <input type="submit" class="btn btn-primary" value="Simpan">
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
				</div>
			</div>
		</div>
@endsection

@section('local-js')
    
    <script type="text/javascript">
	$(document).on("change","#univ1",function(){
        universitas("univ1");
    });
    $(document).on("change","#univ2",function(){
        universitas("univ2");
    });
    $(document).on("change","#univ3",function(){
        universitas("univ3");
    });
    $(document).on("change","#jurusan1",function(){
        universitas("jurusan1");
    });
    $(document).on("change","#jurusan2",function(){
        universitas("jurusan2");
    });
    $(document).on("change","#jurusan3",function(){
        universitas("jurusan3");
    });
    
    function universitas(univ){
        if($("#" + univ).val() == 999999){
            $("." + univ + "-text input").attr({type:"text"});
        }else{  
            $("." + univ + "-text input").attr({type:"hidden"});
        }
    }
    
    $(document).ready(function(){
    	 $(document.body).on("change","#provinsi",function(){
            var id=$(this).val();
            $.ajax({
                url : "{{ url('wilayah/get_kota_kecamatan') }}",
                method : "POST",
                headers: {
	                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	            },
                data : {id: id},
                async : false,
                dataType : 'json',
                success: function(data){
                    var html = '';
                    var i;
                    for(i=0; i<data.length; i++){
                        html += '<option value="'+ data[i].id_wil +'">'+data[i].nm_wil+'</option>';
                    }
                    $('#kotakab').html(html);
                     
                }
            });
        });
         $(document.body).on("change","#kotakab",function(){
            var id=$(this).val();
            $.ajax({
                url : "{{ url('wilayah/get_kota_kecamatan') }}",
                method : "POST",
                headers: {
	                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	            },
                data : {id: id},
                async : false,
                dataType : 'json',
                success: function(data){
                    var html = '';
                    var i;
                    for(i=0; i<data.length; i++){
                        html += '<option value="'+ data[i].id_wil +'">'+data[i].nm_wil+'</option>';
                    }
                    $('#kecamatan').html(html);
                     
                }
            });
        });
        $(document.body).on("change","#jenis_pegawai",function(){
            var id=$("#jenis_pegawai").val();
	        $.ajax({
	            url : "{{ url('pegawai/get_status') }}",
	            method : "POST",
	            headers: {
	                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	            },
	            data : {id: id},
	            async : false,
	            dataType : 'json',
	            success: function(data){
	                var html = '';
	                var i;
	                html += '<option value="0">--- Pilih Jenis Pegawai --- </option>';
	                for(i=0; i<data.length; i++){
	                    html += '<option value="'+ data[i].kode +'">'+data[i].nama+'</option>';
	                }
	                $('#status_pegawai').html(html);
	                 
	            }
	        });
        });
        
        $(document.body).on("click","#simpan",function(){
        	var nama_lengkap = $("#nama_lengkap").val();
	        var posisi_pegawai= $("#status_pegawai").val();
	        var npp = $("#initial_npp").val()+$("#nip").val();
	        var id_progdi = $("#homebase_select").val();
	        var jenis_kelamin = $("#jenis_kelamin").val();
	        var password = $("#password").val();
	        if(!nama_lengkap || !posisi_pegawai || !npp || !id_progdi || !alamat || !jenis_kelamin || !password ){
	        	alert("harap isi semua form");
	        }else{
	        	$.ajax({
	                url : "{{ $url_simple_insert }}",
	                method : "POST",
	                data : "nama_lengkap="+nama_lengkap+"&posisi_pegawai="+posisi_pegawai+"&npp="+npp+"&id_progdi="+id_progdi+"&alamat="+alamat+"&jenis_kelamin="+jenis_kelamin+"&password="+password,
	                success: function(data){
	                    window.location.href="{{ $redirect1 }}"+npp;
	                }
	            });
            }
        });
        $(document.body).on("change","#status_pegawai",function(){
            var today = new Date();
            var month = today.getMonth();
            var year = today.getFullYear();

            year = year.toString().substr(-2);
            month = month.toString();

            if (month.length === 1)
            {
                month = "0" + month;
            }

            var tgl = month + year;
            var status = $(this).val();
            var id = (parseInt({{ $last_id ?? 0 }})+1);
            id = id.toString();

            if(id.length === 1){
                id = "00" + id;
            }else if(id.length === 2){
                id = "0" + id;
            }else{
                id = id;
            }

            var initial_npp = status+tgl;
            var nip = id;

            $.ajax({
                url : "{{ url('pegawai/get_homebase') }}",
                method : "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data : {bagian: id},
                async : false,
                dataType : 'json',
                success: function(data){
                    var html = "";
                    html += '<label class="col-sm-4 col-form-label">Homebase : </label><div class="col-sm-12">';
                    html += '<select name="homebase" id="homebase_select" class="form-control" required>';
                    html += '<option value="0">-- Homebase -- </option>';
                    for(i=0; i<data.length; i++){
                        html += '<option value="'+ data[i].id +'">'+data[i].nama_jurusan+'</option>';
                    }
                    html += '</select></div>';
                    $('#homebase').html(html);
                }
            });
        });
        $(document.body).on('change', '#bagian', function(){
            var id=$(this).val();
            $.ajax({
                url : "{{ url('pegawai/get_jabatan') }}",
                method : "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data : {bagian: id},
                async : false,
                dataType : 'json',
                success: function(data){
                    var html = '';
                    var i;
                    html += '<option value="0">-- Jabatan Struktural -- </option>';
                    for(i=0; i<data.length; i++){
                        html += '<option value="'+ data[i].id +'">'+data[i].jabatan+'</option>';
                    }
                    $('#jabatan').html(html);
                     
                }
            });
        });
        $("#datepicker").datepicker();
        $("#datepicker2").datepicker();
        $("#tglsk_skrg").datepicker();
        $("#tglsk_fungsional").datepicker();
        $("#tglsk_struktural").datepicker();
        $(document.body).on("change","#pendidikans2",function(){
            if($(this).is(":checked")){
                $("#univ2").prop("disabled",false);
                $("#jurusan2").prop("disabled",false);
            }else{
                $("#univ2").prop("disabled",true);
                $("#jurusan2").prop("disabled",true);
            }
        });

        $(document.body).on("change","#pendidikans3",function(){
            if($(this).is(":checked")){
                $("#univ3").prop("disabled",false);
                $("#jurusan3").prop("disabled",false);
            }else{
                $("#univ3").prop("disabled",true);
                $("#jurusan3").prop("disabled",true);
            }
        });	
    });
	$("#detail_status").css({"display":"none"});
	$(document.body).on("change","#status_nikah",function(){
		if($(this).val() == "Lajang"){
			$("#detail_status").css({"display":"none"});
		}else{
			$("#detail_status").css({"display":"block"});
		}
	});
	$(document).ready(function() {

	    function adjustIframeHeight() {
	        var $body   = $('body'),
	            $iframe = $body.data('iframe.fv');
	        if ($iframe) {
	            $iframe.height($body.height());
	        }
	    }
	    var form = $("#create-pegawai").show();

		// Initialize jQuery Validate first
		form.validate({
		  errorPlacement: function errorPlacement(error, element) {
		      element.before(error);
		  },
		  rules: {
		      confirm: {
		          equalTo: "#password-2"
		      }
		  }
		});

		// Then initialize jQuery Steps
		form.steps({
		  headerTag: "h3",
		  bodyTag: "fieldset",
		  transitionEffect: "slideLeft",
		  onStepChanging: function(event, currentIndex, newIndex) {

		      if (currentIndex > newIndex) {
		          return true;
		      }
		      if (newIndex === 3 && Number($("#age-2").val()) < 18) {
		          return false;
		      }
		      if (currentIndex < newIndex) {
		          form.find(".body:eq(" + newIndex + ") label.error").remove();
		          form.find(".body:eq(" + newIndex + ") .error").removeClass("error");
		      }
		      if (form.validate()) {
		          form.validate().settings.ignore = ":disabled,:hidden";
		          return form.valid();
		      }
		      return true;
		  },
		  onStepChanged: function(event, currentIndex, priorIndex) {
		      if (currentIndex === 2 && Number($("#age-2").val()) >= 18) {
		          form.steps("next");
		      }
		      if (currentIndex === 2 && priorIndex === 3) {
		          form.steps("previous");
		      }
		  },
		  onFinishing: function(event, currentIndex) {
		      if (form.validate()) {
		          form.validate().settings.ignore = ":disabled";
		          return form.valid();
		      }
		      return true;
		  },
		  onFinished: function(event, currentIndex) {
		  		var npp = $("#initial_npp").val()+$("#nip").val();
		  		$.ajax({
	                url : "{{ $url_insert_all }}",
	                method : "POST",
	                data : form.serialize(),
	                success: function(data){
	                    if(data == "berhasil"){
                            alert("berhasil menambahkan pegawai baru dengan NPP "+npp);
	                    	window.location.href="{{ $redirect1 }}"+npp;
	                    }else{
                            alert("gagal menambahkan pegawai baru " + data);
	                    	//window.location.href="{{ $redirect1 }}"+npp;
	                    }
	                }
	            });
		  }
		});
        $(".js-example-basic-single").select2();
	});
</script>
@endsection