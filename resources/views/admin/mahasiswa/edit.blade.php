@extends('layouts.default', ['CurrentPage' => $CurrentPage])

@section('content')
		<div class="content-body">
			<div class="container">

				<!-- Row -->
				<div class="row">
					<div class="col-xl-12">
						
						<div class="mb-4 pb-3">
							<a href="{{ url('mahasiswa')}}" class="btn btn-primary"><i class="fa fa-arrow-left me-1"></i>Kembali</a>
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
									<div class="card-header">
                                        <h5>Profile Mahasiswa</h5>
                                    </div>
                                    <div class="card-content">
                                        @if(session('success'))
                                            <div class="alert alert-success">
                                                {{ session('success') }}
                                            </div>
                                        @endif
                                        <div class="dt-responsive table-responsive">
                                            <form action="{{ route('mahasiswa.update', $id) }}" method="post">
                                                @csrf
                                                @method('PUT') 
                                                <table width="80%">
                                                    <tr>
                                                        <td rowspan="4" style="position: top;">
                                                            <center>
                                                                @if($detail[0]['foto_peserta'])
                                                                    <img src="{{ asset('assets/foto_pmb_peserta/' . $detail[0]['foto_peserta']) }}" width="120px" height="160px">
                                                                @else
                                                                    <img src="{{ asset('assets/foto_pmb_peserta/default.png') }}" width="120px" height="160px">
                                                                @endif
                                                                
                                                            </center>
                                                        </td>
                                                        <td>
                                                            <input type="text" name="nim" hidden value="{{ $detail[0]['nim'] }}">NIM 
                                                        </td>
                                                        <td>:</td>
                                                        <td>{{ $detail[0]['nim'] }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Angkatan</td>
                                                        <td>:</td>
                                                        <td><input type="text" name="angkatan" value="{{ substr($detail[0]['nim'], 2, 4) }}" class="form-control" readonly></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Program Studi</td>
                                                        <td>:</td>
                                                        <td>
                                                            <select name="prodi" id="prodi" class="form-control" required> 
                                                                @foreach ($prodi as $p)
                                                                    <option value="{{ $p->id }}" {{ $detail[0]['id_program_studi'] == $p->id ? 'selected' : '' }}>
                                                                        {{ $p->nama_jurusan }}
                                                                    </option>    
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Nama Lengkap</td>
                                                        <td>:</td>
                                                        <td><input type="text" name="nama" value="{{ $detail[0]['nama'] }}" class="form-control"></td>
                                                    </tr>
                                                    <tr>
                                                        <td></td>
                                                        <td>Agama</td>
                                                        <td>:</td>
                                                        <td>
                                                            <select name="agama" id="agama" class="form-control" required> 
                                                                <option value="1" {{ $detail[0]['agama'] == 1 ? 'selected' : '' }}>Islam</option>
                                                                <option value="2" {{ $detail[0]['agama'] == 2 ? 'selected' : '' }}>Kristen</option>
                                                                <option value="3" {{ $detail[0]['agama'] == 3 ? 'selected' : '' }}>Katolik</option>
                                                                <option value="4" {{ $detail[0]['agama'] == 4 ? 'selected' : '' }}>Hindu</option>
                                                                <option value="5" {{ $detail[0]['agama'] == 5 ? 'selected' : '' }}>Budha</option>
                                                                <option value="6" {{ $detail[0]['agama'] == 6 ? 'selected' : '' }}>Konghucu</option>
                                                                <option value="99" {{ $detail[0]['agama'] == 99 ? 'selected' : '' }}>Aliran Kepercayaan</option>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td></td>
                                                        <td>Tempat Lahir</td>
                                                        <td>:</td>
                                                        <td><input type="text" name="tempat_lahir" value="{{ $detail[0]['tempat_lahir'] }}" class="form-control"></td>
                                                    </tr>
                                                    <tr>
                                                        <td></td>
                                                        <td>Tanggal Lahir</td>
                                                        <td>:</td>
                                                        <td><input type="date" name="tgl_lahir" value="{{ $detail[0]['tgl_lahir'] }}" class="form-control"></td>
                                                    </tr>
                                                    <tr>
                                                        <td></td>
                                                        <td>Nomor Telpon</td>
                                                        <td>:</td>
                                                        <td><input type="text" name="telp" value="{{ $detail[0]['telp'] }}" class="form-control"></td>
                                                    </tr>
                                                    
                                                    <tr>
                                                        <td></td>
                                                        <td>Nomor HP</td>
                                                        <td>:</td>
                                                        <td><input type="text" name="hp" value="{{ $detail[0]['hp'] }}" class="form-control"></td>
                                                    </tr>
                                                    <tr>
                                                        <td></td>
                                                        <td>Nomor HP Orang Tua</td>
                                                        <td>:</td>
                                                        <td><input type="text" name="hp_ortu" value="{{ $detail[0]['hp_ortu'] }}" class="form-control"></td>
                                                    </tr>
                                                    <tr>
                                                        <td></td>
                                                        <td>Alamat Semarang</td>
                                                        <td>:</td>
                                                        <td><input type="text" name="alamat_semarang" value="{{ $detail[0]['alamat_semarang'] }}" class="form-control"></td>
                                                    </tr>
                                                    <tr>
                                                        <td></td>
                                                        <td>Email</td>
                                                        <td>:</td>
                                                        <td><input type="text" name="email" value="{{ $detail[0]['email'] }}" class="form-control"></td>
                                                    </tr>
                                                    <tr>
                                                        <td></td>
                                                        <td>Alamat</td>
                                                        <td>:</td>
                                                        <td><input type="text" name="alamat" value="{{ $detail[0]['alamat'] }}" class="form-control"></td>
                                                    </tr>
                                                    <tr>
                                                        <td></td>
                                                        <td>RT</td>
                                                        <td>:</td>
                                                        <td><input type="text" name="rt" value="{{ $detail[0]['rt'] }}" class="form-control"></td>
                                                    </tr>
                                                    <tr>
                                                        <td></td>
                                                        <td>RW</td>
                                                        <td>:</td>
                                                        <td><input type="text" name="rw" value="{{ $detail[0]['rw'] }}" class="form-control"></td>
                                                    </tr>
                                                    <tr>
                                                        <td></td>
                                                        <td>Provinsi</td>
                                                        <td>:</td>
                                                        <td>
                                                            <select name="provinsi" id="provinsi" class="form-control" required>
                                                                <option selected value="{{ $detail[0]['provinsi'] }}">{{ $detail[2]['nm_wil'] ?? 'Pilih Provinsi' }}</option>
                                                                @foreach($wilayah as $w)
                                                                    <option value="{{ $w->id_wil }}">{{ $w->nm_wil }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td></td>
                                                        <td>Kota / Kabupaten</td>
                                                        <td>:</td>
                                                        <td>
                                                            <select name="kokab" id="kotakab" class="form-control" required> 
                                                                <option selected value="{{ $detail[0]['kokab'] }}">{{ $detail[1]['nm_wil'] ?? 'Kota/Kab Tidak Diketahui' }}</option>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                     <tr>
                                                        <td></td>
                                                        <td>Kecamatan</td>
                                                        <td>:</td>
                                                        <td>
                                                            <select name="kecamatan" id="kecamatan" class="form-control" required> 
                                                                <option selected value="{{ $detail[0]['kecamatan'] }}">{{ $detail[0]['nm_wil'] ?? 'Kecamatan Tidak Diketahui' }}</option>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td></td>
                                                        <td>Kelurahan</td>
                                                        <td>:</td>
                                                        <td><input type="text" name="kelurahan" value="{{ $detail[0]['kelurahan'] }}" class="form-control"></td>
                                                    </tr>
                                                   
                                                    
                                                    
                                                    <tr>
                                                        <td></td>
                                                        <td>Status</td>
                                                        <td>:</td>
                                                        <td>
                                                            <select name="status" id="status" class="form-control" required> 
                                                                <option value="1" {{ $detail[0]['status'] == 1 ? 'selected' : '' }}>Aktif</option>
                                                                <option value="2" {{ $detail[0]['status'] == 2 ? 'selected' : '' }}>Cuti</option>
                                                                <option value="3" {{ $detail[0]['status'] == 3 ? 'selected' : '' }}>Keluar</option>
                                                                <option value="4" {{ $detail[0]['status'] == 4 ? 'selected' : '' }}>Lulus</option>
                                                                <option value="5" {{ $detail[0]['status'] == 5 ? 'selected' : '' }}>Meninggal</option>
                                                                <option value="6" {{ $detail[0]['status'] == 6 ? 'selected' : '' }}>DO</option>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                    <tr align="right">
                                                        <td colspan="4"><hr><input class="btn btn-success" type="submit" value="Simpan"></td>
                                                    </tr>
                                                </table>
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
@endsection
@section('local-js')
    <script type="text/javascript">
    $(document).ready(function(){
        $('#provinsi').change(function(){
            var id=$(this).val();
            $.ajax({
                url : "{{ url('wilayah/get_kota_kecamatan') }}",
                method : "POST",
                data : {id: id},
                async : false,
                headers: {
	                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	            },
                dataType : 'json',
                success: function(data){
                    var html = '';
                    var i;
                    for(i=0; i<data.length; i++){
                        html += '<option value="'+ data[i].id_wil +'">'+data[i].nm_wil+'</option>';
                    }
                    
                    $('#kotakab').html(html);
                    $('#kotakab').selectpicker('refresh'); // Wajib dipanggil untuk mereset UI
                    $('#kotakab').trigger('change');
                }
            });
        });
        $('#kotakab').change(function(){
            var id=$(this).val();
            $.ajax({
                url : "{{ url('wilayah/get_kota_kecamatan') }}",
                method : "POST",
                data : {id: id},
                async : false,
                headers: {
	                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	            },
                dataType : 'json',
                success: function(data){
                    var html = '';
                    var i;
                    for(i=0; i<data.length; i++){
                        html += '<option value="'+ data[i].id_wil +'">'+data[i].nm_wil+'</option>';
                    }
                    $('#kecamatan').html(html);
                    $('#kecamatan').selectpicker('refresh'); // Wajib dipanggil untuk mereset UI
                    $('#kecamatan').trigger('change');    
                }
            });
        });
    });
</script>
@endsection