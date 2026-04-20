<?php

namespace App\Http\Controllers;

use App\Models\JamKerjaDetail;
use App\Models\JamKerjaMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PegawaiJamKerjaDetailController extends Controller
{
    public function edit()
    {
        $pegawai = Auth::guard('pegawai')->user();
        if (!$pegawai) {
            abort(403);
        }

        $master = $this->getActiveMaster();
        $detail = null;

        if ($master) {
            $detail = JamKerjaDetail::where('id_jam_kerja', (int) $master->id)
                ->where('id_pegawai', (int) $pegawai->id)
                ->first();
        }

        return view('pegawai.jam_kerja_detail.edit', [
            'title' => 'Input Jam Kerja Dosen',
            'CurrentPage' => 'content',
            'pegawai' => $pegawai,
            'master' => $master,
            'd' => $detail,
        ]);
    }

    public function save(Request $request)
    {
        $pegawai = Auth::guard('pegawai')->user();
        if (!$pegawai) {
            abort(403);
        }

        $master = $this->getActiveMaster();
        if (!$master) {
            return redirect('pegawai/absensi/tambah_jam_kerja_detail')
                ->with('error', 'Jam kerja belum di set oleh admin.');
        }

        $validated = $this->validateRequest($request);

        $existing = JamKerjaDetail::where('id_jam_kerja', (int) $master->id)
            ->where('id_pegawai', (int) $pegawai->id)
            ->first();

        $payload = $this->buildPayload($validated, $pegawai, (int) $master->id);

        if ($existing) {
            $existing->update($payload);
            $message = 'Jam kerja dosen berhasil diperbarui.';
        } else {
            JamKerjaDetail::create($payload);
            $message = 'Jam kerja dosen berhasil disimpan.';
        }

        return redirect('pegawai/absensi/tambah_jam_kerja_detail')->with('status', $message);
    }

    private function getActiveMaster(): ?JamKerjaMaster
    {
        $today = Carbon::today()->toDateString();

        return JamKerjaMaster::query()
            ->where('status', 1)
            ->whereDate('mulai', '<=', $today)
            ->whereDate('selesai', '>=', $today)
            ->orderBy('mulai', 'desc')
            ->orderBy('id', 'desc')
            ->first();
    }

    private function validateRequest(Request $request): array
    {
        $rules = [
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

        $validator->after(function ($validator) use ($request) {
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
        });

        return $validator->validate();
    }

    private function buildPayload(array $validated, $pegawai, int $idJamKerja): array
    {
        $days = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'];
        $payload = [
            'id_jam_kerja' => $idJamKerja,
            'id_pegawai' => (int) $pegawai->id,
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
