<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kartu Rencana Studi</title>
    <style>
        body {
            font-family: "Times New Roman", serif;
            font-size: 12px;
            color: #000;
        }
        .sheet {
            border: 1px solid #9a9a9a;
            padding: 28px 34px 24px;
        }
        .header-table,
        .info-table,
        .krs-table,
        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table td {
            vertical-align: top;
        }
        .logo-cell {
            width: 88px;
        }
        .logo-abbr {
            width: 64px;
            height: 64px;
            display: block;
        }
        .institution {
            text-align: center;
            line-height: 1.35;
        }
        .institution .name {
            font-size: 19px;
            font-weight: bold;
        }
        .institution .meta {
            font-size: 12px;
        }
        .page-title {
            text-align: center;
            font-weight: bold;
            font-size: 15px;
            margin: 24px 0 18px;
        }
        .info-table {
            margin-bottom: 16px;
        }
        .info-table td {
            padding: 2px 4px;
            vertical-align: top;
            font-size: 12px;
        }
        .label {
            width: 110px;
        }
        .separator {
            width: 10px;
        }
        .krs-table {
            margin-top: 6px;
            font-size: 10px;
        }
        .krs-table th,
        .krs-table td {
            border: 1px solid #d4d0d0;
            padding: 2px 4px;
        }
        .krs-table thead th {
            background: #e9e4e4;
            text-align: center;
            font-weight: bold;
        }
        .center {
            text-align: center;
        }
        .right {
            text-align: right;
        }
        .summary {
            margin-top: 8px;
            width: 44%;
            font-size: 12px;
        }
        .summary td {
            padding: 1px 4px;
            font-weight: bold;
        }
        .approval-title {
            margin-top: 28px;
            padding-top: 18px;
            border-top: 1px solid #8f8f8f;
            text-align: center;
            font-weight: bold;
            font-size: 13px;
        }
        .signature-table {
            margin-top: 40px;
            font-size: 12px;
        }
        .signature-table td {
            width: 33.33%;
            text-align: center;
            vertical-align: top;
        }
        .signature-space {
            height: 62px;
        }
        .date-wrap {
            text-align: right;
            padding-right: 26px;
        }
    </style>
</head>
<body>
    <div class="sheet">
        <table class="header-table">
            <tr>
                <td class="logo-cell">
                    <img class="logo-abbr" src="{{ asset(config('dz.site_level.logo')) }}" alt="" width="30" height="30" align="left">
                </td>
                <td class="institution">
                    <div class="name">SEKOLAH TINGGI ILMU FARMASI NUSAPUTERA</div>
                    <div class="meta">Jalan Medoho 3 No.2, Telp/ Fax (024)6747012 Semarang</div>
                    <div class="meta">E-mail : stiferanusaputera@gmail.com</div>
                    <div class="meta">Website: https://www.stifera.ac.id</div>
                </td>
            </tr>
        </table>

        <div class="page-title">KARTU RENCANA STUDI</div>

        <table class="info-table">
            <tr>
                <td class="label">Nama</td>
                <td class="separator">:</td>
                <td>{{ strtoupper((string) ($mahasiswa->nama ?? '-')) }}</td>
                <td class="label">Tahun Ajaran</td>
                <td class="separator">:</td>
                <td>{{ ($tahunAktif->awal ?? '-') . ' - ' . ($tahunAktif->akhir ?? '-') }}</td>
            </tr>
            <tr>
                <td class="label">NIM</td>
                <td class="separator">:</td>
                <td>{{ $mahasiswa->nim ?? '-' }}</td>
                <td class="label">Semester</td>
                <td class="separator">:</td>
                <td>{{ $jenisTA ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Jurusan/Prodi</td>
                <td class="separator">:</td>
                <td>{{ strtoupper(trim((string) ($mahasiswaProfil->nama_program_studi ?? '-'))) }}</td>
                <td class="label">Dosen Wali</td>
                <td class="separator">:</td>
                <td>{{ trim((string) ($mahasiswaProfil->dosen_wali ?? '-')) }}</td>
            </tr>
        </table>

        <table class="krs-table">
            <thead>
                <tr>
                    <th rowspan="2" style="width: 36px;">No</th>
                    <th rowspan="2" style="width: 92px;">Kode MK</th>
                    <th rowspan="2">Nama Mata Kuliah</th>
                    <th rowspan="2" style="width: 46px;">SKS</th>
                    <th colspan="2">Jadwal</th>
                </tr>
                <tr>
                    <th style="width: 120px;">Hari, Jam</th>
                    <th style="width: 90px;">Ruang</th>
                </tr>
            </thead>
            <tbody>
                @forelse($krsRows as $index => $row)
                    @php
                        $ruangValue = (string) ($row->ruang ?? '-');
                        $ruangDisplay = $ruangValue !== '-' ? urldecode($ruangValue) : '-';
                    @endphp
                    <tr>
                        <td class="center">{{ $index + 1 }}</td>
                        <td class="center">{{ $row->kode_mata_kuliah ?? '-' }}</td>
                        <td>{{ $row->nama_mata_kuliah ?? $row->mata_kuliah ?? '-' }}</td>
                        <td class="center">{{ (int) ($row->sks ?? 0) }}</td>
                        <td>{{ trim((string) (($row->hari ?? '-') . ', ' . ($row->sesi ?? '-'))) }}</td>
                        <td class="center" width="70">{{ $ruangDisplay }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="center">Belum ada data KRS.</td>
                    </tr>
                @endforelse
                <tr>
                    <td colspan="3" class="right"><strong>Jumlah SKS</strong></td>
                    <td class="center"><strong>{{ (int) ($totalSks ?? 0) }}</strong></td>
                    <td colspan="2"></td>
                </tr>
            </tbody>
        </table>

        <table class="summary">
            <tr>
                <td>IPS Sebelum</td>
                <td>: {{ rtrim(rtrim(number_format((float) ($ipsTerakhir ?? 0), 2, '.', ''), '0'), '.') }}</td>
            </tr>
            <tr>
                <td>Batas SKS</td>
                <td>: {{ (int) ($batasSks ?? 24) }}</td>
            </tr>
        </table>

        <div class="approval-title">PERSETUJUAN KARTU STUDI</div>

        <table class="signature-table">
            <tr>
                <td>Dosen Wali</td>
                <td class="date-wrap">Semarang, {{ now()->translatedFormat('d M Y') }}</td>
                <td>Mahasiswa</td>
            </tr>
            <tr>
                <td class="signature-space"></td>
                <td class="signature-space"></td>
                <td class="signature-space"></td>
            </tr>
            <tr>
                <td>{{ trim((string) ($mahasiswaProfil->dosen_wali ?? '-')) }}</td>
                <td>ORANG TUA WALI</td>
                <td>{{ strtoupper((string) ($mahasiswa->nama ?? '-')) }}</td>
            </tr>
        </table>
    </div>
</body>
</html>