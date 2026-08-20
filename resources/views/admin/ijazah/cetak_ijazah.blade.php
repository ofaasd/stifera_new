<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Ijazah - {{ $dokumen->mahasiswa->nama }}</title>
    <style>
        @page { size: A4 landscape; margin: 20mm; }
        body {
            font-family: 'Times New Roman', Tahoma, serif;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 0;
            text-align: center;
        }
        .container {
            border: 5px solid double #000;
            padding: 40px;
            height: calc(100% - 80px);
            position: relative;
        }
        .header { margin-bottom: 30px; }
        .header h1 { margin: 0; font-size: 32px; letter-spacing: 2px; }
        .header h2 { margin: 5px 0 0; font-size: 24px; }
        .no-ijazah { text-align: right; font-weight: bold; font-size: 14px; margin-bottom: 20px;}
        .content { font-size: 18px; line-height: 1.6; text-align: justify; text-align-last: center; margin: 0 40px;}
        .student-name { font-size: 28px; font-weight: bold; margin: 20px 0; letter-spacing: 1px; text-transform: uppercase;}
        .student-details { font-size: 18px; margin-bottom: 20px; }
        .footer { margin-top: 60px; display: flex; justify-content: space-between; }
        .signature-block { width: 40%; text-align: center; }
        .signature-block p { margin: 0; font-size: 16px; }
        .signature-space { height: 100px; }
        .signature-name { font-weight: bold; text-decoration: underline; }
        
        @media print {
            body { font-size: 14pt; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="container">
        <div class="no-ijazah">
            No. Ijazah: {{ $dokumen->no_ijazah ?? '........................' }} <br>
            PIN: {{ $dokumen->pin_dikti ?? '........................' }}
        </div>
        
        <div class="header">
            <h1>SEKOLAH TINGGI ILMU FARMASI (STIFERA)</h1>
            <h2>YAYASAN KHARISMA BINA MEDIKA SEMARANG</h2>
        </div>

        <div class="content">
            <p>Berdasarkan Keputusan Badan Akreditasi Nasional Perguruan Tinggi, dengan ini menerangkan bahwa:</p>
            <div class="student-name">{{ $dokumen->mahasiswa->nama }}</div>
            <div class="student-details">
                NIM / NIRM : {{ $dokumen->mahasiswa->nim }}<br>
                Tempat, Tanggal Lahir : {{ $dokumen->mahasiswa->tempat_lahir ?? '.........' }}, {{ \Carbon\Carbon::parse($dokumen->mahasiswa->tanggal_lahir)->translatedFormat('d F Y') }}<br>
                Program Studi : {{ $dokumen->mahasiswa->programStudi->nama_jurusan ?? '-' }}
            </div>
            
            <p>telah menyelesaikan dengan baik dan memenuhi segala syarat pendidikan yang diwajibkan sesuai dengan kurikulum yang berlaku, sehingga kepadanya diberikan Ijazah beserta segala hak dan kewajiban yang melekat pada gelar tersebut.</p>
        </div>

        <div class="footer">
            <div class="signature-block">
                <p>Semarang, {{ \Carbon\Carbon::parse($dokumen->tanggal_terbit ?? $dokumen->periode->tanggal_wisuda)->translatedFormat('d F Y') }}</p>
                <p>Pembantu Ketua Bidang Akademik,</p>
                <div class="signature-space"></div>
                <p class="signature-name">{{ $dokumen->periode->nama_puket_1 ?? '..........................' }}</p>
                <p>NIP. {{ $dokumen->periode->nip_puket_1 ?? '....................' }}</p>
            </div>
            
            <div class="signature-block">
                <p>&nbsp;</p>
                <p>Ketua,</p>
                <div class="signature-space"></div>
                <p class="signature-name">{{ $dokumen->periode->nama_ketua ?? '..........................' }}</p>
                <p>NIP. {{ $dokumen->periode->nip_ketua ?? '....................' }}</p>
            </div>
        </div>
    </div>
</body>
</html>
