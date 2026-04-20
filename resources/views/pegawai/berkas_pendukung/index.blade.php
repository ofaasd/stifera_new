@extends('layouts.default', ['CurrentPage' => $CurrentPage])

@section('content')
<div class="content-body">
    <div class="container">
        <div class="row">
            <div class="col-xl-12">
                <div class="mb-4 pb-3">
                    <a href="{{ route('pegawai.home') }}" class="btn btn-secondary">
                        <i class="fa-solid fa-arrow-left me-1"></i> Kembali
                    </a>
                </div>

                @if(session('status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ $errors->first() }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="card" style="height:auto !important;">
                    <div class="card-header border-0 pb-0">
                        <h5 class="mb-1">BERKAS PENDUKUNG</h5>
                        <p class="text-muted mb-0">Kelola dokumen KTP dan KK melalui tombol pada masing-masing kolom.</p>
                    </div>
                    <div class="card-body pt-3">
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th class="fw-semibold">KTP</th>
                                        <th class="fw-semibold">KK</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        @foreach($documents as $document)
                                            <td>
                                                <button
                                                    type="button"
                                                    class="btn btn-sm {{ $document['exists'] ? 'btn-info' : 'btn-danger' }} px-4"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#berkasModal{{ strtoupper($document['field']) }}"
                                                >
                                                    {{ $document['exists'] ? 'Lihat' : 'Belum Ada' }}
                                                </button>
                                            </td>
                                        @endforeach
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                @foreach($documents as $document)
                    <div class="modal fade" id="berkasModal{{ strtoupper($document['field']) }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Berkas {{ $document['label'] }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    @if($document['exists'])
                                        <div class="mb-4">
                                            <label class="form-label fw-semibold">Dokumen Saat Ini</label>
                                            <div class="border rounded p-3 bg-light">
                                                @if($document['is_pdf'])
                                                    <iframe src="{{ $document['url'] }}" style="width:100%; height:430px; border:0;" title="Preview {{ $document['label'] }}"></iframe>
                                                @elseif($document['is_image'])
                                                    <div class="text-center">
                                                        <img src="{{ $document['url'] }}" alt="{{ $document['label'] }}" class="img-fluid rounded border" style="max-height:430px;">
                                                    </div>
                                                @else
                                                    <p class="mb-2">Preview file tidak tersedia untuk format ini.</p>
                                                @endif

                                                <div class="mt-3 d-flex flex-wrap gap-2 align-items-center">
                                                    <a href="{{ $document['url'] }}" target="_blank" class="btn btn-outline-primary btn-sm">Buka di tab baru</a>
                                                    <span class="text-muted small">{{ $document['file_name'] }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="alert alert-warning">
                                            Berkas {{ $document['label'] }} belum diupload.
                                        </div>
                                    @endif

                                    <div class="mt-4 d-flex justify-content-between flex-wrap gap-2">
                                        @if($document['exists'])
                                            <form action="{{ route('pegawai.berkas-pendukung.destroy', $document['field']) }}" method="POST" onsubmit="return confirm('Hapus berkas {{ $document['label'] }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger">Hapus Berkas</button>
                                            </form>
                                        @else
                                            <span></span>
                                        @endif
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <form action="{{ route('pegawai.berkas-pendukung.store', $document['field']) }}" method="POST" enctype="multipart/form-data" class="w-100">
                                        @csrf
                                        <div>
                                            <label class="form-label fw-semibold">Upload {{ $document['exists'] ? 'Berkas Baru' : 'Berkas' }}</label>
                                            <input type="file" name="berkas" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                                            <small class="text-muted d-block mt-2">Format yang didukung: PDF, JPG, JPEG, PNG. Maksimal 4 MB.</small>
                                        </div>
                                        <div class="mt-4 d-flex justify-content-end gap-2">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection