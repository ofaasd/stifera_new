<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Pengirim (Dosen) <span class="text-danger">*</span></label>
        <select class="form-control" name="id_dosen" required>
            <option value="">Pilih Pengirim</option>
            @foreach($pegawaiList as $pegawai)
                <option value="{{ $pegawai->id }}" {{ (string) old('id_dosen', $d->id_dosen ?? '') === (string) $pegawai->id ? 'selected' : '' }}>
                    {{ $pegawai->npp ?? '-' }} - {{ $pegawai->nama ?? '-' }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label">Tanggal Surat <span class="text-danger">*</span></label>
        <input type="date" class="form-control" name="tgl_surat" value="{{ old('tgl_surat', isset($d) && $d?->tgl_surat ? \Carbon\Carbon::parse($d->tgl_surat)->format('Y-m-d') : '') }}" required>
    </div>
    <div class="col-md-3 mb-3">
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

    <div class="col-md-6 mb-3">
        <label class="form-label">Perihal <span class="text-danger">*</span></label>
        <input type="text" class="form-control" name="perihal" value="{{ old('perihal', $d->perihal ?? '') }}" maxlength="120" required>
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">File Surat {{ isset($d) ? '' : '*' }}</label>
        <input type="file" class="form-control" name="file_surat" {{ isset($d) ? '' : 'required' }}>
        <small class="text-muted">Format: PDF/JPG/JPEG/PNG/DOC/DOCX (maks 4MB).</small>
        @if(isset($d) && !empty($d->file_surat))
            <div class="mt-2">
                <a href="{{ asset('assets/surat_izin/' . $d->file_surat) }}" target="_blank" class="btn btn-info btn-sm" title="Lihat File Surat">
                    <i class="fa fa-eye"></i> Lihat File Surat Saat Ini
                </a>
            </div>
        @endif
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Keterangan <span class="text-danger">*</span></label>
        <textarea class="form-control" name="keterangan" rows="4" required>{{ old('keterangan', $d->keterangan ?? '') }}</textarea>
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
        <input type="date" class="form-control" name="tanggal_mulai" value="{{ old('tanggal_mulai', isset($d) && $d?->tanggal_mulai ? \Carbon\Carbon::parse($d->tanggal_mulai)->format('Y-m-d') : '') }}" required>
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
        <input type="date" class="form-control" name="tanggal_selesai" value="{{ old('tanggal_selesai', isset($d) && $d?->tanggal_selesai ? \Carbon\Carbon::parse($d->tanggal_selesai)->format('Y-m-d') : '') }}" required>
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label">Manager SDM <span class="text-danger">*</span></label>
        <select class="form-control" name="izin_mgr_sdm" required>
            <option value="0" {{ old('izin_mgr_sdm', (string) ($d->izin_mgr_sdm ?? 0)) === '0' ? 'selected' : '' }}>Belum Disetujui</option>
            <option value="1" {{ old('izin_mgr_sdm', (string) ($d->izin_mgr_sdm ?? 0)) === '1' ? 'selected' : '' }}>Disetujui</option>
        </select>
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label">KA Jenjang <span class="text-danger">*</span></label>
        <select class="form-control" name="izin_ka_jenjang" required>
            <option value="0" {{ old('izin_ka_jenjang', (string) ($d->izin_ka_jenjang ?? 0)) === '0' ? 'selected' : '' }}>Belum Disetujui</option>
            <option value="1" {{ old('izin_ka_jenjang', (string) ($d->izin_ka_jenjang ?? 0)) === '1' ? 'selected' : '' }}>Disetujui</option>
        </select>
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label">Dilihat <span class="text-danger">*</span></label>
        <select class="form-control" name="dilihat" required>
            <option value="0" {{ old('dilihat', (string) ($d->dilihat ?? 0)) === '0' ? 'selected' : '' }}>Belum</option>
            <option value="1" {{ old('dilihat', (string) ($d->dilihat ?? 0)) === '1' ? 'selected' : '' }}>Sudah</option>
        </select>
    </div>
</div>
