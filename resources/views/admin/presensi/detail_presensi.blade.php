@extends('layouts.default', ['CurrentPage' => $CurrentPage])

@section('content')
		<div class="content-body">
			<div class="container">

				<!-- Row -->
				<div class="row">
					<div class="col-xl-12">
						<div class="mb-4 pb-3">
							<a href="{{ url('master/presensi')}}" class="btn btn-success" title="download template presensi"><i class="fa fa-arrow-left"></i> Kembali</a>
							<a href="{{ url('content-add')}}" class="btn btn-primary" title="download template presensi">Download Template</a>
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
                                                    <th>Pertemuan</th>
                                                    <th>Tanggal</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
											<tbody>
                                                @php $i = 1; @endphp
                                                @foreach($temu as $a)
                                                    <tr>
                                                        <td>{{ $no++ }}</td>
                                                        <td>{{ "Pertemuan Ke-".$i++ }}</td>
                                                        <td>@php  $date = date_create($a->tgl_pertemuan); echo date_format($date,"d/m/Y"); @endphp</td>
                                                        <td>
                                                                <a href="{{ url('master/presensi/input/'.$a->id)}}" class="btn btn-success" title="Input Presensi"><i class="fa fa-pencil"></i></a>
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