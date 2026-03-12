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
						<div class="filter cm-content-box box-primary">
							<div class="content-title SlideToolHeader">
								<div class="cpa">
									<i class="fa-solid fa-file-lines me-1"></i>Daftar {{ $title }}
								</div>
								<div class="tools">
									<a href="javascript:void(0);" class="expand handle"><i
											class="fal fa-angle-down"></i></a>
								</div>
							</div>
							<div class="cm-content-body form excerpt">
								<div class="card-body pb-4">
									<div class="table-responsive">
										<table class="table">
											<thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>NIP - NIDN</th>
                                                    <th>Nama Pegawai</th>
                                                    <th>Status Homebase</th>
                                                    <th>Jabatan Fungsional</th>
                                                    <!-- <th>Alamat</th>
                                                    <th>Email</th>
                                                    <th>No. Telp / Hp</th> -->
                                                    <th></th>
                                                </tr> 
                                            </thead>
                                            <tbody>
                                            <?php $no = 1;
                                            foreach($pegawai as $a){?>
                                                <tr>
                                                    <td><?php echo $no++ ?></td>
                                                    <td><?php echo $a->pegawai->npp ?? '' ?> - <?php echo  $a->nidn ?></td>
                                                    <td><?php echo  $a->nama_lengkap?></td>
                                                    <td><?php echo  $a->nama_jenis ?></td>
                                                    <td><?php echo  $list_jabfung[$a->jabatan_fungsional_sekarang] ?? '' ?> </td>
                                                    <!-- <td><?php echo  $a->alamat ?></td>
                                                    <td><?php echo  $a->email1 ?> <br /> <?php echo  $a->email2?></td>
                                                    <td><?php echo  $a->notelp ?> <br/> <?php echo  $a->nohp ?></td> -->
                                                    <td><a href="{{url('pegawai/'.$a->id_pegawai.'/edit')}}" title="Edit Pegawai" class="btn btn-primary"><i class="fe fe-pencil"></i></a> <a href="{{url('pegawai/reset_password/'.$a->npp)}}" title="Reset to Default Password" onclick="return confirm('Yakin Reset Password Pegawai?')" class="btn btn-success"><i class="fe fe-key"></i></a>  <a href="{{url('pegawai/delete_pegawai/'.$a->npp)}}" onclick="return confirm('Yakin Delete Data Pegawai?')" title="Delete Data Pegawai" class="btn btn-danger"><i class="fe fe-trash"></i></a></td>
                                                </tr>
                                            <?php } ?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>No</th>
                                                    <th>NIP - NIDN</th>
                                                    <th>Nama Pegawai</th>
                                                    <th>Status Homebase</th>
                                                    <th>Jabatan Fungsional</th>
                                                    <!-- <th>Alamat</th>
                                                    <th>Email</th>
                                                    <th>No. Telp / Hp</th> -->
                                                    <th></th>
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
		</div>
@endsection