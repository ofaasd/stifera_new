@php
    $jenisLabel = (int) $tahun->jenis === 1 ? 'Ganjil' : ((int) $tahun->jenis === 2 ? 'Genap' : '-');
    $tipeLabel = (int) $tahun->tipe_mhs === 2 ? 'RPL' : 'Reguler';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Kuesioner</title>
    <style>
        body {
            font-family: "Times New Roman", serif;
            font-size: 12px;
            color: #000;
        }
        .header-table,
        .info-table,
        .kuesioner-table {
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
            width: 130px;
        }
        .separator {
            width: 10px;
        }
        .kuesioner-table {
            margin-top: 6px;
            font-size: 10px;
        }
        .kuesioner-table th,
        .kuesioner-table td {
            border: 1px solid #9a9a9a;
            padding: 4px 6px;
        }
        .kuesioner-table thead th {
            background: #e9e4e4;
            text-align: center;
            font-weight: bold;
            vertical-align: middle;
        }
        .text-center {
            text-align: center;
        }
        .category-row td {
            background: #e3e3e3;
            font-weight: bold;
        }
        .summary-row td {
            font-weight: bold;
            background: #f8f8f8;
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

        <div class="page-title">REKAP HASIL KUESIONER</div>

        <table class="info-table">
            <tr>
                <td class="label">Nama Dosen</td>
                <td class="separator">:</td>
                <td>{{ isset($dosen) ? strtoupper($dosen->nama_dosen) : '-' }}</td>
                <td class="label">Tahun Ajaran</td>
                <td class="separator">:</td>
                <td>{{ $tahun->awal }}/{{ $tahun->akhir }}</td>
            </tr>
            <tr>
                <td class="label">Mata Kuliah</td>
                <td class="separator">:</td>
                <td>{{ isset($jadwal) ? ($jadwal->kode_mata_kuliah . ' - ' . ($jadwal->nama_mata_kuliah ?? '-')) : '-' }}</td>
                <td class="label">Semester</td>
                <td class="separator">:</td>
                <td>{{ $jenisLabel }} ({{ $tipeLabel }})</td>
            </tr>
            <tr>
                <td class="label">Kelas/Rombel/Prodi</td>
                <td class="separator">:</td>
                <td colspan="4">{{ isset($jadwal) ? ((int)($jadwal->kelas ?? 0) === 3 ? 'RPL' : ((int)($jadwal->kelas ?? 0) === 2 ? 'Karyawan' : 'Reguler')) . ' ' . ($jadwal->rombel ?? '-') . ' / Program Studi ' . ($jadwal->nama_jurusan ?? '-') : '-' }}</td>
            </tr>
        </table>

        <table class="kuesioner-table">
            <thead>
                <tr>
                    <th rowspan="2">Soal</th>
                    <th colspan="4">Jumlah Jawaban Responden</th>
                    <th rowspan="2">Total Jawaban</th>
                    <th rowspan="2">Rata-rata</th>
                </tr>
                <tr>
                    <th>Sangat Tidak Setuju</th>
                    <th>Tidak Setuju</th>
                    <th>Setuju</th>
                    <th>Sangat Setuju</th>
                </tr>
            </thead>
            <tbody>
            @forelse($rekapGroups as $group)
                <tr class="category-row">
                    <td colspan="7">{{ $group['label'] }}</td>
                </tr>
                @foreach($group['items'] as $row)
                    <tr>
                        <td>Q {{ $row->no_soal }}. {{ $row->soal }}</td>
                        <td class="text-center">{{ (int) ($row->count_sts ?? 0) }}</td>
                        <td class="text-center">{{ (int) ($row->count_ts ?? 0) }}</td>
                        <td class="text-center">{{ (int) ($row->count_s ?? 0) }}</td>
                        <td class="text-center">{{ (int) ($row->count_ss ?? 0) }}</td>
                        <td class="text-center">{{ (int) ($row->total_jawaban ?? 0) }}</td>
                        <td class="text-center">{{ number_format((float) ($row->rata_nilai ?? 0), 2) }}</td>
                    </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="7" class="text-center">Data rekap kuesioner belum tersedia.</td>
                </tr>
            @endforelse
            @if($rekapGroups->isNotEmpty())
                <tr class="summary-row">
                    <td>Jumlah</td>
                    <td class="text-center">{{ (int) ($summary['count_sts'] ?? 0) }}</td>
                    <td class="text-center">{{ (int) ($summary['count_ts'] ?? 0) }}</td>
                    <td class="text-center">{{ (int) ($summary['count_s'] ?? 0) }}</td>
                    <td class="text-center">{{ (int) ($summary['count_ss'] ?? 0) }}</td>
                    <td class="text-center">{{ (int) ($summary['total_jawaban'] ?? 0) }}</td>
                    <td></td>
                </tr>
                <tr class="summary-row">
                    <td>Rata-rata</td>
                    <td colspan="5"></td>
                    <td class="text-center">{{ number_format((float) ($summary['rata_nilai'] ?? 0), 2) }}</td>
                </tr>
            @endif
        </tbody>
    </table>
    </div>
</body>
</html>