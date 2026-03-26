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
                            <i class="fa-solid fa-calendar-days me-1"></i>{{ $title }}
                        </div>
                        <div class="tools">
                            <a href="javascript:void(0);" class="expand handle"><i class="fal fa-angle-down"></i></a>
                        </div>
                    </div>
                    <div class="cm-content-body form excerpt">
                        <div class="card-body pb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                                <div>
                                    <strong>Kelola Jadwal Berjalan & History</strong>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="{{ url('master/pertemuan') }}" class="btn btn-success">
                                        <i class="fa-solid fa-calendar-check me-1"></i> Setting Pertemuan
                                    </a>
                                    <a href="{{ url('master/jadwal_krs') }}" class="btn btn-info">
                                        <i class="fa-solid fa-toggle-on me-1"></i> Toggle KRS
                                    </a>
                                </div>
                                <form action="{{ url('master/jadwal/pindah-history') }}" method="POST" class="d-flex gap-2 align-items-center">
                                    @csrf
                                    <select name="id_tahun" class="form-control" required style="min-width: 220px;">
                                        <option value="">-- Pilih Tahun Ajaran (Temp) --</option>
                                        @foreach($tahunDenganJadwalTemp as $t)
                                            @php
                                                $jenis = ((int) $t->jenis === 1) ? 'Ganjil' : (((int) $t->jenis === 2) ? 'Genap' : 'Antara');
                                            @endphp
                                            <option value="{{ $t->id }}">{{ $t->awal }}/{{ $t->akhir }} - {{ $jenis }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn btn-warning" onclick="return confirm('Pindahkan semua jadwal tahun ajaran ini ke history? Data di jadwal berjalan akan dihapus.')">Pindah ke History</button>
                                </form>
                            </div>

                            <ul class="nav nav-tabs mb-3" id="jadwalTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="input-tab" data-bs-toggle="tab" data-bs-target="#input-pane" type="button" role="tab">Input Jadwal</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="daftar-tab" data-bs-toggle="tab" data-bs-target="#daftar-pane" type="button" role="tab">Daftar Jadwal</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history-pane" type="button" role="tab">History Jadwal</button>
                                </li>
                            </ul>

                            <div class="tab-content" id="jadwalTabContent">
                                <div class="tab-pane fade show active" id="input-pane" role="tabpanel">
                                    <div class="table-responsive">
                                        <table id="table-input-jadwal" class="table table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Kode MK</th>
                                                    <th>Nama Mata Kuliah</th>
                                                    <th>SKS</th>
                                                    <th>Semester</th>
                                                    <th>Program Studi</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($matakuliah as $idx => $mk)
                                                    <tr>
                                                        <td>{{ $idx + 1 }}</td>
                                                        <td>{{ $mk->kode_mata_kuliah }}</td>
                                                        <td>{{ $mk->nama_mata_kuliah }}</td>
                                                        <td>{{ $mk->jumlah_sks }}</td>
                                                        <td>{{ $mk->semester }}</td>
                                                        <td>{{ $mk->nama_jurusan ?? '-' }}</td>
                                                        <td>
                                                            <a href="{{ url('master/jadwal/input/' . $mk->kode_mata_kuliah) }}" class="btn btn-primary btn-sm" title="Input Jadwal">
                                                                <i class="fa-solid fa-calendar-plus"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr><td colspan="7" class="text-center">Data mata kuliah tidak tersedia</td></tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="daftar-pane" role="tabpanel">
                                    <div class="table-responsive">
                                        <table id="table-daftar-jadwal" class="table table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Kode MK</th>
                                                    <th>Mata Kuliah</th>
                                                    <th>Dosen</th>
                                                    <th>Hari</th>
                                                    <th>Sesi</th>
                                                    <th>Ruang</th>
                                                    <th>Rombel</th>
                                                    <th>Status</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($jadwalAktif as $idx => $j)
                                                    <tr>
                                                        <td>{{ $idx + 1 }}</td>
                                                        <td>{{ $j->kode_mata_kuliah }}</td>
                                                        <td>{{ $j->nama_mata_kuliah ?? '-' }}</td>
                                                        <td>{{ trim($j->nama_dosen ?? '-') }}</td>
                                                        <td>{{ $j->hari }}</td>
                                                        <td>{{ $j->sesi }}</td>
                                                        <td>{{ $j->ruang }}</td>
                                                        <td>{{ ($j->tipe_mhs == 2 ? 'Karyawan' : 'Reguler') . ' ' . ($j->rombel ?? '-') }}</td>
                                                        <td>{!! (int)$j->status === 1 ? '<span class="badge badge-success">BUKA</span>' : '<span class="badge badge-secondary">TUTUP</span>' !!}</td>
                                                        <td>
                                                            <a href="{{ url('master/jadwal/edit/' . $j->id) }}" class="btn btn-success btn-sm" title="Edit Jadwal">
                                                                <i class="fa-solid fa-pen-to-square"></i>
                                                            </a>
                                                            <form action="{{ url('master/jadwal/delete/' . $j->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Yakin ingin menghapus jadwal ini?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger btn-sm" title="Hapus Jadwal">
                                                                    <i class="fa-solid fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr><td colspan="10" class="text-center">Belum ada jadwal berjalan</td></tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="history-pane" role="tabpanel">
                                    <form method="GET" action="{{ url('master/jadwal') }}" class="row g-2 mb-3">
                                        <input type="hidden" name="active_tab" value="history-pane">
                                        <div class="col-md-4">
                                            <select name="history_tahun" class="form-control">
                                                <option value="">-- Semua Tahun Ajaran --</option>
                                                @foreach($tahunAjarHistory as $t)
                                                    @php
                                                        $jenis = ((int) $t->jenis === 1) ? 'Ganjil' : (((int) $t->jenis === 2) ? 'Genap' : 'Antara');
                                                    @endphp
                                                    <option value="{{ $t->id }}" {{ (string) $selectedHistoryTahun === (string) $t->id ? 'selected' : '' }}>
                                                        {{ $t->awal }}/{{ $t->akhir }} - {{ $jenis }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-8">
                                            <button type="submit" class="btn btn-primary">Filter</button>
                                            <a href="{{ url('master/jadwal?active_tab=history-pane') }}" class="btn btn-light">Reset</a>
                                        </div>
                                    </form>

                                    <div class="table-responsive">
                                        <table id="table-history-jadwal" class="table table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Kode MK</th>
                                                    <th>Mata Kuliah</th>
                                                    <th>Dosen</th>
                                                    <th>Tahun</th>
                                                    <th>Hari</th>
                                                    <th>Sesi</th>
                                                    <th>Ruang</th>
                                                    <th>Rombel</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($jadwalHistory as $idx => $j)
                                                    <tr>
                                                        <td>{{ $idx + 1 }}</td>
                                                        <td>{{ $j->kode_mata_kuliah }}</td>
                                                        <td>{{ $j->nama_mata_kuliah ?? '-' }}</td>
                                                        <td>{{ trim($j->nama_dosen ?? '-') }}</td>
                                                        <td>{{ ($j->awal ?? '-') . '/' . ($j->akhir ?? '-') }}</td>
                                                        <td>{{ $j->hari }}</td>
                                                        <td>{{ $j->sesi }}</td>
                                                        <td>{{ $j->ruang }}</td>
                                                        <td>{{ ($j->tipe_mhs == 2 ? 'Karyawan' : 'Reguler') . ' ' . ($j->rombel ?? '-') }}</td>
                                                    </tr>
                                                @empty
                                                    <tr><td colspan="9" class="text-center">Belum ada history jadwal</td></tr>
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
        const dtInput = $('#table-input-jadwal').DataTable({
            responsive: true,
            pageLength: 25
        });

        const dtDaftar = $('#table-daftar-jadwal').DataTable({
            responsive: true,
            pageLength: 25
        });

        const dtHistory = $('#table-history-jadwal').DataTable({
            responsive: true,
            pageLength: 25
        });

        $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function () {
            dtInput.columns.adjust().draw(false);
            dtDaftar.columns.adjust().draw(false);
            dtHistory.columns.adjust().draw(false);
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
