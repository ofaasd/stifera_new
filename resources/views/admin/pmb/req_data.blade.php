@php
    // OPTIMASI: Tarik data bank sekali saja di luar looping agar tidak memberatkan database
    $rek = \Illuminate\Support\Facades\DB::table('master_rekening')->get();
@endphp
<table id="example" class="table table-striped">
    <thead>
        <tr>
            <th>No</th>
            <th>No. Pendaftaran</th>
            <th>NISN</th>
            <th>Nama Lengkap</th>
            <th>Jenis Pendaftaran</th>
            <th>
                <button onclick="location.href='{{ url('pmb/cetak_formulir_all/') }}';" class="btn btn-secondary">
                    <i class="fa fa-download" aria-hidden="true"></i> Semua
                </button>
            </th>
        </tr>
    </thead>
    <tbody>
        @php $no = 1; @endphp
        @foreach($data as $a)
            <tr>
                <td>{{ $no++ }}</td>
                <td>{{ $a->nopen }}</td>
                <td>{{ $a->nisn }}</td>
                <td>{{ $a->nama }}</td>
                <td>
                    @if($a->jenis_pendaftaran == 1)
                        Peserta Didik Baru
                    @elseif($a->jenis_pendaftaran == 2)
                        Pindahan
                    @elseif($a->jenis_pendaftaran == 11)
                        Alih Jenjang
                    @elseif($a->jenis_pendaftaran == 12)
                        Lintas Jalur
                    @endif
                </td>
                <td>
                    <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#myModal{{ $a->nopen }}">
                        <i class="fa-solid fa-money-bills"></i> Bayar Pendaftaran 
                    </button>
                    
                    &ensp;<a href="{{ url('pmb/cetak_formulir/' . $a->nopen) }}" class="btn btn-success btn-sm" title=""><i class="fa-solid fa-print"></i> Cetak Formulir</a>&ensp; <br>
                    <a style="margin-top: 5px;" href="{{ url('pmb/' . $a->id . '/edit') }}" class="btn btn-info btn-sm" title=""><i class="fa-solid fa-eye"></i> Detail</a>&ensp;
                    <a style="margin-top: 5px;" href="{{ url('pmb/registrasi/' . $a->id) }}" class="btn btn-success btn-sm" title=""><i class="fa-solid fa-address-card"></i> Registrasi</a>&ensp;
                    <a style="margin-top: 5px;" href="{{ url('pmb/delete_calon_mhs/' . $a->id) }}" onclick="return confirm('Yakin Delete Data Calon Mahasiswa?')" class="btn btn-danger btn-sm" title=""><i class="fa-solid fa-trash"></i> Hapus</a>
                </td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th>No</th>
            <th>No. Pendaftaran</th>
            <th>NISN</th>
            <th>Nama Lengkap</th>
            <th>Jenis Pendaftaran</th>
            <th></th>
        </tr>
    </tfoot>
</table>

@foreach($data as $a)
    <div class="modal fade" id="myModal{{ $a->nopen }}" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Verifikasi Pendaftaran</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ url('pmb/save_verifikasi') }}" method="post" enctype="multipart/form-data">
                        @csrf 
                        @php 
                            $qr = \Illuminate\Support\Facades\DB::table('bukti_registrasi')->where('nopen', $a->nopen)->first();
                            
                            $id_rekening = $qr->id_rekening ?? '';
                            $norek_pengirim = $qr->norek_pengirim ?? '';
                            $an_pengirim = $qr->an_pengirim ?? '';
                            $tgl_tf = $qr->tgl_tf ?? '';
                            $bukti = $qr->bukti ?? '';
                        @endphp
                        
                        <input type="hidden" name="nopen" value="{{ $a->nopen }}">
                        
                        <table class="table table-borderless">
                            <tr>
                                <td><label>Transfer Ke Rekening :</label></td>
                                <td>
                                    <select name="id_bank" class="form-control" style="width: 200px;">
                                        @foreach ($rek as $row)
                                            <option value="{{ $row->id }}" {{ $id_rekening == $row->id ? 'selected' : '' }}>
                                                {{ $row->nama_bank }}
                                            </option>   
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td><label>Nomer Rekening Pengirim : </label></td>
                                <td>
                                    <input type="text" class="form-control" name="norek_pengirim" style="width: 200px;" value="{{ $norek_pengirim }}">
                                </td>
                            </tr>
                            <tr>
                                <td><label>Atas Nama : </label></td>
                                <td>
                                    <input type="text" class="form-control" name="an_pengirim" style="width: 200px;" value="{{ $an_pengirim }}">
                                </td>
                            </tr>
                            <tr>
                                <td><label>Tanggal Transfer : </label></td>
                                <td>
                                    <input type="date" class="form-control" name="tgl_tf" style="width: 200px;" value="{{ $tgl_tf }}">
                                </td>
                            </tr>
                            <tr>
                                <td><label>Bukti Transfer : </label></td>
                                <td>
                                    <input type="file" class="form-control" name="bukti" style="width: 200px;"><br>
                                    <div class="alert alert-warning">Ekstensi yang diijinkan jpg,png,jpeg | max : 1 MB</div>
                                    @if ($bukti != '')
                                        <a href="{{ asset('assets/bukti/' . $bukti) }}" target="_blank" class="btn btn-primary">
                                            Lihat Bukti Bayar
                                        </a>
                                    @endif
                                </td>
                                <td></td>
                            </tr>   
                        </table>
                        
                        <div class="text-end mt-3 border-top pt-3">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-info text-white">
                                <i class="fa fa-save"></i> Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endforeach
<script type="text/javascript">
    $(document).ready(function() {
        $('#example').DataTable();
    });
</script>