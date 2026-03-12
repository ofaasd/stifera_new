@extends('layouts.default', ['CurrentPage' => $CurrentPage])
@section('local-css')
    <style>
        #regForm {
            background-color: #ffffff;
            margin-left: 10px;
            padding: 40px;
            width: 100%;
            min-width: 300px;
        }

        h1 {
            text-align: center;  
        }

        input {
            padding: 10px;
            width: 100%;
            font-size: 17px;
            font-family: Raleway;
            border: 1px solid #aaaaaa;
        }

        /* Mark input boxes that gets an error on validation: */
        input.invalid {
            background-color: #ffdddd;
        }

        /* Hide all steps by default: */
        .tab {
            display: none;
        }

        button {
            background-color: #4CAF50;
            color: #ffffff;
            border: none;
            padding: 10px 20px;
            font-size: 17px;
            font-family: Raleway;
            cursor: pointer;
        }

        button:hover {
            opacity: 0.8;
        }

        #prevBtn {
            background-color: #bbbbbb;
        }

        /* Make circles that indicate the steps of the form: */
        .step {
            height: 15px;
            width: 15px;
            margin: 0 2px;
            background-color: #bbbbbb;
            border: none;  
            border-radius: 50%;
            display: inline-block;
            opacity: 0.5;
        }

        .step.active {
            opacity: 1;
        }

        /* Mark the steps that are finished and valid: */
        .step.finish {
            background-color: #4CAF50;
        }
        </style>
@endsection
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
									<form id="regForm" action="{{ url('pmb') }}" method="post" enctype="multipart/form-data">
                                    @csrf 
                                        <div class="tab">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    Nomor KTP :
                                                    <p><input type="text" class="form-control" onchange="cekNik()" id="nik" placeholder="Nomor KTP"  name="ktp" required=""></p>
                                                    
                                                    NISN :
                                                    <p>
                                                        <input type="text" class="form-control" placeholder="NISN"  name="nisn" required="">
                                                        Tidak Tahu NISN anda? <a href="https://nisn.data.kemdikbud.go.id/page/data" target="_blank">cek DISINI</a>
                                                    </p>
                                                    
                                                    Nama Lengkap :
                                                    <p><input type="text" class="form-control" placeholder="Nama Lengkap"  name="nama" required=""></p>
                                                    
                                                    Jenis Kelamin :
                                                    <p>
                                                        <select name="jk" class="form-control" required="">
                                                            <option selected="" disabled="">Jenis Kelamin</option>
                                                            <option value="1">Laki - Laki</option>
                                                            <option value="2">Perempuan</option>
                                                        </select>
                                                    </p>
                                                    
                                                    Agama :
                                                    <p>
                                                        <select name="agama" class="form-control" required="">
                                                            <option value="opt1" selected="" disabled="">Pilih Agama</option>
                                                            <option value="1">Islam</option>
                                                            <option value="2">Kristen</option>
                                                            <option value="3">Katolik</option>
                                                            <option value="4">Hindu</option>
                                                            <option value="5">Budha</option>
                                                            <option value="6">Konghucu</option>
                                                            <option value="99">Lainnya</option>
                                                        </select>
                                                    </p>
                                                    
                                                    Nama Ibu :
                                                    <p><input type="text" class="form-control" placeholder="Nama Ibu"  name="ibu" required=""></p>
                                                    
                                                    Nama Ayah :
                                                    <p><input type="text" class="form-control" placeholder="Nama Ayah"  name="ayah" required=""></p>
                                                    
                                                    Nomor HP Orang Tua :
                                                    <p><input type="text" class="form-control" placeholder="Nomor HP Orang Tua"  name="hp_ortu" required=""></p>
                                                    
                                                    Alamat Semarang :
                                                    <p><textarea class="form-control" style="resize: none;" name="alamat_semarang" placeholder="Alamat Semarang" required=""></textarea></p>
                                                    
                                                    Tinggi Badan :
                                                    <p><input type="number" class="form-control" placeholder="Tinggi Badan"  name="tb" required=""></p>
                                                    
                                                    Berat Badan :
                                                    <p><input type="number" class="form-control" placeholder="Berat Badan"  name="bb" required=""></p>
                                                    
                                                    Tempat Lahir :
                                                    <p><input type="text" class="form-control" placeholder="Tempat Lahir"  name="tl" required=""></p>
                                                    
                                                    Tanggal Lahir :
                                                    <p><input type="date" class="form-control"  name="tgl" required=""></p>
                                                </div>
                                                
                                                <div class="col-sm-6">
                                                    Nomor Handphone :
                                                    <p><input type="text" class="form-control" placeholder="No Handphone"  name="hp" required=""></p>
                                                    
                                                    Email :
                                                    <p><input type="email" class="form-control" placeholder="Email Aktif"  name="email" required=""></p>
                                                    
                                                    Nomor Telepon :
                                                    <p><input type="text" class="form-control" placeholder="Nomor Telepon"  name="telepon" required=""></p>
                                                    
                                                    Status Warga Negara :
                                                    <p>
                                                        <select name="warga_negara" id="wn" class="form-control" required="">
                                                            <option selected="" disabled="">Pilih Warga Negara</option>
                                                            @foreach($warga_negara as $w)
                                                                <option value="{{ $w->id_negara }}" {{($w->id_negara == 'ID') ? "selected":""}}>{{ $w->nm_negara }}</option>
                                                            @endforeach
                                                        </select>
                                                    </p>
                                                    
                                                    Nama Provinsi :
                                                    <p>
                                                        <select name="provinsi" id="provinsi" class="form-control js-example-basic-single" required="">
                                                            <option selected="" disabled="">Pilih Provinsi</option>
                                                            @foreach($wilayah as $w)
                                                                <option value="{{ $w->id_wil }}">{{ $w->nm_wil }}</option>
                                                            @endforeach
                                                        </select>
                                                    </p>
                                                    
                                                    Nama Kota/Kabupaten :
                                                    <p>
                                                        <select name="kotakab" id="kotakab" class="form-control js-example-basic-single" required=""> 
                                                            <option selected="" disabled="">Pilih Kota/Kabupaten</option>
                                                        </select>
                                                    </p>
                                                    
                                                    Nama Kecamatan :
                                                    <p>
                                                        <select name="kecamatan" id="kecamatan" class="form-control js-example-basic-single" required="">
                                                            <option selected="" disabled="">Daftar Kecamatan</option>
                                                        </select>
                                                    </p>
                                                    
                                                    Kode POS :
                                                    <p><input type="text" class="form-control" placeholder="KODE POS"  name="pos" required=""></p>
                                                    
                                                    Nama Kelurahan :
                                                    <p><input type="text" class="form-control" placeholder="Nama Kelurahan"  name="kelurahan" required=""></p>
                                                    
                                                    Alamat :
                                                    <p><textarea class="form-control" style="resize: none;" name="alamat" placeholder="Hanya nama kampung, jalan dan nomor rumah saja" required=""></textarea></p>
                                                    
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            RT :
                                                            <p><input type="text" class="form-control" placeholder="RT"  name="rt" required=""></p>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            RW :
                                                            <p><input type="text" class="form-control" placeholder="RW"  name="rw" required=""></p>
                                                        </div>
                                                    </div>
                                                    
                                                    Sekolah Asal :
                                                    <p>
                                                        <select name="asal_sekolah" id="asal_sekolah" class="form-control js-example-basic-single" required="">
                                                        </select>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="tab">
                                            Tahun Ajaran :
                                            <p>
                                                <select name="gel_ta" class="form-control" required="">
                                                    <option selected="" disabled="">- Pilih Tahun Ajaran -</option>
                                                    @foreach($gel_ta as $ta)
                                                        <option value="{{ $ta->awal }}">{{ $ta->kode }}</option>
                                                    @endforeach
                                                </select>
                                            </p>
                                            
                                            PILIH KELAS :
                                            <p>
                                                <select id="kelas" name="kelas" class="form-control" required="">
                                                    <option value="opt1" selected="" disabled="">- Pilih Kelas -</option>
                                                    @foreach($kelas as $kel)
                                                        <option value="{{ $kel->jalur . '-' . $kel->id }}">{{ $kel->nama_kelas }}</option>
                                                    @endforeach
                                                </select>
                                                <a href="{{ url('master/kelas') }}">Tambah Kelas</a>
                                            </p>
                                            
                                            PILIH JALUR :
                                            <p>
                                                <select id="jalur" name="jalur" class="form-control" required=""></select>
                                            </p>
                                            
                                            <div id="kerdiv" hidden="">
                                                JENIS KERJASAMA (JIKA JALUR KERJSAMA) :
                                                <p>
                                                    <select id="kerjasama" name="kerjasama" class="form-control"></select>
                                                </p>
                                            </div>
                                            
                                            <div id="skl_mou" hidden="">
                                                <p id="mou_akfar"></p>
                                            </div>
                                        </div>
                                        
                                        <div class="tab">
                                            <div id="gel_text" hidden="">
                                                PILIH GELOMBANG :
                                                <p>
                                                    <select name="gelombang" id="gelombang" class="form-control" required="">
                                                        <option selected="" disabled="">Gelombang Pendaftaran</option>
                                                        @foreach($gelombang as $g)
                                                            <option value="{{ $g->id }}">{{ $g->nama_gel_long }}</option>
                                                        @endforeach
                                                    </select>
                                                </p>
                                            </div>
                                            
                                            <div id="pmdp_text" hidden="">
                                                NILAI RATA - RATA SEMESTER 1 :
                                                <p><input type="number" class="form-control" placeholder="NILAI RATA - RATA SEMESTER 1" name="smt1" step="any"></p>
                                                
                                                NILAI RATA - RATA SEMESTER 2 :
                                                <p><input type="number"  class="form-control" placeholder="NILAI RATA - RATA SEMESTER 2" name="smt2" step="any"></p>
                                                
                                                NILAI RATA - RATA SEMESTER 3 :
                                                <p><input type="number"  class="form-control" placeholder="NILAI RATA - RATA SEMESTER 3" name="smt3"  step="any"></p>
                                                
                                                NILAI RATA - RATA SEMESTER 4 :
                                                <p><input type="number"  class="form-control" placeholder="NILAI RATA - RATA SEMESTER 4" name="smt4"  step="any"></p>
                                                
                                                NILAI RATA - RATA SEMESTER 5 :
                                                <p><input type="number"  class="form-control" placeholder="NILAI RATA - RATA SEMESTER 5" name="smt5"  step="any"></p>
                                                
                                                SERTIFIKAT JUARA :
                                                <p><input type="file" name="file1"></p>
                                                <p><input type="text" name="ket1" placeholder="Keterangan Sertifikat"></p>
                                                
                                                SERTIFIKAT JUARA :
                                                <p><input type="file" name="file2"></p>
                                                <p><input type="text" name="ket2" placeholder="Keterangan Sertifikat"></p>
                                                
                                                SERTIFIKAT JUARA :
                                                <p><input type="file" name="file3"></p>
                                                <p><input type="text" name="ket3" placeholder="Keterangan Sertifikat"></p>
                                            </div>
                                            
                                            JENIS PENDAFTARAN : 
                                            <p>
                                                <select name="pendaftaran" class="form-control" required="">
                                                    <option value="opt1" selected="" disabled="">- Pilih Jenis Pendaftaran -</option>
                                                    <option value="1">Peserta Didik Baru</option>
                                                    <option value="2">Pindahan</option>
                                                    <option value="11">Alih Jenjang</option>
                                                    <option value="12">Lintas Jalur</option>
                                                </select>
                                            </p>
                                            
                                            Program Studi 1 :
                                            <p>
                                                <select name="pilihan1" class="form-control" required="">
                                                    <option value="opt1" selected="" disabled="">- Pilih Program Studi 1 -</option>
                                                    @foreach($prodi as $p)
                                                        <option value="{{ $p->id }}">{{ $p->jenjang . ' - ' . $p->nama_jurusan }}</option>
                                                    @endforeach
                                                </select>
                                            </p>
                                            
                                            Program Studi 2 :
                                            <p>
                                                <select name="pilihan2" class="form-control" required="">
                                                    <option value="opt1" selected="" disabled="">- Pilih Program Studi 2 -</option>
                                                    @foreach($prodi as $p)
                                                        <option value="{{ $p->id }}">{{ $p->jenjang . ' - ' . $p->nama_jurusan }}</option>
                                                    @endforeach
                                                </select>
                                            </p>
                                        </div>
                                        
                                        <div class="tab">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    Dapat Info PMB darimana?
                                                    <p>
                                                        <select name="info_pmb" class="form-control" required="">
                                                            <option value="opt1" selected="" disabled="">- Pilih -</option>
                                                            <option value="1">Teman</option>
                                                            <option value="2">Kerabat / Orang Tua</option>
                                                            <option value="3">Sosial Media</option>
                                                            <option value="4">Lainnya</option>
                                                        </select>
                                                    </p>
                                                    
                                                    Ukuran Seragam
                                                    <p>
                                                        <select name="ukuran_seragam" class="form-control" required="">
                                                            <option value="opt1" selected="" disabled="">- Pilih Ukuran Seragam -</option>
                                                            <option value="S">S</option>
                                                            <option value="M">M</option>
                                                            <option value="L">L</option>
                                                            <option value="XL">XL</option>
                                                        </select>
                                                    </p>
                                                </div>
                                                <div class="col-sm-6">
                                                    Upload Foto :
                                                    <p>
                                                        <input type='file' name="foto" onchange="readURL(this);" />
                                                        <br><small>Maksimal 1 MB dengan background merah.</small>
                                                    </p>
                                                    <img id="blah" src="http://placehold.it/180" alt="your image" style="width:225px;height:280px;" />
                                                    <br><br><br>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div style="overflow:auto;">
                                            <div style="float:right;">
                                                <button type="button" id="prevBtn" class="btn btn-secondary" onclick="nextPrev(-1)">Sebelumnya</button>
                                                <button type="button" id="nextBtn" class="btn btn-primary" onclick="nextPrev(1)">Selanjutnya</button>
                                            </div>
                                        </div>
                                        
                                        <div style="text-align:center;margin-top:40px;">
                                            <span class="step"></span>
                                            <span class="step"></span>
                                            <span class="step"></span>
                                            <span class="step"></span>
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
@section('local-js')
<script type="text/javascript">
    // Setup global ajax headers untuk Laravel CSRF (Sangat Penting!)
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#blah').attr('src', e.target.result);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    var currentTab = 0; // Current tab is set to be the first tab (0)
    showTab(currentTab); // Display the current tab

    function showTab(n) {
        var x = document.getElementsByClassName("tab");
        x[n].style.display = "block";
        
        if (n == 0) {
            document.getElementById("prevBtn").style.display = "none";
        } else {
            document.getElementById("prevBtn").style.display = "inline";
        }
        
        if (n == (x.length - 1)) {
            document.getElementById("nextBtn").innerHTML = "Simpan";
        } else {
            document.getElementById("nextBtn").innerHTML = "Selanjutnya";
        }
        fixStepIndicator(n)
    }

    function nextPrev(n) {
        var x = document.getElementsByClassName("tab");
        if (n == 1 && !validateForm()) {
            alert("invalid form");
            return false;
        }
        
        x[currentTab].style.display = "none";
        currentTab = currentTab + n;
        
        if (currentTab >= x.length) {
            document.getElementById("regForm").submit();
            return false;
        }
        showTab(currentTab);
    }

    function validateForm() {
        var x, y, i, valid = true;
        x = document.getElementsByClassName("tab");
        y = x[currentTab].getElementsByTagName("input");
        for (i = 0; i < y.length; i++) {
            if (y[i].value == "" && y[i].required) { // Perbaikan: hanya ngecek field yg required
                y[i].className += " invalid";
                valid = false; // Perbaikan: di CI valid = true, seharusnya false jika gagal
            }
        }
        if (valid) {
            document.getElementsByClassName("step")[currentTab].className += " finish";
        }
        return valid;
    }

    function fixStepIndicator(n) {
        var i, x = document.getElementsByClassName("step");
        for (i = 0; i < x.length; i++) {
            x[i].className = x[i].className.replace(" active", "");
        }
        x[n].className += " active";
    }

    $(document).ready(function(){
        // Kelas Change
        $('#kelas').change(function(){
            var str = $(this).val();
            var str_explode = str.split('-');
            var kelas = str_explode[0];
            
            var isi = '';
            if(kelas == 1){
                isi = '<option value="opt1" selected="" disabled="">- Pilih Jalur -</option><option value="1">PMDP</option><option value="2">Kerjasama</option><option value="3">Umum</option>';
            }else if(kelas == 2){
                isi = '<option value="opt1" selected="" disabled="">- Pilih Jalur -</option><option value="3">Umum</option><option value="2">Kerjasama</option>';
            }
            $('#jalur').html(isi);
        });

        // Jalur Change
        $('#jalur').change(function(){
            var pilihan = $(this).val();
            var get_kelas = $('#kelas').val();
            var str_explode = get_kelas.split('-');
            var kelas = str_explode[0];
            var hasil = '';
            
            if ((pilihan == 2) && (kelas == 1)) {
                hasil = '<option value="opt1" selected="" disabled="">- Pilih Kerjasama -</option><option value="1">MOU AKFAR</option><option value="2">Alumni SMK Nusaputera</option>';
                $('#kerdiv').prop('hidden', false);
                $('#skl_mou').prop('hidden', false);
            }else if((pilihan == 2) && (kelas == 2)){
                hasil = '<option value="opt1" selected="" disabled="">- Pilih Kerjasama -</option><option value="1">MOU AKFAR</option><option value="2">Alumni SMK Nusaputera</option><option value="3">PAFI</option><option value="4">Kimia Farma</option>';
                $('#kerdiv').prop('hidden', false);
                $('#skl_mou').prop('hidden', false);
            }else{
                $('#kerdiv').prop('hidden', true);
                $('#skl_mou').prop('hidden', true);
            }

            if (pilihan == 3 || pilihan == 2) {
                $('#gel_text').prop('hidden', false);
                $('#pmdp_text').prop('hidden', true);
            } else if(pilihan == 1){
                $('#gel_text').prop('hidden', false);
                $('#pmdp_text').prop('hidden', false);
            } else {
                $('#gel_text').prop('hidden', false);
                $('#pmdp_text').prop('hidden', true);
            }
            $('#kerjasama').html(hasil);
        });

        // Kerjasama Change
        $('#kerjasama').change(function(){
            var mou_val = $(this).val();
            var html = '';
            if (mou_val == 1) {
                $('#skl_mou').prop('hidden', false);
                $.ajax({
                    url : "{{ url('pmb/daftar_mou') }}",
                    method: "POST",
                    data:{id: mou_val},
                    // async: false, (Sebaiknya dihindari)
                    dataType: "json",
                    success: function(data){
                        html += 'PILIH SEKOLAH : <p><select id="mou_akfar" name="nama_sekolah_mou" class="form-control">';
                        for (var i = 0; i < data.length; i++) {
                            html += '<option value="'+ data[i].id_sekolah +'">'+data[i].nama_sekolah+'</option>';
                        }
                        html += '</select></p>';
                        $('#mou_akfar').html(html);
                    }
                });
            } else {
                $('#skl_mou').prop('hidden', true);
                $('#mou_akfar').html('');
            }
        });

        $('#provinsi').change(function(){
            var id = $(this).val();
            $.ajax({
                url : "{{ url('wilayah/get_kota_kecamatan') }}",
                method : "POST",
                data : {id: id},
                dataType : 'json',
                success: function(data){
                    var html = '<option selected="" disabled="">Pilih Kota/Kabupaten</option>';
                    for(var i=0; i<data.length; i++){
                        html += '<option value="'+ data[i].id_wil +'">'+data[i].nm_wil+'</option>';
                    }
                    $('#kotakab').html(html);
                }
            });
        });

        $('#kotakab').change(function(){
            var id = $(this).val();
            var id_prov = $('#provinsi').val();
            
            // Mengambil Kecamatan
            $.ajax({
                url : "{{ url('wilayah/get_kota_kecamatan') }}",
                method : "POST",
                data : {id: id},
                dataType : 'json',
                success: function(data){
                    var html = '<option selected="" disabled="">Daftar Kecamatan</option>';
                    for(var i=0; i<data.length; i++){
                        html += '<option value="'+ data[i].id_wil +'">'+data[i].nm_wil+'</option>';
                    }
                    $('#kecamatan').html(html);
                }
            });
            
            // Mengambil Sekolah Asal
            $.ajax({
                url : "{{ url('pmb/daftar_sekolah') }}",
                method : "POST",
                data : {id_prov: id_prov, id_kota: id},
                dataType : 'json',
                success: function(data){
                    var html = '';
                    for(var i=0; i<data.length; i++){
                        html += '<option value="'+ data[i].id +'">'+data[i].nama+'</option>';
                    }
                    $('#asal_sekolah').html(html);
                }
            });
        });

        $('#gelombang').change(function(){
            var val_gel = $(this).val();
            var nopen_pmdp = 20000;
            var nopen_gel = 50000;
            
            var target_url = (val_gel == 'PMDP') ? "{{ url('pmb/nopen_pmdp') }}" : "{{ url('pmb/nopen_gel') }}";
            var id_post = (val_gel == 'PMDP') ? nopen_pmdp : nopen_gel;
            
            $.ajax({
                url : target_url,
                method : "POST",
                data : {id: id_post},
                dataType : 'json',
                success: function(data){
                    var nopen_baru = '';
                    for(var i=0; i<data.length; i++){
                        nopen_baru += '<input type="text" class="form-control" readonly="" value="'+ data[i].nopen +'" name="nopen">';
                    }
                    $('#nopen').html(nopen_baru);
                }
            });
            
            if(val_gel == 'PMDP'){
                $('#label1').html("Kelas X Semester 1");
                $('#label2').html("Kelas X Semester 2");
                $('#label3').html("Kelas XI Semester 1");
                $('#label4').html("Kelas XI Semester 2");
                $('#pmdp1, #pmdp2, #pmdp3, #pmdp4').prop('hidden', false);
            } else {
                $('#label1, #label2, #label3, #label4').html("");
                $('#pmdp1, #pmdp2, #pmdp3, #pmdp4').prop('hidden', true);
            }
        });
        $(".js-example-basic-single").select2();
    });

    function cekNik(){
        var nik = $('#nik').val();
        if(nik.length == 16){
            $.ajax({
                url : "{{ url('pmb/cek_nik') }}",
                method: "POST",
                data: { nik: nik },
                dataType: "json",
                success: function(data){
                    if(data.nik_tersedia == 1){
                        alert('NIK sudah terdaftar!');
                    }
                }
            });
        }
    }
</script>
@endsection