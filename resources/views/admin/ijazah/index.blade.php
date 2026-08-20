@extends('layouts.default')

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <div class="row page-titles">
            <ol class="breadcrumb">
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Manajemen Ijazah & Transkrip</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0)">Data Periode</a></li>
            </ol>
        </div>

        @if(session('success'))
            <div class="alert alert-success solid alert-dismissible fade show">
                <strong>Sukses!</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header border-0 pb-0 d-flex justify-content-between">
                        <h4 class="card-title">Periode Pengurus Ijazah & Transkrip</h4>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPeriodeModal">
                            <i class="fas fa-plus me-2"></i>Tambah Periode
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped custom-table" id="tableIjazah" style="min-width: 845px">
                                <thead>
                                    <tr>
                                        <th>Nama Periode</th>
                                        <th>Tgl Wisuda</th>
                                        <th>Daftar Pejabat Penandatangan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($periodes as $p)
                                    <tr>
                                        <td><strong>{{ $p->nama_periode }}</strong></td>
                                        <td>{{ \Carbon\Carbon::parse($p->tanggal_wisuda)->format('d F Y') }}</td>
                                        <td style="font-size: 12px; line-height:1.2;">
                                            <strong>Ketua:</strong> {{ $p->nama_ketua ?? '-' }} <br>
                                            <strong>PUKET 1:</strong> {{ $p->nama_puket_1 ?? '-' }} <br>
                                            <strong>Ka. Prodi S1:</strong> {{ $p->nama_kaprodi_s1 ?? '-' }} <br>
                                            <strong>Ka. Prodi D3:</strong> {{ $p->nama_kaprodi_d3 ?? '-' }}
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.ijazah.show', $p->id) }}" class="btn btn-sm btn-info shadow"><i class="fas fa-eye"></i> Lihat Mahasiswa</a>
                                            <button class="btn btn-sm btn-warning shadow" data-bs-toggle="modal" data-bs-target="#editPeriodeModal{{ $p->id }}"><i class="fas fa-edit"></i> Pejabat</button>
                                            <form action="{{ route('admin.ijazah.periode.destroy', $p->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus periode ini? Semua dokumen ijazah di dalamnya juga akan terhapus!');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm shadow"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>

                                    <!-- Edit Modal -->
                                    <div class="modal fade" id="editPeriodeModal{{ $p->id }}">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Pejabat Periode: {{ $p->nama_periode }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form action="{{ route('admin.ijazah.updatePeriode', $p->id) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">Nama Ketua</label>
                                                                <input type="text" name="nama_ketua" class="form-control" value="{{ $p->nama_ketua }}">
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">NIP Ketua</label>
                                                                <input type="text" name="nip_ketua" class="form-control" value="{{ $p->nip_ketua }}">
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">Nama PUKET 1</label>
                                                                <input type="text" name="nama_puket_1" class="form-control" value="{{ $p->nama_puket_1 }}">
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">NIP PUKET 1</label>
                                                                <input type="text" name="nip_puket_1" class="form-control" value="{{ $p->nip_puket_1 }}">
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">Nama Kaprodi S-1</label>
                                                                <input type="text" name="nama_kaprodi_s1" class="form-control" value="{{ $p->nama_kaprodi_s1 }}">
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">NIP Kaprodi S-1</label>
                                                                <input type="text" name="nip_kaprodi_s1" class="form-control" value="{{ $p->nip_kaprodi_s1 }}">
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">Nama Kaprodi D-III</label>
                                                                <input type="text" name="nama_kaprodi_d3" class="form-control" value="{{ $p->nama_kaprodi_d3 }}">
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">NIP Kaprodi D-III</label>
                                                                <input type="text" name="nip_kaprodi_d3" class="form-control" value="{{ $p->nip_kaprodi_d3 }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-primary">Simpan Pejabat</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Add -->
<div class="modal fade" id="addPeriodeModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Periode Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.ijazah.storePeriode') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Periode</label>
                        <input type="text" name="nama_periode" class="form-control" placeholder="Cth: Wisuda September 2025" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Wisuda (Cetak Dokumen)</label>
                        <input type="date" name="tanggal_wisuda" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('local-js')
<script>
    $(document).ready(function() {
        if ($.fn.DataTable) {
            $('#tableIjazah').DataTable({
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ hingga _END_ dari _TOTAL_ entri",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    }
                }
            });
        }
    });
</script>
@endsection
