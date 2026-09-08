@extends('layouts.default', ['CurrentPage' => $CurrentPage])

@section('content')
		<div class="content-body">
			<div class="container">

				<!-- Row -->
				<div class="row">
					<div class="col-xl-12">
						<div class="mb-4 pb-3">
							<a href="{{url('master/matakuliah/create')}}" class="btn btn-success btn-round"><font style="color: white;">TAMBAH MATA KULIAH</font></a>
						</div>
						<div class="card">
							<div class="card-header">
								<h4 class="card-title"><i class="fa-solid fa-file-lines me-1"></i>{{ $title }}</h4>
							</div>
							<div class="card-body pb-4">
                                @if(session('status'))
                                    <div class="alert alert-success">
                                        {{ session('status') }}
                                    </div>
                                @endif
                                @if(session('errors'))
                                    <div class="alert alert-danger">
                                        {{ session('errors') }}
                                    </div>
                                @endif
                                <div class="table-responsive">
									<table id="order-table" class="table table-bordered table-striped">
                                        <thead class="bg-primary text-white text-center">
                                            <tr>
                                                <th style="width: 5px;">No</th>
                                                <th style="width: 40px;">Kode</th>
                                                <th style="min-width: 200px; white-space: normal;">Nama Mata Kuliah</th>
                                                <th style="width: 10px;">T/P</th>
                                                <th style="width: 10px;">SKS</th>
                                                <th style="width: 10px;">Smt</th>
                                                <th style="max-width: 100px; white-space: normal;">Kelompok</th>
                                                <th style="max-width: 100px; white-space: normal;">Rumpun</th>
                                                <th style="width: 20px;">Status</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($matakuliah as $a)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $a->kode_mata_kuliah }}</td>
                                                    <td style="white-space: normal; word-break: break-word; min-width: 200px;">{{ $a->nama_mata_kuliah }} / {{ $a->nama_mata_kuliah_eng }}</td>
                                                    <td>{{ $a->tp }}</td>
                                                    <td>{{ $a->jumlah_sks }}</td>
                                                    <td>{{ $a->semester }}</td>
                                                    
                                                    <td style="white-space: normal; word-break: break-word; max-width: 100px;">{{ $kelompok_matakuliah[$a->kelompok_mata_kuliah] ?? '-' }}</td>
                                                    <td style="white-space: normal; word-break: break-word; max-width: 100px;">{{ $a->rumpun }}</td>
                                                    <td>
                                                        @if($a->is_aktif == 1)
                                                            <a href="{{ url('master/matakuliah/update_togle_matkul/'.$a->id) }}" class="btn btn-success">AKTIF</a>
                                                        @elseif($a->is_aktif == 0)
                                                            <a href="{{ url('master/matakuliah/update_togle_matkul/'.$a->id) }}" class="btn btn-danger">TIDAK AKTIF</a>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ url('master/matakuliah/' . str_replace('=','',base64_encode(base64_encode($a->id)))) }}/edit" class="btn btn-success" title="Edit"><i class="fa fa-edit"></i></a>
                                                        <a href="{{ url('master/matakuliah/delete/'.str_replace('=','',base64_encode(base64_encode($a->id)))) }}" onclick="return confirm('Yakin Delete Data Matakuliah?')" class="btn btn-danger" title="Delete"><i class="fa fa-trash"></i></a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th width="5">No</th>
                                                <th width="20px">Kode</th>
                                                <th>Nama Mata Kuliah</th>
                                                <th width="10px">T/P</th>
                                                <th width="10px">SKS</th>
                                                <th width="10px">Smt</th>
                                                <th width="20px">Kelompok Mata Kuliah</th>
                                                <th width="20px">Rumpun Mata Kuliah</th>
                                                <th width="20px">Status</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </tfoot>
                                    </table>
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
        $('#order-table').DataTable({
            language: {
                paginate: {
                    next: '>',
                    previous: '<'
                }
            }
        });
    });
</script>
@endsection