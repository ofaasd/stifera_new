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

                <div class="mb-3">
                    <a href="{{ url('master/jadwal') }}" class="btn btn-light">
                        <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Jadwal
                    </a>
                </div>

                <div class="filter cm-content-box box-primary">
                    <div class="content-title SlideToolHeader">
                        <div class="cpa">
                            <i class="fa-solid fa-toggle-on me-1"></i>{{ $title }}
                        </div>
                        <div class="tools">
                            <a href="javascript:void(0);" class="expand handle"><i class="fal fa-angle-down"></i></a>
                        </div>
                    </div>
                    <div class="cm-content-body form excerpt">
                        <div class="card-body pb-4">
                            <div class="row">
                                <div class="col-md-6 offset-md-3">
                                    <div class="card border-{{ $statusClass }} mb-3">
                                        <div class="card-header bg-{{ $statusClass }} text-white">
                                            <h5 class="mb-0">Status KRS</h5>
                                        </div>
                                        <div class="card-body text-center">
                                            <p class="mb-2">Status saat ini:</p>
                                            <h3 class="mb-4">
                                                <span class="badge bg-{{ $statusClass }} p-3">
                                                    {{ $statusLabel }}
                                                </span>
                                            </h3>
                                            
                                            @if((int) $krs->status === 1)
                                                <i class="fa-solid fa-circle-check text-success" style="font-size: 4rem; margin-bottom: 1rem;"></i>
                                                <p class="text-muted mb-3">Mahasiswa sedang diijinkan untuk melakukan input KRS</p>
                                            @else
                                                <i class="fa-solid fa-circle-xmark text-danger" style="font-size: 4rem; margin-bottom: 1rem;"></i>
                                                <p class="text-muted mb-3">Mahasiswa sedang tidak diijinkan untuk melakukan input KRS</p>
                                            @endif

                                            <form action="{{ url('master/jadwal_krs/toggle') }}" method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-{{ $statusClass }} btn-lg" onclick="return confirm('Yakin ingin mengubah status KRS ke {{ $nextAction }}?')">
                                                    <i class="fa-solid fa-toggle-{{ (int) $krs->status === 1 ? 'on' : 'off' }} me-2"></i>
                                                    {{ $nextAction }} KRS
                                                </button>
                                            </form>
                                        </div>
                                        <div class="card-footer bg-light">
                                            <small class="text-muted">
                                                <i class="fa-solid fa-info-circle me-1"></i>
                                                Terakhir diperbarui: {{ $krs->upadate_at ? $krs->upadate_at->format('d-m-Y H:i:s') : '-' }}
                                            </small>
                                        </div>
                                    </div>

                                    <div class="alert alert-info">
                                        <h6 class="mb-2"><i class="fa-solid fa-circle-info me-2"></i>Informasi:</h6>
                                        <ul class="mb-0">
                                            @if((int) $krs->status === 1)
                                                <li>Mahasiswa dapat melakukn input/edit KRS mereka</li>
                                                <li>Periode KRS sedang berlangsung</li>
                                            @else
                                                <li>Mahasiswa tidak dapat melakukan input KRS</li>
                                                <li>Periode KRS sudah ditutup</li>
                                            @endif
                                        </ul>
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
        // Animation for icons
        $('.text-success, .text-danger').fadeIn();
    });
</script>
@endsection
