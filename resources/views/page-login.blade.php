@extends('layouts.fullwidth', ['CurrentPage' => $CurrentPage])

@section('content')
	<style>
		.portal-login {
			min-height: 100vh;
			padding: 40px 0;
			background: radial-gradient(circle at top right, #f3f9ff 0%, #e7f1ff 35%, #f8fbff 100%);
		}

		.portal-wrapper {
			max-width: 980px;
			margin: 0 auto;
		}

		.portal-header {
			text-align: center;
			margin-bottom: 24px;
		}

		.portal-header img {
			height: 52px;
			object-fit: contain;
			margin-bottom: 18px;
		}

		.portal-title {
			font-weight: 700;
			font-size: 32px;
			color: #0d2e5b;
			margin-bottom: 10px;
		}

		.portal-subtitle {
			font-size: 16px;
			color: #50607a;
			max-width: 680px;
			margin: 0 auto;
		}

		.portal-cards {
			display: grid;
			grid-template-columns: repeat(3, minmax(0, 1fr));
			gap: 18px;
		}

		.portal-card {
			display: block;
			padding: 26px 24px;
			border-radius: 18px;
			background: #ffffff;
			border: 1px solid #dbe8f8;
			box-shadow: 0 8px 24px rgba(16, 69, 129, 0.08);
			text-decoration: none;
			transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
			height: 100%;
		}

		.portal-card:hover,
		.portal-card:focus {
			transform: translateY(-4px);
			border-color: #70a6e8;
			box-shadow: 0 14px 28px rgba(16, 69, 129, 0.14);
			text-decoration: none;
		}

		.portal-icon {
			width: 60px;
			height: 60px;
			display: flex;
			align-items: center;
			justify-content: center;
			border-radius: 14px;
			font-size: 28px;
			margin-bottom: 16px;
			color: #ffffff;
		}

		.portal-admin .portal-icon {
			background: linear-gradient(135deg, #153c73, #2f66ac);
		}

		.portal-pegawai .portal-icon {
			background: linear-gradient(135deg, #1f7a4c, #37a366);
		}

		.portal-mahasiswa .portal-icon {
			background: linear-gradient(135deg, #9d5c06, #d3871d);
		}

		.portal-label {
			font-size: 22px;
			font-weight: 700;
			margin-bottom: 10px;
			color: #13355f;
		}

		.portal-desc {
			font-size: 14px;
			line-height: 1.6;
			color: #53647b;
			margin-bottom: 14px;
		}

		.portal-action {
			font-weight: 600;
			font-size: 14px;
			color: #0f4f99;
		}

		@media (max-width: 992px) {
			.portal-cards {
				grid-template-columns: repeat(2, minmax(0, 1fr));
			}
		}

		@media (max-width: 640px) {
			.portal-login {
				padding: 24px 0;
			}

			.portal-title {
				font-size: 26px;
			}

			.portal-cards {
				grid-template-columns: 1fr;
			}
		}
	</style>

	<div class="portal-login">
		<div class="container-fluid">
			<div class="portal-wrapper">
				<div class="portal-header">
					<img src="{{ asset('images/logo-full.png') }}" alt="Logo STIFERA">
					<h1 class="portal-title">Portal Login SIAKAD STIFERA</h1>
					<p class="portal-subtitle">Pilih jenis akun Anda untuk masuk ke sistem. Klik salah satu box di bawah ini untuk diarahkan ke halaman login sesuai peran.</p>
				</div>

				<div class="portal-cards">
					<a href="{{ route('admin.login') }}" class="portal-card portal-admin">
						<div class="portal-icon">
							<i class="fa fa-shield"></i>
						</div>
						<h2 class="portal-label">Login Admin</h2>
						<p class="portal-desc">Akses panel administrasi untuk mengelola data akademik, pengaturan sistem, dan monitoring aktivitas.</p>
						<span class="portal-action">Masuk sebagai Admin <i class="fa fa-arrow-right ms-1"></i></span>
					</a>

					<a href="{{ route('pegawai.login') }}" class="portal-card portal-pegawai">
						<div class="portal-icon">
							<i class="fa fa-briefcase"></i>
						</div>
						<h2 class="portal-label">Login Pegawai</h2>
						<p class="portal-desc">Masuk untuk mengelola layanan akademik, administrasi pegawai, dan proses operasional harian kampus.</p>
						<span class="portal-action">Masuk sebagai Pegawai <i class="fa fa-arrow-right ms-1"></i></span>
					</a>

					<a href="{{ url('/mahasiswa/login') }}" class="portal-card portal-mahasiswa">
						<div class="portal-icon">
							<i class="fa fa-graduation-cap"></i>
						</div>
						<h2 class="portal-label">Login Mahasiswa</h2>
						<p class="portal-desc">Akses informasi perkuliahan, KRS, KHS, jadwal, dan layanan akademik mahasiswa melalui portal ini.</p>
						<span class="portal-action">Masuk sebagai Mahasiswa <i class="fa fa-arrow-right ms-1"></i></span>
					</a>
				</div>
			</div>
		</div>
	</div>

@endsection