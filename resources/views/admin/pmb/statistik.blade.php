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
									<h5><b>Jumlah Pendaftar</b></h5>
                                    <div style="width: 800px; margin: 0px auto;">
                                        <canvas id="myChart"></canvas>
                                    </div>
                                    <hr>
                                    
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <h6><b>Jumlah Pendaftar PMDP</b></h6>
                                            <div style="width: 300px; margin: 0px auto;">
                                                <canvas id="myChartPMDP"></canvas>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <h6><b>Jumlah Pendaftar Kerjasama</b></h6>
                                            <div style="width: 300px; margin: 0px auto;">
                                                <canvas id="myChartKerjasama"></canvas>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <h6><b>Jumlah Pendaftar Umum</b></h6>
                                            <div style="width: 300px; margin: 0px auto;">
                                                <canvas id="myChartUmum"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <h6><b>Jumlah Pendaftar Laki - Laki</b></h6>
                                            <div style="width: 500px; margin: 0px auto;">
                                                <canvas id="myChartPria"></canvas>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <h6><b>Jumlah Pendaftar Perempuan</b></h6>
                                            <div style="width: 500px; margin: 0px auto;">
                                                <canvas id="myChartPerempuan"></canvas>
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
@php
    // OPTIMASI: Kita mengambil data count sekaligus dengan method bawaan Laravel.
    // Catatan: Sebaiknya blok @php ini dipindahkan ke Controller di masa mendatang.
    
    // 1. Data Keseluruhan Pendaftar
    $total_2020 = \Illuminate\Support\Facades\DB::table('pmb_peserta')->where('angkatan', 2020)->count();
    $total_2021 = \Illuminate\Support\Facades\DB::table('pmb_peserta')->where('angkatan', 2021)->count();
    $total_2022 = \Illuminate\Support\Facades\DB::table('pmb_peserta')->where('angkatan', 2022)->count();
    $total_2023 = \Illuminate\Support\Facades\DB::table('pmb_peserta')->where('angkatan', 2023)->count();
    $total_2024 = \Illuminate\Support\Facades\DB::table('pmb_peserta')->where('angkatan', 2024)->count();
    $total_2025 = \Illuminate\Support\Facades\DB::table('pmb_peserta')->where('angkatan', 2025)->count();

    // 2. Data PMDP (Jalur = 1)
    $pmdp_2020 = \Illuminate\Support\Facades\DB::table('pmb_peserta')->where('jalur_pendaftaran', 1)->where('angkatan', 2020)->count();
    $pmdp_2021 = \Illuminate\Support\Facades\DB::table('pmb_peserta')->where('jalur_pendaftaran', 1)->where('angkatan', 2021)->count();
    $pmdp_2022 = \Illuminate\Support\Facades\DB::table('pmb_peserta')->where('jalur_pendaftaran', 1)->where('angkatan', 2022)->count();
    $pmdp_2023 = \Illuminate\Support\Facades\DB::table('pmb_peserta')->where('jalur_pendaftaran', 1)->where('angkatan', 2023)->count();
    $pmdp_2024 = \Illuminate\Support\Facades\DB::table('pmb_peserta')->where('jalur_pendaftaran', 1)->where('angkatan', 2024)->count();
    $pmdp_2025 = \Illuminate\Support\Facades\DB::table('pmb_peserta')->where('jalur_pendaftaran', 1)->where('angkatan', 2025)->count();

    // 3. Data Kerjasama (Jalur = 2)
    $kerja_2020 = \Illuminate\Support\Facades\DB::table('pmb_peserta')->where('jalur_pendaftaran', 2)->where('angkatan', 2020)->count();
    $kerja_2021 = \Illuminate\Support\Facades\DB::table('pmb_peserta')->where('jalur_pendaftaran', 2)->where('angkatan', 2021)->count();
    $kerja_2022 = \Illuminate\Support\Facades\DB::table('pmb_peserta')->where('jalur_pendaftaran', 2)->where('angkatan', 2022)->count();
    $kerja_2023 = \Illuminate\Support\Facades\DB::table('pmb_peserta')->where('jalur_pendaftaran', 2)->where('angkatan', 2023)->count();
    $kerja_2024 = \Illuminate\Support\Facades\DB::table('pmb_peserta')->where('jalur_pendaftaran', 2)->where('angkatan', 2024)->count();
    $kerja_2025 = \Illuminate\Support\Facades\DB::table('pmb_peserta')->where('jalur_pendaftaran', 2)->where('angkatan', 2025)->count();

    // 4. Data Umum (Jalur = 3)
    $umum_2020 = \Illuminate\Support\Facades\DB::table('pmb_peserta')->where('jalur_pendaftaran', 3)->where('angkatan', 2020)->count();
    $umum_2021 = \Illuminate\Support\Facades\DB::table('pmb_peserta')->where('jalur_pendaftaran', 3)->where('angkatan', 2021)->count();
    $umum_2022 = \Illuminate\Support\Facades\DB::table('pmb_peserta')->where('jalur_pendaftaran', 3)->where('angkatan', 2022)->count();
    $umum_2023 = \Illuminate\Support\Facades\DB::table('pmb_peserta')->where('jalur_pendaftaran', 3)->where('angkatan', 2023)->count();
    $umum_2024 = \Illuminate\Support\Facades\DB::table('pmb_peserta')->where('jalur_pendaftaran', 3)->where('angkatan', 2024)->count();
    $umum_2025 = \Illuminate\Support\Facades\DB::table('pmb_peserta')->where('jalur_pendaftaran', 3)->where('angkatan', 2025)->count();

    // 5. Data Laki-Laki (JK = 1)
    $pria_2020 = \Illuminate\Support\Facades\DB::table('pmb_peserta')->where('jk', 1)->where('angkatan', 2020)->count();
    $pria_2021 = \Illuminate\Support\Facades\DB::table('pmb_peserta')->where('jk', 1)->where('angkatan', 2021)->count();
    $pria_2022 = \Illuminate\Support\Facades\DB::table('pmb_peserta')->where('jk', 1)->where('angkatan', 2022)->count();
    $pria_2023 = \Illuminate\Support\Facades\DB::table('pmb_peserta')->where('jk', 1)->where('angkatan', 2023)->count();
    $pria_2024 = \Illuminate\Support\Facades\DB::table('pmb_peserta')->where('jk', 1)->where('angkatan', 2024)->count();
    $pria_2025 = \Illuminate\Support\Facades\DB::table('pmb_peserta')->where('jk', 1)->where('angkatan', 2025)->count();

    // 6. Data Perempuan (JK = 2)
    $wnt_2020 = \Illuminate\Support\Facades\DB::table('pmb_peserta')->where('jk', 2)->where('angkatan', 2020)->count();
    $wnt_2021 = \Illuminate\Support\Facades\DB::table('pmb_peserta')->where('jk', 2)->where('angkatan', 2021)->count();
    $wnt_2022 = \Illuminate\Support\Facades\DB::table('pmb_peserta')->where('jk', 2)->where('angkatan', 2022)->count();
    $wnt_2023 = \Illuminate\Support\Facades\DB::table('pmb_peserta')->where('jk', 2)->where('angkatan', 2023)->count();
    $wnt_2024 = \Illuminate\Support\Facades\DB::table('pmb_peserta')->where('jk', 2)->where('angkatan', 2024)->count();
    $wnt_2025 = \Illuminate\Support\Facades\DB::table('pmb_peserta')->where('jk', 2)->where('angkatan', 2025)->count();
@endphp
@section('local-js')
<script type="text/javascript">
    const chartLabels = ["2020", "2021", "2022", "2023", "2024", "2025"];
    const bgColor = [
        'rgba(255, 99, 132, 0.2)',
        'rgba(54, 162, 235, 0.2)',
        'rgba(255, 206, 86, 0.2)',
        'rgba(75, 192, 192, 0.2)',
        'rgba(153, 102, 255, 0.2)',
        'rgba(255, 159, 64, 0.2)'
    ];
    const brColor = [
        'rgba(255,99,132,1)',
        'rgba(54, 162, 235, 1)',
        'rgba(255, 206, 86, 1)',
        'rgba(75, 192, 192, 1)',
        'rgba(153, 102, 255, 1)',
        'rgba(255, 159, 64, 1)'
    ];
    const chartOptions = {
        scales: {
            yAxes: [{
                ticks: { beginAtZero:true }
            }]
        }
    };

    // 1. Chart Total Pendaftar
    var ctx = document.getElementById("myChart").getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: chartLabels,
            datasets: [{
                label: 'Jumlah Pendaftar',
                data: [
                    {{ $total_2020 }}, {{ $total_2021 }}, {{ $total_2022 }}, 
                    {{ $total_2023 }}, {{ $total_2024 }}, {{ $total_2025 }}
                ],
                backgroundColor: bgColor,
                borderColor: brColor,
                borderWidth: 1
            }]
        },
        options: chartOptions
    });

    // 2. Chart PMDP
    var ctxPMDP = document.getElementById("myChartPMDP").getContext('2d');
    var myChartPMDP = new Chart(ctxPMDP, {
        type: 'bar',
        data: {
            labels: chartLabels,
            datasets: [{
                label: 'PMDP',
                data: [
                    {{ $pmdp_2020 }}, {{ $pmdp_2021 }}, {{ $pmdp_2022 }}, 
                    {{ $pmdp_2023 }}, {{ $pmdp_2024 }}, {{ $pmdp_2025 }}
                ],
                backgroundColor: bgColor,
                borderColor: brColor,
                borderWidth: 1
            }]
        },
        options: chartOptions
    });

    // 3. Chart Kerjasama
    var ctxKerjasama = document.getElementById("myChartKerjasama").getContext('2d');
    var myChartKerjasama = new Chart(ctxKerjasama, {
        type: 'bar',
        data: {
            labels: chartLabels,
            datasets: [{
                label: 'Kerjasama',
                data: [
                    {{ $kerja_2020 }}, {{ $kerja_2021 }}, {{ $kerja_2022 }}, 
                    {{ $kerja_2023 }}, {{ $kerja_2024 }}, {{ $kerja_2025 }}
                ],
                backgroundColor: bgColor,
                borderColor: brColor,
                borderWidth: 1
            }]
        },
        options: chartOptions
    });

    // 4. Chart Umum
    var ctxUmum = document.getElementById("myChartUmum").getContext('2d');
    var myChartUmum = new Chart(ctxUmum, {
        type: 'bar',
        data: {
            labels: chartLabels,
            datasets: [{
                label: 'Umum',
                data: [
                    {{ $umum_2020 }}, {{ $umum_2021 }}, {{ $umum_2022 }}, 
                    {{ $umum_2023 }}, {{ $umum_2024 }}, {{ $umum_2025 }}
                ],
                backgroundColor: bgColor,
                borderColor: brColor,
                borderWidth: 1
            }]
        },
        options: chartOptions
    });

    // 5. Chart Laki-Laki
    var ctxPria = document.getElementById("myChartPria").getContext('2d');
    var myChartPria = new Chart(ctxPria, {
        type: 'bar',
        data: {
            labels: chartLabels,
            datasets: [{
                label: 'Laki-Laki',
                data: [
                    {{ $pria_2020 }}, {{ $pria_2021 }}, {{ $pria_2022 }}, 
                    {{ $pria_2023 }}, {{ $pria_2024 }}, {{ $pria_2025 }}
                ],
                backgroundColor: bgColor,
                borderColor: brColor,
                borderWidth: 1
            }]
        },
        options: chartOptions
    });

    // 6. Chart Perempuan
    var ctxPerempuan = document.getElementById("myChartPerempuan").getContext('2d');
    var myChartPerempuan = new Chart(ctxPerempuan, {
        type: 'bar',
        data: {
            labels: chartLabels,
            datasets: [{
                label: 'Perempuan',
                data: [
                    {{ $wnt_2020 }}, {{ $wnt_2021 }}, {{ $wnt_2022 }}, 
                    {{ $wnt_2023 }}, {{ $wnt_2024 }}, {{ $wnt_2025 }}
                ],
                backgroundColor: bgColor,
                borderColor: brColor,
                borderWidth: 1
            }]
        },
        options: chartOptions
    });
</script>
@endsection