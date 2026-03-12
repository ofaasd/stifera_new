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
										<div class="col-xl-3 col-sm-6">
											<label class="form-label">Title</label>
											<input type="text" class="form-control mb-xl-0 mb-3"
												id="exampleFormControlInput1" placeholder="Title">
										</div>
										<div class="col-xl-3  col-sm-6 mb-3 mb-xl-0">
											<label class="form-label">Status</label>
											<select class="form-control default-select h-auto wide"
												aria-label="Default select example">
												<option selected>Select Status</option>
												<option value="1">Published</option>
												<option value="2">Draft</option>
												<option value="3">Trash</option>
												<option value="4">Private</option>
												<option value="5">Pending</option>
											</select>
										</div>
										<div class="col-xl-3 col-sm-6">
											<label class="form-label">Date</label>
											<div class="input-hasicon mb-sm-0 mb-3">
												<input name="datepicker" class="form-control bt-datepicker">
												<div class="icon"><i class="far fa-calendar"></i></div>
											</div>
										</div>
										<div class="col-xl-3 col-sm-6 align-self-end">
											<div>
												<button class="btn btn-primary me-2" title="Click here to Search"
													type="button"><i class="fa fa-filter me-1"></i>Filter</button>
												<button class="btn btn-danger light" title="Click here to remove filter"
													type="button">Remove Filter</button>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
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
									<div class="table-responsive">
										<table class="table">
											<thead>
												<tr>
													<th>S.No</th>
													<th>Title</th>
													<th>Status</th>
													<th>Modified</th>
													<th>Actions</th>
												</tr>
											</thead>
											<tbody>
												
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