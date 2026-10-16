<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tautan Tidak Ditemukan | Portal Pengawas Ujian SIAKAD</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="vh-100 bg-light d-flex align-items-center justify-content-center">
    <div class="container text-center">
        <div class="card border-0 shadow-lg mx-auto" style="max-width: 500px; border-radius: 16px;">
            <div class="card-body p-5">
                <div class="mb-4">
                    <span class="badge bg-danger-subtle text-danger p-3 rounded-circle" style="font-size: 2.5rem;">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </span>
                </div>
                <h3 class="fw-bold text-dark mb-2">Tautan Tidak Valid</h3>
                <p class="text-muted mb-4">
                    {{ $message ?? 'Tautan pengawas ujian yang Anda buka tidak valid atau belum diatur oleh admin.' }}
                </p>
                <div class="small text-muted">
                    Pastikan Anda membuka URL lengkap yang diberikan oleh panitia ujian atau administrator SIAKAD STIFERA.
                </div>
            </div>
        </div>
    </div>
</body>
</html>
