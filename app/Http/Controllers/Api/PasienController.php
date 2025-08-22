<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Pasien;
use Carbon\Carbon;

class PasienController extends Controller
{
    /**
     * Display a listing of pasiens
     */
    public function index(Request $request)
    {
        $query = Pasien::query();

        // Search by name, no_rm, or nik
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('no_rm', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        // Filter by jenis kelamin
        if ($request->has('jenis_kelamin')) {
            $query->where('jenis_kelamin', $request->jenis_kelamin);
        }

        // Filter by age range
        if ($request->has('age_min') || $request->has('age_max')) {
            $ageMin = $request->get('age_min', 0);
            $ageMax = $request->get('age_max', 150);

            $dateMax = Carbon::now()->subYears($ageMin)->format('Y-m-d');
            $dateMin = Carbon::now()->subYears($ageMax)->format('Y-m-d');

            $query->whereBetween('tanggal_lahir', [$dateMin, $dateMax]);
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $pasiens = $query->orderBy('nama')->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Pasiens retrieved successfully',
            'data' => $pasiens->items(),
            'meta' => [
                'current_page' => $pasiens->currentPage(),
                'last_page' => $pasiens->lastPage(),
                'per_page' => $pasiens->perPage(),
                'total' => $pasiens->total(),
            ]
        ]);
    }

    /**
     * Store a newly created pasien
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nik' => 'required|string|size:16|unique:pasiens,nik',
            'nama' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date|before:today',
            'tempat_lahir' => 'nullable|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => 'nullable|string',
            'no_telepon' => 'nullable|string|max:15',
            'no_telepon_darurat' => 'nullable|string|max:15',
            'nama_kontak_darurat' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 400);
        }

        // Generate no_rm
        $noRm = $this->generateNoRM();

        $pasien = Pasien::create([
            'no_rm' => $noRm,
            'nik' => $request->nik,
            'nama' => $request->nama,
            'tanggal_lahir' => $request->tanggal_lahir,
            'tempat_lahir' => $request->tempat_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
            'alamat' => $request->alamat,
            'no_telepon' => $request->no_telepon,
            'no_telepon_darurat' => $request->no_telepon_darurat,
            'nama_kontak_darurat' => $request->nama_kontak_darurat
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pasien created successfully',
            'data' => $pasien
        ], 201);
    }

    /**
     * Display the specified pasien
     */
    public function show($id)
    {
        $pasien = Pasien::find($id);

        if (!$pasien) {
            return response()->json([
                'success' => false,
                'message' => 'Pasien not found'
            ], 404);
        }

        // Add computed attributes
        $pasien->append(['umur', 'jenis_kelamin_text']);

        return response()->json([
            'success' => true,
            'message' => 'Pasien retrieved successfully',
            'data' => $pasien
        ]);
    }

    /**
     * Update the specified pasien
     */
    public function update(Request $request, $id)
    {
        $pasien = Pasien::find($id);

        if (!$pasien) {
            return response()->json([
                'success' => false,
                'message' => 'Pasien not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nik' => 'required|string|size:16|unique:pasiens,nik,' . $id,
            'nama' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date|before:today',
            'tempat_lahir' => 'nullable|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => 'nullable|string',
            'no_telepon' => 'nullable|string|max:15',
            'no_telepon_darurat' => 'nullable|string|max:15',
            'nama_kontak_darurat' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 400);
        }

        $pasien->update([
            'nik' => $request->nik,
            'nama' => $request->nama,
            'tanggal_lahir' => $request->tanggal_lahir,
            'tempat_lahir' => $request->tempat_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
            'alamat' => $request->alamat,
            'no_telepon' => $request->no_telepon,
            'no_telepon_darurat' => $request->no_telepon_darurat,
            'nama_kontak_darurat' => $request->nama_kontak_darurat
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pasien updated successfully',
            'data' => $pasien->fresh()
        ]);
    }

    /**
     * Remove the specified pasien
     */
    public function destroy($id)
    {
        $pasien = Pasien::find($id);

        if (!$pasien) {
            return response()->json([
                'success' => false,
                'message' => 'Pasien not found'
            ], 404);
        }

        // Check if pasien has related kunjungan
        $hasKunjungan = $pasien->kunjungans()->exists();

        if ($hasKunjungan) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete pasien. It has related kunjungan data.'
            ], 400);
        }

        $pasien->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pasien deleted successfully'
        ]);
    }

    /**
     * Search pasien by NIK
     */
    public function searchByNik($nik)
    {
        $pasien = Pasien::where('nik', $nik)->first();

        if (!$pasien) {
            return response()->json([
                'success' => false,
                'message' => 'Pasien not found'
            ], 404);
        }

        $pasien->append(['umur', 'jenis_kelamin_text']);

        return response()->json([
            'success' => true,
            'message' => 'Pasien found',
            'data' => $pasien
        ]);
    }

    /**
     * Search pasien by No RM
     */
    public function searchByNoRm($noRm)
    {
        $pasien = Pasien::where('no_rm', $noRm)->first();

        if (!$pasien) {
            return response()->json([
                'success' => false,
                'message' => 'Pasien not found'
            ], 404);
        }

        $pasien->append(['umur', 'jenis_kelamin_text']);

        return response()->json([
            'success' => true,
            'message' => 'Pasien found',
            'data' => $pasien
        ]);
    }

    /**
     * Get riwayat kunjungan for specific pasien
     */
    public function riwayatKunjungan($id)
    {
        $pasien = Pasien::find($id);

        if (!$pasien) {
            return response()->json([
                'success' => false,
                'message' => 'Pasien not found'
            ], 404);
        }

        $kunjungans = $pasien->kunjungans()
            ->with(['dokter', 'poli', 'tindakans', 'diagnosas'])
            ->orderBy('tanggal_kunjungan', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Riwayat kunjungan retrieved successfully',
            'data' => [
                'pasien' => $pasien,
                'kunjungans' => $kunjungans
            ]
        ]);
    }

    /**
     * Generate unique No RM
     */
    private function generateNoRM()
    {
        $today = Carbon::now()->format('Ymd');
        $prefix = 'RM-' . $today . '-';

        // Get last number for today
        $lastPasien = Pasien::where('no_rm', 'like', $prefix . '%')
            ->orderBy('no_rm', 'desc')
            ->first();

        if ($lastPasien) {
            $lastNumber = (int) substr($lastPasien->no_rm, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}
