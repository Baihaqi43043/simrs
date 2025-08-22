<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Pasien;
use App\Kunjungan;
use Carbon\Carbon;

class PasienController extends Controller
{
    // Tidak ada constructor middleware seperti DokterController
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Pasien::query();

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('no_rm', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        // Filter by jenis kelamin
        if ($request->has('jenis_kelamin') && !empty($request->jenis_kelamin)) {
            $query->where('jenis_kelamin', $request->jenis_kelamin);
        }

        // Filter by age range
        if ($request->has('age_min') || $request->has('age_max')) {
            $ageMin = $request->get('age_min', 0);
            $ageMax = $request->get('age_max', 150);

            if ($ageMin > 0 || $ageMax < 150) {
                $dateMax = Carbon::now()->subYears($ageMin)->format('Y-m-d');
                $dateMin = Carbon::now()->subYears($ageMax)->format('Y-m-d');
                $query->whereBetween('tanggal_lahir', [$dateMin, $dateMax]);
            }
        }

        $pasiens = $query->orderBy('nama')->paginate(10);
        // dd($pasiens);

        return view('pasiens.index', compact('pasiens'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pasiens.create');
    }

    /**
     * Store a newly created resource in storage.
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
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Generate no_rm
        $noRm = $this->generateNoRM();

        Pasien::create([
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

        return redirect()->route('pasiens.index')
            ->with('success', 'Data pasien berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $pasien = Pasien::findOrFail($id);
        $pasien->append(['umur', 'jenis_kelamin_text']);

        return view('pasiens.show', compact('pasien'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $pasien = Pasien::findOrFail($id);
        return view('pasiens.edit', compact('pasien'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $pasien = Pasien::findOrFail($id);

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
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
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

        return redirect()->route('pasiens.index')
            ->with('success', 'Data pasien berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $pasien = Pasien::findOrFail($id);

        // Check if pasien has related kunjungan
        $hasKunjungan = $pasien->kunjungans()->exists();

        if ($hasKunjungan) {
            return redirect()->back()
                ->with('error', 'Data pasien tidak dapat dihapus karena memiliki riwayat kunjungan.');
        }

        $pasien->delete();

        return redirect()->route('pasiens.index')
            ->with('success', 'Data pasien berhasil dihapus.');
    }

    /**
     * Show riwayat kunjungan for specific pasien
     */
    public function riwayatKunjungan($id)
    {
        $pasien = Pasien::findOrFail($id);
        $pasien->append(['umur', 'jenis_kelamin_text']);

        $kunjungans = $pasien->kunjungans()
            ->with(['dokter', 'poli', 'tindakans', 'diagnosas'])
            ->orderBy('tanggal_kunjungan', 'desc')
            ->paginate(10);

        return view('pasiens.riwayat-kunjungan', compact('pasien', 'kunjungans'));
    }

    /**
     * Search pasien (AJAX)
     */
    public function search(Request $request)
    {
        $query = Pasien::query();

        if ($request->has('term')) {
            $term = $request->term;
            $query->where(function ($q) use ($term) {
                $q->where('nama', 'like', "%{$term}%")
                  ->orWhere('no_rm', 'like', "%{$term}%")
                  ->orWhere('nik', 'like', "%{$term}%");
            });
        }

        $pasiens = $query->limit(10)->get(['id', 'no_rm', 'nama', 'nik']);

        $results = $pasiens->map(function ($pasien) {
            return [
                'id' => $pasien->id,
                'text' => $pasien->no_rm . ' - ' . $pasien->nama . ' (' . $pasien->nik . ')'
            ];
        });

        return response()->json($results);
    }

    /**
     * Bulk actions for multiple pasiens
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete',
            'selected_ids' => 'required|array',
            'selected_ids.*' => 'exists:pasiens,id'
        ]);

        $selectedIds = $request->selected_ids;
        $action = $request->action;

        try {
            switch ($action) {
                case 'delete':
                    // Check for related kunjungan before delete
                    $pasiensWithKunjungan = Pasien::whereIn('id', $selectedIds)
                        ->whereHas('kunjungans')
                        ->count();

                    if ($pasiensWithKunjungan > 0) {
                        return back()->with('error', 'Beberapa pasien tidak dapat dihapus karena memiliki riwayat kunjungan.');
                    }

                    Pasien::whereIn('id', $selectedIds)->delete();
                    $message = 'Pasien yang dipilih berhasil dihapus.';
                    break;
            }

            return back()->with('success', $message);

        } catch (\Exception $e) {
            \Log::error('Bulk Action Error:', [
                'action' => $action,
                'selected_ids' => $selectedIds,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Terjadi kesalahan saat melakukan aksi bulk.');
        }
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

    public function checkHistory($id)
{
    $hasHistory = Kunjungan::where('pasien_id', $id)->exists();

    return response()->json([
        'is_new_patient' => !$hasHistory
    ]);
}
}
