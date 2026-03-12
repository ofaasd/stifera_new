@extends('layouts.default', ['CurrentPage' => $CurrentPage])

@section('content')
		<div class="content-body">
			<div class="container">

				<!-- Row -->
				<div class="row">
					<div class="col-xl-12">
						<div class="mb-4 pb-3">
							<a href="{{ url('pmb_online/daftar_pmb_invalid')}}" class="btn btn-primary"><i class="fa fa-arrow-left"></i>Kembali</a>
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
									<table class="table">
                                        <thead>
                                            <tr>
                                                <th>Semester</th>
                                                <th>Nilai</th>
                                                <th>Bukti</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Semester 1</td>
                                                <td><?php echo $rapor->nilai_smt1?></td>
                                                <td><a href="https://camaba.stifera.ac.id/assets/rapor/<?php echo $rapor->file_smt1?>" target="_blank"><img src='https://camaba.stifera.ac.id/assets/rapor/<?php echo $rapor->file_smt1?>' width="50%"></a></td>
                                            </tr>
                                            <tr>
                                                <td>Semester 2</td>
                                                <td><?php echo $rapor->nilai_smt2?></td>
                                                <td><a href="https://camaba.stifera.ac.id/assets/rapor/<?php echo $rapor->file_smt2?>" target="_blank"><img src='https://camaba.stifera.ac.id/assets/rapor/<?php echo $rapor->file_smt2?>' width="50%"></a></td>
                                            </tr>
                                            <tr>
                                                <td>Semester 3</td>
                                                <td><?php echo $rapor->nilai_smt3?></td>
                                                <td><a href="https://camaba.stifera.ac.id/assets/rapor/<?php echo $rapor->file_smt3?>" target="_blank"><img src='https://camaba.stifera.ac.id/assets/rapor/<?php echo $rapor->file_smt3?>' width="50%"></a></td>
                                            </tr>
                                            <tr>
                                                <td>Semester 4</td>
                                                <td><?php echo $rapor->nilai_smt4?></td>
                                                <td><a href="https://camaba.stifera.ac.id/assets/rapor/<?php echo $rapor->file_smt4?>" target="_blank"><img src='https://camaba.stifera.ac.id/assets/rapor/<?php echo $rapor->file_smt4?>' width="50%"></a></td>
                                            </tr>
                                            <tr>
                                                <td>Semester 5</td>
                                                <td><?php echo $rapor->nilai_smt5?></td>
                                                <td><a href="https://camaba.stifera.ac.id/assets/rapor/<?php echo $rapor->file_smt5?>" target="_blank"><img src='https://camaba.stifera.ac.id/assets/rapor/<?php echo $rapor->file_smt5?>' width="50%"></a></td>
                                            </tr>
                                        </tbody>
                                    </table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
@endsection