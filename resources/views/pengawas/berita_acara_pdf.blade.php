<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Berita Acara & Presensi Ujian {{ strtoupper($jenis) }} - {{ $jadwal->nama_mata_kuliah ?? '' }}</title>
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 10pt;
            color: #000;
            line-height: 1.4;
        }

        .header-box-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .header-box-table td {
            border: 1px solid #000;
            vertical-align: middle;
        }

        .logo-col {
            width: 85px;
            text-align: center;
            padding: 6px;
        }

        .title-col {
            padding: 8px 12px;
            text-align: center;
        }

        .doc-control-col {
            width: 175px;
            padding: 0;
            vertical-align: top;
            background-color: #f7ede2;
        }

        .doc-control-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
        }

        .doc-control-table td {
            border: none;
            border-bottom: 1px solid #000;
            padding: 4px 6px;
            vertical-align: middle;
        }

        .doc-control-table tr:last-child td {
            border-bottom: none;
        }

        .doc-label {
            width: 65px;
            font-weight: bold;
            border-right: 1px solid #000 !important;
        }

        .institution-title {
            font-size: 12.5pt;
            font-weight: bold;
            line-height: 1.2;
            margin-bottom: 2px;
        }

        .institution-subtitle {
            font-size: 8.5pt;
            line-height: 1.25;
        }

        .form-title-badge {
            font-size: 11pt;
            font-weight: bold;
            line-height: 1.25;
            margin-top: 6px;
            padding-top: 6px;
            border-top: 1px solid #000;
        }

        .narration-text {
            font-size: 10.5pt;
            line-height: 1.6;
            text-align: justify;
            margin-bottom: 14px;
        }

        .peserta-count-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10.5pt;
            margin-bottom: 15px;
        }

        .peserta-count-table td {
            padding: 3px 0;
            vertical-align: top;
        }

        .catatan-title {
            font-size: 10.5pt;
            font-weight: bold;
            font-style: italic;
            margin-bottom: 4px;
        }

        .catatan-box {
            border: 1px dashed #666;
            background-color: #fafafa;
            padding: 8px 12px;
            min-height: 60px;
            font-size: 10pt;
            line-height: 1.4;
            margin-bottom: 18px;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10.5pt;
            margin-top: 10px;
        }

        .signature-table td {
            vertical-align: top;
        }

        .peserta-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
            margin-top: 8px;
        }

        .peserta-table th {
            border: 1px solid #000;
            background-color: #f0f0f0;
            padding: 6px 4px;
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
        }

        .peserta-table td {
            border: 1px solid #000;
            padding: 4px 5px;
            vertical-align: middle;
        }

        .text-center { text-align: center; }
        .text-start { text-align: left; }
        .text-end { text-align: right; }

        .text-success { color: #198754; }
        .text-danger { color: #dc3545; }
        .fw-bold { font-weight: bold; }
    </style>
</head>
<body>

    {{-- ============================================================== --}}
    {{-- HALAMAN 1: FORMULIR BERITA ACARA UJIAN                        --}}
    {{-- ============================================================== --}}

    {{-- Header Tabel Sesuai Format Resmi SPMI --}}
    <table class="header-box-table">
        <tr>
            <td class="logo-col">
                @if(file_exists(public_path('images/logo/logo.png')))
                    <img src="{{ public_path('images/logo/logo.png') }}" style="width: 65px; height: auto;" alt="Logo STIFERA">
                @endif
            </td>
            <td class="title-col">
                <div class="institution-title">SEKOLAH TINGGI ILMU FARMASI NUSAPUTERA</div>
                <div class="institution-subtitle">
                    Jl. Medoho III No.2, Semarang. Telp/Fax 024-6747012.<br>
                    e-mail : <span style="color: blue;">stiferanusaputera@gmail.com</span><br>
                    <span style="text-decoration: underline;">https://www.stiferanusaputera.ac.id</span>
                </div>
                <div class="form-title-badge">
                    FORMULIR<br>
                    BERITA ACARA<br>
                    {{ strtoupper($labelJenisUjian) }}
                </div>
            </td>
            <td class="doc-control-col">
                <table class="doc-control-table">
                    <tr>
                        <td class="doc-label">No.Dokumen</td>
                        <td>SPMI-STIFERA/FM/03/18D/02</td>
                    </tr>
                    <tr>
                        <td class="doc-label">Tanggal</td>
                        <td>30 September 2022</td>
                    </tr>
                    <tr>
                        <td class="doc-label">Revisi</td>
                        <td>01</td>
                    </tr>
                    <tr>
                        <td class="doc-label">Halaman</td>
                        <td>1 dari 2</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Paragraf Narasi Berita Acara --}}
    <div class="narration-text">
        Pada Hari ini <strong>{{ $hariUjian }}</strong> 
        Tanggal <strong>{{ $tglUjian }}</strong> 
        Bulan <strong>{{ $bulanUjian }}</strong> 
        Tahun <strong>{{ $tahunUjian }}</strong> 
        telah diselenggarakan {{ $labelJenisUjian }} 
        {{ $labelTaJenis }} Tahun Akademik {{ $taTeks }}, 
        Mata Kuliah <strong>{{ $jadwal->nama_mata_kuliah ?? '-' }}</strong> 
        Semester <strong>{{ $semesterTeks }}</strong> 
        dari pukul <strong>{{ $jamMulai }}</strong> s.d. <strong>{{ $jamSelesai }}</strong>.
    </div>

    {{-- Tabel Jumlah Peserta Hadir / Tidak Hadir --}}
    <table class="peserta-count-table">
        <tr>
            <td style="width: 140px;">Jumlah peserta</td>
            <td style="width: 15px;">:</td>
            <td><strong>{{ $totalPeserta }}</strong> orang</td>
        </tr>
        <tr>
            <td>Yang hadir</td>
            <td>:</td>
            <td>
                <strong>{{ $totalHadir }}</strong> orang, yakni nomor kursi:
                <span>{{ $nomorKursiHadir }}</span>
            </td>
        </tr>
        <tr>
            <td>Yang tidak hadir</td>
            <td>:</td>
            <td>
                <strong>{{ $totalTidakHadir }}</strong> orang, yakni nomor kursi:
                <span>{{ $nomorKursiTidakHadir }}</span>
            </td>
        </tr>
    </table>

    {{-- Catatan --}}
    <div class="catatan-title">Catatan:</div>
    <div class="catatan-box">
        @if(!empty($catatan))
            {!! nl2br(e($catatan)) !!}
        @else
            <span style="color: #888;">(Tidak ada catatan khusus selama pelaksanaan ujian)</span>
        @endif
    </div>

    <div style="font-size: 10.5pt; margin-bottom: 25px;">
        Demikian berita acara ini dibuat dengan sesungguhnya.
    </div>

    {{-- Tanda Tangan Mengetahui & Pengawas --}}
    <table class="signature-table">
        <tr>
            <td style="width: 52%;">
                Mengetahui,<br>
                Ketua Sekolah Tinggi Ilmu Farmasi Nusaputera,<br><br><br><br><br>
                <strong style="text-decoration: underline;">{{ $namaKetuaSekolah }}</strong><br>
                NIP. {{ $nipKetuaSekolah }}
            </td>
            <td style="width: 48%; padding-left: 30px;">
                Semarang, {{ $tglCetak }}<br>
                Pengawas,<br>
                @if(!empty($masterParaf))
                    <div style="margin: 3px 0;">
                        <img src="{{ $masterParaf }}" style="max-height: 52px; max-width: 130px;" alt="Paraf Pengawas">
                    </div>
                @else
                    <br><br><br><br><br>
                @endif
                @if(!empty($namaPengawas))
                    <strong style="text-decoration: underline;">{{ $namaPengawas }}</strong>
                @else
                    _________________________________________
                @endif
            </td>
        </tr>
    </table>

    {{-- ============================================================== --}}
    {{-- HALAMAN 2: DAFTAR HADIR & STATUS PARAF MAHASISWA              --}}
    {{-- ============================================================== --}}
    <pagebreak />

    <div style="text-align: center; margin-bottom: 12px;">
        <h3 style="margin: 0 0 4px 0; font-size: 13pt; text-transform: uppercase;">
            DAFTAR HADIR & PARAF PESERTA UJIAN {{ strtoupper($labelJenisUjian) }}
        </h3>
        <div style="font-size: 9.5pt; color: #222;">
            Mata Kuliah: <strong>{{ $jadwal->nama_mata_kuliah ?? '-' }}</strong> ({{ $jadwal->kode_mata_kuliah ?? '-' }}) &bull; 
            Ruang: <strong>{{ $jadwal->ruang ?? '-' }}</strong> &bull; 
            TA: <strong>{{ $taTeks }} ({{ $labelTaJenis }})</strong>
        </div>
    </div>

    <table class="peserta-table">
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th style="width: 55px;">No. Kursi</th>
                <th style="width: 85px;">NIM</th>
                <th style="text-align: left;">Nama Mahasiswa</th>
                <th style="width: 175px;">Status Kelayakan Ujian</th>
                <th style="width: 105px;">Paraf Pengawas</th>
                <th style="width: 80px;">Waktu Paraf</th>
            </tr>
        </thead>
        <tbody>
            @forelse($peserta as $idx => $p)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="text-center fw-bold">{{ $p->no_kursi ?? '-' }}</td>
                    <td class="text-center">{{ $p->nim }}</td>
                    <td class="text-start">
                        <div class="fw-bold">{{ $p->nama_mahasiswa ?? '-' }}</div>
                    </td>
                    <td class="text-center">
                        @if($p->is_lolos)
                            <span class="text-success fw-bold">Bisa Ikut Ujian</span><br>
                            <small class="text-success" style="font-size: 7.5pt;">Dosen Wali & BAK Lengkap</small>
                        @else
                            <span class="text-danger fw-bold">Tidak Bisa Ikut</span><br>
                            <small class="text-danger fw-bold" style="font-size: 7.5pt;">{{ $p->keterangan }}</small>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($p->is_lolos)
                            @if(!empty($p->paraf))
                                <img src="{{ $p->paraf }}" style="max-height: 28px; max-width: 75px;" alt="Paraf">
                            @else
                                <span style="color: #888; font-size: 8pt;">(Belum Diparaf)</span>
                            @endif
                        @else
                            <span class="text-danger fw-bold" style="font-size: 8pt;">Terkunci</span>
                        @endif
                    </td>
                    <td class="text-center" style="font-size: 7.5pt;">
                        {{ !empty($p->waktu_paraf) ? \Carbon\Carbon::parse($p->waktu_paraf)->format('d/m/y H:i') : '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 12px;">Tidak ada mahasiswa terdaftar pada jadwal ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>

