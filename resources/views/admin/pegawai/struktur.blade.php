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
									<form method="POST" action="{{ url('pegawai/save_struktur') }}" autocomplete="off">
                                        @csrf 
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label>Ketua Sekolah Tinggi : </label>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-inline">
                                                    <select name="ketua_st" class="js-example-basic-single col-sm-12">
                                                        <option selected value="{{ $s2->ketua_st }} - {{ $s->ketua_st }}">{{ $s->ketua_st }}</option>
                                                        @foreach($npp as $row)
                                                            <option value="{{ $row->npp }} - {{ $row->nama }}">
                                                                {{ $row->npp }} - {{ strtoupper($row->nama) }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row" style="margin-top: 10px;">
                                            <div class="col-md-4">
                                                <label>Pembantu Ketua I Bidang Akademik : </label>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-inline">
                                                    <select name="pembantu_1" class="js-example-basic-single col-sm-12">
                                                        <option selected value="{{ $s2->pembantu_1 }} - {{ $s->pembantu_1 }}">{{ $s->pembantu_1 }}</option>
                                                        @foreach($npp as $row)
                                                            <option value="{{ $row->npp }} - {{ $row->nama }}">
                                                                {{ $row->npp }} - {{ strtoupper($row->nama) }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row" style="margin-top: 10px;">
                                            <div class="col-md-4">
                                                <label>Pembantu Ketua II Bidang SDM & Keuangan : </label>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-inline">
                                                    <select name="pembantu_2" class="js-example-basic-single col-sm-12">
                                                        <option selected value="{{ $s2->pembantu_2 }} - {{ $s->pembantu_2 }}">{{ $s->pembantu_2 }}</option>
                                                        @foreach($npp as $row)
                                                            <option value="{{ $row->npp }} - {{ $row->nama }}">
                                                                {{ $row->npp }} - {{ strtoupper($row->nama) }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row" style="margin-top: 10px;">
                                            <div class="col-md-4">
                                                <label>Pembantu Ketua III Bidang Kemahasiswaan, Humas, Kerjasama & Alumni : </label>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-inline">
                                                    <select name="pembantu_3" class="js-example-basic-single col-sm-12">
                                                        <option selected value="{{ $s2->pembantu_3 }} - {{ $s->pembantu_3 }}">{{ $s->pembantu_3 }}</option>
                                                        @foreach($npp as $row)
                                                            <option value="{{ $row->npp }} - {{ $row->nama }}">
                                                                {{ $row->npp }} - {{ strtoupper($row->nama) }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row" style="margin-top: 10px;">
                                            <div class="col-md-4">
                                                <label>Ketua Program Studi D3 Farmasi : </label>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-inline">
                                                    <select name="prodi_d3" class="js-example-basic-single col-sm-12">
                                                        <option selected value="{{ $s2->prodi_d3 }} - {{ $s->prodi_d3 }}">{{ $s->prodi_d3 }}</option>
                                                        @foreach($npp as $row)
                                                            <option value="{{ $row->npp }} - {{ $row->nama }}">
                                                                {{ $row->npp }} - {{ strtoupper($row->nama) }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row" style="margin-top: 10px;">
                                            <div class="col-md-4">
                                                <label>Ketua Program Studi S1 Farmasi : </label>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-inline">
                                                    <select name="prodi_s1" class="js-example-basic-single col-sm-12">
                                                        <option selected value="{{ $s2->prodi_s1 }} - {{ $s->prodi_s1 }}">{{ $s->prodi_s1 }}</option>
                                                        @foreach($npp as $row)
                                                            <option value="{{ $row->npp }} - {{ $row->nama }}">
                                                                {{ $row->npp }} - {{ strtoupper($row->nama) }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row" style="margin-top: 10px;">
                                            <div class="col-md-4">
                                                <label>Sekertaris Program Studi D3 Farmasi : </label>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-inline">
                                                    <select name="sekertaris_prodi_d3" class="js-example-basic-single col-sm-12">
                                                        <option selected value="{{ $s2->sekertaris_prodi_d3 }} - {{ $s->sekertaris_prodi_d3 }}">{{ $s->sekertaris_prodi_d3 }}</option>
                                                        @foreach($npp as $row)
                                                            <option value="{{ $row->npp }} - {{ $row->nama }}">
                                                                {{ $row->npp }} - {{ strtoupper($row->nama) }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row" style="margin-top: 10px;">
                                            <div class="col-md-4">
                                                <label>Sekertaris Program Studi S1 Farmasi : </label>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-inline">
                                                    <select name="sekertaris_prodi_s1" class="js-example-basic-single col-sm-12">
                                                        <option selected value="{{ $s2->sekertaris_prodi_s1 }} - {{ $s->sekertaris_prodi_s1 }}">{{ $s->sekertaris_prodi_s1 }}</option>
                                                        @foreach($npp as $row)
                                                            <option value="{{ $row->npp }} - {{ $row->nama }}">
                                                                {{ $row->npp }} - {{ strtoupper($row->nama) }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row" style="margin-top: 10px;">
                                            <div class="col-md-4">
                                                <label>Ketua Lembaga Penjaminan Mutu : </label>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-inline">
                                                    <select name="ketua_mutu" class="js-example-basic-single col-sm-12">
                                                        <option selected value="{{ $s2->ketua_mutu }} - {{ $s->ketua_mutu }}">{{ $s->ketua_mutu }}</option>
                                                        @foreach($npp as $row)
                                                            <option value="{{ $row->npp }} - {{ $row->nama }}">
                                                                {{ $row->npp }} - {{ strtoupper($row->nama) }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row" style="margin-top: 10px;">
                                            <div class="col-md-4">
                                                <label>Ketua Lembaga Penelitian & Pengabdian kepada Masyarakat : </label>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-inline">
                                                    <select name="ketua_penelitian" class="js-example-basic-single col-sm-12">
                                                        <option selected value="{{ $s2->ketua_penelitian }} - {{ $s->ketua_penelitian }}">{{ $s->ketua_penelitian }}</option>
                                                        @foreach($npp as $row)
                                                            <option value="{{ $row->npp }} - {{ $row->nama }}">
                                                                {{ $row->npp }} - {{ strtoupper($row->nama) }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row" style="margin-top: 10px;">
                                            <div class="col-md-4">
                                                <label>Sekertaris Lembaga Penelitian & Pengabdian Kepada Masyarakat : </label>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-inline">
                                                    <select name="sekertaris_penelitian" class="js-example-basic-single col-sm-12">
                                                        <option selected value="{{ $s2->sekertaris_penelitian }} - {{ $s->sekertaris_penelitian }}">{{ $s->sekertaris_penelitian }}</option>
                                                        @foreach($npp as $row)
                                                            <option value="{{ $row->npp }} - {{ $row->nama }}">
                                                                {{ $row->npp }} - {{ strtoupper($row->nama) }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row" style="margin-top: 10px;">
                                            <div class="col-md-4">
                                                <label>Koordinator Laboratorium : </label>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-inline">
                                                    <select name="koor_lab" class="js-example-basic-single col-sm-12">
                                                        <option selected value="{{ $s2->koor_lab }} - {{ $s->koor_lab }}">{{ $s->koor_lab }}</option>
                                                        @foreach($npp as $row)
                                                            <option value="{{ $row->npp }} - {{ $row->nama }}">
                                                                {{ $row->npp }} - {{ strtoupper($row->nama) }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row" style="margin-top: 10px;">
                                            <div class="col-md-4">
                                                <label>Koordinator Sarana & Prasarana : </label>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-inline">
                                                    <select name="koor_sarana" class="js-example-basic-single col-sm-12">
                                                        <option selected value="{{ $s2->koor_sarana }} - {{ $s->koor_sarana }}">{{ $s->koor_sarana }}</option>
                                                        @foreach($npp as $row)
                                                            <option value="{{ $row->npp }} - {{ $row->nama }}">
                                                                {{ $row->npp }} - {{ strtoupper($row->nama) }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <br>
                                        <button type="submit" class="btn btn-primary" style="height:30px; padding:5px;"> 
                                            <i class="fa fa-save"></i> Simpan 
                                        </button>
                                    </form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
@endsection
@section('local-js')
    <script>
        $("document").ready(function(){
            $(".js-example-basic-single").select2();
        });
    </script>
@endsection