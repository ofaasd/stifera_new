<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Absensi Kehadiran Mahasiswa</title>
    <style>
        body {
            font-family: "Times New Roman", serif;
            font-size: 11px;
            color: #000;
        }
        .sheet {
            border: 1px solid #9a9a9a;
            padding: 20px 25px;
        }
        .header-table,
        .info-table,
        .presensi-table,
        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table td {
            vertical-align: top;
        }
        .logo-cell {
            width: 70px;
            padding-right: 15px;
        }
        .logo-abbr {
            width: 60px;
            height: 60px;
            display: block;
        }
        .institution {
            text-align: center;
            line-height: 1.3;
        }
        .institution .name {
            font-size: 16px;
            font-weight: bold;
        }
        .institution .meta {
            font-size: 10px;
        }
        .page-title {
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            margin: 16px 0 12px;
        }
        .info-table {
            margin-bottom: 12px;
        }
        .info-table td {
            padding: 2px 3px;
            vertical-align: top;
            font-size: 10px;
        }
        .label {
            width: 90px;
            font-weight: bold;
        }
        .separator {
            width: 8px;
        }
        .presensi-table {
            margin-top: 8px;
            font-size: 9px;
            border: 1px solid #000;
        }
        .presensi-table th,
        .presensi-table td {
            border: 1px solid #000;
            padding: 3px 4px;
        }
        .presensi-table thead th {
            background: #d9d9d9;
            text-align: center;
            font-weight: bold;
            vertical-align: middle;
        }
        .center {
            text-align: center;
        }
        .right {
            text-align: right;
        }
        .signature-img {
            max-width: 60px;
            max-height: 40px;
        }
        .signature-check {
            display: inline-block;
            width: 24px;
            height: 24px;
            vertical-align: middle;
        }
        .signature-check svg {
            width: 100%;
            height: 100%;
            display: block;
        }
        .summary {
            margin-top: 12px;
            width: 45%;
            font-size: 10px;
        }
        .summary td {
            padding: 2px 3px;
            font-weight: bold;
        }
        .approval-title {
            margin-top: 20px;
            padding-top: 12px;
            border-top: 1px solid #8f8f8f;
            text-align: center;
            font-weight: bold;
            font-size: 11px;
        }
        .signature-table {
            margin-top: 30px;
            font-size: 10px;
        }
        .signature-table td {
            width: 33.33%;
            text-align: center;
            vertical-align: top;
            padding: 5px;
        }
        .signature-space {
            height: 50px;
        }
        .date-wrap {
            text-align: right;
            padding-right: 20px;
        }
        hr {
            border: none;
            border-top: 1px solid #000;
            margin: 8px 0;
        }
    </style>
</head>
<body>
    <div class="sheet">
        {{-- Header dengan Logo --}}
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

        <div class="page-title">ABSENSI KEHADIRAN MAHASISWA</div>

        {{-- Info Mahasiswa --}}
        <table class="info-table">
            <tr>
                <td class="label">Nama</td>
                <td class="separator">:</td>
                <td>{{ strtoupper((string) ($mahasiswa->nama ?? '-')) }}</td>
                <td class="label" style="padding-left: 30px;">Program Studi</td>
                <td class="separator">:</td>
                <td>-</td>
            </tr>
            <tr>
                <td class="label">NIM</td>
                <td class="separator">:</td>
                <td>{{ (string) ($mahasiswa->nim ?? '-') }}</td>
                <td class="label" style="padding-left: 30px;">Angkatan</td>
                <td class="separator">:</td>
                <td>-</td>
            </tr>
            @if($tahunAktif)
                <tr>
                    <td class="label">Tahun Ajaran</td>
                    <td class="separator">:</td>
                    <td>
                        {{ ($tahunAktif->awal ?? '-') . '/' . ($tahunAktif->akhir ?? '-') }}
                        @php
                            $jenisTa = (int) ($tahunAktif->jenis ?? 0);
                            $labelJenis = match($jenisTa) { 1 => 'Ganjil', 2 => 'Genap', 3 => 'Antara Ganjil Genap', 4 => 'Antara Genap Ganjil', default => '-' };
                        @endphp
                        <span>({{ $labelJenis }})</span>
                    </td>
                </tr>
            @endif
        </table>

        {{-- Tabel Presensi --}}
        <table class="presensi-table">
            <thead>
                <tr>
                    <th style="width: 30px;">No</th>
                    <th style="width: 70px;">Kode MK</th>
                    <th>Mata Kuliah</th>
                    <th style="width: 50px;">Pert</th>
                    <th style="width: 70px;">Tanggal</th>
                    <th style="width: 45px;">Status</th>
                    <th style="width: 50px;">TTD</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $no = 1;
                    $lastMk = null;
                @endphp
                @forelse($presensiDetails as $detail)
                    @php
                        $statusLabel = '';
                        if ($detail['status'] === 1) {
                            $statusLabel = 'Hadir';
                        } elseif ($detail['status'] === 2) {
                            $statusLabel = 'Izin';
                        } elseif ($detail['status'] === 3) {
                            $statusLabel = 'Alfa';
                        } else {
                            $statusLabel = 'Belum';
                        }
                    @endphp
                    <tr>
                        <td class="center">{{ $no++ }}</td>
                        <td>{{ $detail['kode_mk'] ?? '-' }}</td>
                        <td>{{ $detail['nama_mk'] ?? '-' }}</td>
                        <td class="center">{{ $detail['pertemuan_ke'] ?? '-' }}</td>
                        <td class="center">
                            @if($detail['tanggal'])
                                {{ \Carbon\Carbon::parse($detail['tanggal'])->translatedFormat('d/m/Y') }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="center">{{ $statusLabel }}</td>
                        <td class="center">
                            @php
                                $ttdPath = $detail['ttd'] ?? null;
                                $hasSignatureImage = false;
                                $imageSrc = null;

                                if (!empty($ttdPath)) {
                                    if (str_starts_with($ttdPath, 'data:')) {
                                        $hasSignatureImage = true;
                                        $imageSrc = $ttdPath;
                                    } elseif (filter_var($ttdPath, FILTER_VALIDATE_URL)) {
                                        $hasSignatureImage = true;
                                        $imageSrc = $ttdPath;
                                    } else {
                                        $cleanPath = ltrim($ttdPath, '/');
                                        $customPath = 'images/ttd/' . $cleanPath;
                                        if (is_file(public_path($customPath))) {
                                            $hasSignatureImage = true;
                                            $imageSrc = asset($customPath);
                                        } elseif (is_file(public_path($cleanPath))) {
                                            $hasSignatureImage = true;
                                            $imageSrc = asset($cleanPath);
                                        } else {
                                            $storageFile = storage_path('app/public/' . $cleanPath);
                                            if (is_file($storageFile)) {
                                                $hasSignatureImage = true;
                                                $imageSrc = asset('storage/' . $cleanPath);
                                            }
                                        }
                                    }
                                }
                            @endphp

                            @if($hasSignatureImage && $imageSrc)
                                <img src="{{ $imageSrc }}" alt="TTD" class="signature-img">
                                @else
                                <span class="signature-check">
                                    <svg viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path d="M2 8l3 3 8-8" fill="none" stroke="#000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="center">Tidak ada data presensi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Summary per Mata Kuliah --}}
        @php
            $summaryByMk = [];
            foreach ($presensiDetails as $detail) {
                $key = $detail['kode_mk'];
                if (!isset($summaryByMk[$key])) {
                    $summaryByMk[$key] = [
                        'nama_mk' => $detail['nama_mk'],
                        'total' => 0,
                        'hadir' => 0,
                        'izin' => 0,
                        'alfa' => 0,
                        'belum' => 0,
                    ];
                }
                $summaryByMk[$key]['total']++;
                if ($detail['status'] === 1) {
                    $summaryByMk[$key]['hadir']++;
                } elseif ($detail['status'] === 2) {
                    $summaryByMk[$key]['izin']++;
                } elseif ($detail['status'] === 3) {
                    $summaryByMk[$key]['alfa']++;
                } else {
                    $summaryByMk[$key]['belum']++;
                }
            }
        @endphp

        @if(!empty($summaryByMk))
            <div style="margin-top: 12px; margin-bottom: 12px;">
                <strong>Ringkasan Kehadiran per Mata Kuliah:</strong>
                <table class="presensi-table" style="margin-top: 6px; font-size: 9px;">
                    <thead>
                        <tr>
                            <th>Kode MK</th>
                            <th>Nama Mata Kuliah</th>
                            <th style="width: 40px;">Total</th>
                            <th style="width: 40px;">Hadir</th>
                            <th style="width: 40px;">Izin</th>
                            <th style="width: 40px;">Alfa</th>
                            <th style="width: 40px;">Belum</th>
                            <th style="width: 50px;">% Hadir</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($summaryByMk as $kode => $summary)
                            @php
                                $persenHadir = $summary['total'] > 0 ? round(($summary['hadir'] / $summary['total']) * 100, 1) : 0;
                            @endphp
                            <tr>
                                <td class="center">{{ $kode }}</td>
                                <td>{{ $summary['nama_mk'] }}</td>
                                <td class="center">{{ $summary['total'] }}</td>
                                <td class="center">{{ $summary['hadir'] }}</td>
                                <td class="center">{{ $summary['izin'] }}</td>
                                <td class="center">{{ $summary['alfa'] }}</td>
                                <td class="center">{{ $summary['belum'] }}</td>
                                <td class="center">{{ $persenHadir }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        {{-- Tanda Tangan --}}
        <div class="approval-title">PERSETUJUAN</div>

        <table class="signature-table">
            <tr>
                <td>Dosen Pembimbing Akademik</td>
                <td style="text-align: right; padding-right: 20px;">Semarang, {{ now()->translatedFormat('d F Y') }}</td>
                <td>Mahasiswa</td>
            </tr>
            <tr>
                <td class="signature-space"></td>
                <td class="signature-space"></td>
                <td class="signature-space"></td>
            </tr>
            <tr>
                <td>(.......................................)</td>
                <td></td>
                <td>(.......................................)</td>
            </tr>
        </table>

    </div>
</body>
</html>
