@extends('layouts.default', ['CurrentPage' => $CurrentPage])

@section('content')
		<div class="content-body">
			<div class="container">

				<!-- Row -->
				<div class="row">
					<div class="col-xl-12">
						<div class="mb-4 pb-3">
							
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
									<div class="table-responsive datatables">
										<table id="example" class="display table">
											<thead>
												<tr>
													<th>No.</th>
													<th>NIM</th>
													<th>Nama Lengkap</th>
													<th>Email</th>
													<th>Status</th>
													<th>Actions</th>
												</tr>
											</thead>
											<tbody>
                                                @foreach($data as $row)
                                                    <tr>
                                                        <td>{{ $no++ }}</td>
                                                        <td>{{ $row->nim }}</td>
                                                        <td>{{ $row->nama }}</td>
                                                        <td>{{ $row->email }}</td>
                                                        <td>{{ $status[$row->status] ?? '' }}</td>
                                                        <td><a href="{{url('master/khs/khs_detail/'.$row->id)}}" title="Lihat KHS Detail" class="btn btn-primary"><i class="fa fa-eye"></i></a> </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
											</tbody>
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