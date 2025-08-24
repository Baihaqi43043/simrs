<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Dokter;
use App\JadwalDokter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DokterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Dokter::query();

        // Search functionality - satu input untuk semua
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_dokter', 'like', "%{$search}%")
                  ->orWhere('kode_dokter', 'like', "%{$search}%")
                  ->orWhere('spesialisasi', 'like', "%{$search}%");
            });
        }

        // Filter by status only
        if ($request->has('status') && $request->status !== '') {
            $query->where('is_active', $request->status);
        }

        $dokters = $query->orderBy('nama_dokter')->paginate(15);

        // dd($dokters);
        return view('dokters.index', compact('dokters'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dokters.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Debug log
        Log::info('Store Dokter Request:', [
            'request_data' => $request->all(),
            'method' => $request->method(),
            'url' => $request->url()
        ]);

        try {
            // Validasi
            $validatedData = $request->validate([
                'kode_dokter' => 'required|string|max:10|unique:dokters,kode_dokter',
                'nama_dokter' => 'required|string|max:255',
                'spesialisasi' => 'required|string|max:255',
                'no_telepon' => 'nullable|string|max:15',
                'email' => 'nullable|email|max:255|unique:dokters,email',
                'is_active' => 'nullable|boolean',
            ]);

            // Handle checkbox is_active
            $validatedData['is_active'] = $request->has('is_active') ? 1 : 0;

            // Create dokter
            $dokter = Dokter::create($validatedData);

            Log::info('Dokter Created:', ['dokter' => $dokter->toArray()]);

            return redirect()->route('dokters.index')
                ->with('success', 'Data dokter berhasil ditambahkan!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation Error:', $e->errors());
            return back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Store Dokter Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Dokter $dokter)
    {
        // Load relationships if needed
        $dokter->load(['jadwalDokters.poli']);

        Log::info('Show Dokter Access:', [
            'dokter_id' => $dokter->id,
            'dokter_data' => $dokter->toArray()
        ]);

        return view('dokters.show', compact('dokter'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Dokter $dokter)
    {
        Log::info('Edit Dokter Access:', [
            'dokter_id' => $dokter->id,
            'dokter_data' => $dokter->toArray()
        ]);

        return view('dokters.edit', compact('dokter'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Dokter $dokter)
    {
        // Debug log
        Log::info('Update Dokter Request:', [
            'dokter_id' => $dokter->id,
            'request_data' => $request->all(),
            'method' => $request->method(),
            'url' => $request->url()
        ]);

        try {
            // Validasi
            $validatedData = $request->validate([
                'kode_dokter' => 'required|string|max:10|unique:dokters,kode_dokter,' . $dokter->id,
                'nama_dokter' => 'required|string|max:255',
                'spesialisasi' => 'required|string|max:255',
                'no_telepon' => 'nullable|string|max:15',
                'email' => 'nullable|email|max:255|unique:dokters,email,' . $dokter->id,
                'is_active' => 'nullable|boolean',
            ]);

            Log::info('Validated Data:', $validatedData);

            // Handle checkbox is_active
            $validatedData['is_active'] = $request->has('is_active') ? 1 : 0;

            // Update data
            $updated = $dokter->update($validatedData);

            Log::info('Update Result:', [
                'success' => $updated,
                'updated_data' => $dokter->fresh()->toArray()
            ]);

            if ($updated) {
                return redirect()->route('dokters.index')
                    ->with('success', 'Data dokter berhasil diupdate!');
            } else {
                return back()
                    ->with('error', 'Gagal mengupdate data dokter.')
                    ->withInput();
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation Error:', $e->errors());
            return back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Update Dokter Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Dokter $dokter)
    {
        // dd('This is the destroy method. You can implement your logic here.');
        try {
            // Check if dokter has related data
            $hasKunjungan = $dokter->kunjungans()->exists();
            $hasJadwal = $dokter->jadwalDokters()->exists();

            if ($hasKunjungan || $hasJadwal) {
                return back()->with('error', 'Tidak dapat menghapus dokter. Masih ada data kunjungan atau jadwal yang terkait.');
            }

            $dokter->delete();

            Log::info('Dokter Deleted:', ['dokter_id' => $dokter->id]);

            return redirect()->route('dokters.index')
                ->with('success', 'Data dokter berhasil dihapus!');

        } catch (\Exception $e) {
            Log::error('Delete Dokter Error:', [
                'message' => $e->getMessage(),
                'dokter_id' => $dokter->id
            ]);

            return back()->with('error', 'Terjadi kesalahan saat menghapus data dokter.');
        }
    }

    /**
     * Bulk actions for multiple dokters
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,activate,deactivate',
            'selected_ids' => 'required|array',
            'selected_ids.*' => 'exists:dokters,id'
        ]);

        $selectedIds = $request->selected_ids;
        $action = $request->action;

        try {
            switch ($action) {
                case 'delete':
                    Dokter::whereIn('id', $selectedIds)->delete();
                    $message = 'Dokter yang dipilih berhasil dihapus.';
                    break;

                case 'activate':
                    Dokter::whereIn('id', $selectedIds)->update(['is_active' => 1]);
                    $message = 'Dokter yang dipilih berhasil diaktifkan.';
                    break;

                case 'deactivate':
                    Dokter::whereIn('id', $selectedIds)->update(['is_active' => 0]);
                    $message = 'Dokter yang dipilih berhasil dinonaktifkan.';
                    break;
            }

            return back()->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Bulk Action Error:', [
                'action' => $action,
                'selected_ids' => $selectedIds,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Terjadi kesalahan saat melakukan aksi bulk.');
        }
    }

    /**
     * Export dokters data
     */
    public function export(Request $request)
    {
        // Implementation for export functionality
        // You can use Laravel Excel or similar package

        $query = Dokter::query();

        // Apply same filters as index
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_dokter', 'like', "%{$search}%")
                  ->orWhere('kode_dokter', 'like', "%{$search}%")
                  ->orWhere('spesialisasi', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->status !== '') {
            $query->where('is_active', $request->status);
        }

        $dokters = $query->orderBy('nama_dokter')->get();

        // For now, return JSON (you can implement Excel export later)
        return response()->json([
            'data' => $dokters,
            'total' => $dokters->count(),
            'exported_at' => now()->format('Y-m-d H:i:s')
        ]);
    }

    /**
     * Get jadwal for specific dokter
     */
    public function jadwalDokters(Dokter $dokter)
    {
        $jadwals = $dokter->jadwalDokters()
            ->with('poli')
            ->where('is_active', true)
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->get();

        return view('dokters.jadwal', compact('dokter', 'jadwals'));
    }

    /**
     * AJAX endpoint for Select2
     */
    public function select2(Request $request)
    {
        $query = Dokter::where('is_active', 1);

        if ($request->has('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('nama_dokter', 'like', "%{$search}%")
                  ->orWhere('kode_dokter', 'like', "%{$search}%");
            });
        }

        $dokters = $query->orderBy('nama_dokter')
            ->limit(20)
            ->get(['id', 'kode_dokter', 'nama_dokter', 'spesialisasi']);

        $results = $dokters->map(function ($dokter) {
            return [
                'id' => $dokter->id,
                'text' => $dokter->nama_dokter . ' (' . $dokter->kode_dokter . ') - ' . $dokter->spesialisasi
            ];
        });

        return response()->json([
            'results' => $results,
            'pagination' => ['more' => false]
        ]);
    }
public function getJadwal(Request $request, $dokterId)
{
    // dd($request->all(), $dokterId);
    try {
        // // Validasi parameter
        // $validator = Validator::make([
        //     'dokter_id' => $dokterId,
        //     'hari' => $request->get('hari'),
        //     'tanggal' => $request->get('tanggal')
        // ], [
        //     'dokter_id' => 'required|integer|exists:dokters,id',
        //     'hari' => 'nullable|string|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
        //     'tanggal' => 'nullable|date_format:Y-m-d'
        // ], [
        //     'dokter_id.required' => 'ID Dokter diperlukan.',
        //     'dokter_id.integer' => 'ID Dokter harus berupa angka.',
        //     'dokter_id.exists' => 'Dokter tidak ditemukan.',
        //     'hari.in' => 'Hari tidak valid.',
        //     'tanggal.date_format' => 'Format tanggal tidak valid (YYYY-MM-DD).'
        // ]);

        // if ($validator->fails()) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Validasi gagal.',
        //         'errors' => $validator->errors()
        //     ], 400);
        // }

        $hari = $request->get('hari');
        $tanggal = $request->get('tanggal');

        // Cek apakah dokter aktif
        $dokter = Dokter::find($dokterId);
        if (!$dokter || !$dokter->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Dokter tidak aktif atau tidak ditemukan.',
                'data' => []
            ], 404);
        }

        // Build query
        $query = JadwalDokter::where('dokter_id', $dokterId)
                            ->where('is_active', true); // Hanya jadwal yang aktif

        // Filter berdasarkan hari jika ada
        if ($hari) {
            $query->where('hari', $hari);
        }

        // Filter berdasarkan tanggal jika ada (untuk mengecek hari dari tanggal)
        if ($tanggal && !$hari) {
            try {
                $hariDariTanggal = Carbon::createFromFormat('Y-m-d', $tanggal)->locale('id')->isoFormat('dddd');
                // Mapping hari Indonesia
                $hariMapping = [
                    'Senin' => 'Senin',
                    'Selasa' => 'Selasa',
                    'Rabu' => 'Rabu',
                    'Kamis' => 'Kamis',
                    'Jumat' => 'Jumat',
                    'Sabtu' => 'Sabtu',
                    'Minggu' => 'Minggu'
                ];

                // Alternatif jika locale tidak bekerja
                $dayOfWeek = Carbon::createFromFormat('Y-m-d', $tanggal)->dayOfWeek;
                $dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                $hariDariTanggal = $dayNames[$dayOfWeek];

                $query->where('hari', $hariDariTanggal);
            } catch (\Exception $e) {
                Log::warning('Error parsing tanggal:', ['tanggal' => $tanggal, 'error' => $e->getMessage()]);
            }
        }

        // Ambil data dengan relasi
        $jadwals = $query->with(['poli', 'dokter'])
                        ->orderBy('jam_mulai')
                        ->get();

        // Check jika tidak ada jadwal
        if ($jadwals->isEmpty()) {
            return response()->json([
                'success' => true,
                'message' => 'Tidak ada jadwal ditemukan.',
                'data' => []
            ]);
        }

        // Format data response
        $formattedJadwals = $jadwals->map(function($jadwal) use ($tanggal) {
            // Hitung kuota jika tanggal diberikan
            $kuotaInfo = null;
            if ($tanggal) {
                $kuotaTerpakai = $jadwal->getKuotaTerpakai($tanggal);
                $kuotaTersisa = $jadwal->getKuotaTersisa($tanggal);
                $kuotaInfo = [
                    'kuota_total' => $jadwal->kuota_pasien,
                    'kuota_terpakai' => $kuotaTerpakai,
                    'kuota_tersisa' => $kuotaTersisa,
                    'is_available' => $kuotaTersisa > 0
                ];
            }

            return [
                'id' => $jadwal->id,
                'hari' => $jadwal->hari,
                'jam_mulai' => $jadwal->jam_mulai_formatted ?? Carbon::createFromFormat('H:i:s', $jadwal->jam_mulai)->format('H:i'),
                'jam_selesai' => $jadwal->jam_selesai_formatted ?? Carbon::createFromFormat('H:i:s', $jadwal->jam_selesai)->format('H:i'),
                'jam_mulai_full' => $jadwal->jam_mulai, // Format lengkap untuk keperluan lain
                'jam_selesai_full' => $jadwal->jam_selesai,
                'kuota_pasien' => $jadwal->kuota_pasien,
                'kuota_info' => $kuotaInfo,
                'is_active' => $jadwal->is_active,
                'poli' => [
                    'id' => $jadwal->poli->id ?? null,
                    'nama_poli' => $jadwal->poli->nama_poli ?? 'Poli tidak tersedia',
                    'kode_poli' => $jadwal->poli->kode_poli ?? null
                ],
                'dokter' => [
                    'id' => $jadwal->dokter->id ?? null,
                    'nama_dokter' => $jadwal->dokter->nama_dokter ?? 'Dokter tidak tersedia',
                    'spesialisasi' => $jadwal->dokter->spesialisasi ?? null
                ]
            ];
        });

        // Log untuk debugging (opsional)
        Log::info('Get Jadwal Request:', [
            'dokter_id' => $dokterId,
            'hari' => $hari,
            'tanggal' => $tanggal,
            'jadwal_count' => $jadwals->count()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Jadwal berhasil ditemukan.',
            'data' => $formattedJadwals,
            'meta' => [
                'total' => $jadwals->count(),
                'dokter_id' => $dokterId,
                'hari' => $hari,
                'tanggal' => $tanggal
            ]
        ]);

    } catch (\Exception $e) {
        Log::error('Get Jadwal Error:', [
            'dokter_id' => $dokterId,
            'request' => $request->all(),
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan sistem.',
            'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
        ], 500);
    }
}
}
