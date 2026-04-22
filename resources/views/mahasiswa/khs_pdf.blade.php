<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Hasil Studi Semester</title>
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
        .khs-table,
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
        .khs-table {
            margin-top: 6px;
            font-size: 10px;
        }
        .khs-table th,
        .khs-table td {
            border: 1px solid #d4d0d0;
            padding: 2px 4px;
        }
        .khs-table thead th {
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

        <div class="page-title">HASIL STUDI SEMESTER</div>

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
                <td class="label">Program Studi</td>
                <td class="separator">:</td>
                <td>{{ strtoupper(trim((string) ($mahasiswaProfil->nama_program_studi ?? '-'))) }}</td>
                <td class="label">Dosen Wali</td>
                <td class="separator">:</td>
                <td>{{ trim((string) ($mahasiswaProfil->dosen_wali ?? '-')) }}</td>
            </tr>
        </table>

        <table class="khs-table">
            <thead>
                <tr>
                    <th style="width: 36px;">No</th>
                    <th style="width: 92px;">Kode MK</th>
                    <th>Nama Mata Kuliah</th>
                    <th style="width: 50px;">Nilai Simbol</th>
                    <th style="width: 50px;">Nilai Angka</th>
                    <th style="width: 46px;">SKS</th>
                    <th style="width: 52px;">Kualitas</th>
                </tr>
            </thead>
            <tbody>
                @forelse($khsRows as $index => $row)
                    @php
                        $nilaiHuruf = trim((string) ($row->nhuruf ?? ''));
                        $nilaiAkhir = (int) ($row->nakhir ?? 0);
                        $sks = (int) ($row->jumlah_sks ?? 0);
                        $kualitas = $sks * (function_exists('nbobot') ? nbobot($nilaiHuruf) : 0);
                    @endphp
                    <tr>
                        <td class="center">{{ $index + 1 }}</td>
                        <td class="center">{{ $row->kode_mata_kuliah ?? '-' }}</td>
                        <td>{{ $row->nama_mata_kuliah ?? '-' }}</td>
                        <td class="center">{{ $nilaiHuruf !== '' ? $nilaiHuruf : '-' }}</td>
                        <td class="center">{{ $nilaiAkhir > 0 ? $nilaiAkhir : '-' }}</td>
                        <td class="center">{{ $sks }}</td>
                        <td class="center">{{ $nilaiHuruf !== '' ? number_format((float) $kualitas, 2) : '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="center">Belum ada data nilai.</td>
                    </tr>
                @endforelse
                <tr>
                    <td colspan="5" class="right"><strong>Jumlah</strong></td>
                    <td class="center"><strong>{{ (int) ($statAktif['total_sks'] ?? 0) }}</strong></td>
                    <td></td>
                </tr>
            </tbody>
        </table>

        <table class="summary">
            <tr>
                <td>IPS Semester</td>
                <td>: {{ rtrim(rtrim(number_format((float) ($statAktif['ips'] ?? 0), 2, '.', ''), '0'), '.') }}</td>
            </tr>
            <tr>
                <td>IPK Kumulatif</td>
                <td>: {{ rtrim(rtrim(number_format((float) ($ipkMahasiswa ?? 0), 2, '.', ''), '0'), '.') }}</td>
            </tr>
            <tr>
                <td>Batas SKS</td>
                <td>: 22</td>
            </tr>
        </table>

        <div class="approval-title">PERSETUJUAN KARTU STUDI</div>

        <table class="signature-table">
            <tr>
                <td>Dosen Wali</td>
                <td class="date-wrap">Semarang, {{ now()->translatedFormat('d M Y') }}</td>
                <td>Pembantuan Ketua I Bidang Akademik</td>
            </tr>
            <tr>
                <td class="signature-space"></td>
                <td></td>
                <td class="signature-space"></td>
            </tr>
            <tr>
                <td>{{ trim((string) ($mahasiswaProfil->dosen_wali ?? '-')) }}</td>
                <td></td>
                <td>{{ trim((string) ($pembantuKetuaAkademik ?? '-')) }}</td>
            </tr>
        </table>
    </div>
</body>
</html>
