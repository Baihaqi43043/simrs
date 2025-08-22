<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Dokter;

class DokterController extends Controller
{
    /**
     * Display a listing of dokters
     */
    public function index(Request $request)
    {
        $query = Dokter::query();

        // Filter by active status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // Filter by spesialisasi
        if ($request->has('spesialisasi')) {
            $query->where('spesialisasi', 'like', '%' . $request->spesialisasi . '%');
        }

        // Search by name or code
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_dokter', 'like', "%{$search}%")
                  ->orWhere('kode_dokter', 'like', "%{$search}%")
                  ->orWhere('spesialisasi', 'like', "%{$search}%");
            });
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $dokters = $query->orderBy('nama_dokter')->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Dokters retrieved successfully',
            'data' => $dokters->items(),
            'meta' => [
                'current_page' => $dokters->currentPage(),
                'last_page' => $dokters->lastPage(),
                'per_page' => $dokters->perPage(),
                'total' => $dokters->total(),
            ]
        ]);
    }

    /**
     * Store a newly created dokter
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_dokter' => 'required|string|max:10|unique:dokters,kode_dokter',
            'nama_dokter' => 'required|string|max:255',
            'spesialisasi' => 'required|string|max:255',
            'no_telepon' => 'nullable|string|max:15',
            'email' => 'nullable|email|max:255|unique:dokters,email',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 400);
        }

        $dokter = Dokter::create([
            'kode_dokter' => $request->kode_dokter,
            'nama_dokter' => $request->nama_dokter,
            'spesialisasi' => $request->spesialisasi,
            'no_telepon' => $request->no_telepon,
            'email' => $request->email,
            'is_active' => $request->get('is_active', true)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Dokter created successfully',
            'data' => $dokter
        ], 201);
    }

    /**
     * Display the specified dokter
     */
    public function show($id)
    {
        $dokter = Dokter::find($id);

        if (!$dokter) {
            return response()->json([
                'success' => false,
                'message' => 'Dokter not found'
            ], 404);
        }

        // Load relationships
        $dokter->load(['jadwalDokters.poli']);

        return response()->json([
            'success' => true,
            'message' => 'Dokter retrieved successfully',
            'data' => $dokter
        ]);
    }

    /**
     * Update the specified dokter
     */
    public function update(Request $request, $id)
    {
        $dokter = Dokter::find($id);

        if (!$dokter) {
            return response()->json([
                'success' => false,
                'message' => 'Dokter not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'kode_dokter' => 'required|string|max:10|unique:dokters,kode_dokter,' . $id,
            'nama_dokter' => 'required|string|max:255',
            'spesialisasi' => 'required|string|max:255',
            'no_telepon' => 'nullable|string|max:15',
            'email' => 'nullable|email|max:255|unique:dokters,email,' . $id,
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 400);
        }

        $dokter->update([
            'kode_dokter' => $request->kode_dokter,
            'nama_dokter' => $request->nama_dokter,
            'spesialisasi' => $request->spesialisasi,
            'no_telepon' => $request->no_telepon,
            'email' => $request->email,
            'is_active' => $request->get('is_active', $dokter->is_active)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Dokter updated successfully',
            'data' => $dokter->fresh()
        ]);
    }

    /**
     * Remove the specified dokter
     */
    public function destroy($id)
    {
        $dokter = Dokter::find($id);

        if (!$dokter) {
            return response()->json([
                'success' => false,
                'message' => 'Dokter not found'
            ], 404);
        }

        // Check if dokter has related data
        $hasKunjungan = $dokter->kunjungans()->exists();
        $hasJadwal = $dokter->jadwalDokters()->exists();

        if ($hasKunjungan || $hasJadwal) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete dokter. It has related kunjungan or jadwal data.'
            ], 400);
        }

        $dokter->delete();

        return response()->json([
            'success' => true,
            'message' => 'Dokter deleted successfully'
        ]);
    }

    /**
     * Get jadwal for specific dokter
     */
    public function jadwal($id)
    {
        $dokter = Dokter::find($id);

        if (!$dokter) {
            return response()->json([
                'success' => false,
                'message' => 'Dokter not found'
            ], 404);
        }

        $jadwals = $dokter->jadwalDokters()
            ->with('poli')
            ->where('is_active', true)
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Jadwal dokter retrieved successfully',
            'data' => $jadwals
        ]);
    }

    /**
     * Get today's kunjungans for specific dokter
     */
    public function kunjungansToday($id)
    {
        $dokter = Dokter::find($id);

        if (!$dokter) {
            return response()->json([
                'success' => false,
                'message' => 'Dokter not found'
            ], 404);
        }

        $kunjungans = $dokter->kunjungans()
            ->with(['pasien', 'poli'])
            ->whereDate('tanggal_kunjungan', today())
            ->orderBy('no_antrian')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Today kunjungans retrieved successfully',
            'data' => $kunjungans
        ]);
    }
}
