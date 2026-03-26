@extends('layouts.default', ['CurrentPage' => $CurrentPage])

@section('content')
<div class="content-body">
    <div class="container">
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="mb-2">Beranda Pegawai</h4>
                        <p class="mb-1">Anda login sebagai akun pegawai terpisah dari admin.</p>
                        <p class="mb-0"><strong>Nama:</strong> {{ $pegawai->nama ?? '-' }} | <strong>Username:</strong> {{ $pegawai->usrnm ?? '-' }}</p>

                        <form method="POST" action="{{ route('pegawai.logout') }}" class="mt-3">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-sm">Logout Pegawai</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
