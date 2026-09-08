@extends('layouts.default', ['CurrentPage' => $CurrentPage])

@section('content')
		<div class="content-body">
			<div class="container">

				<!-- Row -->
				<div class="row">
					<div class="col-xl-12">
                        <div class="mb-4 pb-3">
							<a href="{{ url('pegawai/create')}}" class="btn btn-primary">Tambah {{$title}}</a>
						</div>
						<div class="card">
							<div class="card-header">
								<h4 class="card-title"><i class="fa-solid fa-file-lines me-1"></i>Daftar {{ $title }}</h4>
							</div>
							<div class="card-body pb-4">
									<div class="table-responsive">
                                            <table id="order-table" class="table table-bordered table-striped">
											<thead class="bg-primary text-white text-center">
                                                <tr>
                                                    <th style="width:50px;">No</th>
                                                    <th style="width:120px;">NIP - NIDN</th>
                                                    <th style="min-width:160px; white-space:normal;">Nama Pegawai</th>
                                                    <th style="width:120px;">Status</th>
                                                    <th style="max-width:150px; white-space:normal;">Homebase</th>
                                                    <th style="max-width:120px; white-space:normal;">Jabatan Fungsional</th>
                                                    <th>Aksi</th>
                                                </tr> 
                                            </thead>
                                            <tbody>
                                            <?php $no = 1;
                                            foreach($pegawai as $a){?>
                                                <tr>
                                                    <td class="text-center"><?php echo $no++ ?></td>
                                                    <td><?php echo $a->pegawai->npp ?? '' ?> - <?php echo  $a->nidn ?></td>
                                                    <td style="white-space: normal; word-break: break-word; min-width: 160px;"><?php echo  $a->nama_lengkap?></td>
                                                    <td class="text-center">
                                                        <?php echo  $a->nama_jenis ?>
                                                    </td>
                                                    <td style="white-space: normal; word-break: break-word; max-width: 150px;">
                                                        <small><?php echo  $a->nama_homebase ?? '-' ?></small>
                                                    </td>
                                                    <td style="white-space: normal; word-break: break-word; max-width: 120px;"><?php echo  $list_jabfung[$a->jabatan_fungsional_sekarang] ?? '' ?> </td>
                                                    <td class="text-center">
                                                        <a href="{{url('pegawai/'.$a->id_pegawai.'/edit')}}" title="Detail Pegawai" class="btn btn-info btn-sm"><i class="fa fa-eye"></i></a> 
                                                        <a href="{{url('pegawai/'.$a->id_pegawai.'/edit')}}" title="Edit Pegawai" class="btn btn-primary btn-sm"><i class="fa fa-pencil"></i></a> 
                                                        <a href="{{url('pegawai/reset_password/'.$a->npp)}}" title="Reset to Default Password" onclick="return confirm('Yakin Reset Password Pegawai?')" class="btn btn-success btn-sm"><i class="fa fa-key"></i></a> 
                                                        <a href="{{url('pegawai/delete_pegawai/'.$a->npp)}}" onclick="return confirm('Yakin Delete Data Pegawai?')" title="Delete Data Pegawai" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></a>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>No</th>
                                                    <th>NIP - NIDN</th>
                                                    <th>Nama Pegawai</th>
                                                    <th>Status</th>
                                                    <th>Homebase</th>
                                                    <th>Jabatan Fungsional</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </tfoot>
										</table>
										
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
            $('#order-table').DataTable({
                pageLength: 25
            });
        });
    </script>
@endsection