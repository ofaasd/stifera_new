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
									<form action="{{ route('gelombang.update', $a->id) }}" method="post">
                                        @csrf 
                                        @method('PUT')
                                            <table class="table table-borderless">
                                                <tr>
                                                    <td style="width: 200px;">Nomor Gelombang</td>
                                                    <td style="width: 10px;">:</td>
                                                    <td><input type="number" name="no_gel" value="{{ $a->no_gel }}" class="form-control" required></td>
                                                </tr>
                                                <tr>
                                                    <td>Kode Gelombang</td>
                                                    <td>:</td>
                                                    <td><input type="text" name="nama_gel" value="{{ $a->nama_gel }}" class="form-control" required></td>
                                                </tr>
                                                <tr>
                                                    <td>Nama Gelombang</td>
                                                    <td>:</td>
                                                    <td><input type="text" name="nama_gel_long" value="{{ $a->nama_gel_long }}" class="form-control" required></td>
                                                </tr>
                                                <tr>
                                                    <td>Tanggal Mulai</td>
                                                    <td>:</td>
                                                    <td><input type="date" name="tgl_mulai" value="{{ $a->tgl_mulai }}" class="form-control" required></td>
                                                </tr>
                                                <tr>
                                                    <td>Tanggal Akhir</td>
                                                    <td>:</td>
                                                    <td><input type="date" name="tgl_akhir" value="{{ $a->tgl_akhir }}" class="form-control" required></td>
                                                </tr>
                                                <tr>
                                                    <td>Tanggal Ujian</td>
                                                    <td>:</td>
                                                    <td><input type="date" name="ujian" value="{{ $a->ujian }}" class="form-control" required></td>
                                                </tr>
                                                <tr>
                                                    <td>Jam Ujian</td>
                                                    <td>:</td>
                                                    <td><input type="time" name="jam_ujian" value="{{ $a->jam_ujian }}" class="form-control" required></td>
                                                </tr>
                                                <tr>
                                                    <td>Pengumuman Ujian</td>
                                                    <td>:</td>
                                                    <td><input type="date" name="pengumuman" value="{{ $a->pengumuman }}" class="form-control" required></td>
                                                </tr>
                                                <tr>
                                                    <td>Mulai Register</td>
                                                    <td>:</td>
                                                    <td><input type="date" name="reg_mulai" value="{{ $a->reg_mulai }}" class="form-control" required></td>
                                                </tr>
                                                <tr>
                                                    <td>Batas Register</td>
                                                    <td>:</td>
                                                    <td><input type="date" name="reg_akhir" value="{{ $a->reg_akhir }}" class="form-control" required></td>
                                                </tr>
                                                <tr>
                                                    <td>Tahun Ajaran</td>
                                                    <td>:</td>
                                                    <td>
                                                        <select name="tahun" class="form-control" required>
                                                            {{-- Asumsi: Variabel $pmb_ta (daftar semua Tahun Ajaran) dikirim dari Controller --}}
                                                            @foreach($pmb_ta as $ta)
                                                                <option value="{{ $ta->id }}" {{ $a->tahun == $ta->id ? 'selected' : '' }}>
                                                                    {{ $ta->awal . '/' . $ta->akhir }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Jenis Gelombang</td>
                                                    <td>:</td>
                                                    <td>
                                                        <select name="jenis" class="form-control" required>
                                                            <option disabled value="">Jenis Gelombang</option>
                                                            <option value="1" {{ $a->jenis == 1 ? 'selected' : '' }}>Reguler</option>
                                                            <option value="2" {{ $a->jenis == 2 ? 'selected' : '' }}>Khusus</option>
                                                            <option value="3" {{ $a->jenis == 3 ? 'selected' : '' }}>PMDK</option>
                                                        </select>
                                                    </td>
                                                </tr>
                                            </table>  
                                            
                                            <input type="hidden" name="id" value="{{ $a->id }}" readonly>
                                            
                                            <div class="mt-3">
                                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button> 
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