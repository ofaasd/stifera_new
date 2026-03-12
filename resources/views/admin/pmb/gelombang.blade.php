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
									@if(session('status_delete'))
                                        <div class="alert alert-success">
                                            {{ session('status_delete') }}
                                        </div>
                                    @endif

                                    <div class="card-header">
                                        <a href="#" data-bs-toggle="modal" data-bs-target="#myModalTambahTA" class="btn btn-primary btn-round btn-sm">GELOMBANG TAHUN AJARAN</a> 
                                    </div>

                                    <div class="card-block">
                                        <div class="dt-responsive table-responsive">
                                            <table id="order-table" class="table table-striped table-bordered nowrap">
                                                <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Kode Tahun Gelombang</th>
                                                        <th>Awal Tahun</th>
                                                        <th>Akhir Tahun</th>
                                                        <th>Aktif</th>
                                                        <th>Tindakan</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php $no = 1; @endphp
                                                    @foreach($pmb_ta as $ta)
                                                        <tr>
                                                            <td>{{ $no++ }}</td>
                                                            <td>{{ $ta->kode }}</td>
                                                            <td>{{ $ta->awal }}</td>
                                                            <td>{{ $ta->akhir }}</td>
                                                            <td>{{ $ta->is_active == 1 ? 'Aktif' : 'Tidak Aktif' }}</td>
                                                            <td>
                                                                <a href="#" data-bs-toggle="modal" data-bs-target="#myModalEdit{{ $ta->id }}" class="btn btn-warning btn-sm">
                                                                    <span><i class="fa fa-edit" style="color: white;"></i></span>
                                                                </a>
                                                                <a href="#" data-bs-toggle="modal" data-bs-target="#myModalDelete{{ $ta->id }}" class="btn btn-danger btn-sm">
                                                                    <span><i class="fa fa-trash" style="color: white;"></i></span>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <hr>

                                    <div class="card-header">
                                        <a href="{{ url('pmb/gelombang/create') }}" class="btn btn-success btn-round btn-sm">TAMBAH GELOMBANG</a> 
                                        <h5 class="mt-3">DAFTAR GELOMBANG</h5>
                                    </div>

                                    <div class="card-block">
                                        <div class="dt-responsive table-responsive">
                                            <table id="gelombang-table" class="table table-striped table-bordered nowrap">
                                                <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Kode Gelombang</th>
                                                        <th>Nomor Gelombang</th>
                                                        <th>Nama Gelombang</th>
                                                        <th>Tanggal Mulai</th>
                                                        <th>Tanggal Akhir</th>
                                                        <th>#</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php $no = 1; @endphp
                                                    @foreach($data as $a)
                                                        <tr>
                                                            <td>{{ $no++ }}</td>
                                                            <td>{{ $a->nama_gel }}</td>
                                                            <td>{{ $a->no_gel }}</td>
                                                            <td>{{ $a->nama_gel_long }}</td>
                                                            <td>{{ date('d-m-Y', strtotime($a->tgl_mulai)) }}</td>
                                                            <td>{{ date('d-m-Y', strtotime($a->tgl_akhir)) }}</td>
                                                            <td>
                                                                <a href="{{ url('pmb/gelombang/'.$a->id.'/edit') }}" class="btn btn-primary">
                                                                    <span><i class="fa fa-edit" style="color: white;"></i></span>
                                                                </a>
                                                                <form action="{{ url('pmb/gelombang/' . $a->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-danger">
                                                                        <span><i class="fa fa-trash" style="color: white;"></i></span>
                                                                    </button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div id="myModalTambahTA" class="modal fade bs-example-modal-lg" role="dialog">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Tambah Tahun Ajaran</h5>
                                                    <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
                                                </div>
                                                <div class="modal-body">
                                                    <form action="{{ url('pmb/gelombang_ta') }}" method="post">
                                                        @csrf
                                                        <input type="text" name="tahun_awal" placeholder="Tahun Awal" class="form-control" required>
                                                        <input style="margin-top: 10px;" type="text" name="tahun_akhir" placeholder="Tahun Akhir" class="form-control" required>
                                                        <button style="margin-top: 10px;" type="submit" class="btn btn-primary">Tambah</button>
                                                    </form>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    @foreach($pmb_ta as $ta)
                                        <div id="myModalEdit{{ $ta->id }}" class="modal fade bs-example-modal-lg" role="dialog">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Edit Tahun Ajaran</h5>
                                                        <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="{{ url('pmb/gelombang_ta_update') }}" method="post">
                                                            @csrf
                                                            <input type="hidden" name="id" value="{{ $ta->id }}">
                                                            
                                                            <label>Tahun Awal</label>
                                                            <input type="text" name="tahun_awal" value="{{ $ta->awal }}" class="form-control" required>
                                                            
                                                            <label class="mt-2">Tahun Akhir</label>
                                                            <input type="text" name="tahun_akhir" value="{{ $ta->akhir }}" class="form-control" required>
                                                            
                                                            <div class="form-group mt-2">
                                                                <label for="Status">Status</label>
                                                                <select name="status" class="form-control">
                                                                    <option value="0" {{ $ta->is_active == 0 ? 'selected' : '' }}>Tidak Aktif</option>
                                                                    <option value="1" {{ $ta->is_active == 1 ? 'selected' : '' }}>Aktif</option>
                                                                </select>
                                                            </div>
                                                            
                                                            <button style="margin-top: 10px;" type="submit" class="btn btn-success btn-sm">Simpan</button>
                                                        </form>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div id="myModalDelete{{ $ta->id }}" class="modal fade bs-example-modal-lg" role="dialog">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Konfirmasi Hapus</h5>
                                                        <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="{{ url('pmb/gelombang_ta_hapus') }}" method="post">
                                                            @csrf
                                                            <input type="hidden" name="id" value="{{ $ta->id }}">
                                                            <p>Apakah anda yakin akan menghapus data tahun ajaran <b>{{ $ta->kode }}</b> ini?</p>
                                                            
                                                            <div class="text-right mt-3">
                                                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
@endsection