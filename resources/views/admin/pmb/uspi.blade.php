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
                                    @if(session('success'))
                                        <div class="alert alert-success">
                                            {{ session('success') }}
                                        </div>
                                    @endif
                                    @if(session('error'))
                                        <div class="alert alert-danger">
                                            {{ session('error') }}
                                        </div>
                                    @endif
                                    <table id="order-table" class="table table-striped table-bordered nowrap">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Prodi</th>
                                                <th>Kelas</th>
                                                <th>SPI</th>
                                                <th>SKS</th>
                                                <th>Operasional</th>
                                                <th>Kemahasiswaan</th>
                                                <th>Seragam</th>
                                                <th>#</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($data as $a)
                                                <tr>
                                                    <td>{{ $no++ }}</td>
                                                    <td>{{ $a->prodi }}</td>
                                                    
                                                    {{-- Ternary operator langsung di dalam kurung kurawal Blade --}}
                                                    <td>{{ $a->kelas == 1 ? 'Reguler' : 'Karyawan' }}</td>
                                                    
                                                    {{-- Mengganti helper rp() dengan number_format bawaan PHP --}}
                                                    <td>Rp {{ number_format($a->total_spi, 0, ',', '.') }}</td>
                                                    <td>Rp {{ number_format($a->total_sks, 0, ',', '.') }}</td>
                                                    <td>Rp {{ number_format($a->operasional, 0, ',', '.') }}</td>
                                                    <td>Rp {{ number_format($a->kemahasiswaan, 0, ',', '.') }}</td>
                                                    <td>Rp {{ number_format($a->seragam, 0, ',', '.') }}</td>
                                                    
                                                    <td>
                                                        <a href="{{ url('pmb/uspi_edit/' . $a->id) }}" class="btn btn-primary">
                                                            <span><i class="fa fa-edit"></i></span>
                                                        </a>
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
@endsection