@extends('layouts.default', ['CurrentPage' => $CurrentPage])

@section('content')
		<div class="content-body">
			<div class="container">
				<!-- Statistik Dashboard -->
				<div class="row mb-4">
					<div class="col-xl-3 col-sm-6">
						<div class="card">
							<div class="card-body">
								<div class="d-flex align-items-center justify-content-between">
									<div>
										<h5 class="mb-2">Jumlah Mahasiswa</h5>
										<h3 class="mb-0">{{$var['jumlah_mahasiswa']}}</h3>
									</div>
									<div class="avatar avatar-lg">
										<span class="avatar-title bg-primary-light rounded-circle">
											<i class="fa fa-users text-primary"></i>
										</span>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xl-3 col-sm-6">
						<div class="card">
							<div class="card-body">
								<div class="d-flex align-items-center justify-content-between">
									<div>
										<h5 class="mb-2">Jumlah Dosen</h5>
										<h3 class="mb-0">{{$var['jumlah_dosen']}}</h3>
									</div>
									<div class="avatar avatar-lg">
										<span class="avatar-title bg-success-light rounded-circle">
											<i class="fa fa-book text-success"></i>
										</span>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xl-3 col-sm-6">
						<div class="card">
							<div class="card-body">
								<div class="d-flex align-items-center justify-content-between">
									<div>
										<h5 class="mb-2">Jumlah Matakuliah</h5>
										<h3 class="mb-0">{{$var['matakuliah']}}</h3>
									</div>
									<div class="avatar avatar-lg">
										<span class="avatar-title bg-danger-light rounded-circle">
											<i class="fa fa-graduation-cap text-danger"></i>
										</span>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xl-3 col-sm-6">
						<div class="card">
							<div class="card-body">
								<div class="d-flex align-items-center justify-content-between">
									<div>
										<h5 class="mb-2">Mahasiswa Baru</h5>
										<h3 class="mb-0">{{$var['mahasiswa_baru']}}</h3>
									</div>
									<div class="avatar avatar-lg">
										<span class="avatar-title bg-info-light rounded-circle">
											<i class="fa fa-star text-info"></i>
										</span>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
@endsection
