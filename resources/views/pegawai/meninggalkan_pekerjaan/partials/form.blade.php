<div class="row">
    <div class="col-md-4 mb-3">
        <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
        <input type="date" class="form-control" name="tanggal" value="{{ old('tanggal', isset($d) && $d?->tanggal ? \Carbon\Carbon::parse($d->tanggal)->format('Y-m-d') : '') }}" required>
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label">Waktu Mulai <span class="text-danger">*</span></label>
        <input type="time" class="form-control" name="waktu_mulai" value="{{ old('waktu_mulai', isset($d) && $d?->waktu_mulai ? \Carbon\Carbon::parse($d->waktu_mulai)->format('H:i') : '') }}" required>
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label">Kategori</label>
        <select class="form-control" name="id_kategori">
            <option value="">Pilih Kategori</option>
            @foreach($kategoriList as $kategori)
                <option value="{{ $kategori->id }}" {{ (string) old('id_kategori', $d->id_kategori ?? '') === (string) $kategori->id ? 'selected' : '' }}>
                    {{ $kategori->nama }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-4 mb-3">
        <label class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
        <input type="date" class="form-control" name="tanggal_selesai" value="{{ old('tanggal_selesai', isset($d) && $d?->tanggal_selesai ? \Carbon\Carbon::parse($d->tanggal_selesai)->format('Y-m-d') : '') }}" required>
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label">Waktu Selesai <span class="text-danger">*</span></label>
        <input type="time" class="form-control" name="waktu_selesai" value="{{ old('waktu_selesai', isset($d) && $d?->waktu_selesai ? \Carbon\Carbon::parse($d->waktu_selesai)->format('H:i') : '') }}" required>
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label">Lampiran {{ isset($d) ? '' : '*' }}</label>
        <input type="file" class="form-control" name="lampiran" {{ isset($d) ? '' : 'required' }}>
        <small class="text-muted">Format: PDF/JPG/JPEG/PNG/DOC/DOCX (maks 4MB).</small>
        @if(isset($d) && !empty($d->lampiran))
            <div class="mt-2">
                <a href="{{ asset('assets/izin_meninggalkan_pekerjaan/' . $d->lampiran) }}" target="_blank" class="btn btn-info btn-sm" title="Lihat Lampiran">
                    <i class="fa fa-eye"></i> Lihat Lampiran Saat Ini
                </a>
            </div>
        @endif
    </div>

    <div class="col-md-12 mb-3">
        <label class="form-label">Keperluan <span class="text-danger">*</span></label>
        <textarea class="form-control" name="keperluan" rows="4" required>{{ old('keperluan', $d->keperluan ?? '') }}</textarea>
    </div>
</div>
