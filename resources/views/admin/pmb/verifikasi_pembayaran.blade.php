@extends('layouts.default', ['CurrentPage' => $CurrentPage])

@section('content')
		<div class="content-body">
			<div class="container">

				<!-- Row -->
				<div class="row">
					<div class="col-xl-12">
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
									@if(session('status_delete'))
                                        <div class="alert alert-success">
                                            {{ session('status_delete') }}
                                        </div>
                                    @endif                                    <div class="dt-responsive table-responsive">
                                        <table id="order-table" class="table table-striped table-bordered nowrap">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>No. Pendaftaran</th>
                                                    <th>NISN</th>
                                                    <th>Nama Lengkap</th>
                                                    <th>Jenis Pendaftaran</th>
                                                    <th>Registrasi Pendaftaran</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($daftar_pmb as $a)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $a->nopen }}</td>
                                                        <td>{{ $a->nisn }}</td>
                                                        <td>{{ $a->nama }}</td>
                                                        <td>
                                                            @if($a->jenis_pendaftaran == 1)
                                                                Peserta Didik Baru
                                                            @elseif($a->jenis_pendaftaran == 2)
                                                                Pindahan
                                                            @elseif($a->jenis_pendaftaran == 11)
                                                                Alih Jenjang
                                                            @elseif($a->jenis_pendaftaran == 12)
                                                                Lintas Jalur
                                                            @endif
                                                        </td>
                                                        <td>
                                                            {{-- Menampilkan status pembayaran dari array --}}
                                                            {{ $status_pembayaran2[$a->registrasi_pendaftaran] ?? '-' }}
                                                        </td>
                                                        <td>
                                                            <a href="#" data-bs-toggle="modal" data-bs-target="#verifikasi{{ $a->id }}" class="btn btn-primary btn-mini" title="Verifikasi"><i class="fa fa-edit"></i></a> 
                                                            <a href="#" data-bs-toggle="modal" data-bs-target="#detail{{ $a->id }}" class="btn btn-success btn-mini" title="Lihat Bukti"><i class="fa fa-eye"></i></a> 
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>No</th>
                                                    <th>No. Pendaftaran</th>
                                                    <th>NISN</th>
                                                    <th>Nama Lengkap</th>
                                                    <th>Jenis Pendaftaran</th>
                                                    <th>Registrasi Pendaftaran</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>

                                    @foreach($daftar_pmb as $a)
                                        <div class="modal fade" id="verifikasi{{ $a->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">Verifikasi Registrasi Pendaftaran</h4>
                                                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form method="POST" onsubmit="return false" id="form_edit{{ $a->id }}" enctype="multipart/form-data">
                                                            <div class="form-group row">
                                                                <label class="col-sm-4 col-form-label" for="jenjang">Verifikasi</label>
                                                                <div class="col-sm-6">
                                                                    <select name="verifikasi" id="verifikasi-select{{ $a->id }}" class="form-control">
                                                                        @foreach($status_pembayaran as $key => $value)
                                                                            <option value="{{ $key }}" {{ $key == $a->registrasi_pendaftaran ? 'selected' : '' }}>
                                                                                {{ $value }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                    <input type="hidden" id="nopen{{ $a->id }}" value="{{ $a->nopen }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-12 col-form-label text-right">
                                                                <button type="button" class="btn btn-primary btn-simpan-verifikasi" data-id="{{ $a->id }}" data-bs-dismiss="modal">Simpan</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <script>
                                            
                                        </script>

                                        <div class="modal fade" id="detail{{ $a->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">Detail Registrasi Pendaftaran</h4>
                                                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        @php 
                                                            // Eksekusi Query di View (Sebaiknya di masa depan dipindah ke Controller via Relasi Eloquent)
                                                            $bukti = \Illuminate\Support\Facades\DB::table('bukti_registrasi')
                                                                        ->where('nopen', $a->nopen)
                                                                        ->orderBy('id', 'desc')
                                                                        ->first();
                                                        @endphp

                                                        @if(!$bukti)
                                                            <div class='alert alert-danger'>Belum Ada Bukti Pembayaran</div>
                                                        @else
                                                            <table class="table table-borderless">
                                                                <tr>
                                                                    <td>Pengirim</td>
                                                                    <td>{{ $bukti->an_pengirim }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>No. Rekening Pengirim</td>
                                                                    <td>{{ $bukti->norek_pengirim }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Tanggal Transfer</td>
                                                                    <td>{{ \Carbon\Carbon::parse($bukti->tgl_tf)->format('d-m-Y') }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Bukti Transfer</td>
                                                                    <td>
                                                                        <a href="https://camaba.stifera.ac.id/assets/bukti/{{ $bukti->bukti }}" target="_blank">
                                                                            <img src="https://camaba.stifera.ac.id/assets/bukti/{{ $bukti->bukti }}" style="width: 50%; max-width: 150px; border-radius: 8px;">
                                                                        </a>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        @endif

                                                        <div class="col-sm-12 text-right mt-3">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
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
        // Memastikan DOM sudah siap dan jQuery sudah diload
        document.addEventListener("DOMContentLoaded", function() {
            
            // Event Delegation: Mengikat klik pada semua tombol yang punya class btn-simpan-verifikasi
            $(document).on('click', '.btn-simpan-verifikasi', function() {
                
                // Mengambil ID dari atribut data-id tombol yang diklik
                let id = $(this).data('id');
                
                // Mengambil nilai berdasarkan ID unik masing-class
                let verifikasiVal = $("#verifikasi-select" + id).val();
                let nopenVal = $("#nopen" + id).val();

                $.ajax({
                    url : "{{ url('pmb_online/simpan_verifikasi') }}",
                    method : "POST",
                    data : {
                        "_token": "{{ csrf_token() }}",
                        "verifikasi": verifikasiVal,
                        "nopen": nopenVal
                    },
                    success: function(data){
                        if(data == "1"){
                            // Refresh halaman untuk melihat perubahan
                            location.reload(); 
                        } else {
                            alert("DATA GAGAL DI TAMBAHKAN"); 
                        }
                        $('.modal-backdrop').remove();
                    },
                    error: function(xhr){
                        alert("Terjadi kesalahan pada server!");
                    }
                });
            });
            
        });
        function refresh_table(){
            $.ajax({
                url : "{{url('pmb_online/refresh_verfikasi')}}",
                type : "GET",
                //async : false,
                //dataType : 'json',
                success: function(data){
                $("#tbl_verifikasi").html(data);
                
                }
            });        
        }
    </script>
@endsection