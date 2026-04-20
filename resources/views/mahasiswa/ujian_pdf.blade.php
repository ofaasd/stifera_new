<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kartu Ujian {{ strtoupper($jenisKartu) }}</title>
    <style>
        body {
            font-family: serif;
            font-size: 12px;
            color: #000;
        }
        .sheet {
            border: 1px solid #222;
            padding: 8px;
        }
        .title {
            text-align: center;
            font-size: 28px;
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
            font-size: 11px;
        }
        .logo-wrap {
            width: 90px;
            text-align: center;
        }
        .logo {
            width: 78px;
            height: 78px;
            object-fit: contain;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }
        .data-table th,
        .data-table td {
            border: 1px solid #222;
            padding: 2px 3px;
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
            font-size: 12px;
            padding: 2px 4px;
        }
    </style>
</head>
<body>
    <div class="sheet">
        <div class="title">KARTU UJIAN {{ strtoupper($jenisKartu) }}</div>

        <table class="head-table">
            <tr>
                <td class="logo-wrap" rowspan="4">
                    @php $logoPath = public_path('images/logo-full.png'); @endphp
                    @if(file_exists($logoPath))
                        <img class="logo" src="{{ $logoPath }}" alt="Logo">
                    @endif
                </td>
                <td width="34%">NIM</td>
                <td width="30%">: {{ $mahasiswa->nim ?? '-' }}</td>
                <td width="20%">Semester</td>
                <td>: {{ $jenisTA ?? '-' }}</td>
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
                    <th rowspan="2" style="width: 35px;">No</th>
                    <th rowspan="2" style="width: 90px;">Kode</th>
                    <th rowspan="2">Matakuliah</th>
                    <th rowspan="2" style="width: 45px;">SKS</th>
                    <th rowspan="2" style="width: 55px;">Ruang</th>
                    <th rowspan="2" style="width: 85px;">No. Kursi</th>
                    <th colspan="2" style="width: 180px;">Tanggal Ujian {{ strtoupper($jenisKartu) }}</th>
                    <th rowspan="2" style="width: 60px;">Paraf</th>
                </tr>
                <tr>
                    <th style="width: 85px;">Tanggal</th>
                    <th style="width: 95px;">Waktu</th>
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
                <td width="50%">Dosen Wali</td>
                <td width="50%" class="center">Semarang, {{ now()->translatedFormat('d M Y') }}</td>
            </tr>
            <tr>
                <td style="height: 40px;"></td>
                <td class="center">Kepala Tata Usaha</td>
            </tr>
            <tr>
                <td>{{ trim((string) ($mahasiswaProfil->dosen_wali ?? '-')) }}</td>
                <td class="center">{{ trim((string) ($mahasiswaProfil->nama_fakultas ?? '-')) }}</td>
            </tr>
        </table>
    </div>
</body>
</html>
