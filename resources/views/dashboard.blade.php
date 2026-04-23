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

				<div class="row">
					<div class="col-xl-8">
						<div class="card">
							<div class="card-body">
								<h5 class="mb-3">Statistik PMB</h5>
								<div style="height: 400px;">
									<canvas id="pmbPerTahunChart"></canvas>
								</div>
								<div class="small text-muted mt-2">Jumlah pendaftar PMB per tahun ajaran (berdasarkan kolom angkatan).</div>
							</div>
						</div>
					</div>
					<div class="col-xl-4">
						<div class="card">
							<div class="card-body">
								<h5 class="mb-3">Saran dan Masukan</h5>
								@if(($var['masukan_terbaru']->count() ?? 0) === 0)
									<div class="text-muted">Belum ada saran dan masukan.</div>
								@else
									@foreach($var['masukan_terbaru'] as $item)
										<div class="border-bottom pb-2 mb-2">
											<div class="fw-semibold">{{ trim((string) ($item->nama_mahasiswa ?? 'Mahasiswa')) }}</div>
											<div class="small text-muted">{{ (string) ($item->saran ?? '-') }}</div>
											<div class="small text-secondary mt-1">
												<i class="fa fa-clock me-1"></i>
												{{ !empty($item->tanggal) ? \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d') : '-' }}
											</div>
										</div>
									@endforeach
								@endif
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
@endsection

@section('local-js')
<script>
	(function () {
		const canvas = document.getElementById('pmbPerTahunChart');
		if (!canvas || typeof Chart === 'undefined') {
			return;
		}

		const rawLabels = @json(($var['pmb_per_tahun'] ?? collect())->pluck('tahun')->values());
		const labels = rawLabels.map((year) => {
			const yearNumber = Number(year);
			if (Number.isFinite(yearNumber) && yearNumber > 0) {
				return `${yearNumber}/${yearNumber + 1}`;
			}

			return String(year ?? '-');
		});
		const values = @json(($var['pmb_per_tahun'] ?? collect())->pluck('total')->values());

		new Chart(canvas, {
			type: 'bar',
			data: {
				labels: labels,
				datasets: [{
					label: 'Jumlah Pendaftar',
					data: values,
					backgroundColor: 'rgba(0, 87, 187, 0.20)',
					borderColor: '#0057bb',
					borderWidth: 1.5
				}]
			},
			options: {
				responsive: true,
				maintainAspectRatio: false,
				plugins: {
					legend: {
						display: false
					}
				},
				scales: {
					y: {
						beginAtZero: true,
						ticks: {
							precision: 0
						}
					}
				}
			}
		});
	})();
</script>
@endsection
