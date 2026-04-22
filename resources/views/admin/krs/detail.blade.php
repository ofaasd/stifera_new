@extends('layouts.default', ['CurrentPage' => $CurrentPage])

@section('content')
<div class="content-body">
    <div class="container">
        <div class="row">
            <div class="col-xl-12">

                @if(session('status'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="filter cm-content-box box-primary">
                    <div class="content-title SlideToolHeader">
                        <div class="cpa">
                            <i class="fa-solid fa-clipboard-list me-1"></i>Detail KRS Mahasiswa
                        </div>
                        <div class="tools">
                            <a href="javascript:void(0);" class="expand handle"><i class="fal fa-angle-down"></i></a>
                        </div>
                    </div>

                    <div class="cm-content-body form excerpt">
                        <div class="card-body pb-4">

                            <div class="mb-3">
                                <a href="{{ url('master/krs') }}" class="btn btn-secondary">
                                    <i class="fa fa-arrow-left"></i> Kembali
                                </a>
                                <a href="{{ url('master/krs/download/' . $idTahun . '/' . ($mahasiswa->nim ?? '')) }}" class="btn btn-primary ms-2">
                                    <i class="fa fa-download"></i> Download KRS
                                </a>
                            </div>

                            {{-- Info Mahasiswa --}}
                            <div class="table-responsive mb-3">
                                <table class="table table-borderless w-auto" style="min-width: 480px;">
                                    <tbody>
                                        <tr>
                                            <td class="fw-bold" style="width:160px;">NIM</td>
                                            <td class="px-2">:</td>
                                            <td>{{ $mahasiswa->nim ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Nama Mahasiswa</td>
                                            <td class="px-2">:</td>
                                            <td>{{ $mahasiswa->nama ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Tahun Ajaran</td>
                                            <td class="px-2">:</td>
                                            <td>
                                                {{ ($tahun->awal ?? '') . ' - ' . ($tahun->akhir ?? '') }}
                                                @if(!empty($jenisTA)) ({{ $jenisTA }}) @endif
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            @if($adaNilai)
                                <div class="alert alert-warning">
                                    <i class="fa fa-exclamation-triangle me-1"></i>
                                    Sebagian mata kuliah pada KRS ini sudah memiliki nilai yang dimasukkan.
                                    Baris yang sudah ada nilai tidak dapat diubah atau dihapus.
                                </div>
                            @endif

                            {{-- Tabel KRS --}}
                            <div class="table-responsive">
                                <table id="table-detail-krs" class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Kode MK</th>
                                            <th>Nama Mata Kuliah</th>
                                            <th>SKS</th>
                                            <th>Hari</th>
                                            <th>Sesi</th>
                                            <th>Ruang</th>
                                            <th>Dosen</th>
                                            <th>Status Nilai</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($krsRows as $idx => $row)
                                            @php
                                                $ruangRaw = (string) ($row->ruang ?? '');
                                                $ruangDecoded = urldecode($ruangRaw);
                                                $ruangClean = preg_replace('/\s+/', ' ', trim($ruangDecoded));
                                            @endphp
                                            <tr>
                                                <td>{{ $idx + 1 }}</td>
                                                <td>{{ $row->mata_kuliah ?? '-' }}</td>
                                                <td>{{ $row->nama_mata_kuliah ?? $row->mata_kuliah ?? '-' }}</td>
                                                <td>{{ $row->sks ?? '-' }}</td>
                                                <td>{{ $row->hari ?? '-' }}</td>
                                                <td>{{ $row->sesi ?? '-' }}</td>
                                                <td>{{ $ruangClean !== '' ? $ruangClean : '-' }}</td>
                                                <td>{{ trim($row->nama_dosen ?? '-') }}</td>
                                                <td>
                                                    @if((int)($row->ada_nilai ?? 0) === 1)
                                                        <span class="badge bg-danger">Ada Nilai</span>
                                                    @else
                                                        <span class="badge bg-secondary">Belum Ada Nilai</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if((int)($row->ada_nilai ?? 0) === 0)
                                                        <div class="d-flex gap-1">
                                                            <a href="{{ url('master/krs/edit-krs/' . $row->id) }}"
                                                               class="btn btn-warning btn-sm"
                                                               title="Edit KRS">
                                                                <i class="fa fa-edit"></i>
                                                            </a>
                                                            <form action="{{ url('master/krs/hapus-krs/' . $row->id) }}"
                                                                  method="POST"
                                                                  onsubmit="return confirm('Hapus mata kuliah {{ addslashes($row->nama_mata_kuliah ?? $row->mata_kuliah) }} dari KRS mahasiswa ini?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger btn-sm" title="Hapus KRS">
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    @else
                                                        <span class="badge bg-dark" title="Tidak dapat diubah karena sudah ada nilai">
                                                            <i class="fa fa-lock me-1"></i>Terkunci
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="10" class="text-center">Belum ada data KRS untuk mahasiswa ini.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    @if($krsRows->isNotEmpty())
                                        <tfoot>
                                            <tr>
                                                <td colspan="3" class="fw-bold text-end">Total SKS</td>
                                                <td class="fw-bold">{{ $krsRows->sum('sks') }}</td>
                                                <td colspan="6"></td>
                                            </tr>
                                        </tfoot>
                                    @endif
                                </table>
                            </div>

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
    $(document).ready(function () {
        $('#table-detail-krs').DataTable({ responsive: true, pageLength: 25, ordering: false });
    });
</script>
@endsection
