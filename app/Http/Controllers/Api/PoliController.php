<?php

// ============================================
// app/Http/Controllers/Api/PoliController.php
// ============================================

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Poli;

class PoliController extends Controller
{
    /**
     * Display a listing of polis
     */
    public function index(Request $request)
    {
         // Manual token check for testing
    $authHeader = $request->header('Authorization');
    if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
        return response()->json([
            'success' => false,
            'message' => 'Missing or invalid authorization header'
        ], 401);
    }

    $token = substr($authHeader, 7); // Remove 'Bearer ' prefix
    $user = \App\User::where('api_token', $token)->first();

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid token'
        ], 401);
    }

        $query = Poli::query();

        // Filter by active status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // Search by name or code
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_poli', 'like', "%{$search}%")
                  ->orWhere('kode_poli', 'like', "%{$search}%");
            });
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $polis = $query->orderBy('nama_poli')->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Polis retrieved successfully',
            'data' => $polis->items(),
            'meta' => [
                'current_page' => $polis->currentPage(),
                'last_page' => $polis->lastPage(),
                'per_page' => $polis->perPage(),
                'total' => $polis->total(),
            ]
        ]);
    }

    /**
     * Store a newly created poli
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_poli' => 'required|string|max:10|unique:polis,kode_poli',
            'nama_poli' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 400);
        }

        $poli = Poli::create([
            'kode_poli' => $request->kode_poli,
            'nama_poli' => $request->nama_poli,
            'deskripsi' => $request->deskripsi,
            'is_active' => $request->get('is_active', true)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Poli created successfully',
            'data' => $poli
        ], 201);
    }

    /**
     * Display the specified poli
     */
    public function show($id)
    {
        $poli = Poli::find($id);

        if (!$poli) {
            return response()->json([
                'success' => false,
                'message' => 'Poli not found'
            ], 404);
        }

        // Load relationships
        $poli->load(['jadwalDokters.dokter']);

        return response()->json([
            'success' => true,
            'message' => 'Poli retrieved successfully',
            'data' => $poli
        ]);
    }

    /**
     * Update the specified poli
     */
    public function update(Request $request, $id)
    {
        $poli = Poli::find($id);

        if (!$poli) {
            return response()->json([
                'success' => false,
                'message' => 'Poli not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'kode_poli' => 'required|string|max:10|unique:polis,kode_poli,' . $id,
            'nama_poli' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 400);
        }

        $poli->update([
            'kode_poli' => $request->kode_poli,
            'nama_poli' => $request->nama_poli,
            'deskripsi' => $request->deskripsi,
            'is_active' => $request->get('is_active', $poli->is_active)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Poli updated successfully',
            'data' => $poli->fresh()
        ]);
    }

    /**
     * Remove the specified poli
     */
    public function destroy($id)
    {
        $poli = Poli::find($id);

        if (!$poli) {
            return response()->json([
                'success' => false,
                'message' => 'Poli not found'
            ], 404);
        }

        // Check if poli has related data
        $hasKunjungan = $poli->kunjungans()->exists();
        $hasJadwal = $poli->jadwalDokters()->exists();

        if ($hasKunjungan || $hasJadwal) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete poli. It has related kunjungan or jadwal data.'
            ], 400);
        }

        $poli->delete();

        return response()->json([
            'success' => true,
            'message' => 'Poli deleted successfully'
        ]);
    }

    /**
     * Get jadwal dokters for specific poli
     */
    public function jadwalDokters($id)
    {
        $poli = Poli::find($id);

        if (!$poli) {
            return response()->json([
                'success' => false,
                'message' => 'Poli not found'
            ], 404);
        }

        $jadwals = $poli->jadwalDokters()
            ->with('dokter')
            ->where('is_active', true)
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Jadwal dokters retrieved successfully',
            'data' => $jadwals
        ]);
    }
}
