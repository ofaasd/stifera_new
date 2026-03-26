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
                            <i class="fa-solid fa-key me-1"></i>{{ $title }}
                        </div>
                        <div class="tools">
                            <a href="javascript:void(0);" class="expand handle"><i class="fal fa-angle-down"></i></a>
                        </div>
                    </div>

                    <div class="cm-content-body form excerpt">
                        <div class="card-body pb-4">
                            <div class="alert alert-warning">
                                Password akan direset menjadi hash dari NPP masing-masing pegawai.
                            </div>

                            <div class="table-responsive">
                                <table id="table-reset-password-pegawai" class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>NPP</th>
                                            <th>Username</th>
                                            <th>Nama Pegawai</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($pegawai as $idx => $p)
                                            <tr>
                                                <td>{{ $idx + 1 }}</td>
                                                <td>{{ $p->npp ?? '-' }}</td>
                                                <td>{{ $p->usrnm ?? '-' }}</td>
                                                <td>{{ $p->nama_lengkap ?? $p->nama ?? '-' }}</td>
                                                <td>
                                                    @if((int) $p->status === 1)
                                                        <span class="badge bg-success">Aktif</span>
                                                    @else
                                                        <span class="badge bg-secondary">Non Aktif</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if(!empty($p->npp))
                                                        <form method="POST" action="{{ url('akademik/perwalian/reset_password/' . $p->npp) }}" onsubmit="return confirm('Reset password pegawai ini? Password baru akan mengikuti NPP dan disimpan dalam hash.')">
                                                            @csrf
                                                            <button type="submit" class="btn btn-warning btn-sm" title="Reset Password">
                                                                <i class="fa fa-key"></i>
                                                            </button>
                                                        </form>
                                                    @else
                                                        <span class="text-muted">NPP kosong</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">Data pegawai tidak tersedia.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
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
        $('#table-reset-password-pegawai').DataTable({
            responsive: true,
            pageLength: 25
        });
    });
</script>
@endsection
