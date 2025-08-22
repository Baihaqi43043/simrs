<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Dokter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
}
