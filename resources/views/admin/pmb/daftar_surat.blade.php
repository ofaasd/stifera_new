@extends('layouts.default', ['CurrentPage' => $CurrentPage])
@section('local-css')
    <style>
        .cm-content-box .cm-content-body .table .btn{
            width:auto;
            padding-left:10px ;
            padding-right:10px ;
        }
    </style>
@endsection
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
								<div class="card-body pb-4" id="tampil">
									<div class="table-responsive">
										<table id="order-table" class="table table-striped table-bordered nowrap">
											<thead>
												<tr>
													<th>No</th>
													<th>No. Pendaftaran</th>
													<th>NISN</th>
													<th>Nama Lengkap</th>
													<th>Jenis Pendaftaran</th>
													<th>Aksi</th>
												</tr>
											</thead>
											<tbody>
												@php $no = 1; @endphp
												@foreach($daftar_surat as $a)
													<tr>
														<td>{{ $no++ }}</td>
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
															<button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#myModal{{ $a->id }}">
																Surat Pengumuman
															</button>
														</td>
													</tr>
												@endforeach
											</tbody>
										</table>

										@foreach($daftar_surat as $a)
											<div class="modal fade" id="myModal{{ $a->id }}" tabindex="-1" role="dialog" aria-hidden="true">
												<div class="modal-dialog">
													<div class="modal-content">
														<div class="modal-header">
															<h4 class="modal-title">{{ $a->nama }}</h4>
															<button type="button" class="close" data-bs-dismiss="modal">&times;</button>
														</div>
														<div class="modal-body">
															<form action="{{ url('pmb/surat_pengumuman') }}" method="post">
																@csrf <input type="hidden" name="id" value="{{ $a->id }}">
																
																<p>Hasil Calon Mahasiswa : </p>
																<select class="form-control mb-3" name="status" style="width: 100%;" required>
																	<option selected disabled value="">-- Pilih Hasil --</option>
																	<option value="DITERIMA">DITERIMA</option>
																	<option value="TIDAK DITERIMA">TIDAK DITERIMA</option>
																</select>
																
																<button type="submit" class="btn btn-info mt-2">Cetak</button>
															</form>
														</div>
														<div class="modal-footer">
															<button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
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
		</div>
@endsection
@section('local-js')
    <script type="text/javascript">
    $(document).ready(function() {
        $('#order-table').DataTable();
    });
</script>
@endsection