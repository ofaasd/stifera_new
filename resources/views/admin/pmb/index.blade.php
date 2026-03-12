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
									<i class="fa-sharp fa-solid fa-filter me-2"></i>Filter
								</div>
								<div class="tools">
									<a href="javascript:void(0);" class="expand handle"><i
											class="fal fa-angle-down"></i></a>
								</div>
							</div>
							<div class="cm-content-body form excerpt">
								<div class="card-body">
									<div class="row">
										
										<div class="col-xl-3  col-sm-6 mb-3 mb-xl-0">
											<label class="form-label">Tahun Ajaran</label>
											<select class="form-control default-select h-auto wide"
												aria-label="Default select example" id="tahun">
                                                @for($i=2018; $i <= date('Y')+5; $i++)
                                                    <option value="{{$i}}" {{($year_now == $i) ? "selected":""}}>{{$i}}</option>
                                                @endfor
											</select>
										</div>
										
										<div class="col-xl-3 col-sm-6 align-self-end">
											<div>
												<button class="btn btn-primary me-2" title="Click here to Search"
													type="button" onclick="cari()"><i class="fa fa-filter me-1" ></i>Filter</button>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
                        <div class="mb-4 pb-3">
                            <a href="{{ url('pmb/create')}}" class="btn btn-success">+ Tambah Calon Mahasiswa</a>
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
								<div class="card-body pb-4" id="tampil">
									
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
        function cari(){
            var tahun = $('#tahun').val();
            $.ajax({
            url : "{{url('pmb/req_data/')}}",
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data:{tahun:tahun},
            async: false,
            success: function(data){
                    $('#tampil').html(data);                
                }
            });
        }
    </script>
@endsection