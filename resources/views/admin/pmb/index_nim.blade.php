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
						
					</div>
				</div>
			</div>
		</div>
@endsection
@section('local-js')
    <script>
        function cari(){
            var tahun = $('#tahun').val();
            window.location.href= "{{url('pmb/preview_generate_nim/')}}/"+tahun;
        }
    </script>
@endsection