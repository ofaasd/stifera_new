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
						<div class="card">
							<div class="card-header">
								<h4 class="card-title"><i class="fa-solid fa-list-check me-1"></i>{{ $title }}</h4>
							</div>
							<div class="card-body pb-4">
								<div class="table-responsive">
										<table id="example" class="table table-bordered table-striped">
											<thead class="bg-primary text-white text-center">
                                                <tr>
                                                    <th>No</th>
                                                    <th>Kode</th>
                                                    <th style="min-width: 140px; white-space: normal;">Nama</th>
                                                    <th style="max-width: 200px; white-space: normal;">Pengampu</th>
                                                    <th style="max-width: 150px; white-space: normal;">Hari, Jam</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
											<tbody>
                                                @foreach($temu as $a)
                                                    <tr>
                                                        <td>{{ $no++ }}</td>
                                                        <td>{{ $a->kode_mata_kuliah }}</td>
                                                        <td style="white-space: normal; word-break: break-word; min-width: 140px;">{{ $a->nama_mata_kuliah }}</td>
                                                        <td style="white-space: normal; word-break: break-word; max-width: 200px;">{{ $a->nama_dosen }}</td>
                                                        <td style="white-space: normal; word-break: break-word; max-width: 150px;">{{ $a->hari.", ".$a->sesi."  ".$a->ruang }}</td>
                                                        <td class="text-center">
                                                            <a href="{{ url('master/presensi/tanggal/'.$a->id) }}" class="btn btn-success btn-sm" title="Edit Presensi"><i class="fa fa-pencil"></i></a>
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
@endsection

@section('local-js')
<script>
    $(document).ready(function() {
        if ($.fn.DataTable.isDataTable('#example')) {
            $('#example').DataTable().destroy();
        }
        $('#example').DataTable({
            language: {
                paginate: {
                    next: '>',
                    previous: '<'
                }
            }
        });
    });
</script>
@endsection