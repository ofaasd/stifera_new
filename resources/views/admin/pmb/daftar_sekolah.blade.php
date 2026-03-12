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
									<table id="order-table" class="table table-striped table-bordered nowrap">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>NPSN</th>
                                                <th>NSS</th>
                                                <th>Jenis</th>
                                                <th>Nama Sekolah</th>
                                                <th>Alamat</th>
                                                <th>Telepon</th>
                                                <th>Email</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($data as $a)
                                            <tr>
                                                <td>{{ $no++ }}</td>
                                                <td>{{ $a->npsn }}</td>
                                                <td>{{ $a->nss }}</td>
                                                <td>{{ $a->jenis }}</td>
                                                <td>{{ $a->nama }}</td>
                                                <td>{{ $a->alamat }}</td>
                                                <td>{{ $a->telepon }}</td>
                                                <td>{{ $a->email }}</td>
                                            </tr>
                                        @endforeach
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
    <script type="text/javascript">
    $(document).ready(function() {
        $('#order-table').DataTable();
    });
</script>
@endsection