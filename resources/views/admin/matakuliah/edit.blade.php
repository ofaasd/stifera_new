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
                                    <form action="{{ route('matakuliah.update', $d->id) }}" method="post">
                                        @csrf
                                        @method('PUT')
                                        
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    
                                                    <div class="row">
                                                        <div class="col-sm-2"><b>Kode Mata Kuliah</b></div>
                                                        <div class="col-sm-2">
                                                            <input type="hidden" name="id" class="form-control" value="{{ $d->id }}">
                                                            <input type="text" name="kode_mata_kuliah" class="form-control" value="{{ $d->kode_mata_kuliah }}" required="">
                                                        </div>  
                                                    </div>
                                                    <br>
                                                    
                                                    <div class="row">
                                                        <div class="col-sm-2"><b>Nama Mata Kuliah</b></div>
                                                        <div class="col-sm-2">
                                                            <input type="text" name="nama_mata_kuliah" class="form-control" value="{{ $d->nama_mata_kuliah }}" required="">
                                                        </div>  
                                                    </div>
                                                    <br>
                                                    
                                                    <div class="row">
                                                        <div class="col-sm-2"><b>Nama Inggris Mata Kuliah</b></div>
                                                        <div class="col-sm-2">
                                                            <input type="text" name="nama_mata_kuliah_eng" class="form-control" value="{{ $d->nama_mata_kuliah_eng }}" required="">
                                                        </div>  
                                                    </div>
                                                    <br>
                                                    
                                                    <div class="row">
                                                        <div class="col-sm-2"><b>SKS Mata Kuliah</b></div>
                                                        <div class="col-sm-2">
                                                            <input type="number" name="sks" class="form-control" value="{{ $d->jumlah_sks }}" required="">
                                                        </div>  
                                                    </div>
                                                    <br>
                                                    
                                                    <div class="row">
                                                        <div class="col-sm-2"><b>Semester Mata Kuliah</b></div>
                                                        <div class="col-sm-2">
                                                            <input type="number" name="smt" class="form-control" value="{{ $d->semester }}" required="">
                                                        </div>  
                                                    </div>
                                                    <br>
                                                    
                                                    <div class="row">
                                                        <div class="col-sm-2"><b>Teori / Praktek</b></div>
                                                        <div class="col-sm-2">
                                                            <select name="tp" class="form-control" required="">
                                                                <option value="T" {{ $d->tp == 'T' ? 'selected' : '' }}>Teori</option>
                                                                <option value="P" {{ $d->tp == 'P' ? 'selected' : '' }}>Praktek</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <br>
                                                    
                                                    <div class="row">
                                                        <div class="col-sm-2"><b>Kelompok Mata Kuliah</b></div>
                                                        <div class="col-sm-2">
                                                            <select name="kel_mata_kuliah" class="form-control" required="">
                                                                <option disabled="">---Kelompok Mata Kuliah---</option>
                                                                @foreach($kelompok_matakuliah as $a)
                                                                    <option value="{{ $a->id }}" {{ $a->id == $d->kelompok_mata_kuliah ? 'selected' : '' }}>
                                                                        {{ $a->nama_kelompok }} / {{ $a->nama_kelompok_eng }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>  
                                                    </div>
                                                    <br>
                                                    
                                                    <div class="row">
                                                        <div class="col-sm-2"><b>Rumpun Mata Kuliah</b></div>
                                                        <div class="col-sm-2">
                                                            <select name="rumpun_mata_kuliah" class="form-control" required="">
                                                                <option disabled="">---Rumpun Mata Kuliah---</option>
                                                                @foreach($rumpun as $r)
                                                                    <option value="{{ $r->id }}" {{ $r->id == $d->rumpun_mata_kuliah ? 'selected' : '' }}>
                                                                        {{ $r->nama_rumpun }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <br>
                                                    
                                                    <div class="row">
                                                        <div class="col-sm-2"><b>Status Mata Kuliah</b></div>
                                                        <div class="col-sm-2">
                                                            <select name="status" class="form-control" required="">
                                                                <option value="1" {{ $d->is_aktif == 1 ? 'selected' : '' }}>AKTIF</option>
                                                                <option value="0" {{ $d->is_aktif == 0 ? 'selected' : '' }}>TIDAK AKTIF</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <br>
                                                    
                                                    <div class="row">
                                                        <div class="col-sm-2"><b>Program Studi</b></div>
                                                        <div class="col-sm-2">
                                                            <p>
                                                                <select name="program_studi" class="form-control">
                                                                    @foreach($program_studi as $row)
                                                                        <option value="{{ $row->id }}" {{ $row->id == $d->id_program_studi ? 'selected' : '' }}>
                                                                            {{ $row->nama_jurusan }}
                                                                        </option>    
                                                                    @endforeach
                                                                </select>
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <br><br>
                                                    
                                                    <center><input type="submit" class="btn btn-success btn-round" value="Update"></center>
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