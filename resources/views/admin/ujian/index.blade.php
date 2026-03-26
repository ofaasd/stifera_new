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
                            <i class="fa-solid fa-file-pen me-1"></i>{{ $title }}
                        </div>
                        <div class="tools">
                            <a href="javascript:void(0);" class="expand handle"><i class="fal fa-angle-down"></i></a>
                        </div>
                    </div>

                    <div class="cm-content-body form excerpt">
                        <div class="card-body pb-4">
                            <ul class="nav nav-tabs mb-3" id="ujianTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="reguler-tab" data-bs-toggle="tab" data-bs-target="#reguler-pane" type="button" role="tab">Reguler</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="rpl-tab" data-bs-toggle="tab" data-bs-target="#rpl-pane" type="button" role="tab">RPL</button>
                                </li>
                            </ul>

                            <div class="tab-content" id="ujianTabContent">
                                <div class="tab-pane fade show active" id="reguler-pane" role="tabpanel">
                                    <div class="mb-2 fw-bold">
                                        Tahun Ajaran Aktif Reguler:
                                        @if($tahunReguler)
                                            {{ $tahunReguler->awal }}/{{ $tahunReguler->akhir }}
                                            ({{ (int) $tahunReguler->jenis === 1 ? 'Ganjil' : ((int) $tahunReguler->jenis === 2 ? 'Genap' : '-') }})
                                        @else
                                            -
                                        @endif
                                    </div>

                                    <div class="table-responsive">
                                        <table id="table-ujian-reguler" class="table table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Kode MK</th>
                                                    <th>Mata Kuliah</th>
                                                    <th>Dosen</th>
                                                    <th>Rombel</th>
                                                    <th>Jadwal Kuliah</th>
                                                    <th>Status Pengaturan</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($jadwalReguler as $idx => $row)
                                                    <tr>
                                                        <td>{{ $idx + 1 }}</td>
                                                        <td>{{ $row->kode_mata_kuliah }}</td>
                                                        <td>{{ $row->nama_mata_kuliah ?? '-' }}</td>
                                                        <td>{{ trim($row->nama_dosen ?? '-') }}</td>
                                                        <td>{{ $row->rombel ?? '-' }}</td>
                                                        <td>{{ $row->hari ?? '-' }} / {{ $row->sesi ?? '-' }} / {{ $row->ruang ?? '-' }}</td>
                                                        <td>
                                                            @if((int) $row->sudah_diatur === 1)
                                                                <span class="badge bg-success">Sudah Diatur</span>
                                                            @else
                                                                <span class="badge bg-warning text-dark">Belum Diatur</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <a href="{{ url('master/pengaturan-ujian/detail/' . $row->id) }}" class="btn btn-primary btn-sm" title="Detail Pengaturan Ujian">
                                                                <i class="fa fa-eye"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr><td colspan="8" class="text-center">Belum ada jadwal untuk Reguler.</td></tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="rpl-pane" role="tabpanel">
                                    <div class="mb-2 fw-bold">
                                        Tahun Ajaran Aktif RPL:
                                        @if($tahunRpl)
                                            {{ $tahunRpl->awal }}/{{ $tahunRpl->akhir }}
                                            ({{ (int) $tahunRpl->jenis === 1 ? 'Ganjil' : ((int) $tahunRpl->jenis === 2 ? 'Genap' : '-') }})
                                        @else
                                            -
                                        @endif
                                    </div>

                                    <div class="table-responsive">
                                        <table id="table-ujian-rpl" class="table table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Kode MK</th>
                                                    <th>Mata Kuliah</th>
                                                    <th>Dosen</th>
                                                    <th>Rombel</th>
                                                    <th>Jadwal Kuliah</th>
                                                    <th>Status Pengaturan</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($jadwalRpl as $idx => $row)
                                                    <tr>
                                                        <td>{{ $idx + 1 }}</td>
                                                        <td>{{ $row->kode_mata_kuliah }}</td>
                                                        <td>{{ $row->nama_mata_kuliah ?? '-' }}</td>
                                                        <td>{{ trim($row->nama_dosen ?? '-') }}</td>
                                                        <td>{{ $row->rombel ?? '-' }}</td>
                                                        <td>{{ $row->hari ?? '-' }} / {{ $row->sesi ?? '-' }} / {{ $row->ruang ?? '-' }}</td>
                                                        <td>
                                                            @if((int) $row->sudah_diatur === 1)
                                                                <span class="badge bg-success">Sudah Diatur</span>
                                                            @else
                                                                <span class="badge bg-warning text-dark">Belum Diatur</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <a href="{{ url('master/pengaturan-ujian/detail/' . $row->id) }}" class="btn btn-primary btn-sm" title="Detail Pengaturan Ujian">
                                                                <i class="fa fa-eye"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr><td colspan="8" class="text-center">Belum ada jadwal untuk RPL.</td></tr>
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
    </div>
</div>
@endsection

@section('local-js')
<script>
    $(document).ready(function () {
        const dtReguler = $('#table-ujian-reguler').DataTable({ responsive: true, pageLength: 25 });
        const dtRpl = $('#table-ujian-rpl').DataTable({ responsive: true, pageLength: 25 });

        $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function () {
            dtReguler.columns.adjust().draw(false);
            dtRpl.columns.adjust().draw(false);
        });

        const activeTab = @json($activeTab ?? null);
        if (activeTab) {
            const tabButton = document.querySelector(`button[data-bs-target="#${activeTab}"]`);
            if (tabButton) {
                const tab = new bootstrap.Tab(tabButton);
                tab.show();
            }
        }
    });
</script>
@endsection
