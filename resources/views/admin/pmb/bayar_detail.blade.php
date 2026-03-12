@extends('layouts.default', ['CurrentPage' => $CurrentPage])

@section('content')
		<div class="content-body">
			<div class="container">

				<!-- Row -->
				<div class="row">
					<div class="col-xl-12">
						<div class="mb-4 pb-3">
							<a href="{{ url('pmb/bayar')}}" class="btn btn-primary">Kembali</a>
						</div>
						<div class="filter cm-content-box box-primary">
							<div class="content-title SlideToolHeader">
								<div class="cpa">
									<i class="fa-solid fa-file-lines me-1"></i>{{ $title }}
								</div>
								<div class="tools">
									<a href="javascript:void(0);" class="expand handle"><i
											class="fal fa-angle-down"></i></a>
								</div>
							</div>
							<div class="cm-content-body form excerpt">
								<div class="card-body pb-4">
									@if(session('status_delete'))
                                        <div class="alert alert-success">
                                            {{ session('status_delete') }}
                                        </div>
                                    @endif
									@if(session('error'))
                                        <div class="alert alert-danger">
                                            {{ session('error') }}
                                        </div>
                                    @endif

                                    @php
                                        if ($cek > 0) {
                                            $tagihan_sebelum = $invoice->tagihan_sekarang;
                                        } else {
                                            $tagihan_sebelum = $data->jml_tahap_1 + $data->jml_tahap_2 + $data->jml_tahap_3;
                                        }
                                    @endphp

                                    <form action="{{ url('pmb/bayar') }}" method="POST">
                                        @csrf <table class="table table-borderless" style="width: 40%;">
                                            <tr>
                                                <td>Tagihan Sebelum</td>
                                                <td>:</td>
                                                <td>
                                                    Rp {{ number_format($tagihan_sebelum, 2, ',', '.') }}
                                                    <input type="hidden" name="id" value="{{ $id }}">
                                                    <input type="hidden" name="tagihan_sebelum" value="{{ $tagihan_sebelum }}">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Dibayar</td>
                                                <td>:</td>
                                                <td><input type="text" name="dibayar" class="form-control" required></td>
                                            </tr>
                                            <tr>
                                                <td>Tgl Pembayaran</td>
                                                <td>:</td>
                                                <td><input type="date" name="tgl_pembayaran" class="form-control" required></td>
                                            </tr>
                                            <tr>
                                                <td>Nomer Rekening Pengirim</td>
                                                <td>:</td>
                                                <td><input type="text" name="norek_pengirim" class="form-control" required></td>
                                            </tr>
                                            <tr>
                                                <td>Atas Nama Pengirim</td>
                                                <td>:</td>
                                                <td><input type="text" name="an_pengirim" class="form-control" required></td>
                                            </tr>
                                            <tr>
                                                <td>Media Pembayaran</td>
                                                <td>:</td>
                                                <td>
                                                    <select name="media_pembayaran" class="form-control" required>
                                                        <option selected disabled value="">-- Pilih Media Pembayaran --</option>
                                                        <option value="ATM">ATM</option>
                                                        <option value="BANK">BANK</option>
                                                        <option value="I-Banking">I-Banking</option>
                                                        <option value="M-Banking">M-Banking</option>
                                                        <option value="Lainnya">Lainnya</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Keterangan</td>
                                                <td>:</td>
                                                <td><input type="text" name="keterangan" class="form-control" placeholder="Isi jika Lainnya"></td>
                                            </tr>
                                            <tr>
                                                <td colspan="3">
                                                    <button type="submit" class="btn btn-primary mt-2">Simpan</button>
                                                </td>
                                            </tr>
                                        </table>
                                    </form>

                                    <br>

                                    <h5>RIWAYAT PEMBAYARAN</h5>
                                    <hr>

                                    <div class="dt-responsive table-responsive">
                                        <table id="order-table" class="table table-striped table-bordered nowrap">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Tagihan Sebelum</th>
                                                    <th>Dibayar</th>
                                                    <th>Sisa Tagihan</th>
                                                    <th>Tanggal Pembayaran</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($riwayat as $a)
                                                    <tr>
                                                        {{-- Menggunakan variabel bawaan Blade $loop->iteration untuk nomor urut --}}
                                                        <td>{{ $loop->iteration }}</td>
                                                        
                                                        {{-- Format Rupiah --}}
                                                        <td>Rp {{ number_format($a->tagihan_sebelum, 2, ',', '.') }}</td>
                                                        <td>Rp {{ number_format($a->dibayar, 2, ',', '.') }}</td>
                                                        <td>Rp {{ number_format($a->tagihan_sekarang, 2, ',', '.') }}</td>
                                                        
                                                        {{-- Format Tanggal --}}
                                                        <td>{{ \Carbon\Carbon::parse($a->tgl_pembayaran)->format('d-M-Y') }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
@endsection