<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kartu Ujian {{ strtoupper($jenisKartu) }}</title>
    <style>
        body {
            font-family: serif;
            font-size: 10px;
            color: #000;
        }
        .sheet {
            border: 1px solid #222;
            padding: 8px;
        }
        .title {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            border: 1px solid #222;
            margin-bottom: 8px;
            padding: 4px;
        }
        .head-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        .head-table td {
            vertical-align: top;
            padding: 2px 4px;
            font-size: 9px;
        }
        .logo-wrap {
            width: 70px;
            text-align: center;
        }
        .logo {
            width: 54px;
            height: 54px;
            object-fit: contain;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8px;
        }
        .data-table th,
        .data-table td {
            border: 1px solid #222;
            padding: 1px 2px;
        }
        .data-table thead th {
            text-align: center;
            font-weight: bold;
        }
        .center { text-align: center; }
        .footer {
            margin-top: 70px;
            width: 100%;
            border-collapse: collapse;
        }
        .footer td {
            vertical-align: top;
            font-size: 10px;
            padding: 2px 4px;
        }
    </style>
</head>
<body>
    <div class="sheet">
        <table class="head-table">
            <tr>
                <td class="logo-wrap" rowspan="5">
                    <img class="logo" src="{{ asset(config('dz.site_level.logo')) }}" alt="Logo" width="30" height="30" align="left">
                </td>
                <td colspan="4" class="title">KARTU UJIAN {{ strtoupper($jenisKartu) }}</td>
            </tr>
            <tr>
                <td width="26%">NIM</td>
                <td width="28%">: {{ $mahasiswa->nim ?? '-' }}</td>
                <td width="16%">Semester</td>
                <td width="30%">: {{ $jenisTA ?? '-' }}</td>
            </tr>
            <tr>
                <td>Nama</td>
                <td>: {{ $mahasiswa->nama ?? '-' }}</td>
                <td>Tahun Ajaran</td>
                <td>: {{ ($tahunAktif->awal ?? '-') . ' - ' . ($tahunAktif->akhir ?? '-') }}</td>
            </tr>
            <tr>
                <td>Program Studi</td>
                <td>: {{ trim((string) ($mahasiswaProfil->nama_program_studi ?? '-')) }}</td>
                <td>Dosen Wali</td>
                <td>: {{ trim((string) ($mahasiswaProfil->dosen_wali ?? '-')) }}</td>
            </tr>
            <tr>
                <td>Fakultas</td>
                <td>: {{ trim((string) ($mahasiswaProfil->nama_fakultas ?? '-')) }}</td>
                <td>Email Dosen</td>
                <td>: {{ $mahasiswaProfil->email_dosen_wali ?? '-' }}</td>
            </tr>
        </table>

        <table class="data-table">
            <thead>
                <tr>
                    <th rowspan="2" style="width: 26px;">No</th>
                    <th rowspan="2" style="width: 68px;">Kode</th>
                    <th rowspan="2">Matakuliah</th>
                    <th rowspan="2" style="width: 32px;">SKS</th>
                    <th rowspan="2" style="width: 38px;">Ruang</th>
                    <th rowspan="2" style="width: 50px;">No. Kursi</th>
                    <th colspan="2" style="width: 125px;">Tanggal Ujian {{ strtoupper($jenisKartu) }}</th>
                    <th rowspan="2" style="width: 34px;">Paraf</th>
                </tr>
                <tr>
                    <th style="width: 60px;">Tanggal</th>
                    <th style="width: 65px;">Waktu</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ujianRows as $idx => $row)
                    @php
                        if ($jenisKartu === 'uts') {
                            $tgl = !empty($row->tanggal_uts_t) ? \Illuminate\Support\Carbon::parse($row->tanggal_uts_t)->format('d-m-Y') : '-';
                            $mulai = !empty($row->mulai_uts_t) ? \Illuminate\Support\Carbon::parse($row->mulai_uts_t)->format('H:i') : '--:--';
                            $selesai = !empty($row->selesai_uts_t) ? \Illuminate\Support\Carbon::parse($row->selesai_uts_t)->format('H:i') : '--:--';
                        } else {
                            $tgl = !empty($row->tanggal_uas_t) ? \Illuminate\Support\Carbon::parse($row->tanggal_uas_t)->format('d-m-Y') : '-';
                            $mulai = !empty($row->mulai_uas_t) ? \Illuminate\Support\Carbon::parse($row->mulai_uas_t)->format('H:i') : '--:--';
                            $selesai = !empty($row->selesai_uas_t) ? \Illuminate\Support\Carbon::parse($row->selesai_uas_t)->format('H:i') : '--:--';
                        }
                        $waktu = ($mulai === '--:--' && $selesai === '--:--') ? '-' : ($mulai . ' - ' . $selesai);
                    @endphp
                    <tr>
                        <td class="center">{{ $idx + 1 }}</td>
                        <td>{{ $row->kode_mata_kuliah ?? '-' }}</td>
                        <td>{{ $row->nama_mata_kuliah ?? '-' }}</td>
                        <td class="center">{{ (int) ($row->jumlah_sks ?? 0) }}</td>
                        <td class="center">{{ $row->ruang ?? '-' }}</td>
                        <td class="center">{{ $row->no_kursi ?? '-' }}</td>
                        <td class="center">{{ $tgl }}</td>
                        <td class="center">{{ $waktu }}</td>
                        <td></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="center">Data kartu ujian belum tersedia.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <table class="footer">
            <tr>
                <td width="50%"><center>Dosen Wali</center></td>
                <td width="50%" class="center">Semarang, {{ now()->translatedFormat('d M Y') }}</td>
            </tr>
            <tr>
                <td style="height: 40px;"></td>
                <td class="center">Kepala Tata Usaha</td>
            </tr>
            <tr>
                <td><center>{{ trim((string) ($mahasiswaProfil->dosen_wali ?? '-')) }}</center></td>
                <td class="center">FAKULTAS {{ trim((string) ($mahasiswaProfil->nama_fakultas ?? '-')) }}</td>
            </tr>
        </table>
    </div>
</body>
</html>
