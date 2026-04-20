@extends('layouts.fullwidth', ['CurrentPage' => $CurrentPage])

@section('content')
	<div class="authincation h-100">
		<div class="container-fluid h-100">
			<div class="row h-100">
				<div class="col-lg-6 col-md-12 col-sm-12 mx-auto align-self-center">
					<div class="login-form">
						@if($errors->any())
							<div class="alert alert-danger">
								<ul>
									@foreach($errors->all() as $error)
										<li>{{ $error }}</li>
									@endforeach
								</ul>
							</div>
						@endif
						<div class="text-center">
							<img src="{{ asset('images/logo-full.png') }}" class="mb-3 login-sm-logo mx-auto" alt="">
							<h3 class="title">Sign In</h3>
							<p>Sign in to your account to start using SIAKAD STIFERA</p>
						</div>
						<form action="{{ url('login') }}" method="POST">
							@csrf
							<div class="mb-4">
								<label class="mb-1">Email<span class="text-danger"> *</span></label>
								<input type="email" class="form-control form-control" name="email" placeholder="hello@example.com">
							</div>
							<div class="mb-4 position-relative">
								<label class="mb-1">Password<span class="text-danger"> *</span></label>
								<input type="password" id="dz-password" name="password" class="form-control form-control"
									placeholder="Password">
								<span class="show-pass eye">

									<i class="fa fa-eye-slash"></i>
									<i class="fa fa-eye"></i>

								</span>
							</div>
							<div class="form-row d-flex justify-content-between mt-4 mb-2">
								<div class="mb-4">
									<div class="form-check custom-checkbox mb-3">
										<input type="checkbox" class="form-check-input" id="customCheckBox1">
										<label class="form-check-label mt-1" for="customCheckBox1">Remember my
											preference</label>
									</div>
								</div>
								<div class="mb-4">
									<a href="{{ route('pegawai.password.reset.form') }}" class="btn-link text-primary">Forgot
										Password?</a>
								</div>
							</div>
							<div class="text-center mb-4 d-grid gap-2">
								<button type="submit" class="btn btn-primary">Sign In</button>
								<a href="{{ route('login') }}" class="btn btn-outline-secondary">Kembali ke Login Portal</a>
							</div>
							<!-- <h6 class="login-title"><span class="px-3">Other Login Method</span></h6>

							<div class="mb-3">
								<ul class="d-flex align-self-center justify-content-center gap-3 list-unstyled">
									<li>
										<a target="_blank" href="https://www.facebook.com/"
											class="btn btn-primary">Login Pegawai</a>
									</li>
									<li>
										<a target="_blank" href="https://www.facebook.com/"
											class="btn btn-primary">Login Mahasiswa</a>
									</li>
									
								</ul>
							</div>
							<p class="text-center">Not registered?
								<a class="btn-link text-primary" href="page-register">Register</a>
							</p> -->
						</form>
					</div>
				</div>
				<div class="col-xl-6 col-lg-6">
					<div class="pages-left h-100">
						<div class="login-content">
							<a href="{{ url('index') }}"><img src="{{ asset('images/logo-full.png') }}" class="mb-3" alt=""></a>
							<p>Your true value is determined by how much more you give in value than you take in
								payment. ...</p>
						</div>
						<div class="login-media text-center">
							<img src="{{ asset('images/login.png') }}" alt="">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

@endsection