<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Poli;

class PoliController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // dd('This is the Poli index page. You can implement your logic here.');
        try {
            $query = Poli::query();

            // Search functionality
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('nama_poli', 'like', "%{$search}%")
                      ->orWhere('kode_poli', 'like', "%{$search}%");
                });
            }

            // Filter by status
            if ($request->has('status') && $request->status !== '') {
                $query->where('is_active', (bool) $request->status);
            }

            // Pagination
            $perPage = $request->get('per_page', 15);
            $polis = $query->orderBy('nama_poli')->paginate($perPage);

            // Add today's visit count for each poli (if relation exists)
            foreach ($polis as $poli) {
                $poli->total_kunjungan_today = $poli->getTotalKunjunganToday();
            }

            return view('polis.index', compact('polis'));

        } catch (\Exception $e) {
            Log::error('Web Poli Index Error: ' . $e->getMessage());
            return view('polis.index', ['polis' => collect()])
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('polis.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode_poli' => 'required|string|max:10|unique:polis,kode_poli',
            'nama_poli' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
        ], [
            'kode_poli.required' => 'Kode poli harus diisi',
            'kode_poli.max' => 'Kode poli maksimal 10 karakter',
            'kode_poli.unique' => 'Kode poli sudah digunakan',
            'nama_poli.required' => 'Nama poli harus diisi',
            'nama_poli.max' => 'Nama poli maksimal 255 karakter',
        ]);

        try {
            $poli = Poli::create([
                'kode_poli' => strtoupper($request->kode_poli),
                'nama_poli' => $request->nama_poli,
                'deskripsi' => $request->deskripsi,
                'is_active' => $request->has('is_active') ? true : false
            ]);

            Log::info('Poli created successfully: ' . $poli->kode_poli);

            return redirect()->route('polis.index')
                ->with('success', 'Poli "' . $poli->nama_poli . '" berhasil ditambahkan');

        } catch (\Exception $e) {
            Log::error('Web Poli Store Error: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'Gagal menambahkan poli: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $poli = Poli::with(['jadwalDokters'])->findOrFail($id);
            $poli->total_kunjungan_today = $poli->getTotalKunjunganToday();
        // dd($poli);

            return view('polis.show', compact('poli'));

        } catch (\Exception $e) {
            Log::error('Web Poli Show Error: ' . $e->getMessage());
            return redirect()->route('polis.index')
                ->with('error', 'Poli tidak ditemukan');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $poli = Poli::findOrFail($id);
        // dd($poli);

            return view('polis.edit', compact('poli'));

        } catch (\Exception $e) {
            Log::error('Web Poli Edit Error: ' . $e->getMessage());
            return redirect()->route('polis.index')
                ->with('error', 'Poli tidak ditemukan');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // dd($request->all(),$id);
        $request->validate([
            'kode_poli' => 'required|string|max:10|unique:polis,kode_poli,' . $id,
            'nama_poli' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
        ], [
            'kode_poli.required' => 'Kode poli harus diisi',
            'kode_poli.max' => 'Kode poli maksimal 10 karakter',
            'kode_poli.unique' => 'Kode poli sudah digunakan',
            'nama_poli.required' => 'Nama poli harus diisi',
            'nama_poli.max' => 'Nama poli maksimal 255 karakter',
        ]);

        try {
            $poli = Poli::findOrFail($id);
            $oldName = $poli->nama_poli;

            $poli->update([
                'kode_poli' => strtoupper($request->kode_poli),
                'nama_poli' => $request->nama_poli,
                'deskripsi' => $request->deskripsi,
                'is_active' => $request->has('is_active') ? true : false
            ]);

            Log::info('Poli updated successfully: ' . $poli->kode_poli);

            return redirect()->route('polis.index')
                ->with('success', 'Poli "' . $poli->nama_poli . '" berhasil diupdate');

        } catch (\Exception $e) {
            Log::error('Web Poli Update Error: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'Gagal mengupdate poli: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $poli = Poli::findOrFail($id);
            $name = $poli->nama_poli;

            // Safety check - prevent deletion if has related data
            if ($poli->kunjungans && $poli->kunjungans()->exists()) {
                return back()->with('error', 'Poli "' . $name . '" tidak dapat dihapus karena masih memiliki data kunjungan');
            }

            if ($poli->jadwalDokters && $poli->jadwalDokters()->exists()) {
                return back()->with('error', 'Poli "' . $name . '" tidak dapat dihapus karena masih memiliki jadwal dokter');
            }

            $poli->delete();

            Log::info('Poli deleted successfully: ' . $poli->kode_poli);

            return redirect()->route('polis.index')
                ->with('success', 'Poli "' . $name . '" berhasil dihapus');

        } catch (\Exception $e) {
            Log::error('Web Poli Destroy Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus poli: ' . $e->getMessage());
        }
    }

    /**
     * Show jadwal dokters for specific poli
     */
    public function jadwalDokters($id)
    {
        try {
            $poli = Poli::with(['jadwalDokters.dokter'])->findOrFail($id);
            $jadwals = $poli->jadwalDokters()->orderBy('hari')->orderBy('jam_mulai')->get();

            return view('polis.jadwal-dokters', compact('poli', 'jadwals'));

        } catch (\Exception $e) {
            Log::error('Web Poli Jadwal Dokters Error: ' . $e->getMessage());
            return redirect()->route('polis.index')
                ->with('error', 'Poli tidak ditemukan');
        }
    }

    /**
     * AJAX endpoint for Select2 dropdown
     */
    public function select2(Request $request)
    {
        try {
            $search = $request->get('q');

            $polis = Poli::active()
                ->when($search, function($query) use ($search) {
                    return $query->where('nama_poli', 'like', "%{$search}%")
                                ->orWhere('kode_poli', 'like', "%{$search}%");
                })
                ->orderBy('nama_poli')
                ->limit(10)
                ->get(['id', 'kode_poli', 'nama_poli']);

            $formatted = $polis->map(function($poli) {
                return [
                    'id' => $poli->id,
                    'text' => $poli->kode_poli . ' - ' . $poli->nama_poli
                ];
            });

            return response()->json([
                'results' => $formatted
            ]);

        } catch (\Exception $e) {
            Log::error('Web Poli Select2 Error: ' . $e->getMessage());
            return response()->json(['results' => []]);
        }
    }

    /**
     * Export polis to Excel/CSV
     */
    public function export(Request $request)
    {
        try {
            $query = Poli::query();

            // Apply same filters as index
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('nama_poli', 'like', "%{$search}%")
                      ->orWhere('kode_poli', 'like', "%{$search}%");
                });
            }

            if ($request->has('status') && $request->status !== '') {
                $query->where('is_active', (bool) $request->status);
            }

            $polis = $query->orderBy('nama_poli')->get();

            // Generate CSV
            $filename = 'data_poli_' . date('Y-m-d_H-i-s') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function() use ($polis) {
                $file = fopen('php://output', 'w');

                // Header CSV
                fputcsv($file, ['Kode Poli', 'Nama Poli', 'Deskripsi', 'Status', 'Dibuat', 'Diupdate']);

                // Data
                foreach ($polis as $poli) {
                    fputcsv($file, [
                        $poli->kode_poli,
                        $poli->nama_poli,
                        $poli->deskripsi ?? '-',
                        $poli->is_active ? 'Aktif' : 'Tidak Aktif',
                        $poli->created_at->format('d/m/Y H:i'),
                        $poli->updated_at->format('d/m/Y H:i'),
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            Log::error('Web Poli Export Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengexport data poli');
        }
    }

    /**
     * Bulk operations (activate/deactivate multiple polis)
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'selected_ids' => 'required|array|min:1',
            'selected_ids.*' => 'exists:polis,id'
        ], [
            'action.required' => 'Aksi harus dipilih',
            'selected_ids.required' => 'Pilih minimal 1 poli',
            'selected_ids.min' => 'Pilih minimal 1 poli',
        ]);

        try {
            $selectedIds = $request->selected_ids;
            $action = $request->action;
            $count = 0;

            switch ($action) {
                case 'activate':
                    $count = Poli::whereIn('id', $selectedIds)->update(['is_active' => true]);
                    $message = $count . ' poli berhasil diaktifkan';
                    break;

                case 'deactivate':
                    $count = Poli::whereIn('id', $selectedIds)->update(['is_active' => false]);
                    $message = $count . ' poli berhasil dinonaktifkan';
                    break;

                case 'delete':
                    // Check for related data before deletion
                    $polisWithRelations = Poli::whereIn('id', $selectedIds)
                        ->whereHas('kunjungans')
                        ->orWhereHas('jadwalDokters')
                        ->count();

                    if ($polisWithRelations > 0) {
                        return back()->with('error', 'Beberapa poli tidak dapat dihapus karena masih memiliki data terkait');
                    }

                    $count = Poli::whereIn('id', $selectedIds)->delete();
                    $message = $count . ' poli berhasil dihapus';
                    break;
            }

            Log::info('Bulk action performed: ' . $action . ' on ' . $count . ' polis');

            return redirect()->route('polis.index')->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Web Poli Bulk Action Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal melakukan aksi bulk: ' . $e->getMessage());
        }
    }
}
