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
									<form method="POST" action="" autocomplete="off">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-inline">
                                                    <!-- <input type="text" class="form-control" id="myInput" placeholder="Masukan npp. Cth : 070220002"> -->
                                                    <select name="universitasid[]" id="myInput" class="js-example-basic-single col-sm-12" placeholder="--Nama Universitas--" >
                                                    <?php
                                                        //echo "<option value='0'></option>";
                                                        foreach($pegawai as $row){
                                                            echo "<option value=" . $row->id . "  ";
                                                            
                                                            echo ">" . $row->npp . " - " . $row->nama . "</option>";
                                                        }
                                                    ?> 
                                                </select>

                                                    

                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                            <a href="#" class="btn btn-primary" id="cari" style="height:30px; padding:5px;"> <i class="fa fa-search"></i> Cari </a>
                                            </div>
                                        </div>
                                        </form>
								</div>
							</div>
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
									<div class="result">
										
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
        $("document").ready(function(){
            $(".js-example-basic-single").select2();
            $('#cari').click(function(e){
                e.preventDefault();
                var id = $('#myInput').val();
                $.ajax({
                    url: "{{ url('pegawai/lihat_krm') }}",
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: id
                    },
                    success: function(response) {
                        $('.result').html(response);
                    }
                });
            });
        });
        
    </script>
@endsection