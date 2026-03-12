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
                                                <th>Nopen</th>
                                                <th>NIM</th>
                                                <th>Nama Siswa</th>
                                                <th>#</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $no = 1; @endphp
                                            @foreach($data as $a)
                                                <tr>
                                                    <td>{{ $no++ }}</td>
                                                    <td>{{ $a->nopen }}</td>
                                                    <td>{{ $a->nim }}</td>
                                                    <td>{{ $a->nama }}</td>
                                                    <td>
                                                        <a href="{{ url('pmb/bayar_detail/' . $a->ids) }}" class="btn btn-primary">
                                                            <span><i class="fa fa-edit"></i></span>
                                                        </a>
                                                    </td>
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