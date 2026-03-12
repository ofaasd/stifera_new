@extends('layouts.default', ['CurrentPage' => $CurrentPage])

@section('content')
		<div class="content-body">
			<div class="container">

				<!-- Row -->
				<div class="row">
					<div class="col-xl-12">
						<div class="mb-4 pb-3">
							<a href="{{ url('content-add')}}" class="btn btn-primary">Add {{$title}}</a>
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
                                                    <th>No</th>
                                                    <th>Kode</th>
                                                    <th>Nama</th>
                                                    <th>Pengampu</th>
                                                    <th>Hari, Jam</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
											<tbody>
                                                @foreach($temu as $a)
                                                    <tr>
                                                        <td>{{ $no++ }}</td>
                                                        <td>{{ $a->kode_mata_kuliah }}</td>
                                                        <td>{{ $a->nama_mata_kuliah }}</td>
                                                        <td>{{ $a->nama_dosen }}</td>
                                                        <td>{{ $a->hari.", ".$a->sesi."  ".$a->ruang; }}</td>
                                                        <td>
                                                            <a href="{{ url('master/presensi/tanggal/'.$a->id); }}" class="btn btn-success" title="Edit Presensi"><i class="fa fa-pencil"></i></a>
                                                        </td>
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