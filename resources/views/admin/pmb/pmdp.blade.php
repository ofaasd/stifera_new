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
									<a href="{{ url('pmb/download_pmdp') }}" class="btn btn-primary">
                                        <span class="fa fa-download"></span> Download Excel
                                    </a>
                                    <br><br>

                                    <div class="dt-responsive table-responsive">
                                        <table id="order-table" class="table table-striped table-bordered nowrap">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>No. Pendaftaran</th>
                                                    <th>NISN</th>
                                                    <th>Nama Lengkap</th>
                                                    <th>Jenis Pendaftaran</th>
                                                    <th>Nilai Total</th>
                                                    <th>Jurusan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php $no = 1; @endphp
                                                @foreach($list as $a)
                                                    <tr>
                                                        <td>{{ $no++ }}</td>
                                                        <td>{{ $a->nopen }}</td>
                                                        <td>{{ $a->nisn }}</td>
                                                        <td>{{ $a->nama }}</td>
                                                        
                                                        <td>
                                                            @if($a->jenis_pendaftaran == 1)
                                                                Peserta Didik Baru
                                                            @elseif($a->jenis_pendaftaran == 2)
                                                                Pindahan
                                                            @elseif($a->jenis_pendaftaran == 11)
                                                                Alih Jenjang
                                                            @elseif($a->jenis_pendaftaran == 12)
                                                                Lintas Jalur
                                                            @endif
                                                        </td>
                                                        
                                                        <td>{{ round($a->peringkat_pmdp) }}</td>
                                                        
                                                        <td>
                                                            {{ collect($prodi ?? [])->firstWhere('id', $a->pilihan1)?->nama_jurusan ?? '-' }}
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
		</div>
@endsection
@section('local-js')
    <script type="text/javascript">
    $(document).ready(function() {
        $('#order-table').DataTable();
    });
</script>
@endsection