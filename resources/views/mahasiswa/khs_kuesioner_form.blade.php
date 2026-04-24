@extends('layouts.default', ['CurrentPage' => $CurrentPage])

@section('local-css')
<style>
    .kuesioner-card {
        height: auto !important;
    }
</style>
@endsection

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-12">
                @if(session('status'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="card kuesioner-card border-0 shadow-sm mb-4">
                    <div class="card-body d-flex flex-wrap justify-content-between align-items-start gap-3">
                        <div>
                            <h4 class="mb-1">Isi Kuesioner Perkuliahan</h4>
                            <p class="text-muted mb-2">Jawaban kuesioner digunakan untuk evaluasi pembelajaran.</p>
                            <div class="small text-muted">
                                <div>NIM: <span class="fw-semibold">{{ $mahasiswa->nim ?? '-' }}</span></div>
                                <div>Nama: <span class="fw-semibold">{{ $mahasiswa->nama ?? '-' }}</span></div>
                                <div>
                                    Tahun Ajaran Aktif:
                                    <span class="fw-semibold">
                                        @if($tahunAktif)
                                            {{ ($tahunAktif->awal ?? '-') . '/' . ($tahunAktif->akhir ?? '-') }}
                                            @if(!empty($jenisTA)) ({{ $jenisTA }}) @endif
                                        @else
                                            -
                                        @endif
                                    </span>
                                </div>
                            </div>
                            <div class="mt-2">
                                <span class="badge bg-primary">Progress: {{ $progressDone }} dari {{ $progressTotal }} kuesioner selesai</span>
                            </div>
                        </div>
                        <div>
                            <a href="{{ route('mahasiswa.khs.index') }}" class="btn btn-outline-secondary">
                                Kembali ke Daftar Kuesioner
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card kuesioner-card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="alert alert-info mb-0">
                            Mengisi untuk: <strong>{{ $selectedPair->label ?? '-' }}</strong>
                        </div>
                    </div>
                </div>

                <div class="card kuesioner-card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="mb-3">Daftar Soal</h5>

                        @if($soalList->isNotEmpty())
                            <form method="POST" action="{{ route('mahasiswa.khs.kuesioner.store') }}">
                                @csrf
                                <input type="hidden" name="id_jadwal" value="{{ (int) ($selectedPair->id_jadwal ?? 0) }}">
                                <input type="hidden" name="id_dosen" value="{{ (int) ($selectedPair->id_dosen ?? 0) }}">

                                @foreach($soalList as $idx => $soal)
                                    @php
                                        $selectedValue = old('jawaban.' . $soal->id, $selectedAnswers[(int) $soal->id] ?? null);
                                    @endphp
                                    <div class="mb-4 p-3 border rounded">
                                        <div class="fw-semibold mb-3">{{ $idx + 1 }}. {{ $soal->soal }}</div>
                                        <div class="d-flex flex-wrap gap-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="jawaban[{{ $soal->id }}]" id="jawaban-{{ $soal->id }}-1" value="1" {{ (int) $selectedValue === 1 ? 'checked' : '' }} required>
                                                <label class="form-check-label" for="jawaban-{{ $soal->id }}-1">1 - Sangat Tidak Setuju</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="jawaban[{{ $soal->id }}]" id="jawaban-{{ $soal->id }}-2" value="2" {{ (int) $selectedValue === 2 ? 'checked' : '' }} required>
                                                <label class="form-check-label" for="jawaban-{{ $soal->id }}-2">2 - Tidak Setuju</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="jawaban[{{ $soal->id }}]" id="jawaban-{{ $soal->id }}-3" value="3" {{ (int) $selectedValue === 3 ? 'checked' : '' }} required>
                                                <label class="form-check-label" for="jawaban-{{ $soal->id }}-3">3 - Setuju</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="jawaban[{{ $soal->id }}]" id="jawaban-{{ $soal->id }}-4" value="4" {{ (int) $selectedValue === 4 ? 'checked' : '' }} required>
                                                <label class="form-check-label" for="jawaban-{{ $soal->id }}-4">4 - Sangat Setuju</label>
                                            </div>
                                        </div>
                                        @error('jawaban.' . $soal->id)
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @endforeach

                                <button type="submit" class="btn btn-success">Simpan Kuesioner</button>
                            </form>
                        @else
                            <div class="alert alert-warning mb-0">
                                Data soal kuesioner belum tersedia untuk tahun ajaran ini.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
