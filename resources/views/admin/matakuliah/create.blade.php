@extends('layouts.default', ['CurrentPage' => $CurrentPage])

@section('content')
		<div class="content-body">
			<div class="container">

				<!-- Row -->
				<div class="row">
					<div class="col-xl-12">
						<div class="mb-4 pb-3">
							<a href="{{ url('master/matakuliah')}}" class="btn btn-primary">Kembali</a>
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
                                @if(session('status'))
                                    <div class="alert alert-success">
                                        {{ session('status') }}
                                    </div>
                                @endif
								<div class="card-body pb-4">
                                    <form action="{{ url('master/matakuliah') }}" method="post">
                                        @csrf 
                                        
                                        <div class="col-sm-12">
                                            <div class="col-sm-6">
                                                Kode Mata Kuliah :
                                                <p><input type="text" class="form-control" placeholder="Kode Mata Kuliah" name="kode_mata_kuliah" required=""></p>
                                            </div>
                                            <div class="col-sm-6">
                                                Nama Mata Kuliah :
                                                <p><input type="text" class="form-control" placeholder="Nama Mata Kuliah" name="nama_mata_kuliah" required=""></p>
                                            </div>
                                            <div class="col-sm-6">
                                                Nama Inggris Mata Kuliah :
                                                <p><input type="text" class="form-control" placeholder="Nama Inggris Mata Kuliah" name="nama_mata_kuliah_eng" required=""></p>
                                            </div>
                                            <div class="col-sm-6">
                                                Jumlah SKS :
                                                <p><input type="number" class="form-control" placeholder="0" name="sks" required="" min="0"></p>
                                            </div>
                                            <div class="col-sm-6">
                                                Semester :
                                                <p><input type="number" class="form-control" placeholder="0" name="smt" required="" min="0"></p>
                                            </div>
                                            <div class="col-sm-6">
                                                Teori/Praktek :
                                                <p>
                                                    <select name="tp" class="form-control">
                                                        <option selected="" disabled="">---Teori / Praktek---</option>
                                                        <option value="T">Teori</option>
                                                        <option value="P">Praktek</option>
                                                    </select>
                                                </p>
                                            </div>
                                            <div class="col-sm-6">
                                                Kelompok Mata Kuliah :
                                                <p>
                                                    <select name="kel_mata_kuliah" class="form-control">
                                                        <option selected="" disabled="">---Kelompok Mata Kuliah---</option>
                                                        @foreach($kelompok_matakuliah as $a)
                                                            <option value="{{ $a->id }}">{{ $a->nama_kelompok }} / {{ $a->nama_kelompok_eng }}</option>
                                                        @endforeach
                                                    </select>
                                                </p>
                                            </div>
                                            <div class="col-sm-6">
                                                Rumpun Mata Kuliah :
                                                <p>
                                                    <select name="rumpun_mata_kuliah" class="form-control">
                                                        <option selected="" disabled="">---Rumpun Mata Kuliah---</option>
                                                        @foreach($rumpun as $a)
                                                            <option value="{{ $a->id }}">{{ $a->nama_rumpun }}</option>
                                                        @endforeach
                                                    </select>
                                                </p>
                                            </div>
                                            <div class="col-sm-6">
                                                Status Mata Kuliah :
                                                <p>
                                                    <select name="status" class="form-control">
                                                        <option selected="" disabled="">---Status Mata Kuliah---</option>
                                                        <option value="0">Tidak Aktif</option>
                                                        <option value="1">Aktif</option>
                                                    </select>
                                                </p>
                                            </div>
                                            <div class="col-sm-6">
                                                Program Studi :
                                                <p>
                                                    <select name="program_studi" class="form-control">
                                                        @foreach($program_studi as $row)
                                                            <option value="{{ $row->id }}">{{ $row->nama_jurusan }}</option>    
                                                        @endforeach
                                                    </select>
                                                </p>
                                            </div>
                                            <div class="col-md-12">
                                                <center><input type="submit" class="btn btn-success btn-round" value="Tambah"></center>
                                            </div>
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