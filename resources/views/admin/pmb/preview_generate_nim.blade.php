@extends('layouts.default', ['CurrentPage' => $CurrentPage])

@section('content')
		<div class="content-body">
			<div class="container">
                <div class="mb-4 pb-3">
                    <a href="{{ url('pmb/index_nim')}}" class="btn btn-success"><i class="fa fa-arrow-left"></i> Kembali</a>
                </div>
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
									<div class="row" style="margin:20px 0">
                                        <div class="col-md-6">
                                            <h3>Data Baru</h3>
                                            <hr />
                                            
                                            <form method="POST" action="{{ url('pmb/upload_pmb') }}" enctype="multipart/form-data">
                                                @csrf <p>Upload File PMB</p>
                                                <input type="hidden" name="ta" value="{{ $ta }}">
                                                <input type="file" name="file_excel" class="form-control"><br />
                                                <button type="submit" class="btn btn-primary">Upload</button>
                                            </form>
                                        </div>
                                        <div class="col-md-6">
                                            <a href="{{ url('pmb/generate_nim_proccess/' . $ta) }}" class="btn btn-primary" style="float:right">
                                                Generate
                                            </a>
                                        </div>
                                    </div>

                                    <form method="POST" action="{{ url('pmb/simpan_maba') }}">
                                        @csrf <table class="table" id="data_baru">
                                            <thead>
                                                <tr>
                                                    <th>No. Pendaftaran</th>
                                                    <th>Nama</th>
                                                    <th>No. KTP</th>
                                                    <th>No. HP</th>
                                                    <th>Prodi</th>
                                                    <th>Jenis Kelas</th>
                                                    <th>Status</th>
                                                    <th>NIM</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($data as $row)
                                                    <tr>
                                                        <td>{{ $row->nopen }}</td>
                                                        <td>{{ $row->nama }}</td>
                                                        <td>{{ $row->noktp }}</td>
                                                        <td>{{ $row->hp }}</td>
                                                        
                                                        <input type="hidden" name="ta" value="{{ $ta }}">
                                                        <input type="hidden" name="nopen[]" value="{{ $row->nopen }}">
                                                        
                                                        <td>
                                                            <select name="program_studi[]" class="form-control">
                                                                @foreach($jurusan as $r)
                                                                    <option value="{{ $r->id }}" {{ $r->id == $row->pilihan1 ? 'selected' : '' }}>
                                                                        {{ $r->nama_jurusan }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        
                                                        <td>
                                                            <select name="kelas[]" class="form-control">
                                                                @foreach($a as $key => $value)
                                                                    <option value="{{ $key }}" {{ $key == $row->kelas ? 'selected' : '' }}>
                                                                        {{ $value }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        
                                                        <td>
                                                            <select name="is_inject[]" class="form-control">
                                                                <option value="0" {{ $row->is_inject == 0 ? 'selected' : '' }}>Sembunyikan</option>
                                                                <option value="1" {{ $row->is_inject == 1 ? 'selected' : '' }}>Munculkan</option>
                                                            </select>
                                                        </td>
                                                        
                                                        <td>{{ $row->nim }}</td>
                                                    </tr>
                                                @endforeach  
                                            </tbody>    
                                        </table>
                                        
                                        <button type="submit" class="btn btn-primary col-md-12">Simpan</button>
                                    </form>

                                    <br />
                                    <hr />

                                    <h3>Calon Mahasiswa Terdaftar</h3>
                                    <br />
                                    <table class="table" id="data_baru2">
                                        <thead>
                                            <tr>
                                                <th>No. Pendaftaran</th>
                                                <th>Nama</th>
                                                <th>No. KTP</th>
                                                <th>No. HP</th>
                                                <th>Prodi</th>
                                                <th>Jenis Kelas</th>
                                                <th>NIM</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($data_selesai as $row)
                                                <tr>
                                                    <td>{{ $row->nopen }}</td>
                                                    <td>{{ $row->nama }}</td>
                                                    <td>{{ $row->noktp }}</td>
                                                    <td>{{ $row->hp }}</td>
                                                    
                                                    <td>
                                                        {{-- Karena di controller Laravel kita mengirim $jurusan sebagai Collection, 
                                                            kita bisa menggunakan fitur firstWhere() yang sangat praktis ini 
                                                            daripada harus melooping ulang seluruh array jurusan --}}
                                                        {{ collect($jurusan)->firstWhere('id', $row->pilihan1)->nama_jurusan ?? '-' }}
                                                    </td>
                                                    
                                                    <td>
                                                        {{-- Mengambil value dari array berdasarkan key ($row->kelas) --}}
                                                        {{ $a[$row->kelas] ?? '-' }}
                                                    </td>
                                                    
                                                    <td>{{ $row->nim }}</td>
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