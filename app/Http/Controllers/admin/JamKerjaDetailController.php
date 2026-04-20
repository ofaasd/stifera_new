<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\JamKerjaDetail;
use App\Models\JamKerjaMaster;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JamKerjaDetailController extends Controller
{
    public function index(string $id_jam_kerja)
    {
        $master = JamKerjaMaster::find($id_jam_kerja);
        if (!$master) {
            return redirect('simpeg/absensi/jam_kerja_master')->with('error', 'Data jam kerja master tidak ditemukan.');
        }

        $data['title'] = 'Detail Jam Kerja Dosen';
        $data['CurrentPage'] = 'content';
        $data['master'] = $master;
        $data['detailList'] = JamKerjaDetail::where('id_jam_kerja', (int) $id_jam_kerja)
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.jam_kerja_detail.index', $data);
    }

    public function create(string $id_jam_kerja)
    {
        $master = JamKerjaMaster::find($id_jam_kerja);
        if (!$master) {
            return redirect('simpeg/absensi/jam_kerja_master')->with('error', 'Data jam kerja master tidak ditemukan.');
        }

        $data['title'] = 'Tambah Detail Jam Kerja Dosen';
        $data['CurrentPage'] = 'content';
        $data['master'] = $master;
        $data['pegawaiList'] = Pegawai::orderBy('nama')->get(['id', 'npp', 'nama', 'usrnm']);

        return view('admin.jam_kerja_detail.create', $data);
    }

    public function store(Request $request, string $id_jam_kerja)
    {
        $master = JamKerjaMaster::find($id_jam_kerja);
        if (!$master) {
            return redirect('simpeg/absensi/jam_kerja_master')->with('error', 'Data jam kerja master tidak ditemukan.');
        }

        $validated = $this->validateRequest($request, (int) $id_jam_kerja);
        $pegawai = Pegawai::find((int) $validated['id_pegawai']);

        JamKerjaDetail::create($this->buildPayload($validated, $pegawai, (int) $id_jam_kerja));

        return redirect('simpeg/absensi/jam_kerja_master/' . $id_jam_kerja . '/detail')->with('status', 'Detail jam kerja dosen berhasil ditambahkan.');
    }

    public function edit(string $id_jam_kerja, string $id)
    {
        $master = JamKerjaMaster::find($id_jam_kerja);
        if (!$master) {
            return redirect('simpeg/absensi/jam_kerja_master')->with('error', 'Data jam kerja master tidak ditemukan.');
        }

        $detail = JamKerjaDetail::where('id_jam_kerja', (int) $id_jam_kerja)->where('id', (int) $id)->first();
        if (!$detail) {
            return redirect('simpeg/absensi/jam_kerja_master/' . $id_jam_kerja . '/detail')->with('error', 'Detail jam kerja dosen tidak ditemukan.');
        }

        $data['title'] = 'Edit Detail Jam Kerja Dosen';
        $data['CurrentPage'] = 'content';
        $data['master'] = $master;
        $data['d'] = $detail;
        $data['pegawaiList'] = Pegawai::orderBy('nama')->get(['id', 'npp', 'nama', 'usrnm']);

        return view('admin.jam_kerja_detail.edit', $data);
    }

    public function update(Request $request, string $id_jam_kerja, string $id)
    {
        $master = JamKerjaMaster::find($id_jam_kerja);
        if (!$master) {
            return redirect('simpeg/absensi/jam_kerja_master')->with('error', 'Data jam kerja master tidak ditemukan.');
        }

        $detail = JamKerjaDetail::where('id_jam_kerja', (int) $id_jam_kerja)->where('id', (int) $id)->first();
        if (!$detail) {
            return redirect('simpeg/absensi/jam_kerja_master/' . $id_jam_kerja . '/detail')->with('error', 'Detail jam kerja dosen tidak ditemukan.');
        }

        $validated = $this->validateRequest($request, (int) $id_jam_kerja, (int) $id);
        $pegawai = Pegawai::find((int) $validated['id_pegawai']);

        $detail->update($this->buildPayload($validated, $pegawai, (int) $id_jam_kerja));

        return redirect('simpeg/absensi/jam_kerja_master/' . $id_jam_kerja . '/detail')->with('status', 'Detail jam kerja dosen berhasil diperbarui.');
    }

    public function destroy(string $id_jam_kerja, string $id)
    {
        $detail = JamKerjaDetail::where('id_jam_kerja', (int) $id_jam_kerja)->where('id', (int) $id)->first();
        if (!$detail) {
            return redirect('simpeg/absensi/jam_kerja_master/' . $id_jam_kerja . '/detail')->with('error', 'Detail jam kerja dosen tidak ditemukan.');
        }

        $detail->delete();

        return redirect('simpeg/absensi/jam_kerja_master/' . $id_jam_kerja . '/detail')->with('status', 'Detail jam kerja dosen berhasil dihapus.');
    }

    private function validateRequest(Request $request, int $idJamKerja, ?int $detailId = null): array
    {
        $rules = [
            'id_pegawai' => 'required|integer|exists:pegawai,id',
            'status' => 'required|in:0,1',
            'jam_senin_mulai' => 'nullable|date_format:H:i',
            'jam_senin_selesai' => 'nullable|date_format:H:i',
            'jam_selasa_mulai' => 'nullable|date_format:H:i',
            'jam_selasa_selesai' => 'nullable|date_format:H:i',
            'jam_rabu_mulai' => 'nullable|date_format:H:i',
            'jam_rabu_selesai' => 'nullable|date_format:H:i',
            'jam_kamis_mulai' => 'nullable|date_format:H:i',
            'jam_kamis_selesai' => 'nullable|date_format:H:i',
            'jam_jumat_mulai' => 'nullable|date_format:H:i',
            'jam_jumat_selesai' => 'nullable|date_format:H:i',
            'jam_sabtu_mulai' => 'nullable|date_format:H:i',
            'jam_sabtu_selesai' => 'nullable|date_format:H:i',
            'jam_minggu_mulai' => 'required|date_format:H:i',
            'jam_minggu_selesai' => 'required|date_format:H:i',
        ];

        $validator = Validator::make($request->all(), $rules);

        $validator->after(function ($validator) use ($request, $idJamKerja, $detailId) {
            $days = [
                'senin' => 'Senin',
                'selasa' => 'Selasa',
                'rabu' => 'Rabu',
                'kamis' => 'Kamis',
                'jumat' => 'Jumat',
                'sabtu' => 'Sabtu',
                'minggu' => 'Minggu',
            ];

            foreach ($days as $dayKey => $dayLabel) {
                $mulai = $request->input('jam_' . $dayKey . '_mulai');
                $selesai = $request->input('jam_' . $dayKey . '_selesai');

                if (($mulai && !$selesai) || (!$mulai && $selesai)) {
                    $validator->errors()->add('jam_' . $dayKey . '_selesai', 'Jam ' . $dayLabel . ' harus diisi lengkap (mulai dan selesai).');
                    continue;
                }

                if (!empty($mulai) && !empty($selesai) && strtotime($selesai) <= strtotime($mulai)) {
                    $validator->errors()->add('jam_' . $dayKey . '_selesai', 'Jam selesai ' . $dayLabel . ' harus lebih besar dari jam mulai.');
                }
            }

            if ($request->filled('id_pegawai')) {
                $query = JamKerjaDetail::where('id_jam_kerja', $idJamKerja)
                    ->where('id_pegawai', (int) $request->input('id_pegawai'));

                if ($detailId !== null) {
                    $query->where('id', '!=', $detailId);
                }

                if ($query->exists()) {
                    $validator->errors()->add('id_pegawai', 'Pegawai ini sudah memiliki detail jam kerja pada master yang sama.');
                }
            }
        });

        return $validator->validate();
    }

    private function buildPayload(array $validated, Pegawai $pegawai, int $idJamKerja): array
    {
        $days = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'];
        $payload = [
            'id_jam_kerja' => $idJamKerja,
            'id_pegawai' => (int) $validated['id_pegawai'],
            'email' => (string) ($pegawai->usrnm ?? '-'),
            'nama' => (string) ($pegawai->nama ?? ('Pegawai #' . $pegawai->id)),
            'status' => (int) $validated['status'],
        ];

        foreach ($days as $day) {
            $start = $validated['jam_' . $day . '_mulai'] ?? null;
            $end = $validated['jam_' . $day . '_selesai'] ?? null;
            $isRequired = $day === 'minggu';

            $payload['jam_' . $day . '_mulai'] = $this->normalizeTime($start, $isRequired);
            $payload['jam_' . $day . '_selesai'] = $this->normalizeTime($end, $isRequired);
            $payload['jumlah_' . $day] = $this->calculateDuration($start, $end, $isRequired);
        }

        return $payload;
    }

    private function normalizeTime(?string $time, bool $required = false): ?string
    {
        if (empty($time)) {
            return $required ? '00:00:00' : null;
        }

        return $time . ':00';
    }

    private function calculateDuration(?string $start, ?string $end, bool $required = false): ?string
    {
        if (empty($start) || empty($end)) {
            return $required ? '00:00' : null;
        }

        $minutes = (int) ((strtotime($end) - strtotime($start)) / 60);
        if ($minutes < 0) {
            $minutes = 0;
        }

        return sprintf('%02d:%02d', intdiv($minutes, 60), $minutes % 60);
    }
}
