@extends('layouts.default', ['CurrentPage' => $CurrentPage])

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h4 class="mb-2">Keuangan Mahasiswa</h4>
                        <!-- <p class="text-muted mb-4">Fitur keuangan mahasiswa masih dalam tahap pengembangan.</p> -->

                        <div class="alert alert-light border mb-0">
                            Belum ada tagihan yang tersedia.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
