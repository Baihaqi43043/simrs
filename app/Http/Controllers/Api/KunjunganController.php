<?php

// ============================================
// app/Http/Controllers/Api/KunjunganController.php
// ============================================

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Kunjungan;
use App\Pasien;
use App\Dokter;
use App\Poli;
use App\JadwalDokter;
use Carbon\Carbon;

class KunjunganController extends Controller
{
    /**
     * Display a listing of kunjungans
     */
    public function index(Request $request)
    {
        $query = Kunjungan::with(['pasien', 'dokter', 'poli', 'jadwalDokter']);

        // Filter by tanggal
        if ($request->has('tanggal_kunjungan')) {
            $query->whereDate('tanggal_kunjungan', $request->tanggal_kunjungan);
        }

        // Filter by date range
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('tanggal_kunjungan', [$request->start_date, $request->end_date]);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by dokter
        if ($request->has('dokter_id')) {
            $query->where('dokter_id', $request->dokter_id);
        }

        // Filter by poli
        if ($request->has('poli_id')) {
            $query->where('poli_id', $request->poli_id);
        }

        // Filter by pasien
        if ($request->has('pasien_id')) {
            $query->where('pasien_id', $request->pasien_id);
        }

        // Filter by jenis kunjungan
        if ($request->has('jenis_kunjungan')) {
            $query->where('jenis_kunjungan', $request->jenis_kunjungan);
        }

        // Search by patient name or no_kunjungan
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_kunjungan', 'like', "%{$search}%")
                  ->orWhereHas('pasien', function ($subQ) use ($search) {
                      $subQ->where('nama', 'like', "%{$search}%")
                           ->orWhere('no_rm', 'like', "%{$search}%");
                  });
            });
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $kunjungans = $query->orderBy('tanggal_kunjungan', 'desc')
                           ->orderBy('no_antrian')
                           ->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Kunjungans retrieved successfully',
            'data' => $kunjungans->items(),
            'meta' => [
                'current_page' => $kunjungans->currentPage(),
                'last_page' => $kunjungans->lastPage(),
                'per_page' => $kunjungans->perPage(),
                'total' => $kunjungans->total(),
            ]
        ]);
    }

    /**
     * Store a newly created kunjungan
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pasien_id' => 'required|exists:pasiens,id',
            'dokter_id' => 'required|exists:dokters,id',
            'poli_id' => 'required|exists:polis,id',
            'jadwal_dokter_id' => 'nullable|exists:jadwal_dokters,id',
            'tanggal_kunjungan' => 'required|date|after_or_equal:today',
            'jam_kunjungan' => 'nullable|date_format:H:i',
            'jenis_kunjungan' => 'required|in:baru,lama',
            'cara_bayar' => 'required|in:umum,bpjs,asuransi',
            'keluhan_utama' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 400);
        }

        DB::beginTransaction();

        try {
            $tanggal = Carbon::parse($request->tanggal_kunjungan);

            // Validate jadwal dokter if provided
            if ($request->jadwal_dokter_id) {
                $jadwal = JadwalDokter::find($request->jadwal_dokter_id);

                // Check if date matches schedule day
                $dayName = strtolower($tanggal->format('l'));
                $dayMapping = [
                    'monday' => 'senin', 'tuesday' => 'selasa', 'wednesday' => 'rabu',
                    'thursday' => 'kamis', 'friday' => 'jumat', 'saturday' => 'sabtu', 'sunday' => 'minggu'
                ];

                if ($jadwal->hari !== $dayMapping[$dayName]) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Selected date does not match doctor schedule day'
                    ], 400);
                }

                // Check quota availability
                if (!$jadwal->isKuotaAvailable($tanggal)) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Doctor quota is full for this date'
                    ], 400);
                }
            }

            // Generate no_kunjungan
            $noKunjungan = $this->generateNoKunjungan($tanggal);

            // Generate no_antrian
            $noAntrian = $this->generateNoAntrian($request->poli_id, $tanggal);

            // Determine jenis_kunjungan automatically if not specified correctly
            $pasien = Pasien::find($request->pasien_id);
            $isNewPatient = $pasien->isPassienBaru();

            if ($request->jenis_kunjungan === 'baru' && !$isNewPatient) {
                // Patient already has visit history, should be 'lama'
                $jenisKunjungan = 'lama';
            } else {
                $jenisKunjungan = $request->jenis_kunjungan;
            }

            // Create kunjungan
            $kunjungan = Kunjungan::create([
                'no_kunjungan' => $noKunjungan,
                'pasien_id' => $request->pasien_id,
                'dokter_id' => $request->dokter_id,
                'poli_id' => $request->poli_id,
                'jadwal_dokter_id' => $request->jadwal_dokter_id,
                'tanggal_kunjungan' => $tanggal,
                'jam_kunjungan' => $request->jam_kunjungan,
                'no_antrian' => $noAntrian,
                'jenis_kunjungan' => $jenisKunjungan,
                'cara_bayar' => $request->cara_bayar,
                'keluhan_utama' => $request->keluhan_utama,
                'status' => 'menunggu',
                'total_biaya' => 0,
                'created_by' => $request->user()->id
            ]);

            $kunjungan->load(['pasien', 'dokter', 'poli', 'jadwalDokter', 'createdBy']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Kunjungan created successfully',
                'data' => $kunjungan
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create kunjungan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified kunjungan
     */
    public function show($id)
    {
        $kunjungan = Kunjungan::with([
            'pasien', 'dokter', 'poli', 'jadwalDokter',
            'tindakans.dikerjakan', 'diagnosas.didiagnosa', 'createdBy'
        ])->find($id);

        if (!$kunjungan) {
            return response()->json([
                'success' => false,
                'message' => 'Kunjungan not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Kunjungan retrieved successfully',
            'data' => $kunjungan
        ]);
    }

    /**
     * Update the specified kunjungan
     */
    public function update(Request $request, $id)
    {
        $kunjungan = Kunjungan::find($id);

        if (!$kunjungan) {
            return response()->json([
                'success' => false,
                'message' => 'Kunjungan not found'
            ], 404);
        }

        // Check if kunjungan can be updated
        if (!$kunjungan->canBeUpdated()) {
            return response()->json([
                'success' => false,
                'message' => 'Kunjungan cannot be updated. Current status: ' . $kunjungan->status
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'dokter_id' => 'sometimes|exists:dokters,id',
            'poli_id' => 'sometimes|exists:polis,id',
            'jadwal_dokter_id' => 'nullable|exists:jadwal_dokters,id',
            'tanggal_kunjungan' => 'sometimes|date|after_or_equal:today',
            'jam_kunjungan' => 'nullable|date_format:H:i',
            'cara_bayar' => 'sometimes|in:umum,bpjs,asuransi',
            'keluhan_utama' => 'nullable|string|max:1000',
            'catatan' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 400);
        }

        DB::beginTransaction();

        try {
            // Update fields
            $updateData = $request->only([
                'dokter_id', 'poli_id', 'jadwal_dokter_id', 'tanggal_kunjungan',
                'jam_kunjungan', 'cara_bayar', 'keluhan_utama', 'catatan'
            ]);

            // If tanggal_kunjungan is updated, regenerate no_antrian
            if (isset($updateData['tanggal_kunjungan'])) {
                $newTanggal = Carbon::parse($updateData['tanggal_kunjungan']);
                $poliId = $updateData['poli_id'] ?? $kunjungan->poli_id;
                $updateData['no_antrian'] = $this->generateNoAntrian($poliId, $newTanggal);
            }

            $kunjungan->update($updateData);
            $kunjungan->load(['pasien', 'dokter', 'poli', 'jadwalDokter']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Kunjungan updated successfully',
                'data' => $kunjungan
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update kunjungan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified kunjungan
     */
    public function destroy($id)
    {
        $kunjungan = Kunjungan::find($id);

        if (!$kunjungan) {
            return response()->json([
                'success' => false,
                'message' => 'Kunjungan not found'
            ], 404);
        }

        // Check if kunjungan can be cancelled
        if (!$kunjungan->canBeCancelled()) {
            return response()->json([
                'success' => false,
                'message' => 'Kunjungan cannot be cancelled. Current status: ' . $kunjungan->status
            ], 400);
        }

        // Check if kunjungan has related medical records
        $hasTindakan = $kunjungan->tindakans()->exists();
        $hasDiagnosa = $kunjungan->diagnosas()->exists();

        if ($hasTindakan || $hasDiagnosa) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete kunjungan. It has related medical records (tindakan/diagnosa).'
            ], 400);
        }

        $kunjungan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kunjungan deleted successfully'
        ]);
    }

    /**
     * Update kunjungan status
     */
    public function updateStatus(Request $request, $id)
    {
        $kunjungan = Kunjungan::find($id);

        if (!$kunjungan) {
            return response()->json([
                'success' => false,
                'message' => 'Kunjungan not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:menunggu,sedang_dilayani,selesai,batal'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 400);
        }

        // Validate status transition
        $currentStatus = $kunjungan->status;
        $newStatus = $request->status;

        $validTransitions = [
            'menunggu' => ['sedang_dilayani', 'batal'],
            'sedang_dilayani' => ['selesai', 'menunggu'],
            'selesai' => [], // Cannot change from selesai
            'batal' => [] // Cannot change from batal
        ];

        if (!in_array($newStatus, $validTransitions[$currentStatus])) {
            return response()->json([
                'success' => false,
                'message' => "Cannot change status from {$currentStatus} to {$newStatus}"
            ], 400);
        }

        $kunjungan->update(['status' => $newStatus]);

        return response()->json([
            'success' => true,
            'message' => 'Kunjungan status updated successfully',
            'data' => [
                'id' => $kunjungan->id,
                'no_kunjungan' => $kunjungan->no_kunjungan,
                'previous_status' => $currentStatus,
                'current_status' => $newStatus,
                'status_text' => $kunjungan->status_text
            ]
        ]);
    }

    /**
     * Get today's kunjungans
     */
    public function todayAll(Request $request)
    {
        $query = Kunjungan::with(['pasien', 'dokter', 'poli'])
            ->whereDate('tanggal_kunjungan', today());

        // Filter by status if provided
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $kunjungans = $query->orderBy('no_antrian')->get();

        return response()->json([
            'success' => true,
            'message' => 'Today kunjungans retrieved successfully',
            'data' => [
                'tanggal' => today()->format('Y-m-d'),
                'total' => $kunjungans->count(),
                'kunjungans' => $kunjungans
            ]
        ]);
    }

    /**
     * Get today's kunjungans by poli
     */
    public function todayByPoli($poliId)
    {
        $poli = Poli::find($poliId);
        if (!$poli) {
            return response()->json([
                'success' => false,
                'message' => 'Poli not found'
            ], 404);
        }

        $kunjungans = Kunjungan::with(['pasien', 'dokter'])
            ->where('poli_id', $poliId)
            ->whereDate('tanggal_kunjungan', today())
            ->orderBy('no_antrian')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Today kunjungans by poli retrieved successfully',
            'data' => [
                'poli' => $poli,
                'tanggal' => today()->format('Y-m-d'),
                'total' => $kunjungans->count(),
                'kunjungans' => $kunjungans
            ]
        ]);
    }

    /**
     * Get today's kunjungans by dokter
     */
    public function todayByDokter($dokterId)
    {
        $dokter = Dokter::find($dokterId);
        if (!$dokter) {
            return response()->json([
                'success' => false,
                'message' => 'Dokter not found'
            ], 404);
        }

        $kunjungans = Kunjungan::with(['pasien', 'poli'])
            ->where('dokter_id', $dokterId)
            ->whereDate('tanggal_kunjungan', today())
            ->orderBy('no_antrian')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Today kunjungans by dokter retrieved successfully',
            'data' => [
                'dokter' => $dokter,
                'tanggal' => today()->format('Y-m-d'),
                'total' => $kunjungans->count(),
                'kunjungans' => $kunjungans
            ]
        ]);
    }

    /**
     * Get antrian info for kunjungan
     */
    public function antrianInfo($id)
    {
        $kunjungan = Kunjungan::with(['pasien', 'dokter', 'poli'])->find($id);

        if (!$kunjungan) {
            return response()->json([
                'success' => false,
                'message' => 'Kunjungan not found'
            ], 404);
        }

        // Get queue position and waiting info
        $currentPosition = $kunjungan->no_antrian;

        $beforeMe = Kunjungan::where('poli_id', $kunjungan->poli_id)
            ->whereDate('tanggal_kunjungan', $kunjungan->tanggal_kunjungan)
            ->where('no_antrian', '<', $currentPosition)
            ->whereIn('status', ['menunggu', 'sedang_dilayani'])
            ->count();

        $afterMe = Kunjungan::where('poli_id', $kunjungan->poli_id)
            ->whereDate('tanggal_kunjungan', $kunjungan->tanggal_kunjungan)
            ->where('no_antrian', '>', $currentPosition)
            ->whereIn('status', ['menunggu'])
            ->count();

        $currentlyServed = Kunjungan::where('poli_id', $kunjungan->poli_id)
            ->whereDate('tanggal_kunjungan', $kunjungan->tanggal_kunjungan)
            ->where('status', 'sedang_dilayani')
            ->first();

        return response()->json([
            'success' => true,
            'message' => 'Antrian info retrieved successfully',
            'data' => [
                'kunjungan' => $kunjungan,
                'antrian_info' => [
                    'no_antrian' => $currentPosition,
                    'status' => $kunjungan->status,
                    'status_text' => $kunjungan->status_text,
                    'before_me' => $beforeMe,
                    'after_me' => $afterMe,
                    'estimated_waiting' => $beforeMe * 15, // Assume 15 minutes per patient
                    'currently_served' => $currentlyServed ? [
                        'no_antrian' => $currentlyServed->no_antrian,
                        'pasien' => $currentlyServed->pasien->nama
                    ] : null
                ]
            ]
        ]);
    }

    /**
     * Generate nomor kunjungan
     */
    private function generateNoKunjungan($tanggal)
    {
        $dateStr = $tanggal->format('Ymd');
        $prefix = 'KJ-' . $dateStr . '-';

        $lastKunjungan = Kunjungan::where('no_kunjungan', 'like', $prefix . '%')
            ->orderBy('no_kunjungan', 'desc')
            ->first();

        if ($lastKunjungan) {
            $lastNumber = (int) substr($lastKunjungan->no_kunjungan, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Generate nomor antrian
     */
    private function generateNoAntrian($poliId, $tanggal)
    {
        $lastAntrian = Kunjungan::where('poli_id', $poliId)
            ->whereDate('tanggal_kunjungan', $tanggal)
            ->max('no_antrian');

        return ($lastAntrian ?? 0) + 1;
    }

    /**
     * Generate nomor antrian (public endpoint)
     */
    public function generateNomorAntrian(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'poli_id' => 'required|exists:polis,id',
            'tanggal' => 'required|date|after_or_equal:today'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 400);
        }

        $poli = Poli::find($request->poli_id);
        $tanggal = Carbon::parse($request->tanggal);
        $noAntrian = $this->generateNoAntrian($request->poli_id, $tanggal);

        return response()->json([
            'success' => true,
            'message' => 'Nomor antrian generated successfully',
            'data' => [
                'poli' => $poli,
                'tanggal' => $tanggal->format('Y-m-d'),
                'no_antrian' => $noAntrian
            ]
        ]);
    }
}
