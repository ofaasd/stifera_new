<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Absensi Kehadiran Mahasiswa</title>
    <style>
        body {
            font-family: "Times New Roman", serif;
            font-size: 10px;
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
            font-size: 9px;
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
            font-size: 8px;
            border: 1px solid #000;
        }
        .presensi-table th,
        .presensi-table td {
            border: 1px solid #000;
            padding: 2px 3px;
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
            max-width: 50px;
            max-height: 35px;
        }
        .attendance-signature {
            max-width: 34px;
            max-height: 20px;
            display: block;
            margin: 0 auto;
        }
        .signature-check {
            display: inline-block;
            width: 20px;
            height: 18px;
            line-height: 18px;
            text-align: center;
            font-size: 12px;
            font-weight: bold;
        }
        .signature-check svg {
            width: 100%;
            height: 100%;
            display: block;
        }
        .pertemuan-date {
            font-size: 7px;
            line-height: 1.15;
            white-space: nowrap;
        }
        .summary {
            margin-top: 12px;
            width: 45%;
            font-size: 9px;
        }
        .summary td {
            padding: 2px 3px;
            font-weight: bold;
        }
        .signature-table {
            margin-top: 20px;
            font-size: 9px;
            page-break-inside: avoid;
            break-inside: avoid-page;
        }
        .signature-table tr,
        .signature-table td,
        .signature-table tbody {
            page-break-inside: avoid;
            break-inside: avoid-page;
        }
        .signature-table td {
            width: 33.33%;
            text-align: left;
            vertical-align: top;
            padding: 5px;
            page-break-inside: avoid;
            break-inside: avoid-page;
        }
        .signature-space {
            height: 58px;
        }
        .signature-name {
            font-weight: bold;
            text-decoration: underline;
            display: inline-block;
            margin-bottom: 2px;
        }
        .signature-center {
            text-align: center;
        }
        .signature-right {
            text-align: right;
        }
        .pagination-note {
            text-align: center;
            font-size: 8px;
            margin-top: 8px;
            color: #666;
        }
        .signature-section {
            margin-top: 18mm;
            page-break-inside: avoid;
            break-inside: avoid-page;
        }
        .bac-sheet {
            page-break-before: always;
            padding-top: 4mm;
        }
        .bac-header-table,
        .bac-info-table,
        .bac-table {
            width: 100%;
            border-collapse: collapse;
        }
        .bac-header-table td {
            vertical-align: top;
        }
        .bac-brand-row td {
            padding-bottom: 6px;
        }
        .bac-title {
            text-align: center;
            font-weight: bold;
            font-size: 12px;
            line-height: 1.25;
            margin-bottom: 14px;
        }
        .bac-title-wrap {
            width: 100%;
            text-align: center;
        }
        .bac-left-brand {
            width: 220px;
            font-size: 9px;
            line-height: 1.2;
        }
        .bac-info-table {
            margin: 10px 0 12px;
            font-size: 9px;
        }
        .bac-info-table td {
            padding: 2px 4px;
            vertical-align: top;
        }
        .bac-table {
            font-size: 8px;
            border: 1px solid #000;
        }
        .bac-table th,
        .bac-table td {
            border: 1px solid #000;
            padding: 4px 4px;
            vertical-align: top;
        }
        .bac-table thead th {
            text-align: center;
            font-weight: bold;
            line-height: 1.15;
        }
        .bac-row-space {
            height: 44px;
        }
        .bac-signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 18px;
            font-size: 9px;
            page-break-inside: avoid;
        }
        .bac-signature-table td {
            width: 50%;
            vertical-align: top;
            padding: 0;
        }
        .bac-signature-right {
            text-align: right;
        }
        .bac-signature-space {
            height: 42px;
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

        {{-- Info Jadwal --}}
        <table class="info-table">
            <tr>
                <td class="label">Kode MK</td>
                <td class="separator">:</td>
                <td>{{ (string) ($jadwal->kode_mata_kuliah ?? '-') }}</td>
                <td class="label" style="padding-left: 30px;">Mata Kuliah</td>
                <td class="separator">:</td>
                <td>{{ (string) ($jadwal->nama_mata_kuliah ?? '-') }}</td>
            </tr>
            <tr>
                <td class="label">Pengampu</td>
                <td class="separator">:</td>
                <td>{{ trim((string) ($jadwal->nama_dosen ?? '-')) }}</td>
                <td class="label" style="padding-left: 30px;">Kelas</td>
                <td class="separator">:</td>
                <td>{{ (($jadwal->tipe_mhs == 2 ? 'Karyawan' : 'Reguler') . ' ' . ($jadwal->rombel ?? '-')) }}</td>
            </tr>
            <tr>
                <td class="label">Hari / Jam</td>
                <td class="separator">:</td>
                <td>{{ ($jadwal->hari ?? '-') }} / {{ ($jadwal->sesi ?? '-') }}</td>
                <td class="label" style="padding-left: 30px;">Ruang</td>
                <td class="separator">:</td>
                <td>{{ ($jadwal->ruang ?? '-') }}</td>
            </tr>
        </table>

        {{-- Tabel Presensi --}}
        <table class="presensi-table">
            <thead>
                <tr>
                    <th style="width: 25px;">No</th>
                    <th style="width: 60px;">NIM</th>
                    <th style="width: 140px;">Nama Mahasiswa</th>
                    @foreach($pertemuanList as $prt)
                        <th style="width: 48px;" class="pertemuan-date">
                            @if(!empty($prt->tgl_pertemuan))
                                {{ \Carbon\Carbon::parse($prt->tgl_pertemuan)->format('d/m') }}
                            @else
                                P{{ $prt->id_pertemuan }}
                            @endif
                        </th>
                    @endforeach
                    
                </tr>
            </thead>
            <tbody>
                @php
                    $no = 1;
                @endphp
                @forelse($mahasiswaList as $mhs)
                    @php
                        $hadirCount = 0;
                        $izinCount = 0;
                        $alfaCount = 0;
                    @endphp
                    <tr>
                        <td class="center">{{ $no++ }}</td>
                        <td>{{ $mhs->nim }}</td>
                        <td>{{ $mhs->nama ?? '-' }}</td>
                        @foreach($pertemuanList as $prt)
                            @php
                                $p = $presensiIndex[(string) $mhs->nim . '|' . (string) $prt->tgl_pertemuan] ?? null;
                                $statusDisplay = '';
                                if ($p) {
                                    if ($p['status'] === 1) {
                                        $hadirCount++;
                                    } elseif ($p['status'] === 2) {
                                        $statusDisplay = 'I';
                                        $izinCount++;
                                    } elseif ($p['status'] === 3) {
                                        $statusDisplay = 'A';
                                        $alfaCount++;
                                    }
                                }
                            @endphp
                            <td class="center">
                                @php
                                    $imageSrc = null;
                                    $hasSignature = false;
                                    if ($p && $p['status'] === 1 && !empty($p['ttd'])) {
                                        $ttdVal = $p['ttd'];
                                        if (str_starts_with($ttdVal, 'data:')) {
                                            $hasSignature = true;
                                            $imageSrc = $ttdVal;
                                        } elseif (filter_var($ttdVal, FILTER_VALIDATE_URL)) {
                                            $hasSignature = true;
                                            $imageSrc = $ttdVal;
                                        } else {
                                            $clean = ltrim($ttdVal, '/');
                                            $publicFile = public_path($clean);
                                            if (is_file($publicFile)) {
                                                $hasSignature = true;
                                                $imageSrc = asset($clean);
                                            } else {
                                                $storageFile = storage_path('app/public/' . $clean);
                                                if (is_file($storageFile)) {
                                                    $hasSignature = true;
                                                    $imageSrc = asset('storage/' . $clean);
                                                }
                                            }
                                        }
                                    }
                                @endphp

                                @if($p && $p['status'] === 1)
                                    @if($hasSignature && $imageSrc)
                                        <img src="{{ $imageSrc }}" alt="TTD" class="attendance-signature">
                                    @else
                                        <span class="signature-check">
                                            <svg viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                                <path d="M2 8l3 3 8-8" fill="none" stroke="#000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </span>
                                    @endif
                                @else
                                    {{ $statusDisplay }}
                                @endif
                            </td>
                        @endforeach
                        
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($pertemuanList) + 3 }}" class="center">Tidak ada data mahasiswa.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Keterangan --}}
        <div style="margin-top: 10px; font-size: 8px;">
            <strong>Keterangan:</strong> TTD = Hadir, I = Izin, A = Alfa (Tanpa Keterangan), (kosong) = Belum Tercatat
        </div>

        {{-- Tanda Tangan --}}
        @php
            // Jika data sudah padat, pindahkan blok tanda tangan ke halaman berikutnya agar tidak terbelah.
            $forceSignatureNewPage = count($mahasiswaList) >= 17;
            $jenjangProdi = strtoupper(trim((string) ($jadwal->jenjang_prodi ?? 'S1')));
            $labelJenjang = str_contains($jenjangProdi, 'D3') ? 'D3' : 'S1';
            $namaKetuaProdi = trim((string) ($ketuaProdi->nama_gelar ?? '-'));
            $nipKetuaProdi = trim((string) ($ketuaProdi->nip_pns ?? ''));
            $namaDosen1 = trim((string) ($jadwal->nama_dosen ?? '-'));
            $nipDosen1 = trim((string) ($jadwal->nip_dosen ?? ''));
            $namaDosen2 = trim((string) ($jadwal->nama_dosen2 ?? '-'));
            $nipDosen2 = trim((string) ($jadwal->nip_dosen2 ?? ''));
        @endphp
        @if($forceSignatureNewPage)
            <pagebreak />
        @endif
        <div class="signature-section">
            <table class="signature-table">
                <tr>
                    <td>Ketua Prodi {{ $labelJenjang }} FARMASI</td>
                    <td class="signature-center">Semarang, {{ now()->translatedFormat('d-m-Y') }}<br>Dosen Pengampu</td>
                    <td class="signature-right">Dosen Pengampu 2</td>
                </tr>
                <tr>
                    <td class="signature-space"></td>
                    <td class="signature-space"></td>
                    <td class="signature-space"></td>
                </tr>
                <tr>
                    <td>
                        <span class="signature-name">{{ $namaKetuaProdi !== '' ? $namaKetuaProdi : '-' }}</span><br>
                        NIP. {{ $nipKetuaProdi !== '' ? $nipKetuaProdi : '-' }}
                    </td>
                    <td class="signature-center">
                        <span class="signature-name">{{ $namaDosen1 !== '' ? $namaDosen1 : '-' }}</span><br>
                        NIP. {{ $nipDosen1 !== '' ? $nipDosen1 : '-' }}
                    </td>
                    <td class="signature-right">
                        <span class="signature-name">{{ $namaDosen2 !== '' ? $namaDosen2 : '-' }}</span><br>
                        NIP. {{ $nipDosen2 !== '' ? $nipDosen2 : '-' }}
                    </td>
                </tr>
            </table>

            <div class="pagination-note">
                <em>Laporan ini dicetak otomatis oleh sistem akademik pada {{ now()->translatedFormat('d F Y H:i:s') }}</em>
            </div>
        </div>

        @php
            $jenisTahun = (int) ($jadwal->jenis_tahun ?? 0);
            $labelSemester = match($jenisTahun) {
                1 => 'GANJIL',
                2 => 'GENAP',
                3 => 'ANTARA GANJIL GENAP',
                4 => 'ANTARA GENAP GANJIL',
                default => '-',
            };
            $namaDosenGabung = trim((string) ($jadwal->nama_dosen ?? '-'));
            if (trim((string) ($jadwal->nama_dosen2 ?? '')) !== '') {
                $namaDosenGabung .= ' dan ' . trim((string) ($jadwal->nama_dosen2 ?? ''));
            }
            $ruangBac = !empty($jadwal->ruang) ? urldecode((string) $jadwal->ruang) : '-';
            $namaKetuaSekolah = trim((string) ($ketuaSekolah->nama_gelar ?? '-'));
            $nipKetuaSekolah = trim((string) ($ketuaSekolah->nip_pns ?? ''));
        @endphp

        <div class="bac-sheet">
            <table class="bac-header-table">
                <tr class="bac-brand-row">
                    <td class="bac-left-brand">
                        <img class="logo-abbr" src="{{ asset(config('dz.site_level.logo')) }}" alt="" width="30" height="30" align="left">
                        <div style="font-weight:bold; margin-top:2px;">SEKOLAH TINGGI ILMU FARMASI NUSAPUTERA</div>
                        <div>Kampus: Jl. Medoho III No. 2 Semarang</div>
                        <div>Telp/Fax. (024) 6747012</div>
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div class="bac-title-wrap">
                            <div class="bac-title">
                            <center>
                            BERITA ACARA PERKULIAHAN<br>
                            SEKOLAH TINGGI ILMU FARMASI NUSAPUTERA<br>
                            TAHUN AKADEMIK {{ ($jadwal->tahun_awal ?? '-') . '/' . ($jadwal->tahun_akhir ?? '-') }} {{ $labelSemester }}
                            </center>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>

            <table class="bac-info-table">
                <tr>
                    <td style="width: 120px;">Nama Dosen</td>
                    <td style="width: 10px;">:</td>
                    <td>{{ $namaDosenGabung }}</td>
                    <td style="width: 120px;">Bobot SKS</td>
                    <td style="width: 10px;">:</td>
                    <td style="width: 140px;">{{ (int) ($jadwal->jumlah_sks ?? 0) }}</td>
                </tr>
                <tr>
                    <td>Nama Matakuliah</td>
                    <td>:</td>
                    <td>{{ $jadwal->nama_mata_kuliah ?? '-' }}</td>
                    <td>Ruang</td>
                    <td>:</td>
                    <td>{{ $ruangBac }}</td>
                </tr>
                <tr>
                    <td>Jam Perkuliahan</td>
                    <td>:</td>
                    <td>{{ $jadwal->sesi ?? '-' }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </table>

            <table class="bac-table">
                <thead>
                    <tr>
                        <th rowspan="2" style="width: 26px;">No.</th>
                        <th rowspan="2" style="width: 130px;">Rencana Tanggal Pertemuan</th>
                        <th rowspan="2">Materi Kontrak Perkuliahan</th>
                        <th rowspan="2" style="width: 108px;">Tanggal Pelaksanaan</th>
                        <th rowspan="2" style="width: 110px;">Sub Bahasan</th>
                        <th colspan="2" style="width: 110px;">Tanda Tangan</th>
                        <th colspan="2" style="width: 130px;">Jumlah Mahasiswa</th>
                    </tr>
                    <tr>
                        <th style="width: 55px;">Dosen</th>
                        <th style="width: 55px;">MHS</th>
                        <th style="width: 65px;">Hadir</th>
                        <th style="width: 65px;">Tidak Hadir</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($beritaAcaraRows as $index => $row)
                        <tr>
                            <td class="center">{{ $index + 1 }}</td>
                            <td>
                                @if(!empty($row['rencana_tanggal']))
                                    {{ \Carbon\Carbon::parse($row['rencana_tanggal'])->format('d/m/Y') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $row['materi'] !== '' ? $row['materi'] : '-' }}</td>
                            <td>
                                @if(!empty($row['tanggal_pelaksanaan']))
                                    {{ \Carbon\Carbon::parse($row['tanggal_pelaksanaan'])->format('d/m/Y') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $row['sub_bahasan'] !== '' ? $row['sub_bahasan'] : '-' }}</td>
                            <td class="bac-row-space"></td>
                            <td class="bac-row-space"></td>
                            <td class="center">{{ (int) ($row['mhs_hadir'] ?? 0) }}</td>
                            <td class="center">{{ (int) ($row['mhs_tidak_hadir'] ?? 0) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="center">Belum ada data berita acara perkuliahan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <table class="bac-signature-table">
                <tr>
                    <td>
                        Mengetahui,<br>
                        Ketua Sekolah Tinggi Ilmu Farmasi Nusaputera
                    </td>
                    <td class="bac-signature-right">
                        Ketua Prodi {{ $labelJenjang }} FARMASI
                    </td>
                </tr>
                <tr>
                    <td class="bac-signature-space"></td>
                    <td class="bac-signature-space"></td>
                </tr>
                <tr>
                    <td>
                        <span class="signature-name">{{ $namaKetuaSekolah !== '' ? $namaKetuaSekolah : '-' }}</span><br>
                        NIP. {{ $nipKetuaSekolah !== '' ? $nipKetuaSekolah : '-' }}
                    </td>
                    <td class="bac-signature-right">
                        <span class="signature-name">{{ $namaKetuaProdi !== '' ? $namaKetuaProdi : '-' }}</span><br>
                        NIP. {{ $nipKetuaProdi !== '' ? $nipKetuaProdi : '-' }}
                    </td>
                </tr>
            </table>
        </div>

    </div>
</body>
</html>
