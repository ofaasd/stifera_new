@extends('layouts.default', ['CurrentPage' => $CurrentPage])

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-12">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h4 class="mb-3">Selamat Datang, {{ $dosenInfo->nama_lengkap ?? $pegawai->nama ?? '-' }}</h4>
                        <p class="mb-1 text-muted">Anda login sebagai akun pegawai/dosen terpisah dari admin.</p>
                        <div class="small">
                            <p class="mb-1"><strong>Nama Lengkap:</strong> {{ $dosenInfo->nama_lengkap ?? $pegawai->nama ?? '-' }}</p>
                            <p class="mb-1"><strong>NIDN:</strong> {{ $dosenInfo->nidn ?? '-' }}</p>
                            <p class="mb-0"><strong>Username:</strong> {{ $pegawai->usrnm ?? '-' }}</p>
                        </div>

                        <form method="POST" action="{{ route('pegawai.logout') }}" class="mt-4">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-sm">Logout Pegawai</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-3">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body text-center">
                        <div class="mb-2">
                            <i class="feather icon-users" style="font-size: 48px; color: #007bff;"></i>
                        </div>
                        <h5 class="mb-1">Mahasiswa Perwalian</h5>
                        <div class="display-5 fw-bold text-primary">{{ $mahasiswaPerwalianCount }}</div>
                        <p class="text-muted small mb-0">Jumlah mahasiswa perwalian anda</p>
                    </div>
                </div>
            </div>
        </div>

        @if($krmList->isNotEmpty())
            <div class="row">
                <div class="col-xl-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="feather icon-book-open"></i> Daftar KRM (Kartu Rencana Mengajar)
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Kode Mata Kuliah</th>
                                            <th>Nama Mata Kuliah</th>
                                            <th>SKS</th>
                                            <th>Hari</th>
                                            <th>Sesi</th>
                                            <th>Ruang</th>
                                            <th>Berkas RPS</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($krmList as $krm)
                                            <tr>
                                                <td>{{ $krm->kode_mata_kuliah }}</td>
                                                <td>{{ $krm->nama_mata_kuliah ?? '-' }}</td>
                                                <td class="text-center">{{ $krm->sks ?? '-' }}</td>
                                                <td>{{ $krm->hari ?? '-' }}</td>
                                                <td>{{ $krm->sesi ?? '-' }}</td>
                                                <td>{{ $krm->ruang ?? '-' }}</td>
                                                <td>
                                                    @if($krm->rps)
                                                        <a href="{{ asset('storage/' . $krm->rps) }}" target="_blank" class="btn btn-sm btn-outline-primary" title="Download RPS">
                                                            <i class="feather icon-download"></i> RPS
                                                        </a>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
