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
            font-family: dejavusans, sans-serif;
            font-size: 10px;
            color: #111;
        }

        h3,
        p {
            margin: 0 0 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #444;
            padding: 6px;
            vertical-align: middle;
        }

        th {
            background: #efefef;
            text-align: center;
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
    <h3>Rekap Hasil Kuesioner</h3>
    <table style="width: 60%; border: none; margin-bottom: 10px;">
        <tr>
            <td style="border: none; padding: 2px; width: 100px;"><strong>Tahun Ajaran</strong></td>
            <td style="border: none; padding: 2px;">: TA {{ $tahun->awal }}/{{ $tahun->akhir }} ({{ $jenisLabel }}) - {{ $tipeLabel }}</td>
        </tr>
        @if(isset($jadwal))
        <tr>
            <td style="border: none; padding: 2px;"><strong>Mata Kuliah</strong></td>
            <td style="border: none; padding: 2px;">: {{ $jadwal->kode_mata_kuliah }} - {{ $jadwal->nama_mata_kuliah ?? '-' }}</td>
        </tr>
        <tr>
            <td style="border: none; padding: 2px;"><strong>Kelas / Rombel</strong></td>
            <td style="border: none; padding: 2px;">: {{ (int)($jadwal->kelas ?? 0) === 3 ? 'RPL' : ((int)($jadwal->kelas ?? 0) === 2 ? 'Karyawan' : 'Reguler') }} {{ $jadwal->rombel ?? '-' }}</td>
        </tr>
        @endif
        @if(isset($dosen))
        <tr>
            <td style="border: none; padding: 2px;"><strong>Dosen</strong></td>
            <td style="border: none; padding: 2px;">: {{ $dosen->nama_dosen ?? '-' }}</td>
        </tr>
        @endif
    </table>

    <table>
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
</body>
</html>