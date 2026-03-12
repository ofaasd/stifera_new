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
									<form action="{{ url('pmb/gelombang') }}" method="post">
                                    @csrf 
                                        <table class="table table-borderless">
                                            <tr>
                                                <td style="width: 200px;">Nomor Gelombang</td>
                                                <td style="width: 10px;">:</td>
                                                <td><input type="number" name="no_gel" class="form-control" required></td>
                                            </tr>
                                            <tr>
                                                <td>Kode Gelombang</td>
                                                <td>:</td>
                                                <td><input type="text" name="nama_gel" class="form-control" required></td>
                                            </tr>
                                            <tr>
                                                <td>Nama Gelombang</td>
                                                <td>:</td>
                                                <td><input type="text" name="nama_gel_long" class="form-control" required></td>
                                            </tr>
                                            <tr>
                                                <td>Tanggal Mulai</td>
                                                <td>:</td>
                                                <td><input type="date" name="tgl_mulai" class="form-control" required></td>
                                            </tr>
                                            <tr>
                                                <td>Tanggal Akhir</td>
                                                <td>:</td>
                                                <td><input type="date" name="tgl_akhir" class="form-control" required></td>
                                            </tr>
                                            <tr>
                                                <td>Tanggal Ujian</td>
                                                <td>:</td>
                                                <td><input type="date" name="ujian" class="form-control" required></td>
                                            </tr>
                                            <tr>
                                                <td>Jam Ujian</td>
                                                <td>:</td>
                                                <td><input type="time" name="jam_ujian" class="form-control" required></td>
                                            </tr>
                                            <tr>
                                                <td>Pengumuman Ujian</td>
                                                <td>:</td>
                                                <td><input type="date" name="pengumuman" class="form-control" required></td>
                                            </tr>
                                            <tr>
                                                <td>Mulai Register</td>
                                                <td>:</td>
                                                <td><input type="date" name="reg_mulai" class="form-control" required></td>
                                            </tr>
                                            <tr>
                                                <td>Batas Register</td>
                                                <td>:</td>
                                                <td><input type="date" name="reg_akhir" class="form-control" required></td>
                                            </tr>
                                            <tr>
                                                <td>Tahun Ajaran</td>
                                                <td>:</td>
                                                <td>
                                                    <select name="tahun" class="form-control" required>
                                                        {{-- Pastikan variabel $ta dikirim dari Controller saat menampilkan view ini --}}
                                                        <option value="{{ $ta->id }}" selected>{{ $ta->awal . '/' . $ta->akhir }}</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Jenis Gelombang</td>
                                                <td>:</td>
                                                <td>
                                                    <select name="jenis" class="form-control" required>
                                                        <option selected disabled value="">Pilih Jenis Gelombang</option>
                                                        <option value="1">Reguler</option>
                                                        <option value="2">Khusus</option>
                                                        <option value="3">PMDK</option>
                                                    </select>
                                                </td>
                                            </tr>
                                        </table>
                                        
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