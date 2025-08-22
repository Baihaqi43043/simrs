<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Tindakan;
use App\Kunjungan;
use App\Dokter;
use Carbon\Carbon;

class TindakanController extends Controller
{
    /**
     * Display a listing of tindakans
     */
    public function index(Request $request)
    {
        $query = Tindakan::with(['kunjungan.pasien', 'dikerjakan']);

        // Filter by kunjungan
        if ($request->has('kunjungan_id')) {
            $query->where('kunjungan_id', $request->kunjungan_id);
        }

        // Filter by status
        if ($request->has('status_tindakan')) {
            $query->where('status_tindakan', $request->status_tindakan);
        }

        // Filter by kategori
        if ($request->has('kategori_tindakan')) {
            $query->where('kategori_tindakan', $request->kategori_tindakan);
        }

        // Filter by dokter
        if ($request->has('dikerjakan_oleh')) {
            $query->where('dikerjakan_oleh', $request->dikerjakan_oleh);
        }

        // Filter by date range
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('tanggal_tindakan', [$request->start_date, $request->end_date]);
        }

        // Filter by tanggal
        if ($request->has('tanggal_tindakan')) {
            $query->whereDate('tanggal_tindakan', $request->tanggal_tindakan);
        }

        // Search by kode or nama tindakan
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_tindakan', 'like', "%{$search}%")
                  ->orWhere('nama_tindakan', 'like', "%{$search}%")
                  ->orWhereHas('kunjungan.pasien', function ($subQ) use ($search) {
                      $subQ->where('nama', 'like', "%{$search}%")
                           ->orWhere('no_rm', 'like', "%{$search}%");
                  });
            });
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $tindakans = $query->orderBy('tanggal_tindakan', 'desc')
                          ->orderBy('created_at', 'desc')
                          ->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Tindakans retrieved successfully',
            'data' => $tindakans->items(),
            'meta' => [
                'current_page' => $tindakans->currentPage(),
                'last_page' => $tindakans->lastPage(),
                'per_page' => $tindakans->perPage(),
                'total' => $tindakans->total(),
            ]
        ]);
    }

    /**
     * Store a newly created tindakan
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kunjungan_id' => 'required|exists:kunjungans,id',
            'kode_tindakan' => 'required|string|max:50',
            'nama_tindakan' => 'required|string|max:255',
            'kategori_tindakan' => 'required|in:pemeriksaan,lab,radiologi,tindakan_medis,operasi',
            'jumlah' => 'required|integer|min:1',
            'tarif_satuan' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:1000',
            'dikerjakan_oleh' => 'required|exists:dokters,id',
            'tanggal_tindakan' => 'nullable|date|after_or_equal:today',
            'status_tindakan' => 'nullable|in:rencana,sedang_dikerjakan,selesai,batal'
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
            // Check if kunjungan exists and can accept tindakan
            $kunjungan = Kunjungan::find($request->kunjungan_id);

            if (!$kunjungan->canAddTindakan()) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot add tindakan. Kunjungan status: ' . $kunjungan->status
                ], 400);
            }

            // Check for duplicate tindakan
            $existingTindakan = Tindakan::where('kunjungan_id', $request->kunjungan_id)
                ->where('kode_tindakan', $request->kode_tindakan)
                ->where('status_tindakan', '!=', 'batal')
                ->first();

            if ($existingTindakan) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Tindakan with this code already exists for this kunjungan'
                ], 400);
            }

            // Create tindakan
            $tindakan = Tindakan::create([
                'kunjungan_id' => $request->kunjungan_id,
                'kode_tindakan' => $request->kode_tindakan,
                'nama_tindakan' => $request->nama_tindakan,
                'kategori_tindakan' => $request->kategori_tindakan,
                'jumlah' => $request->jumlah,
                'tarif_satuan' => $request->tarif_satuan,
                'keterangan' => $request->keterangan,
                'dikerjakan_oleh' => $request->dikerjakan_oleh,
                'tanggal_tindakan' => $request->tanggal_tindakan ?? now(),
                'status_tindakan' => $request->status_tindakan ?? 'rencana'
            ]);

            $tindakan->load(['kunjungan.pasien', 'dikerjakan']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tindakan created successfully',
                'data' => $tindakan
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create tindakan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified tindakan
     */
    public function show($id)
    {
        $tindakan = Tindakan::with([
            'kunjungan.pasien',
            'kunjungan.dokter',
            'kunjungan.poli',
            'dikerjakan'
        ])->find($id);

        if (!$tindakan) {
            return response()->json([
                'success' => false,
                'message' => 'Tindakan not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Tindakan retrieved successfully',
            'data' => $tindakan
        ]);
    }

    /**
     * Update the specified tindakan
     */
    public function update(Request $request, $id)
    {
        $tindakan = Tindakan::find($id);

        if (!$tindakan) {
            return response()->json([
                'success' => false,
                'message' => 'Tindakan not found'
            ], 404);
        }

        // Check if tindakan can be updated
        if (!$tindakan->canBeUpdated()) {
            return response()->json([
                'success' => false,
                'message' => 'Tindakan cannot be updated. Current status: ' . $tindakan->status_tindakan
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'kode_tindakan' => 'sometimes|string|max:50',
            'nama_tindakan' => 'sometimes|string|max:255',
            'kategori_tindakan' => 'sometimes|in:pemeriksaan,lab,radiologi,tindakan_medis,operasi',
            'jumlah' => 'sometimes|integer|min:1',
            'tarif_satuan' => 'sometimes|numeric|min:0',
            'keterangan' => 'nullable|string|max:1000',
            'dikerjakan_oleh' => 'sometimes|exists:dokters,id',
            'tanggal_tindakan' => 'sometimes|date',
            'status_tindakan' => 'sometimes|in:rencana,sedang_dikerjakan,selesai,batal'
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
            // Check for duplicate if kode_tindakan is being updated
            if ($request->has('kode_tindakan') && $request->kode_tindakan !== $tindakan->kode_tindakan) {
                $existingTindakan = Tindakan::where('kunjungan_id', $tindakan->kunjungan_id)
                    ->where('kode_tindakan', $request->kode_tindakan)
                    ->where('id', '!=', $id)
                    ->where('status_tindakan', '!=', 'batal')
                    ->first();

                if ($existingTindakan) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Tindakan with this code already exists for this kunjungan'
                    ], 400);
                }
            }

            // Update tindakan
            $updateData = $request->only([
                'kode_tindakan', 'nama_tindakan', 'kategori_tindakan',
                'jumlah', 'tarif_satuan', 'keterangan', 'dikerjakan_oleh',
                'tanggal_tindakan', 'status_tindakan'
            ]);

            $tindakan->update($updateData);
            $tindakan->load(['kunjungan.pasien', 'dikerjakan']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tindakan updated successfully',
                'data' => $tindakan
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update tindakan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified tindakan
     */
    public function destroy($id)
    {
        $tindakan = Tindakan::find($id);

        if (!$tindakan) {
            return response()->json([
                'success' => false,
                'message' => 'Tindakan not found'
            ], 404);
        }

        // Check if tindakan can be deleted
        if (!$tindakan->canBeDeleted()) {
            return response()->json([
                'success' => false,
                'message' => 'Tindakan cannot be deleted. Current status: ' . $tindakan->status_tindakan
            ], 400);
        }

        $tindakan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tindakan deleted successfully'
        ]);
    }

    /**
     * Update tindakan status
     */
    public function updateStatus(Request $request, $id)
    {
        $tindakan = Tindakan::find($id);

        if (!$tindakan) {
            return response()->json([
                'success' => false,
                'message' => 'Tindakan not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'status_tindakan' => 'required|in:rencana,sedang_dikerjakan,selesai,batal'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 400);
        }

        // Validate status transition
        $currentStatus = $tindakan->status_tindakan;
        $newStatus = $request->status_tindakan;

        $validTransitions = [
            'rencana' => ['sedang_dikerjakan', 'batal'],
            'sedang_dikerjakan' => ['selesai', 'batal'],
            'selesai' => [], // Cannot change from selesai
            'batal' => [] // Cannot change from batal
        ];

        if (!in_array($newStatus, $validTransitions[$currentStatus])) {
            return response()->json([
                'success' => false,
                'message' => "Cannot change status from {$currentStatus} to {$newStatus}"
            ], 400);
        }

        $tindakan->update(['status_tindakan' => $newStatus]);

        return response()->json([
            'success' => true,
            'message' => 'Tindakan status updated successfully',
            'data' => [
                'id' => $tindakan->id,
                'kode_tindakan' => $tindakan->kode_tindakan,
                'previous_status' => $currentStatus,
                'current_status' => $newStatus,
                'status_text' => $tindakan->status_text
            ]
        ]);
    }

    /**
     * Get tindakans by kunjungan
     */
    public function byKunjungan($kunjunganId)
    {
        $kunjungan = Kunjungan::with('pasien')->find($kunjunganId);

        if (!$kunjungan) {
            return response()->json([
                'success' => false,
                'message' => 'Kunjungan not found'
            ], 404);
        }

        $tindakans = Tindakan::with('dikerjakan')
            ->where('kunjungan_id', $kunjunganId)
            ->orderBy('tanggal_tindakan')
            ->orderBy('created_at')
            ->get();

        $totalBiaya = $tindakans->sum('total_biaya');

        return response()->json([
            'success' => true,
            'message' => 'Tindakans by kunjungan retrieved successfully',
            'data' => [
                'kunjungan' => $kunjungan,
                'total_tindakan' => $tindakans->count(),
                'total_biaya' => $totalBiaya,
                'tindakans' => $tindakans
            ]
        ]);
    }

    /**
     * Get tindakans by kategori
     */
    public function byKategori(Request $request, $kategori)
    {
        $validKategori = ['pemeriksaan', 'lab', 'radiologi', 'tindakan_medis', 'operasi'];

        if (!in_array($kategori, $validKategori)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid kategori tindakan'
            ], 400);
        }

        $query = Tindakan::with(['kunjungan.pasien', 'dikerjakan'])
            ->where('kategori_tindakan', $kategori);

        // Filter by date if provided
        if ($request->has('tanggal')) {
            $query->whereDate('tanggal_tindakan', $request->tanggal);
        }

        // Filter by status if provided
        if ($request->has('status')) {
            $query->where('status_tindakan', $request->status);
        }

        $tindakans = $query->orderBy('tanggal_tindakan', 'desc')->get();

        return response()->json([
            'success' => true,
            'message' => 'Tindakans by kategori retrieved successfully',
            'data' => [
                'kategori' => $kategori,
                'total_tindakan' => $tindakans->count(),
                'total_biaya' => $tindakans->sum('total_biaya'),
                'tindakans' => $tindakans
            ]
        ]);
    }

    /**
     * Get today's tindakans
     */
    public function todayTindakans(Request $request)
    {
        $query = Tindakan::with(['kunjungan.pasien', 'dikerjakan'])
            ->whereDate('tanggal_tindakan', today());

        // Filter by status if provided
        if ($request->has('status')) {
            $query->where('status_tindakan', $request->status);
        }

        // Filter by kategori if provided
        if ($request->has('kategori')) {
            $query->where('kategori_tindakan', $request->kategori);
        }

        $tindakans = $query->orderBy('created_at')->get();

        // Group by status for summary
        $summary = [
            'rencana' => $tindakans->where('status_tindakan', 'rencana')->count(),
            'sedang_dikerjakan' => $tindakans->where('status_tindakan', 'sedang_dikerjakan')->count(),
            'selesai' => $tindakans->where('status_tindakan', 'selesai')->count(),
            'batal' => $tindakans->where('status_tindakan', 'batal')->count(),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Today tindakans retrieved successfully',
            'data' => [
                'tanggal' => today()->format('Y-m-d'),
                'total_tindakan' => $tindakans->count(),
                'total_biaya' => $tindakans->sum('total_biaya'),
                'summary' => $summary,
                'tindakans' => $tindakans
            ]
        ]);
    }

    /**
     * Bulk update status for multiple tindakans
     */
    public function bulkUpdateStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tindakan_ids' => 'required|array',
            'tindakan_ids.*' => 'exists:tindakans,id',
            'status_tindakan' => 'required|in:rencana,sedang_dikerjakan,selesai,batal'
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
            $tindakans = Tindakan::whereIn('id', $request->tindakan_ids)->get();
            $updated = 0;
            $failed = [];

            foreach ($tindakans as $tindakan) {
                $currentStatus = $tindakan->status_tindakan;
                $newStatus = $request->status_tindakan;

                $validTransitions = [
                    'rencana' => ['sedang_dikerjakan', 'batal'],
                    'sedang_dikerjakan' => ['selesai', 'batal'],
                    'selesai' => [],
                    'batal' => []
                ];

                if (in_array($newStatus, $validTransitions[$currentStatus])) {
                    $tindakan->update(['status_tindakan' => $newStatus]);
                    $updated++;
                } else {
                    $failed[] = [
                        'id' => $tindakan->id,
                        'kode_tindakan' => $tindakan->kode_tindakan,
                        'reason' => "Cannot change from {$currentStatus} to {$newStatus}"
                    ];
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Bulk status update completed',
                'data' => [
                    'total_requested' => count($request->tindakan_ids),
                    'updated' => $updated,
                    'failed' => count($failed),
                    'failed_items' => $failed
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to bulk update status: ' . $e->getMessage()
            ], 500);
        }
    }
}
