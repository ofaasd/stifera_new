<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>CV Pegawai</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            color: #222;
        }
        .header {
            text-align: center;
            margin-bottom: 18px;
        }
        .header h2 {
            margin: 0;
            font-size: 18px;
        }
        .photo {
            text-align: right;
            margin-bottom: 8px;
        }
        .photo img {
            width: 110px;
            height: 140px;
            object-fit: cover;
            border: 1px solid #999;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        td {
            border: 1px solid #bbb;
            padding: 7px 8px;
            vertical-align: top;
        }
        td.label {
            width: 30%;
            font-weight: bold;
            background: #f5f5f5;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Curriculum Vitae Pegawai</h2>
    </div>

    @if(!empty($cv->foto) && file_exists(public_path('assets/foto_pegawai/' . $cv->foto)))
        <div class="photo">
            <img src="{{ public_path('assets/foto_pegawai/' . $cv->foto) }}" alt="Foto Pegawai">
        </div>
    @endif

    <table>
        <tr><td class="label">NPP</td><td>{{ $cv->npp }}</td></tr>
        <tr><td class="label">Nama Lengkap</td><td>{{ $cv->nama_lengkap }}</td></tr>
        <tr><td class="label">NIDN</td><td>{{ $cv->nidn }}</td></tr>
        <tr><td class="label">Jenis Kelamin</td><td>{{ $cv->jenis_kelamin }}</td></tr>
        <tr><td class="label">Tempat Lahir</td><td>{{ $cv->tempat_lahir }}</td></tr>
        <tr><td class="label">Tanggal Lahir</td><td>{{ $cv->tanggal_lahir }}</td></tr>
        <tr><td class="label">Agama</td><td>{{ $cv->agama }}</td></tr>
        <tr><td class="label">Status Nikah</td><td>{{ $cv->status_nikah }}</td></tr>
        <tr><td class="label">Alamat</td><td>{{ $cv->alamat }}</td></tr>
        <tr><td class="label">Provinsi</td><td>{{ $cv->provinsi }}</td></tr>
        <tr><td class="label">Kota/Kabupaten</td><td>{{ $cv->kotakab }}</td></tr>
        <tr><td class="label">Kecamatan</td><td>{{ $cv->kecamatan }}</td></tr>
        <tr><td class="label">Kelurahan</td><td>{{ $cv->kelurahan }}</td></tr>
        <tr><td class="label">Email</td><td>{{ $cv->email1 }}</td></tr>
        <tr><td class="label">No. HP</td><td>{{ $cv->nohp }}</td></tr>
        <tr><td class="label">No. Telp</td><td>{{ $cv->notelp }}</td></tr>
        <tr><td class="label">No. KTP</td><td>{{ $cv->no_ktp }}</td></tr>
        <tr><td class="label">No. KK</td><td>{{ $cv->no_kk }}</td></tr>
        <tr><td class="label">Gelar Depan</td><td>{{ $cv->gelar_depan }}</td></tr>
        <tr><td class="label">Gelar Belakang</td><td>{{ $cv->gelar_belakang }}</td></tr>
        <tr><td class="label">Status Pegawai</td><td>{{ $cv->status_pegawai }}</td></tr>
        <tr><td class="label">Homebase</td><td>{{ $cv->homebase }}</td></tr>
    </table>
</body>
</html>
