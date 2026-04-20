@extends('layouts.default', ['CurrentPage' => $CurrentPage])

@section('content')
<div class="content-body">
    <div class="container-fluid">
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

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
            </div>

            <div class="col-lg-5">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Kirim Kritik & Saran</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('mahasiswa.masukan.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="nim" class="form-label">NIM</label>
                                <input type="text" id="nim" class="form-control" value="{{ $mahasiswa->nim }}" readonly>
                            </div>

                            <div class="mb-3">
                                <label for="saran" class="form-label">Masukan</label>
                                <textarea
                                    id="saran"
                                    name="saran"
                                    class="form-control"
                                    rows="7"
                                    placeholder="Tuliskan kritik atau saran Anda untuk admin sistem"
                                    required
                                >{{ old('saran') }}</textarea>
                                <small class="text-muted">Masukan Anda akan diterima oleh admin sistem untuk ditindaklanjuti.</small>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Kirim Masukan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Riwayat Masukan Saya</h4>
                        <span class="badge bg-light text-dark">{{ $masukanList->count() }} item</span>
                    </div>
                    <div class="card-body p-0">
                        @if($masukanList->isEmpty())
                            <div class="p-4 text-muted">Belum ada masukan yang dikirim.</div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Status</th>
                                            <th>Ringkasan</th>
                                            <th class="text-end">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($masukanList as $row)
                                            <tr>
                                                <td>{{ optional($row->tanggal)->format('d-m-Y H:i') }}</td>
                                                <td>
                                                    @php
                                                        $badgeClass = 'secondary';
                                                        if ($row->status === 'belum') {
                                                            $badgeClass = 'warning';
                                                        } elseif ($row->status === 'proses') {
                                                            $badgeClass = 'info';
                                                        } elseif ($row->status === 'selesai') {
                                                            $badgeClass = 'success';
                                                        }
                                                    @endphp
                                                    <span class="badge bg-{{ $badgeClass }}">{{ ucfirst($row->status) }}</span>
                                                </td>
                                                <td>{{ \Illuminate\Support\Str::limit($row->saran, 80) }}</td>
                                                <td class="text-end">
                                                    <a href="{{ route('mahasiswa.masukan.show', $row->id) }}" class="btn btn-sm btn-outline-primary">Lihat</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
