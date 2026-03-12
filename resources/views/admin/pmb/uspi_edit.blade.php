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
                                    <form action="{{ url('pmb/uspi_update') }}" method="POST">
                                    @csrf 
                                        <table class="table table-borderless">
                                            <tr>
                                                <td style="width: 200px;">Kelas</td>
                                                <td style="width: 10px;">:</td>
                                                <td>
                                                    <select name="kelas" class="form-control" required>
                                                        <option value="1" {{ $a->kelas == 1 ? 'selected' : '' }}>Reguler</option>
                                                        <option value="2" {{ $a->kelas == 2 ? 'selected' : '' }}>Karyawan</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Biaya SPI</td>
                                                <td>:</td>
                                                <td><input type="text" name="total_spi" value="{{ $a->total_spi }}" class="form-control" required></td>
                                            </tr>
                                            <tr>
                                                <td>Biaya SKS</td>
                                                <td>:</td>
                                                <td><input type="text" name="total_sks" value="{{ $a->total_sks }}" class="form-control" required></td>
                                            </tr>
                                            <tr>
                                                <td>Biaya Operasional</td>
                                                <td>:</td>
                                                <td><input type="text" name="operasional" value="{{ $a->operasional }}" class="form-control" required></td>
                                            </tr>
                                            <tr>
                                                <td>Kemahasiswaan</td>
                                                <td>:</td>
                                                <td><input type="text" name="kemahasiswaan" value="{{ $a->kemahasiswaan }}" class="form-control" required></td>
                                            </tr>
                                            <tr>
                                                <td>Seragam</td>
                                                <td>:</td>
                                                <td><input type="text" name="seragam" value="{{ $a->seragam }}" class="form-control" required></td>
                                            </tr>
                                        </table>  
                                            
                                        <input type="hidden" name="id" value="{{ $a->id }}" readonly>
                                        
                                        <div class="mt-3">
                                            <button type="submit" class="btn btn-primary">Simpan</button> 
                                        </div>
                                    </form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
@endsection