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

                <div class="filter cm-content-box box-primary">
                    <div class="content-title SlideToolHeader">
                        <div class="cpa">
                            <i class="fa fa-user-secret me-1"></i>{{ $title }}
                        </div>
                        <div class="tools">
                            <a href="javascript:void(0);" class="expand handle"><i class="fal fa-angle-down"></i></a>
                        </div>
                    </div>
                    <div class="cm-content-body form excerpt">
                        <div class="card-body pb-4">
                            <p class="text-muted mb-3">Log ini mencatat setiap aktivitas admin yang masuk ke akun mahasiswa melalui fitur impersonasi.</p>
                            <div class="table-responsive">
                                <table id="log-table" class="table table-striped table-bordered nowrap">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Admin</th>
                                            <th>NIM Mahasiswa</th>
                                            <th>IP Address</th>
                                            <th>Waktu Masuk</th>
                                            <th>Waktu Keluar</th>
                                            <th>Durasi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($logs as $i => $row)
                                            @php
                                                $durasi = '-';
                                                if ($row->logout_at && $row->login_at) {
                                                    $diff = \Carbon\Carbon::parse($row->login_at)->diffInMinutes(\Carbon\Carbon::parse($row->logout_at));
                                                    $durasi = $diff . ' menit';
                                                }
                                            @endphp
                                            <tr>
                                                <td>{{ $i + 1 }}</td>
                                                <td><span class="badge badge-danger light">{{ $row->admin_usrnm }}</span></td>
                                                <td>{{ $row->nim }}</td>
                                                <td>{{ $row->ip_address ?? '-' }}</td>
                                                <td>{{ $row->login_at ? \Carbon\Carbon::parse($row->login_at)->format('d-m-Y H:i:s') : '-' }}</td>
                                                <td>{{ $row->logout_at ? \Carbon\Carbon::parse($row->logout_at)->format('d-m-Y H:i:s') : '<span class="badge badge-warning light">Belum keluar</span>' }}</td>
                                                <td>{{ $durasi }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center">Belum ada log impersonasi.</td>
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
        $('#log-table').DataTable({
            responsive: true,
            order: [[4, 'desc']],
            pageLength: 25
        });
    });
</script>
@endsection
