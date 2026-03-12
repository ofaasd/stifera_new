<style>
    .table {
        border-collapse: collapse;
    }
    .table, .td, .th {
        border: 1px solid black;
    }
    .table, .td {
        padding-left: 10px;
    }
    .text-center {
        text-align: center;
    }
</style>

@php 
    $tgl_1 = date('Y') + 1; 
@endphp

@foreach($cetak as $c)
    @php
        $nilai_pmdp = $c->peringkat_pmdp;
        // Asumsi data prodi (seluruh jurusan) sudah dilempar dari controller ke view
        // Jika belum, Anda perlu menambahkannya di controller: 
        // $pmb['daftar_prodi'] = DB::table('program_studi')->get();
        $prodi1 = collect($daftar_prodi ?? [])->firstWhere('id', $c->pilihan1)?->nama_jurusan ?? '-';
        $prodi2 = collect($daftar_prodi ?? [])->firstWhere('id', $c->pilihan2)?->nama_jurusan ?? '-';
    @endphp
    
    <table width="100%">
        <tr>
            <td width="60px" style="padding-bottom: 0px; padding-right: 0px;">
                <img src="{{ public_path('assets/images/logo/logo.png') }}" style="width: 90px; height: 90px;">
            </td>
            <td colspan="5" style="padding-bottom: 0px; padding-left: 0px;" class="text-center">
                <h5>
                    <font style="font-size: 14px;">PANITIA PENERIMAAN MAHASISWA BARU</font><br>
                    <font style="font-size: 16px;">SEKOLAH TINGGI ILMU FARMASI NUSAPUTERA SEMARANG</font><br>
                    <font style="font-size: 14px;">TAHUN AKADEMIK {{ date('Y') }} - {{ $tgl_1 }}</font><br>
                    <font style="font-size: 12px;">JL. MEDOHO III NO. 2 SEMARANG, TELP/FAX (024)6747012</font>
                </h5>
            </td>
        </tr>
        <tr>
            <td colspan="6" style="padding-top: 0px;">
                <img src="{{ public_path('assets/images/line.jpg') }}">
            </td>
        </tr>
        <tr>
            <td colspan="6">
                <p>
                    <font style="font-size: 14px;">
                        <div style="text-align: right;">Semarang, {{ date('d-m-Y') }}</div>
                        <br>
                        No&emsp;&emsp;&emsp;&emsp;&nbsp;&nbsp;: 001/S-D/P-PMB/AKFAR-IX/{{ $c->nopen }}<br>
                        Hal&emsp;&emsp;&emsp;&emsp; : Pemberitahuan Hasil Seleksi Uji Tulis dan Kebijakan Keuangan<br>
                        Lampiran&emsp;&nbsp;: - <br><br>
                        Kepada Yth.<br>
                        Sdr.i {{ $c->nama }}<br>
                        Di Tempat<br><br>
                        &emsp;&emsp;Dengan Hormat,<br>
                        
                        @foreach($get_gelombang as $gel)
                            @php
                                if($gel->nama_gel != "PMDP"){ 
                                    $gelombang = explode(' ', $gel->nama_gel_long);
                                    $nama_gel_tampil = $gelombang[1] ?? '';
                                } else {
                                    $nama_gel_tampil = $gel->nama_gel_long;
                                }
                            @endphp
                            
                            Berdasarkan hasil Seleksi Penerimaan Mahasiswa Baru Sekolah Tinggi Ilmu Farmasi Nusaputera Semarang Gelombang {{ $nama_gel_tampil }} Tahun Akademik {{ date('Y') }} - {{ $tgl_1 }}<br>
                            
                            Nama&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&nbsp;&nbsp;&nbsp; : {{ $c->nama }}<br>
                            No. Pendaftaran&emsp;&emsp;&nbsp;&nbsp;&nbsp;: {{ $c->nopen }}<br>
                            Program Studi&emsp;&emsp;&emsp;&nbsp;&nbsp;&nbsp;: {{ $prodi1 }} / {{ $prodi2 }}<br>
                            
                            Jalur/Kelas&emsp;&emsp;&emsp;&emsp;&emsp;&nbsp;&nbsp;: 
                            {!! $gel->nama_gel != "PMDP" ? "Umum/<strike>Khusus</strike>**)" : "<strike>Umum/</strike>Khusus **)" !!}
                            {!! $c->kelas == 1 ? "Reguler/<strike>Karyawan</strike>" : "<strike>Reguler/</strike>Karyawan **)" !!}<br>
                            
                            Nilai Tes Masuk&emsp;&emsp;&emsp;: 
                            @php
                                $nilai_tampil = '';
                                if($c->jalur_pendaftaran != 1 && $c->jalur_pendaftaran != 2) {
                                    // Asumsi $get_nilai dilempar dari controller
                                    $nilai_tampil = $get_nilai->first()?->total_score ?? '';
                                } else {
                                    $nilai_tampil = $nilai_pmdp;
                                }
                            @endphp
                            {{ $nilai_tampil }} (dalam skala 100)<br><br>
                        @endforeach
                    </font>
                    
                    <font style="font-size: 18px">
                        &emsp;&emsp;<b>{!! $statusCalon == 'DITERIMA' ? 'DITERIMA / <strike>TIDAK DITERIMA</strike>' : '<strike>DITERIMA</strike> / TIDAK DITERIMA' !!}</b><br>
                        &emsp;&emsp;SEBAGAI MAHASISWA SEKOLAH TINGGI ILMU FARMASI NUSAPUTERA
                    </font><br><br>
                    
                    <font style="font-size: 14px;">
                        Berkaitan dengan hal tersebut, mahasiswa yang bersangkutan diberikan ketentuan sebagai berikut :<br>
                        &emsp; •&emsp;<b>Melaksanakan Pembayaran I sampai dengan tanggal {{ date('d-m-Y', strtotime($tgl_tahap_1)) }}</b><br>
                        &emsp; •&emsp;<b>Besar SPI yang harus dibayarkan adalah RP {{ number_format($spi, 2, ',', '.') }}</b><br>
                        &emsp;&emsp;&emsp;Dengan rincian pembayaran sebagai berikut : <br>
                    </font>
                </p>
            </td>
        </tr>
    </table>
@endforeach

<table width="100%" class="table">
    <tr>
        <th class="th">No</th>
        <th class="th">Uraian Pembayaran</th>
        <th class="th">Nominal</th>
    </tr>
    <tr>
        <td class="td">&emsp;&nbsp;&nbsp;&nbsp;1</td>
        <td class="td">SPI</td>
        <td class="td">Rp. {!! strlen($spi-$potongan_spi) > 6 ? " " : "&emsp;&nbsp;" !!}{{ number_format($spi - $potongan_spi, 2, ',', '.') }}</td>
    </tr>
    <tr>
        <td class="td">&emsp;&nbsp;&nbsp;&nbsp;2</td>
        <td class="td">SKS</td>
        <td class="td">Rp. {!! strlen($sks) > 6 ? " " : "&emsp;&nbsp;" !!}{{ number_format($sks, 2, ',', '.') }}</td>
    </tr>
    <tr>
        <td class="td">&emsp;&nbsp;&nbsp;&nbsp;3</td>
        <td class="td">Operasional</td>
        <td class="td">Rp. {{ number_format($operasional, 2, ',', '.') }}</td>
    </tr>
    <tr>
        <td class="td">&emsp;&nbsp;&nbsp;&nbsp;4</td>
        <td class="td">Kemahasiswaan</td>
        <td class="td">Rp.&emsp;&nbsp;{{ number_format($kemahasiswaan, 2, ',', '.') }}</td>
    </tr>
    <tr>
        <td class="td">&emsp;&nbsp;&nbsp;&nbsp;5</td>
        <td class="td">Seragam dan Alat Praktikum</td>
        <td class="td">Rp. {{ number_format($seragam, 2, ',', '.') }}</td>
    </tr>
    <tr>
        <td class="td"></td>
        <td class="td"><b>Total Pembayaran</b></td>
        <td class="td"><b>Rp. {{ number_format(($spi - $potongan_spi + $sks + $operasional + $kemahasiswaan + $seragam), 2, ',', '.') }}</b></td>
    </tr>
</table>

<p style="text-align: justify; font-size: 14px;">
    Demikian &nbsp;pemberitahuan kami, &nbsp;untuk informasi lebih lanjut dapat &nbsp;menghubungi &nbsp;Sekretariat PMB Sekolah Tinggi Ilmu Farmasi Nusaputera, Jl. Medoho III No.2 Semarang, Telp. (024)6747012 atau untuk konfirmasi pembayaran transfer kepada Ketua Biro Administrasi Keuangan (BAK) Rima Oktaliani 085875650995. <br><br>
    Atas perhatiannya diucapkan terimakasih.<br><br>
</p>

<table width="100%" style="border: none;">
    <tr>
        <td width="60%" style="border: none;"></td>
        <td width="40%" style="border: none; text-align: center;">
            <font style="font-size: 14px;">
                Hormat Kami,<br>
                Ketua PMB<br><br><br><br>
                <u>{{ $ketuaPMB }}</u><br>
                NIP. 060707084
            </font>
        </td>
    </tr>
</table>

<br>
<font style="font-size: 8px;">
    <i>Dicetak {{ date('d-m-Y') }} oleh {{ session('nama_user') ?? 'Administrator' }}</i>
</font>