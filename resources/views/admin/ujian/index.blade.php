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

                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title"><i class="fa-solid fa-file-pen me-1"></i>{{ $title }}</h4>
                    </div>

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
                                        <table id="table-ujian-reguler" class="table table-bordered table-striped">
                                            <thead class="bg-primary text-white text-center">
                                                <tr>
                                                    <th style="width: 50px;">No</th>
                                                    <th>Kode MK</th>
                                                    <th style="min-width: 150px; white-space: normal;">Mata Kuliah</th>
                                                    <th style="max-width: 150px; white-space: normal;">Dosen</th>
                                                    <th style="width: 80px;">Rombel</th>
                                                    <th style="max-width: 150px; white-space: normal;">Jadwal Kuliah</th>
                                                    <th>Status Pengaturan</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($jadwalReguler as $idx => $row)
                                                    <tr>
                                                        <td class="text-center">{{ $idx + 1 }}</td>
                                                        <td>{{ $row->kode_mata_kuliah }}</td>
                                                        <td style="white-space: normal; word-break: break-word; min-width: 150px;">{{ $row->nama_mata_kuliah ?? '-' }}</td>
                                                        <td style="white-space: normal; word-break: break-word; max-width: 150px;">{{ trim($row->nama_dosen ?? '-') }}</td>
                                                        <td class="text-center">{{ $row->rombel ?? '-' }}</td>
                                                        <td style="white-space: normal; word-break: break-word; max-width: 150px;">{{ $row->hari ?? '-' }} / {{ $row->sesi ?? '-' }} / {{ $row->ruang ?? '-' }}</td>
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
                                        <table id="table-ujian-rpl" class="table table-bordered table-striped">
                                            <thead class="bg-primary text-white text-center">
                                                <tr>
                                                    <th style="width: 50px;">No</th>
                                                    <th>Kode MK</th>
                                                    <th style="min-width: 150px; white-space: normal;">Mata Kuliah</th>
                                                    <th style="max-width: 150px; white-space: normal;">Dosen</th>
                                                    <th style="width: 80px;">Rombel</th>
                                                    <th style="max-width: 150px; white-space: normal;">Jadwal Kuliah</th>
                                                    <th>Status Pengaturan</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($jadwalRpl as $idx => $row)
                                                    <tr>
                                                        <td class="text-center">{{ $idx + 1 }}</td>
                                                        <td>{{ $row->kode_mata_kuliah }}</td>
                                                        <td style="white-space: normal; word-break: break-word; min-width: 150px;">{{ $row->nama_mata_kuliah ?? '-' }}</td>
                                                        <td style="white-space: normal; word-break: break-word; max-width: 150px;">{{ trim($row->nama_dosen ?? '-') }}</td>
                                                        <td class="text-center">{{ $row->rombel ?? '-' }}</td>
                                                        <td style="white-space: normal; word-break: break-word; max-width: 150px;">{{ $row->hari ?? '-' }} / {{ $row->sesi ?? '-' }} / {{ $row->ruang ?? '-' }}</td>
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
@endsection

@section('local-js')
<script>
    $(document).ready(function () {
        const dtSettings = {
            pageLength: 25,
            language: {
                paginate: {
                    next: '>',
                    previous: '<'
                }
            }
        };
        const dtReguler = $('#table-ujian-reguler').DataTable(dtSettings);
        const dtRpl = $('#table-ujian-rpl').DataTable(dtSettings);

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
