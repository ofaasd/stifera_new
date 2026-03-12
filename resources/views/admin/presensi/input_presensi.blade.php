@extends('layouts.default', ['CurrentPage' => $CurrentPage])
@section('local-css')
    <style>
        .dropdown-toggle{
            width : 100% !important;
            padding-left:20px !important;
        }
    </style>
@endsection
@section('content')
        @php
            if (!is_null($memos)) {
                $memo = $memos->memo;
                $sub = $memos->sub;
                $hdr = $memos->mhs_hadir;
            }else{
                $memo = null;
                $sub = null;
                $hdr = 0;
            }
        @endphp
		<div class="content-body">
			<div class="container">
                <div class="mb-4 pb-3">
                    <a href="{{ url('master/presensi/tanggal/' . $jadwal_id)}}" class="btn btn-success"><i class="fa fa-arrow-left"></i> Kembali</a>
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
                                    @if(session('presensi'))
                                        <div class="alert alert-success">{{ session('presensi') }}</div>
                                    @endif
                                    <form action="{{ url('master/presensi/simpan') }}" method="post">
                                        @csrf <div class="dt-responsive table-responsive">
                                            <label>Materi Kontrak Perkuliahan :</label>
                                            <textarea class="form-control" name="memo">{{ $memo }}</textarea>
                                            <br>
                                            
                                            <label>Sub Pembahasan :</label>
                                            <textarea class="form-control" name="sub">{{ $sub }}</textarea>
                                            <br>
                                            
                                            <label>Jumlah Mahasiswa Hadir : {{ $hdr }}</label>
                                            <br>
                                            
                                            <table class="table table-striped table-bordered nowrap">
                                                <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>NIM</th>
                                                        <th>Nama</th>
                                                        <th>Tanggal</th>
                                                        <th>Status Presensi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php 
                                                        $no = 1; 
                                                        $total_rec = 0; 
                                                    @endphp

                                                    @foreach($mhs as $a)
                                                        @php
                                                            $status = \Illuminate\Support\Facades\DB::table('master_presensi')
                                                                ->where('nim', $a->nim)
                                                                ->where('tgl_pertemuan', $tgl_pertemuan)
                                                                ->first();

                                                            $stat_val = $status->status ?? null;
                                                            $total_rec++; // Menghitung total data
                                                        @endphp
                                                        
                                                        <tr>
                                                            <td>{{ $no++ }}</td>
                                                            <td>
                                                                <input value="{{ $a->id_jadwal }}" type="hidden" name="id_jadwal[]">
                                                                <input value="{{ $a->nim }}" type="hidden" name="nim[]">
                                                                {{ $a->nim }}
                                                            </td>
                                                            <td>{{ $a->nama }}</td>
                                                            <td>
                                                                <input value="{{ $tgl_pertemuan }}" type="hidden" name="tgl[]">
                                                                {{ date('d/m/Y', strtotime($tgl_pertemuan)) }}
                                                            </td>
                                                            <td>
                                                                
                                                                    <select name="status[]" class="nice-select form-control border-border col-md-12">
                                                                        <option value="1" {{ empty($stat_val) || $stat_val == 1 ? 'selected' : '' }}>Hadir</option>
                                                                        <option value="2" {{ $stat_val == 2 ? 'selected' : '' }}>Izin</option>
                                                                        <option value="3" {{ $stat_val == 3 ? 'selected' : '' }}>Tanpa Keterangan</option>
                                                                    </select>
                                                                
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            
                                            <input value="{{ $total_rec }}" type="hidden" name="total">
                                            <input value="{{ $id_pertemuan }}" type="hidden" name="id_pertemuan">
                                            
                                            <br>
                                            <button type="submit" class="btn btn-success">
                                                <i class="fa fa-save"></i> Simpan
                                            </button>
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