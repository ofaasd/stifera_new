<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Transkrip Belakang - {{ $dokumen->mahasiswa->nama }}</title>
    <style>
        @page { size: A4 portrait; margin: 15mm; }
        body { font-family: 'Times New Roman', Tahoma, serif; line-height: 1.1; font-size: 10px; }
        
        .title { text-align: center; font-weight: bold; font-size: 14px; margin-bottom: 5px; text-transform: uppercase;}
        
        .main-table { width: 100%; border-collapse: collapse; border: 1px solid #000; }
        .main-table th, .main-table td { border: 1px solid #000; }
        
        .split-td { vertical-align: top; padding: 0; width: 50%; }
        
        table.inner-table { width: 100%; border-collapse: collapse; }
        .inner-table th { background: #fff; padding: 5px; font-size: 10px; border-bottom: 1px solid #000; }
        .inner-table td { padding: 2px 4px; border: none; font-size: 9px; vertical-align: top;}
        .col-matkul { width: auto; font-weight: bold;}
        .col-en { font-weight: normal; font-style: italic;}
        .col-sks { width: 30px; text-align: center; border-left: 1px solid #000; }
        .col-nilai { width: 40px; text-align: center; border-left: 1px solid #000; }
        
        .category-header { font-weight: bold; font-size: 10px; text-transform: uppercase; padding: 4px; }
        .category-en { font-style: italic; font-weight: bold; font-size: 9px;}
        
        .footer-info { margin-top: 5px; font-size: 10px; border-bottom: 1px solid #000; padding-bottom: 5px;}
        
        .signature-area { margin-top: 10px; width: 100%; display: table; }
        .sig-block { display: table-cell; width: 50%; text-align: center; vertical-align: top;}
        .sig-space { height: 60px; }
        .sig-name { font-weight: bold; text-decoration: underline; }
    </style>
</head>
<body onload="window.print()">
    
    <div class="title">DAFTAR MATA KULIAH</div>

    <table class="main-table">
        <tr>
            <!-- Kolom Kiri -->
            <td class="split-td">
                <table class="inner-table">
                    <thead>
                        <tr>
                            <th colspan="2">Mata Kuliah<br><span style="font-style:italic; font-weight:normal;">Courses</span></th>
                            <th class="col-sks">SKS<br><span style="font-style:italic; font-weight:normal;">Credit</span></th>
                            <th class="col-nilai">Nilai<br><span style="font-style:italic; font-weight:normal;">Grade</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(['A', 'B', 'C'] as $catKey)
                            @if(isset($categories[$catKey]))
                                <tr>
                                    <td colspan="4" class="category-header">
                                        {{ $categories[$catKey]['name'] }}
                                    </td>
                                </tr>
                                @foreach($categories[$catKey]['items'] as $item)
                                <tr>
                                    <td style="width:50px;">{{ $item['kode'] }}</td>
                                    <td class="col-matkul">{{ $item['nama_mk'] }}<br><span class="col-en">{{ $item['nama_eng'] ?? $item['nama_mk'] }}</span></td>
                                    <td class="col-sks">{{ $item['sks'] }}</td>
                                    <td class="col-nilai">{{ $item['nilai_huruf'] }}</td>
                                </tr>
                                @endforeach
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </td>
            
            <!-- Kolom Kanan -->
            <td class="split-td" style="border-left: 1px solid #000;">
                <table class="inner-table">
                    <thead>
                        <tr>
                            <th colspan="2">Mata Kuliah<br><span style="font-style:italic; font-weight:normal;">Courses</span></th>
                            <th class="col-sks">SKS<br><span style="font-style:italic; font-weight:normal;">Credit</span></th>
                            <th class="col-nilai">Nilai<br><span style="font-style:italic; font-weight:normal;">Grade</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(['D', 'E'] as $catKey)
                            @if(isset($categories[$catKey]))
                                <tr>
                                    <td colspan="4" class="category-header">
                                        {{ $categories[$catKey]['name'] }}
                                    </td>
                                </tr>
                                @foreach($categories[$catKey]['items'] as $item)
                                <tr>
                                    <td style="width:50px;">{{ $item['kode'] }}</td>
                                    <td class="col-matkul">{{ $item['nama_mk'] }}<br><span class="col-en">{{ $item['nama_eng'] ?? $item['nama_mk'] }}</span></td>
                                    <td class="col-sks">{{ $item['sks'] }}</td>
                                    <td class="col-nilai">{{ $item['nilai_huruf'] }}</td>
                                </tr>
                                @endforeach
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </td>
        </tr>
    </table>
    
    <div class="footer-info">
        Keterangan / Remark:<br>
        Bobot / Weight : A=4; AB=3,5; B=3; BC=2,5; C=2; D=1; E=0<br>
        Kategori Kelulusan / Passing Categories:<br>
        IPK / GPA : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 2.76 - 3.50 &nbsp;&nbsp;&nbsp; Memuaskan / Satisfying<br>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 3.51 - 4.00 &nbsp;&nbsp;&nbsp; Sangat Memuaskan / Very Satisfying (Cumlaude)
        <br><br>
        <span style="font-style:italic;">Transkrip ini tidak berlaku jika ada koreksi dari siapapun.<br>This transcript is invalid if there are any correction whatsoever.</span>
    </div>

    <div class="signature-area">
        <div class="sig-block">
            <p style="margin-bottom: 2px;">Ketua</p>
            <p style="margin-top: 0;">Sekolah Tinggi Ilmu Farmasi Nusaputera</p>
            <div class="sig-space"></div>
            <p class="sig-name">{{ $ketua ?? 'apt. Rizky Arvian H., M.Farm' }}</p>
            <p>NIP. {{ $nip_ketua ?? '0741178' }}</p>
        </div>
        <div class="sig-block">
            <p style="margin-bottom: 2px;">Semarang, {{ \Carbon\Carbon::parse($dokumen->tanggal_terbit ?? $dokumen->periode->tanggal_wisuda)->translatedFormat('d F Y') }}</p>
            <p style="margin-top: 0;">Ketua Prodi {{ $dokumen->mahasiswa->id_program_studi == 1 ? 'D III Farmasi' : 'S 1 Farmasi' }}</p>
            <div class="sig-space"></div>
            <p class="sig-name">{{ $kaprodi ?? 'apt. Wahyu Setiyaningsih, M.Farm' }}</p>
            <p>NIP. {{ $nip_kaprodi ?? '0703093' }}</p>
        </div>
    </div>
</body>
</html>
