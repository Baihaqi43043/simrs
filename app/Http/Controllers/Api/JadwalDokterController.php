<?php

// ============================================
// app/Http/Controllers/Api/JadwalDokterController.php
// ============================================

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\JadwalDokter;
use App\Dokter;
use App\Poli;
use Carbon\Carbon;

class JadwalDokterController extends Controller
{
    /**
     * Display a listing of jadwal dokters
     */
    public function index(Request $request)
    {
        $query = JadwalDokter::with(['dokter', 'poli']);

        // Filter by active status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // Filter by dokter
        if ($request->has('dokter_id')) {
            $query->where('dokter_id', $request->dokter_id);
        }

        // Filter by poli
        if ($request->has('poli_id')) {
            $query->where('poli_id', $request->poli_id);
        }

        // Filter by hari
        if ($request->has('hari')) {
            $query->where('hari', $request->hari);
        }

        // Search by dokter or poli name
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('dokter', function ($q) use ($search) {
                $q->where('nama_dokter', 'like', "%{$search}%");
            })->orWhereHas('poli', function ($q) use ($search) {
                $q->where('nama_poli', 'like', "%{$search}%");
            });
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $jadwals = $query->orderBy('hari')
                        ->orderBy('jam_mulai')
                        ->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Jadwal dokters retrieved successfully',
            'data' => $jadwals->items(),
            'meta' => [
                'current_page' => $jadwals->currentPage(),
                'last_page' => $jadwals->lastPage(),
                'per_page' => $jadwals->perPage(),
                'total' => $jadwals->total(),
            ]
        ]);
    }

    /**
     * Store a newly created jadwal dokter
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dokter_id' => 'required|exists:dokters,id',
            'poli_id' => 'required|exists:polis,id',
            'hari' => 'required|in:senin,selasa,rabu,kamis,jumat,sabtu,minggu',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'kuota_pasien' => 'required|integer|min:1|max:50',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 400);
        }

        // Check for time conflicts
        $conflict = JadwalDokter::where('dokter_id', $request->dokter_id)
            ->where('hari', $request->hari)
            ->where('is_active', true)
            ->where(function ($query) use ($request) {
                $query->whereBetween('jam_mulai', [$request->jam_mulai, $request->jam_selesai])
                    ->orWhereBetween('jam_selesai', [$request->jam_mulai, $request->jam_selesai])
                    ->orWhere(function ($q) use ($request) {
                        $q->where('jam_mulai', '<=', $request->jam_mulai)
                          ->where('jam_selesai', '>=', $request->jam_selesai);
                    });
            })
            ->exists();

        if ($conflict) {
            return response()->json([
                'success' => false,
                'message' => 'Schedule conflict detected. Doctor already has schedule in this time range.'
            ], 400);
        }

        $jadwal = JadwalDokter::create([
            'dokter_id' => $request->dokter_id,
            'poli_id' => $request->poli_id,
            'hari' => $request->hari,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'kuota_pasien' => $request->kuota_pasien,
            'is_active' => $request->get('is_active', true)
        ]);

        $jadwal->load(['dokter', 'poli']);

        return response()->json([
            'success' => true,
            'message' => 'Jadwal dokter created successfully',
            'data' => $jadwal
        ], 201);
    }

    /**
     * Display the specified jadwal dokter
     */
    public function show($id)
    {
        $jadwal = JadwalDokter::with(['dokter', 'poli'])->find($id);

        if (!$jadwal) {
            return response()->json([
                'success' => false,
                'message' => 'Jadwal dokter not found'
            ], 404);
        }

        // Add quota information for today if applicable
        $today = Carbon::today();
        $dayName = strtolower($today->format('l'));
        $dayMapping = [
            'monday' => 'senin',
            'tuesday' => 'selasa',
            'wednesday' => 'rabu',
            'thursday' => 'kamis',
            'friday' => 'jumat',
            'saturday' => 'sabtu',
            'sunday' => 'minggu'
        ];

        if ($jadwal->hari === $dayMapping[$dayName]) {
            $jadwal->kuota_terpakai_today = $jadwal->getKuotaTerpakai($today);
            $jadwal->kuota_tersisa_today = $jadwal->getKuotaTersisa($today);
        }

        return response()->json([
            'success' => true,
            'message' => 'Jadwal dokter retrieved successfully',
            'data' => $jadwal
        ]);
    }

    /**
     * Update the specified jadwal dokter
     */
    public function update(Request $request, $id)
    {
        $jadwal = JadwalDokter::find($id);

        if (!$jadwal) {
            return response()->json([
                'success' => false,
                'message' => 'Jadwal dokter not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'dokter_id' => 'required|exists:dokters,id',
            'poli_id' => 'required|exists:polis,id',
            'hari' => 'required|in:senin,selasa,rabu,kamis,jumat,sabtu,minggu',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'kuota_pasien' => 'required|integer|min:1|max:50',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 400);
        }

        // Check for time conflicts (excluding current schedule)
        $conflict = JadwalDokter::where('dokter_id', $request->dokter_id)
            ->where('hari', $request->hari)
            ->where('is_active', true)
            ->where('id', '!=', $id)
            ->where(function ($query) use ($request) {
                $query->whereBetween('jam_mulai', [$request->jam_mulai, $request->jam_selesai])
                    ->orWhereBetween('jam_selesai', [$request->jam_mulai, $request->jam_selesai])
                    ->orWhere(function ($q) use ($request) {
                        $q->where('jam_mulai', '<=', $request->jam_mulai)
                          ->where('jam_selesai', '>=', $request->jam_selesai);
                    });
            })
            ->exists();

        if ($conflict) {
            return response()->json([
                'success' => false,
                'message' => 'Schedule conflict detected. Doctor already has schedule in this time range.'
            ], 400);
        }

        $jadwal->update([
            'dokter_id' => $request->dokter_id,
            'poli_id' => $request->poli_id,
            'hari' => $request->hari,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'kuota_pasien' => $request->kuota_pasien,
            'is_active' => $request->get('is_active', $jadwal->is_active)
        ]);

        $jadwal->load(['dokter', 'poli']);

        return response()->json([
            'success' => true,
            'message' => 'Jadwal dokter updated successfully',
            'data' => $jadwal
        ]);
    }

    /**
     * Remove the specified jadwal dokter
     */
    public function destroy($id)
    {
        $jadwal = JadwalDokter::find($id);

        if (!$jadwal) {
            return response()->json([
                'success' => false,
                'message' => 'Jadwal dokter not found'
            ], 404);
        }

        // Check if jadwal has related kunjungan
        $hasKunjungan = $jadwal->kunjungans()->exists();

        if ($hasKunjungan) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete jadwal. It has related kunjungan data.'
            ], 400);
        }

        $jadwal->delete();

        return response()->json([
            'success' => true,
            'message' => 'Jadwal dokter deleted successfully'
        ]);
    }

    /**
     * Get available schedules for specific date
     */
    public function getAvailableSchedules($tanggal)
    {
        try {
            $date = Carbon::createFromFormat('Y-m-d', $tanggal);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid date format. Use Y-m-d format.'
            ], 400);
        }

        $dayName = strtolower($date->format('l'));
        $dayMapping = [
            'monday' => 'senin',
            'tuesday' => 'selasa',
            'wednesday' => 'rabu',
            'thursday' => 'kamis',
            'friday' => 'jumat',
            'saturday' => 'sabtu',
            'sunday' => 'minggu'
        ];

        $hari = $dayMapping[$dayName];

        $jadwals = JadwalDokter::with(['dokter', 'poli'])
            ->where('hari', $hari)
            ->where('is_active', true)
            ->orderBy('jam_mulai')
            ->get();

        // Add availability info for each schedule
        $jadwals->map(function ($jadwal) use ($date) {
            $jadwal->kuota_terpakai = $jadwal->getKuotaTerpakai($date);
            $jadwal->kuota_tersisa = $jadwal->getKuotaTersisa($date);
            $jadwal->is_available = $jadwal->isKuotaAvailable($date);
            $jadwal->tanggal = $date->format('Y-m-d');
            return $jadwal;
        });

        return response()->json([
            'success' => true,
            'message' => 'Available schedules retrieved successfully',
            'data' => [
                'tanggal' => $date->format('Y-m-d'),
                'hari' => $hari,
                'jadwals' => $jadwals
            ]
        ]);
    }

    /**
     * Get jadwal by dokter and hari
     */
    public function getByDokterHari($dokterId, $hari)
    {
        $dokter = Dokter::find($dokterId);
        if (!$dokter) {
            return response()->json([
                'success' => false,
                'message' => 'Dokter not found'
            ], 404);
        }

        $validHari = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'];
        if (!in_array($hari, $validHari)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid hari. Use: ' . implode(', ', $validHari)
            ], 400);
        }

        $jadwals = JadwalDokter::with(['poli'])
            ->where('dokter_id', $dokterId)
            ->where('hari', $hari)
            ->where('is_active', true)
            ->orderBy('jam_mulai')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Jadwal dokter retrieved successfully',
            'data' => [
                'dokter' => $dokter,
                'hari' => $hari,
                'jadwals' => $jadwals
            ]
        ]);
    }

    /**
     * Get weekly schedule for dokter
     */
    public function getWeeklySchedule($dokterId)
    {
        $dokter = Dokter::find($dokterId);
        if (!$dokter) {
            return response()->json([
                'success' => false,
                'message' => 'Dokter not found'
            ], 404);
        }

        $jadwals = JadwalDokter::with(['poli'])
            ->where('dokter_id', $dokterId)
            ->where('is_active', true)
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->get();

        // Group by hari
        $weeklySchedule = $jadwals->groupBy('hari');

        $orderedDays = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'];
        $orderedSchedule = [];

        foreach ($orderedDays as $day) {
            $orderedSchedule[$day] = $weeklySchedule->get($day, collect());
        }

        return response()->json([
            'success' => true,
            'message' => 'Weekly schedule retrieved successfully',
            'data' => [
                'dokter' => $dokter,
                'weekly_schedule' => $orderedSchedule
            ]
        ]);
    }

    /**
     * Check schedule availability for booking
     */
    public function checkAvailability(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'jadwal_dokter_id' => 'required|exists:jadwal_dokters,id',
            'tanggal' => 'required|date|after_or_equal:today'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 400);
        }

        $jadwal = JadwalDokter::with(['dokter', 'poli'])->find($request->jadwal_dokter_id);
        $tanggal = Carbon::parse($request->tanggal);

        // Verify that the requested date matches the schedule day
        $dayName = strtolower($tanggal->format('l'));
        $dayMapping = [
            'monday' => 'senin',
            'tuesday' => 'selasa',
            'wednesday' => 'rabu',
            'thursday' => 'kamis',
            'friday' => 'jumat',
            'saturday' => 'sabtu',
            'sunday' => 'minggu'
        ];

        if ($jadwal->hari !== $dayMapping[$dayName]) {
            return response()->json([
                'success' => false,
                'message' => 'Selected date does not match schedule day. Schedule is for ' . $jadwal->hari
            ], 400);
        }

        $kuotaTerpakai = $jadwal->getKuotaTerpakai($tanggal);
        $kuotaTersisa = $jadwal->getKuotaTersisa($tanggal);
        $isAvailable = $jadwal->isKuotaAvailable($tanggal);

        return response()->json([
            'success' => true,
            'message' => 'Schedule availability checked successfully',
            'data' => [
                'jadwal' => $jadwal,
                'tanggal' => $tanggal->format('Y-m-d'),
                'kuota_total' => $jadwal->kuota_pasien,
                'kuota_terpakai' => $kuotaTerpakai,
                'kuota_tersisa' => $kuotaTersisa,
                'is_available' => $isAvailable,
                'can_book' => $isAvailable && $jadwal->is_active
            ]
        ]);
    }

    /**
     * Get schedule statistics
     */
    public function getStatistics(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfWeek());
        $endDate = $request->get('end_date', Carbon::now()->endOfWeek());

        try {
            $startDate = Carbon::parse($startDate);
            $endDate = Carbon::parse($endDate);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid date format'
            ], 400);
        }

        $totalJadwal = JadwalDokter::where('is_active', true)->count();
        $totalDokter = JadwalDokter::where('is_active', true)->distinct('dokter_id')->count();
        $totalPoli = JadwalDokter::where('is_active', true)->distinct('poli_id')->count();

        // Most busy day
        $busyDay = JadwalDokter::selectRaw('hari, COUNT(*) as total')
            ->where('is_active', true)
            ->groupBy('hari')
            ->orderBy('total', 'desc')
            ->first();

        return response()->json([
            'success' => true,
            'message' => 'Schedule statistics retrieved successfully',
            'data' => [
                'period' => [
                    'start_date' => $startDate->format('Y-m-d'),
                    'end_date' => $endDate->format('Y-m-d')
                ],
                'total_jadwal_aktif' => $totalJadwal,
                'total_dokter_terjadwal' => $totalDokter,
                'total_poli_tersedia' => $totalPoli,
                'hari_tersibuk' => $busyDay ? $busyDay->hari : null,
                'jumlah_jadwal_tersibuk' => $busyDay ? $busyDay->total : 0
            ]
        ]);
    }
}
