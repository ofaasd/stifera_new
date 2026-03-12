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
							<div class="cm-content-body form excerpt" style="overflow-x:scroll">
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
								<div class="card-body pb-4">
									<table id="order-table" class="table table-striped table-bordered nowrap">
                                        <thead>
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
                                        </thead>
                                        <tbody>
                                            @foreach($matakuliah as $a)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $a->kode_mata_kuliah }}</td>
                                                    <td>{{ $a->nama_mata_kuliah }} / {{ $a->nama_mata_kuliah_eng }}</td>
                                                    <td>{{ $a->tp }}</td>
                                                    <td>{{ $a->jumlah_sks }}</td>
                                                    <td>{{ $a->semester }}</td>
                                                    
                                                    <td>{{ $kelompok_matakuliah[$a->kelompok_mata_kuliah] ?? '-' }}</td>
                                                    
                                                    <td>{{ $a->rumpun }}</td>
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
		</div>
@endsection
@section('local-js')
    <script type="text/javascript">
    $(document).ready(function() {
        $('#order-table').DataTable();
    });
</script>
@endsection