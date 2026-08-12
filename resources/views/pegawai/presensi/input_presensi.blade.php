@extends('layouts.default', ['CurrentPage' => $CurrentPage])

@section('local-css')
    <style>
        .dropdown-toggle {
            width: 100% !important;
            padding-left: 20px !important;
        }
    </style>
@endsection

@section('content')
    @php
        if (!is_null($memos)) {
            $memo = $memos->memo;
            $sub  = $memos->sub;
            $hdr  = $memos->mhs_hadir;
        } else {
            $memo = null;
            $sub  = null;
            $hdr  = 0;
        }
    @endphp

    <div class="content-body">
        <div class="container">
            <div class="mb-4 pb-3">
                <a href="{{ url('dosen/presensi/tanggal/' . $jadwal_id) }}" class="btn btn-success">
                    <i class="fa fa-arrow-left"></i> Kembali
                </a>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="filter cm-content-box box-primary">
                        <div class="content-title SlideToolHeader">
                            <div class="cpa">
                                <i class="fa-solid fa-file-lines me-1"></i>{{ $title }}
                            </div>
                            <div class="tools">
                                <a href="javascript:void(0);" class="expand handle"><i class="fal fa-angle-down"></i></a>
                            </div>
                        </div>
                        <div class="cm-content-body form excerpt">
                            <div class="card-body pb-4">
                                @if(session('presensi'))
                                    <div class="alert alert-success alert-dismissible fade show">
                                        {{ session('presensi') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                @endif
                                @if(session('error'))
                                    <div class="alert alert-danger alert-dismissible fade show">
                                        {{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                @endif

                                <form action="{{ url('dosen/presensi/simpan') }}" method="post">
                                    @csrf
                                    <div class="dt-responsive table-responsive">

                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Materi / Kontrak Perkuliahan :</label>
                                            <textarea class="form-control" name="memo" rows="3">{{ $memo }}</textarea>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Sub Pembahasan :</label>
                                            <textarea class="form-control" name="sub" rows="3">{{ $sub }}</textarea>
                                        </div>

                                        <p class="fw-bold">Jumlah Mahasiswa Hadir : {{ $hdr }}</p>

                                        <table class="table table-striped table-bordered nowrap">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>NIM</th>
                                                    <th>Nama</th>
                                                    <th>Tanggal</th>
                                                    <th>Status Presensi</th>
                                                    <th>TTD Mahasiswa</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $no       = 1;
                                                    $total_rec = 0;
                                                @endphp
                                                @foreach($mhs as $a)
                                                    @php
                                                        $status = \Illuminate\Support\Facades\DB::table('master_presensi')
                                                            ->where('nim', $a->nim)
                                                            ->where('id_jadwal', $a->id_jadwal)
                                                            ->where('tgl_pertemuan', $tgl_pertemuan)
                                                            ->first();

                                                        $stat_val = $status->status ?? null;
                                                        $ttd_val = $status->ttd ?? null;
                                                        $total_rec++;
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $no++ }}</td>
                                                        <td>
                                                            <input type="hidden" name="id_jadwal[]" value="{{ $a->id_jadwal }}">
                                                            <input type="hidden" name="nim[]" value="{{ $a->nim }}">
                                                            {{ $a->nim }}
                                                        </td>
                                                        <td>{{ $a->nama }}</td>
                                                        <td>
                                                            <input type="hidden" name="tgl[]" value="{{ $tgl_pertemuan }}">
                                                            {{ $tgl_pertemuan ? date('d/m/Y', strtotime($tgl_pertemuan)) : '-' }}
                                                        </td>
                                                        <td>
                                                            <select name="status[]" class="form-select">
                                                                <option value="0" {{ empty($stat_val) || $stat_val == 0 ? 'selected' : '' }}>Belum Absen</option>
                                                                <option value="1" {{ $stat_val == 1 ? 'selected' : '' }}>Hadir</option>
                                                                <option value="2" {{ $stat_val == 2 ? 'selected' : '' }}>Izin</option>
                                                                <option value="3" {{ $stat_val == 3 ? 'selected' : '' }}>Tanpa Keterangan</option>
                                                            </select>
                                                        </td>
                                                        <td class="text-center">
                                                            @php
                                                                $hasSig = false;
                                                                $sigSrc = null;
                                                                if (!empty($ttd_val)) {
                                                                    if (\Illuminate\Support\Str::startsWith($ttd_val, 'data:image')) {
                                                                        $hasSig = true;
                                                                        $sigSrc = $ttd_val;
                                                                    } else {
                                                                        $customPath = 'images/ttd/' . $ttd_val;
                                                                        if (is_file(public_path($customPath))) {
                                                                            $hasSig = true;
                                                                            $sigSrc = asset($customPath);
                                                                        } elseif (is_file(public_path($ttd_val))) {
                                                                            $hasSig = true;
                                                                            $sigSrc = asset($ttd_val);
                                                                        }
                                                                    }
                                                                }
                                                            @endphp

                                                            @if($hasSig)
                                                                <img src="{{ $sigSrc }}" alt="TTD {{ $a->nim }}" style="max-width: 180px; max-height: 80px; border: 1px solid #ddd; border-radius: 6px; background: #fff;">
                                                            @else
                                                                <span class="text-muted small">Belum ada TTD</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>

                                        <input type="hidden" name="total" value="{{ $total_rec }}">
                                        <input type="hidden" name="id_pertemuan" value="{{ $id_pertemuan }}">

                                        <div class="mt-3">
                                            <button type="submit" class="btn btn-success">
                                                <i class="fa fa-save"></i> Simpan
                                            </button>
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
