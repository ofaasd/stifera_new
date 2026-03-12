@extends('layouts.default', ['CurrentPage' => $CurrentPage])

@section('content')
		<div class="content-body">
			<div class="container">

				<!-- Row -->
				<div class="row">
					<div class="col-xl-12">
						<div class="mb-4 pb-3">
							<a href="{{ url('master/khs/list_mhs')}}" class="btn btn-primary"><i class="fa fa-arrow-left me-1"></i>Kembali</a>
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
								<div class="card-body pb-4">
                                    <b>
                                        {{ $ta }} <font color="green">(Aktif)</font> 
                                        <a href="{{ url('master/khs/cetak_khs') }}">Download KHS</a>
                                    </b>
                                    
                                    <div class="dt-responsive table-responsive">
                                        <table class="table table-bordered nowrap">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Dosen</th>
                                                    <th>Kode</th>
                                                    <th>Nama Matakuliah</th>
                                                    <th>SKS</th>
                                                    <th>Tugas</th>
                                                    <th>UTS</th>
                                                    <th>UAS</th>
                                                    <th>Nilai Akhir</th>
                                                    <th>Nilai Huruf</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php 
                                                    $no = 1; 
                                                    $jumlah_aktif = 0; 
                                                    $total_point_aktif = 0; 
                                                @endphp

                                                @foreach($khs as $a)
                                                    @php
                                                        $v_tugas = ($a->validasi_tugas == 0) ? '-' : 'v';
                                                        $v_uts   = ($a->validasi_uts == 0) ? '-' : 'v';
                                                        $v_uas   = ($a->validasi_uas == 0) ? '-' : 'v';

                                                        // Inisialisasi default agar tidak undefined
                                                        $point_aktif = 0;
                                                        $pecah_nhuruf = '';
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $no++ }}</td>
                                                        <td>{{ $a->nama_dosen }}</td>
                                                        <td>{{ $a->kode_mata_kuliah }}</td>
                                                        <td>{{ $a->mata_kuliah }}</td>
                                                        <td><center>{{ $a->jumlah_sks }}</center></td>
                                                        
                                                        <td>
                                                            <center>
                                                                {{ is_null($a->ntugas) || $a->publish_tugas == 0 ? "- | $v_tugas" : "$a->ntugas | $v_tugas" }}
                                                            </center>
                                                        </td>
                                                        
                                                        <td>
                                                            <center>
                                                                {{ is_null($a->nuts) || $a->publish_uts == 0 ? "- | $v_uts" : "$a->nuts | $v_uts" }}
                                                            </center>
                                                        </td>
                                                        
                                                        <td>
                                                            <center>
                                                                {{ is_null($a->nuas) || $a->publish_uas == 0 ? "- | $v_uas" : "$a->nuas | $v_uas" }}
                                                            </center>
                                                        </td>
                                                        
                                                        <td>
                                                            <center>
                                                                {{ is_null($a->nakhir) || $a->publish_tugas == 0 || $a->publish_uts == 0 || $a->publish_uas == 0 ? "- | $v_uas" : "$a->nakhir | $v_uas" }}
                                                            </center>
                                                        </td>
                                                        
                                                        <td>
                                                            <center>
                                                                @php
                                                                    if (is_null($a->nhuruf) || $a->publish_tugas == 0 || $a->publish_uts == 0 || $a->publish_uas == 0) {
                                                                        echo "- | " . $v_uas;
                                                                    } else {
                                                                        $pecah_nhuruf = $a->nhuruf;
                                                                        switch ($pecah_nhuruf) {
                                                                            case 'E':  echo '<font color="red">E</font>'; break;
                                                                            case 'D':  echo '<font color="brown">D</font>'; break;
                                                                            case 'CD': echo '<font color="brown">CD</font>'; break;
                                                                            case 'C':  echo '<font color="orange">C</font>'; break;
                                                                            case 'BC': echo '<font color="orange">BC</font>'; break;
                                                                            case 'B':  echo '<font color="green">B</font>'; break;
                                                                            case 'AB': echo '<font color="green">AB</font>'; break;
                                                                            case 'A':  echo '<font color="black">A</font>'; break;
                                                                        }
                                                                        $point_aktif = nbobot($pecah_nhuruf); 
                                                                    }
                                                                    
                                                                    $total_point_aktif += $point_aktif * $a->jumlah_sks;
                                                                    $jumlah_aktif += $a->jumlah_sks;
                                                                @endphp
                                                            </center>
                                                        </td>
                                                    </tr>
                                                @endforeach

                                                @php 
                                                    $ips_aktif = ($jumlah_aktif == 0) ? 0 : ($total_point_aktif / $jumlah_aktif);
                                                @endphp
                                            </tbody>
                                        </table>
                                        
                                        <table width="100%">
                                            <tr>
                                                <td align="left"><b>Jumlah SKS : {{ $jumlah_aktif }}</b></td>
                                                <td align="right"><b>Indeks Prestasi Sementara (IPS) : {{ number_format($ips_aktif, 2) }}</b></td>
                                            </tr>
                                        </table>
                                    </div>

                                    @foreach($ta_history2 as $h)
                                        <hr>
                                        @php
                                            $ta_id = $h->id;
                                            $jenis = '';
                                            
                                            if ($h->jenis == 1) $jenis = 'Ganjil';
                                            elseif ($h->jenis == 2) $jenis = 'Genap';
                                            elseif ($h->jenis == 3) $jenis = 'Antara Ganjil Genap';
                                            elseif ($h->jenis == 4) $jenis = 'Antara Genap Ganjil';
                                            
                                            // Asumsi session nim sudah diset di controller
                                            $nim_session = session('nim');
                                        @endphp
                                        
                                        <b>
                                            {{ $h->awal }} - {{ $h->akhir }} {{ $jenis }} 
                                            <a href="{{ url('master/khs/cetak_khs_history/' . $ta_id . '-' . $nim_session) }}">Download KHS</a>
                                        </b>
                                        
                                        <div class="dt-responsive table-responsive">
                                            <table class="table table-bordered nowrap">
                                                <thead>
                                                    <tr>
                                                        <th>Dosen</th>
                                                        <th>Kode</th>
                                                        <th>Nama Matakuliah</th>
                                                        <th>SKS</th>
                                                        <th>Tugas</th>
                                                        <th>UTS</th>
                                                        <th>UAS</th>
                                                        <th>Nilai Akhir</th>
                                                        <th>Nilai Huruf</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php 
                                                        $jumlah = 0; 
                                                        $total_point = 0; 
                                                    @endphp

                                                    @foreach($krs_history[$h->id] as $krs)
                                                        @php
                                                            $nhuruf_ = empty($krs->nhuruf) ? 'E' : $krs->nhuruf;
                                                            $point = nbobot($nhuruf_);
                                                            
                                                            $total_point += $point * $krs->jumlah_sks;
                                                            $jumlah += $krs->jumlah_sks;
                                                        @endphp
                                                        <tr>
                                                            <td>{{ $krs->nama_dosen }}</td>
                                                            <td>{{ $krs->kode_mata_kuliah }}</td>
                                                            <td>{{ $krs->mata_kuliah }}</td>
                                                            <td><center>{{ $krs->jumlah_sks }}</center></td>
                                                            <td><center>{{ round($krs->ntugas, 2) }}</center></td>
                                                            <td><center>{{ round($krs->nuts, 2) }}</center></td>
                                                            <td><center>{{ round($krs->nuas, 2) }}</center></td>
                                                            <td><center>{{ round($krs->nakhir, 2) }}</center></td>
                                                            <td>
                                                                <center>
                                                                    @switch($nhuruf_)
                                                                        @case('E')  <font color="red">E</font> @break
                                                                        @case('D')  <font color="brown">D</font> @break
                                                                        @case('CD') <font color="brown">CD</font> @break
                                                                        @case('C')  <font color="orange">C</font> @break
                                                                        @case('BC') <font color="orange">BC</font> @break
                                                                        @case('B')  <font color="green">B</font> @break
                                                                        @case('AB') <font color="green">AB</font> @break
                                                                        @case('A')  <font color="black">A</font> @break
                                                                    @endswitch
                                                                </center>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            
                                            @php 
                                                $ips = ($jumlah == 0) ? 0 : ($total_point / $jumlah);
                                            @endphp
                                            
                                            <table width="100%">
                                                <tr>
                                                    <td align="left"><b>Jumlah SKS : {{ $jumlah }}</b></td>
                                                    <td align="right"><b>Indeks Prestasi Sementara (IPS) : {{ number_format($ips, 2) }}</b></td>
                                                </tr>
                                            </table>
                                        </div>
                                    @endforeach


                                    @foreach($ta_history as $h)
                                        <hr>
                                        @php
                                            $ta_id = $h->id;
                                            $jenis = '';
                                            
                                            if ($h->jenis == 1) $jenis = 'Ganjil';
                                            elseif ($h->jenis == 2) $jenis = 'Genap';
                                            elseif ($h->jenis == 3) $jenis = 'Antara Ganjil Genap';
                                            elseif ($h->jenis == 4) $jenis = 'Antara Genap Ganjil';
                                            
                                            $nim_session = session('nim'); 
                                            
                                        @endphp
                                        
                                        <b>
                                            {{ $h->awal }} - {{ $h->akhir }} {{ $jenis }} 
                                            <a href="{{ url('master/khs/cetak_khs_history/' . $ta_id . '-' . $nim_session) }}">Download KHS</a>
                                        </b>
                                        
                                        <div class="dt-responsive table-responsive">
                                            <table class="table table-bordered nowrap">
                                                <thead>
                                                    <tr>
                                                        <th>Dosen</th>
                                                        <th>Kode</th>
                                                        <th>Nama Matakuliah</th>
                                                        <th>SKS</th>
                                                        <th>Tugas</th>
                                                        <th>UTS</th>
                                                        <th>UAS</th>
                                                        <th>Nilai Akhir</th>
                                                        <th>Nilai Huruf</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php 
                                                        $jumlah = 0; 
                                                        $total_point = 0; 
                                                    @endphp

                                                    @foreach($krs_history[$h->id] as $krs)
                                                        @php
                                                            $nhuruf_ = empty($krs->nhuruf) ? 'E' : $krs->nhuruf;
                                                            $point = nbobot($nhuruf_);
                                                            
                                                            $total_point += $point * $krs->jumlah_sks ?? 0;
                                                            $jumlah += $krs->jumlah_sks ?? 0;
                                                        @endphp
                                                        <tr>
                                                            <td>{{ $krs->nama_dosen }}</td>
                                                            <td>{{ $krs->kode_mata_kuliah }}</td>
                                                            <td>{{ $krs->mata_kuliah }}</td>
                                                            <td><center>{{ $krs->jumlah_sks ?? 0 }}</center></td>
                                                            <td><center>{{ round($krs->ntugas, 2) }}</center></td>
                                                            <td><center>{{ round($krs->nuts, 2) }}</center></td>
                                                            <td><center>{{ round($krs->nuas, 2) }}</center></td>
                                                            <td><center>{{ round($krs->nakhir, 2) }}</center></td>
                                                            <td>
                                                                <center>
                                                                    @switch($nhuruf_)
                                                                        @case('E')  <font color="red">E</font> @break
                                                                        @case('D')  <font color="brown">D</font> @break
                                                                        @case('CD') <font color="brown">CD</font> @break
                                                                        @case('C')  <font color="orange">C</font> @break
                                                                        @case('BC') <font color="orange">BC</font> @break
                                                                        @case('B')  <font color="green">B</font> @break
                                                                        @case('AB') <font color="green">AB</font> @break
                                                                        @case('A')  <font color="black">A</font> @break
                                                                    @endswitch
                                                                </center>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            
                                            @php 
                                                $ips = ($jumlah == 0) ? 0 : ($total_point / $jumlah);
                                            @endphp
                                            
                                            <table width="100%">
                                                <tr>
                                                    <td align="left"><b>Jumlah SKS : {{ $jumlah }}</b></td>
                                                    <td align="right"><b>Indeks Prestasi Sementara (IPS) : {{ number_format($ips, 2) }}</b></td>
                                                </tr>
                                            </table>
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