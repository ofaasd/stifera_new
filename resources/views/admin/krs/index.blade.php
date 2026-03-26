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
                            <i class="fa-solid fa-clipboard-list me-1"></i>Kartu Rencana Studi (KRS)
                        </div>
                        <div class="tools">
                            <a href="javascript:void(0);" class="expand handle"><i class="fal fa-angle-down"></i></a>
                        </div>
                    </div>

                    @if(!empty($traceSqlEnabled) && !empty($traceSqlData))
                        <div class="alert alert-warning m-3 mb-0">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <strong>SQL Trace Aktif</strong>
                                <a class="btn btn-sm btn-light" href="{{ url('master/krs') }}">Matikan Trace</a>
                            </div>
                            <small>Trace ini hanya tampil saat URL memakai parameter ?trace_sql=1</small>
                            <hr>
                            @foreach($traceSqlData as $item)
                                <div class="mb-3">
                                    <div><strong>{{ $item['label'] }}</strong></div>
                                    <div><small>Raw SQL:</small></div>
                                    <pre class="bg-light p-2 mb-1" style="white-space: pre-wrap;">{{ $item['sql'] }}</pre>
                                    <div><small>Bindings:</small></div>
                                    <pre class="bg-light p-2 mb-1" style="white-space: pre-wrap;">{{ json_encode($item['bindings'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                    <div><small>Final SQL:</small></div>
                                    <pre class="bg-light p-2" style="white-space: pre-wrap;">{{ $item['final_sql'] }}</pre>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="cm-content-body form excerpt">
                        <div class="card-body pb-4">
                            <ul class="nav nav-tabs mb-3" id="krsTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="reguler-tab" data-bs-toggle="tab" data-bs-target="#reguler-pane" type="button" role="tab">Reguler</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="rpl-tab" data-bs-toggle="tab" data-bs-target="#rpl-pane" type="button" role="tab">RPL</button>
                                </li>
                            </ul>

                            <div class="tab-content" id="krsTabContent">
                                <div class="tab-pane fade show active" id="reguler-pane" role="tabpanel">
                                    <div class="d-flex gap-2 mb-3 flex-wrap">
                                        <form action="{{ url('master/krs/update-transkrip/1') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-info" onclick="return confirm('Pindahkan seluruh KRS Reguler pada tahun ajaran aktif ke KRS arsip?')">
                                                Update Transkrip Reguler
                                            </button>
                                        </form>
                                        <form action="{{ url('master/krs/update-transkrip/2') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-info" onclick="return confirm('Pindahkan seluruh KRS RPL pada tahun ajaran aktif ke KRS arsip?')">
                                                Update Transkrip RPL
                                            </button>
                                        </form>
                                    </div>

                                    <table class="table table-borderless w-auto mb-3" style="min-width: 560px;">
                                        <tbody>
                                            <tr>
                                                <td class="fw-bold">Total Mahasiswa Aktif</td>
                                                <td class="px-3">:</td>
                                                <td class="fw-bold">{{ $totalMahasiswaAktifReguler }}</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">Total Mahasiswa Input KRS</td>
                                                <td class="px-3">:</td>
                                                <td class="fw-bold">{{ $totalMahasiswaInputKrsReguler }}</td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <div class="mb-3">
                                        <a href="{{ url('master/krs/download-log/1') }}" class="btn btn-success rounded-pill px-4">Download Log KRS</a>
                                    </div>

                                    <div class="table-responsive">
                                        <table id="table-krs-reguler" class="table table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Tahun Ajaran</th>
                                                    <th>NIM</th>
                                                    <th>Nama Mahasiswa</th>
                                                    <th>Jumlah KRS</th>
                                                    <th>Status Keuangan</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $taReguler = '-';
                                                    if ($tahunReguler) {
                                                        $jenisReguler = ((int) $tahunReguler->jenis === 1) ? 'Ganjil' : (((int) $tahunReguler->jenis === 2) ? 'Genap' : '-');
                                                        $taReguler = $tahunReguler->awal . ' - ' . $tahunReguler->akhir . ' (' . $jenisReguler . ')';
                                                    }
                                                @endphp
                                                @forelse($krsListReguler as $idx => $row)
                                                    <tr>
                                                        <td>{{ $idx + 1 }}</td>
                                                        <td>{{ $taReguler }}</td>
                                                        <td>{{ $row->nim }}</td>
                                                        <td>{{ $row->nama_mhs ?? '-' }}</td>
                                                        <td style="background-color:#198754 !important; color:#fff !important; font-weight:bold !important;">{{ (int) ($row->total_sks ?? 0) }}/24</td>
                                                        <td>BELUM LUNAS</td>
                                                        <td>
                                                            @if($tahunReguler)
                                                                <a href="{{ url('master/krs/detail/' . $tahunReguler->id . '/' . $row->nim) }}" class="btn btn-primary btn-sm" title="Detail KRS">
                                                                    <i class="fa fa-eye"></i> Detail
                                                                </a>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr><td colspan="7" class="text-center">Belum ada KRS berjalan Reguler</td></tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="rpl-pane" role="tabpanel">
                                    <div class="d-flex gap-2 mb-3 flex-wrap">
                                        <form action="{{ url('master/krs/update-transkrip/1') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-info" onclick="return confirm('Pindahkan seluruh KRS Reguler pada tahun ajaran aktif ke KRS arsip?')">
                                                Update Transkrip Reguler
                                            </button>
                                        </form>
                                        <form action="{{ url('master/krs/update-transkrip/2') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-info" onclick="return confirm('Pindahkan seluruh KRS RPL pada tahun ajaran aktif ke KRS arsip?')">
                                                Update Transkrip RPL
                                            </button>
                                        </form>
                                    </div>

                                    <table class="table table-borderless w-auto mb-3" style="min-width: 560px;">
                                        <tbody>
                                            <tr>
                                                <td class="fw-bold">Total Mahasiswa Aktif</td>
                                                <td class="px-3">:</td>
                                                <td class="fw-bold">{{ $totalMahasiswaAktifRpl }}</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">Total Mahasiswa Input KRS</td>
                                                <td class="px-3">:</td>
                                                <td class="fw-bold">{{ $totalMahasiswaInputKrsRpl }}</td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <div class="mb-3">
                                        <a href="{{ url('master/krs/download-log/2') }}" class="btn btn-success rounded-pill px-4">Download Log KRS</a>
                                    </div>

                                    <div class="table-responsive">
                                        <table id="table-krs-rpl" class="table table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Tahun Ajaran</th>
                                                    <th>NIM</th>
                                                    <th>Nama Mahasiswa</th>
                                                    <th>Jumlah KRS</th>
                                                    <th>Status Keuangan</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $taRpl = '-';
                                                    if ($tahunRpl) {
                                                        $jenisRpl = ((int) $tahunRpl->jenis === 1) ? 'Ganjil' : (((int) $tahunRpl->jenis === 2) ? 'Genap' : '-');
                                                        $taRpl = $tahunRpl->awal . ' - ' . $tahunRpl->akhir . ' (' . $jenisRpl . ')';
                                                    }
                                                @endphp
                                                @forelse($krsListRpl as $idx => $row)
                                                    <tr>
                                                        <td>{{ $idx + 1 }}</td>
                                                        <td>{{ $taRpl }}</td>
                                                        <td>{{ $row->nim }}</td>
                                                        <td>{{ $row->nama_mhs ?? '-' }}</td>
                                                        <td style="background-color:#198754 !important; color:#fff !important; font-weight:bold !important;">{{ (int) ($row->total_sks ?? 0) }}/24</td>
                                                        <td>BELUM LUNAS</td>
                                                        <td>
                                                            @if($tahunRpl)
                                                                <a href="{{ url('master/krs/detail/' . $tahunRpl->id . '/' . $row->nim) }}" class="btn btn-primary btn-sm" title="Detail KRS">
                                                                    <i class="fa fa-eye"></i> Detail
                                                                </a>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr><td colspan="7" class="text-center">Belum ada KRS berjalan RPL</td></tr>
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
        const dtReguler = $('#table-krs-reguler').DataTable({ responsive: true, pageLength: 10 });
        const dtRpl = $('#table-krs-rpl').DataTable({ responsive: true, pageLength: 10 });

        $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function () {
            dtReguler.columns.adjust().draw(false);
            dtRpl.columns.adjust().draw(false);
        });

        const activeTab = @json($activeTab ?? null);
        if (activeTab) {
            const tabButton = document.querySelector(utton[data-bs-target="#${activeTab}"]);
            if (tabButton) {
                const tab = new bootstrap.Tab(tabButton);
                tab.show();
            }
        }
    });
</script>
@endsection
