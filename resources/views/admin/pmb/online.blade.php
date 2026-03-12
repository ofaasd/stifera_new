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
                                    @if(session('success'))
                                        <div class="alert alert-success">
                                            {{ session('success') }}
                                        </div>
                                    @endif
                                    @if(session('error'))
                                        <div class="alert alert-danger">
                                            {{ session('error') }}
                                        </div>
                                    @endif
                                    <table id="order-table" class="table table-striped table-bordered nowrap">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>No. Pendaftaran</th>
                                                <th>NISN</th>
                                                <th>Nama Lengkap</th>
                                                <th>Jenis Pendaftaran</th>
                                                <th>Tahun Angkatan</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($daftar_pmb as $a)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ empty($a->nopen) ? "Belum Validasi" : $a->nopen }}</td>
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
                                                    <td>{{ $a->angkatan }}</td>
                                                    <td>
                                                        <button type="button" class="btn btn-info btn-mini" data-bs-toggle="modal" data-bs-target="#myModal{{ $a->user_id }}" title="Bayar Pendaftaran">
                                                            <i class="fa fa-money-bill"></i>
                                                        </button>
                                                        <a href="{{ url('pmb_online/lihat_rapor/' . $a->user_id) }}" class="btn btn-primary btn-mini" title="Lihat Rapor">
                                                            <i class="fa fa-eye"></i> 
                                                        </a> 
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
                                                <th>Tahun Angkatan</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </tfoot>
                                    </table>

                                    @foreach($daftar_pmb as $a)
                                        <div class="modal fade" id="myModal{{ $a->user_id }}" role="dialog">
                                            <div class="modal-dialog">
                                            
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                        <h4 class="modal-title">Verifikasi Pendaftaran</h4>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="{{ url('pmb_online/saveVerifikasi') }}" method="post" enctype="multipart/form-data">
                                                            @csrf @php 
                                                                // Mengambil data Rekening dan Bukti Registrasi menggunakan DB Facades Laravel
                                                                // Asumsi $rek sudah dipassing dari controller, jika belum bisa query seperti ini:
                                                                
                                                                
                                                                $qr = \Illuminate\Support\Facades\DB::table('bukti_registrasi')
                                                                        ->where('user_id', $a->user_id)
                                                                        ->first();

                                                                // Set default value jika data tidak ditemukan
                                                                $id_rekening    = $qr ? $qr->id_rekening : '';
                                                                $norek_pengirim = $qr ? $qr->norek_pengirim : '';
                                                                $an_pengirim    = $qr ? $qr->an_pengirim : '';
                                                                $tgl_tf         = $qr ? $qr->tgl_tf : '';
                                                                $bukti          = $qr ? $qr->bukti : '';
                                                            @endphp
                                                            
                                                            <input type="hidden" name="user_id" value="{{ $a->user_id }}">
                                                            <input type="hidden" name="nopen" value="">
                                                            
                                                            <table class="table table-borderless">
                                                                <tr>
                                                                    <td><label>Transfer Ke Rekening :</label></td>
                                                                    <td>
                                                                        <select name="id_bank" class="form-control" style="width: 200px;" required>
                                                                            {{-- Looping daftar bank dari tabel master_rekening --}}
                                                                            @foreach ($rek as $row)
                                                                                <option value="{{ $row->id }}" {{ $id_rekening == $row->id ? 'selected' : '' }}>
                                                                                    {{ $row->nama_bank }}
                                                                                </option>   
                                                                            @endforeach
                                                                        </select>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td><label>Nomer Rekening Pengirim : </label></td>
                                                                    <td>
                                                                        <input type="text" name="norek_pengirim" class="form-control" style="width: 200px;" value="{{ $norek_pengirim }}" required>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td><label>Atas Nama : </label></td>
                                                                    <td>
                                                                        <input type="text" name="an_pengirim" class="form-control" style="width: 200px;" value="{{ $an_pengirim }}" required>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td><label>Tanggal Transfer : </label></td>
                                                                    <td>
                                                                        <input type="date" name="tgl_tf" class="form-control" style="width: 200px;" value="{{ $tgl_tf }}" required>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td><label>Bukti Transfer : </label></td>
                                                                    <td>
                                                                        {{-- Hilangkan required jika bukti sifatnya opsional untuk diupdate --}}
                                                                        <input type="file" name="bukti" class="form-control-file" style="width: 200px;">
                                                                        <br>
                                                                        
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan=2>
                                                                        @if ($bukti != '')
                                                                            <a href="{{ asset('assets/bukti/' . $bukti) }}" target="_blank" class="btn btn-success" style="width:auto;">Lihat Bukti Bayar Saat Ini</a>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                                
                                                            </table>
                                                            <button type="submit" class="btn btn-info btn-sm" style="width:auto;">
                                                                <span><i class="fa fa-save"></i></span> Simpan
                                                            </button>
                                                        </form>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
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
    <script type="text/javascript">
    $(document).ready(function() {
        $('#order-table').DataTable();
    });
</script>
@endsection