<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Transkrip Depan - {{ $dokumen->mahasiswa->nama }}</title>
    <style>
        @page { size: A4 portrait; margin: 20mm; }
        body { font-family: 'Times New Roman', Tahoma, serif; line-height: 1.5; font-size: 14px; }
        .header { text-align: center; margin-bottom: 30px; font-weight: bold; }
        .header h1 { font-size: 20px; text-transform: uppercase; margin:0;}
        .header h2 { font-size: 18px; text-transform: uppercase; margin:0;}
        .header h3 { font-size: 16px; margin-top:20px;}
        
        .biodata-table { width: 100%; margin-top: 30px; margin-bottom: 40px; border-collapse: collapse; }
        .biodata-table td { padding: 8px 4px; vertical-align: top; font-size: 14px;}
        .biodata-table td.label-col { width: 40%; font-weight: bold;}
        .val-col { width: 60%; font-weight: bold; }
        
        .title-box { margin-top: 30px; }
        .title-box p { font-weight: bold; margin: 0; }
        
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h1>SEKOLAH TINGGI ILMU FARMASI NUSAPUTERA</h1>
        <h2>SEMARANG</h2>
        <br>
        <h2>TRANSKRIP AKADEMIK</h2>
        <p style="font-weight:normal; font-style:italic;">Academic Transcript</p>
        
        <div style="text-align: right; font-size: 12px; margin-top: 20px;">
            <p>Nomor / Number : {{ $dokumen->no_transkrip ?? '-' }}</p>
            <p>Nomor Ijazah / Certificate Serial Number : {{ $dokumen->no_ijazah ?? '-' }}</p>
        </div>
    </div>

    <table class="biodata-table">
        <tr>
            <td class="label-col">Nama / Name</td>
            <td>:</td>
            <td class="val-col">{{ strtoupper($dokumen->mahasiswa->nama) }}</td>
        </tr>
        <tr>
            <td class="label-col">Tempat, Tanggal Lahir /<br><span style="font-style:italic;font-weight:normal">Place, Date of Birth</span></td>
            <td>:</td>
            <td class="val-col">{{ strtoupper($dokumen->mahasiswa->tempat_lahir ?? '-') }}, {{ \Carbon\Carbon::parse($dokumen->mahasiswa->tanggal_lahir)->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td class="label-col">Nomor Induk Mahasiswa / NIM PDDIKTI<br><span style="font-style:italic;font-weight:normal">Student Registration Number</span></td>
            <td>:</td>
            <td class="val-col">{{ $dokumen->mahasiswa->nim }} / {{ $dokumen->pin_dikti ?? '-' }}</td>
        </tr>
        <tr><td colspan="3">&nbsp;</td></tr>
        <tr>
            <td class="label-col">Program Studi / Study Program</td>
            <td>:</td>
            <td class="val-col">Farmasi</td>
        </tr>
        <tr>
            <td class="label-col">Jenjang Pendidikan / Level of Education</td>
            <td>:</td>
            <td class="val-col">{{ $dokumen->mahasiswa->id_program_studi == 1 ? 'Diploma III' : 'Strata I' }}</td>
        </tr>
        <tr><td colspan="3">&nbsp;</td></tr>
        <tr>
            <td class="label-col">Tanggal Masuk / Year of Entry</td>
            <td>:</td>
            <td class="val-col">{{ \Carbon\Carbon::parse($dokumen->mahasiswa->angkatan . '-09-01')->format('d F Y') }}</td>
        </tr>
        <tr>
            <td class="label-col">Tanggal Lulus / Date of Graduate</td>
            <td>:</td>
            <td class="val-col">{{ \Carbon\Carbon::parse($dokumen->periode->tanggal_wisuda)->format('d F Y') }}</td>
        </tr>
        <tr><td colspan="3">&nbsp;</td></tr>
        <tr>
            <td class="label-col">Indeks Prestasi Kumulatif (IPK) / <br><span style="font-style:italic;font-weight:normal">Grade Point Average (GPA)</span></td>
            <td>:</td>
            <td class="val-col">{{ $ipk }}</td>
        </tr>
        <tr>
            <td class="label-col">Jumlah SKS / Total of Credit</td>
            <td>:</td>
            <td class="val-col">{{ $totalSKS }}</td>
        </tr>
        <tr>
            <td class="label-col">Jumlah (SKS x Bobot) / Total of (Credit x Weight)</td>
            <td>:</td>
            <td class="val-col">-</td>
        </tr>
        <tr>
            <td class="label-col">Kategori Kelulusan / Passing Category</td>
            <td>:</td>
            <td class="val-col text-uppercase">/ {{ strtoupper($dokumen->kategori_kelulusan) }}</td>
        </tr>
    </table>

    <div class="title-box" style="margin-top: 40px;">
        <table style="width: 100%; border:0">
            <tr>
                <td style="width: 100px; vertical-align:top; font-weight:bold;">Judul Tugas Akhir</td>
                <td style="width: 10px; vertical-align:top;">:</td>
                <td style="vertical-align:top; font-weight:bold; font-size:14px; text-transform:uppercase;">
                    <!-- Simulasi judul tugas akhir diambil dari yudisium berkas atau static (krn tidak ada field) -->
                    {{ 'HUBUNGAN DUKUNGAN KELUARGA DENGAN KEPATUHAN KONSUMSI OBAT PASIEN' }}
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
