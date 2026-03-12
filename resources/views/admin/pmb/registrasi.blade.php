@extends('layouts.default', ['CurrentPage' => $CurrentPage])

@section('content')
		<div class="content-body">
			<div class="container">

				<!-- Row -->
				<div class="row">
					<div class="col-xl-12">
						<div class="mb-4 pb-3">
							<a href="{{ url('content-add')}}" class="btn btn-primary">Kembali</a>
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
                                    <form action="{{ url('pmb/surat_pernyataan2') }}" method="post">
                                        @csrf <h4 class="sub-title">DETAIL PEMBAYARAN</h4>
                                        
                                        @php
                                            // Menghitung jumlah operasional berdasarkan data $o (dari controller)
                                            $jml_operasional = $o->operasional + $o->kemahasiswaan + $o->seragam;
                                        @endphp
                                        
                                        <input type="number" class="form-control" id="jml_operasional" value="{{ $jml_operasional }}" hidden>

                                        
                                            <div class="form-group row">
                                                <div class="col-sm-6">
                                                    <input type="hidden" class="form-control" value="{{ $d->id }}" name="id">
                                                    
                                                    <div class="col-sm-12">
                                                        <label><b>Sumbangan Pendidikan Institusi (SPI) :</b></label>
                                                    </div>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control" name="spi" id="spi" value="{{ $spi }}">
                                                    </div>
                                                    
                                                    <div class="col-sm-6">
                                                        <br>
                                                        <label><b>Operasional :</b></label>
                                                    </div>
                                                    <div class="col-sm-8">
                                                        <input type="number" class="form-control" name="operasional" id="operasional" value="{{ $operasional }}">
                                                    </div>
                                                    
                                                    <div class="col-sm-6">
                                                        <br>
                                                        <label><b>Potongan SPI :</b></label>
                                                    </div>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control" name="potongan" id="potongan" value="{{ $potongan_spi }}">
                                                        <br>
                                                        <a href="#" class="btn btn-primary" onclick="hitung()">Hitung</a>
                                                        <p id="total"></p>
                                                    </div>
                                                </div>

                                                <div class="col-sm-6">
                                                    <div class="col-sm-12">
                                                        <label><b>Total Biaya Satuan Kredit Semester (SKS) :</b></label>
                                                    </div>
                                                    <div class="col-sm-8">
                                                        <input type="number" class="form-control" id="sks" name="sks" value="{{ $sks }}">
                                                    </div>
                                                    
                                                    <div class="col-sm-6">
                                                        <br>
                                                        <label><b>Kemahasiswaan :</b></label>
                                                    </div>
                                                    <div class="col-sm-8">
                                                        <input type="number" class="form-control" name="kemahasiswaan" id="kemahasiswaan" value="{{ $kemahasiswaan }}">
                                                    </div>
                                                    
                                                    <div class="col-sm-6">
                                                        <br>
                                                        <label><b>Seragam :</b></label>
                                                    </div>
                                                    <div class="col-sm-8">
                                                        <input type="number" class="form-control" name="seragam" id="seragam" value="{{ $seragam }}">
                                                    </div>
                                                    
                                                    <div {{ $d->jalur_pendaftaran != 3 ? 'hidden' : '' }}>
                                                        <div class="col-sm-6">
                                                            <label><b>Nilai Tes Masuk (0-100) :</b></label>
                                                        </div>
                                                        <div class="col-sm-8">
                                                            <input type="number" name="tes" class="form-control" max="100" min="0" value="0" 
                                                                {{ $d->is_bayar == 1 ? 'readonly' : '' }} 
                                                                {{ $d->jalur_pendaftaran == 3 ? 'required' : '' }}>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-sm-6 mt-3">
                                                    <div class="col-sm-6">
                                                        <label><b>Jumlah Tahap 1 :</b></label>
                                                    </div>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control" name="jml_tahap_1" id="jml_1" value="{{ $jml_tahap_1 }}">
                                                    </div>
                                                </div>
                                                <div class="col-sm-6 mt-3">
                                                    <div class="col-sm-6">
                                                        <label><b>Tanggal Tahap 1 :</b></label>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sm-8">
                                                            <input type="date" class="form-control" name="tgl_tahap_1" value="{{ $tgl_tahap_1 }}">
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <select class="form-control" id="tahap_1" onchange="tahap_1JS({{ $d->nopen }})">
                                                                <option value="0" {{ $status_tahap_1 == 0 ? 'selected' : '' }}>belum</option>
                                                                <option value="1" {{ $status_tahap_1 == 1 ? 'selected' : '' }}>sudah</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-sm-6 mt-3">
                                                    <div class="col-sm-6">
                                                        <label><b>Jumlah Tahap 2 :</b></label>
                                                    </div>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control" name="jml_tahap_2" id="jml_2" value="{{ $jml_tahap_2 }}">
                                                    </div>
                                                </div>
                                                <div class="col-sm-6 mt-3">
                                                    <div class="col-sm-6">
                                                        <label><b>Tanggal Tahap 2 :</b></label>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sm-8">
                                                            <input type="date" class="form-control" name="tgl_tahap_2" value="{{ $tgl_tahap_2 }}">
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <select class="form-control" id="tahap_2" onchange="tahap_2JS({{ $d->nopen }})">
                                                                <option value="0" {{ $status_tahap_2 == 0 ? 'selected' : '' }}>belum</option>
                                                                <option value="1" {{ $status_tahap_2 == 1 ? 'selected' : '' }}>sudah</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-sm-6 mt-3">
                                                    <div class="col-sm-6">
                                                        <label><b>Jumlah Tahap 3 :</b></label>
                                                    </div>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control" name="jml_tahap_3" id="jml_3" value="{{ $jml_tahap_3 }}">
                                                    </div>
                                                </div>
                                                <div class="col-sm-6 mt-3">
                                                    <div class="col-sm-6">
                                                        <label><b>Tanggal Tahap 3 :</b></label>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sm-8">
                                                            <input type="date" class="form-control" name="tgl_tahap_3" value="{{ $tgl_tahap_3 }}">
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <select class="form-control" id="tahap_3" onchange="tahap_3JS({{ $d->nopen }})">
                                                                <option value="0" {{ $status_tahap_3 == 0 ? 'selected' : '' }}>belum</option>
                                                                <option value="1" {{ $status_tahap_3 == 1 ? 'selected' : '' }}>sudah</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-sm-6 mt-3">
                                                    <div class="col-sm-6">
                                                        <label><b>Jumlah Tahap 4 :</b></label>
                                                    </div>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control" name="jml_tahap_4" id="jml_4" value="{{ $jml_tahap_3 }}">
                                                    </div>
                                                </div>
                                                <div class="col-sm-6 mt-3">
                                                    <div class="col-sm-6">
                                                        <label><b>Tanggal Tahap 4 :</b></label>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sm-8">
                                                            <input type="date" class="form-control" name="tgl_tahap_4" value="{{ $tgl_tahap_3 }}">
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <select class="form-control" id="tahap_4" onchange="tahap_4JS({{ $d->nopen }})">
                                                                <option value="0" {{ $status_tahap_4 ?? 0 == 0 ? 'selected' : '' }}>belum</option>
                                                                <option value="1" {{ $status_tahap_4 ?? 0 == 1 ? 'selected' : '' }}>sudah</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group row">
                                                <div class="col-sm-6">
                                                    <input type="hidden" class="form-control" readonly name="nopen" value="{{ $d->nopen }}" maxlength="16">
                                                    <input type="hidden" class="form-control" name="nama" value="{{ $d->nama }}">
                                                    <textarea class="form-control" name="alamat" style="resize: none;" hidden>{{ $d->alamat }}</textarea>
                                                    
                                                    <select name="kecamatan" id="kecamatan" class="form-control" hidden>
                                                        <option selected value="{{ $d->kecamatan }}">{{ $data['nm_kec'] ?? '' }}</option>
                                                    </select>
                                                    
                                                    <input type="hidden" class="form-control" name="kelurahan" value="{{ $d->kelurahan }}">
                                                    <input type="hidden" class="form-control" name="rt" value="{{ $d->rt }}">
                                                    <input type="hidden" class="form-control" name="rw" value="{{ $d->rw }}">
                                                    <input type="hidden" class="form-control" name="hp" value="{{ $d->hp }}">
                                                    <input type="hidden" class="form-control" name="telpon" value="{{ $d->telpon }}">
                                                    
                                                    <select name="provinsi" id="provinsi" class="form-control" hidden>
                                                        <option selected value="{{ $d->provinsi }}">{{ $data['nm_prop'] ?? '' }}</option>
                                                    </select>
                                                    <select name="kotakab" id="kotakab" class="form-control" hidden> 
                                                        <option selected value="{{ $d->kotakab }}">{{ $data['nm_kab'] ?? '' }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-sm-6" style="margin:20px 0">
                                                    <button type="submit" class="btn btn-primary btn-round" style="float:left; display:inline-block">Simpan</button>
                                                </div>
                                                <div class="col-sm-6" style="margin:20px 0">
                                                    <a style="float:left; display:inline-block" target="_blank" href="{{ url('pmb/download_surat_pernyataan/' . $d->id) }}" class="btn btn-primary btn-round">
                                                        Surat Pernyataan
                                                    </a>
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
@section('local-js')
    <script type="text/javascript">
        // 1. Setup Token CSRF Global untuk semua AJAX POST
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // 2. Fungsi AJAX Tahap 1 - 4
        function tahap_1JS(nopen) {
            var tahap_1 = $('#tahap_1').val();
            $.ajax({
                url: "{{ url('pmb/tahap_1') }}", 
                method: "POST",
                data: { nopen: nopen, tahap_1: tahap_1 },
                dataType: 'json',
                success: function(data) {
                    alert('Data berhasil disimpan');
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    alert('Gagal menyimpan data.');
                }
            });
        }

        function tahap_2JS(nopen) {
            var tahap_2 = $('#tahap_2').val();
            $.ajax({
                url: "{{ url('pmb/tahap_2') }}",
                method: "POST",
                data: { nopen: nopen, tahap_2: tahap_2 },
                dataType: 'json',
                success: function(data) {
                    alert('Data berhasil disimpan');
                },
                error: function(xhr, status, error) {
                    alert('Gagal menyimpan data.');
                }
            });
        }

        function tahap_3JS(nopen) {
            var tahap_3 = $('#tahap_3').val();
            $.ajax({
                url: "{{ url('pmb/tahap_3') }}",
                method: "POST",
                data: { nopen: nopen, tahap_3: tahap_3 },
                dataType: 'json',
                success: function(data) {
                    alert('Data berhasil disimpan');
                },
                error: function(xhr, status, error) {
                    alert('Gagal menyimpan data.');
                }
            });
        }

        function tahap_4JS(nopen) {
            var tahap_4 = $('#tahap_4').val();
            $.ajax({
                url: "{{ url('pmb/tahap_4') }}",
                method: "POST",
                data: { nopen: nopen, tahap_4: tahap_4 },
                dataType: 'json',
                success: function(data) {
                    alert('Data berhasil disimpan');
                },
                error: function(xhr, status, error) {
                    alert('Gagal menyimpan data.');
                }
            });
        }

        // 3. Fungsi Perhitungan
        function hitung() {
            // Ambil nilai dari input, gunakan || 0 untuk mencegah NaN jika input kosong
            var spi = parseInt($('#spi').val()) || 0;
            var sks = parseInt($('#sks').val()) || 0;
            var kemahasiswaan = parseInt($('#kemahasiswaan').val()) || 0;
            var seragam = parseInt($('#seragam').val()) || 0;
            var operasional = parseInt($('#operasional').val()) || 0;
            var potongan = parseInt($('#potongan').val()) || 0;

            var total = kemahasiswaan + operasional + seragam + spi + sks - potongan;
            
            var thp_1 = total / 2;
            var thp_2 = thp_1 / 2;
            
            console.log("Kemahasiswaan: ", kemahasiswaan);
            console.log("Seragam: ", seragam);
            console.log("SPI: ", spi);
            
            // Memasukkan hasil hitungan ke dalam input
            $('#jml_1').val(thp_1);
            $('#jml_2').val(thp_2);
            $('#jml_3').val(thp_2); // Sesuai kode awal, jml_3 diisi thp_2
            $('#total').text("Total : Rp. " + formatRupiah(total.toString())); // Format angka jadi Rupiah
        }

        // 4. Event Listener untuk Format Rupiah
        // Gunakan pengecekan (if) agar tidak error jika element id="rupiah" tidak ada di DOM
        var inputRupiah = document.getElementById('rupiah');
        if(inputRupiah) {
            inputRupiah.addEventListener('keyup', function(e) {
                this.value = formatRupiah(this.value, '');
            });
        }

        var inputRupiah1 = document.getElementById('rupiah1');
        if(inputRupiah1) {
            inputRupiah1.addEventListener('keyup', function(e) {
                this.value = formatRupiah(this.value, ''); // Bisa langsung pakai formatRupiah utama
            });
        }

        // 5. Fungsi Format Angka ke Rupiah (Ribuan)
        // Cukup satu fungsi saja, tidak perlu dibuat duplikat menjadi formatrupiah1()
        function formatRupiah(angka, prefix) {
            var number_string = angka.replace(/[^,\d]/g, '').toString(),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            return prefix == undefined ? rupiah : (rupiah ? '' + rupiah : '');
        }
    </script>
@endsection