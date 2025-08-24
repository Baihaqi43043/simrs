<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\JadwalDokter;
use App\Dokter;
use App\Poli;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class JadwalDokterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // dd('This is the Jadwal Dokter index page. You can implement the logic to fetch and display the jadwal dokter here.');
        $query = JadwalDokter::with(['dokter', 'poli']);

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('dokter', function ($subQ) use ($search) {
                    $subQ->where('nama_dokter', 'like', "%{$search}%")
                         ->orWhere('kode_dokter', 'like', "%{$search}%");
                })->orWhereHas('poli', function ($subQ) use ($search) {
                    $subQ->where('nama_poli', 'like', "%{$search}%")
                         ->orWhere('kode_poli', 'like', "%{$search}%");
                });
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('is_active', $request->status);
        }

        // Filter by dokter
        if ($request->has('dokter_id') && $request->dokter_id) {
            $query->where('dokter_id', $request->dokter_id);
        }

        // Filter by poli
        if ($request->has('poli_id') && $request->poli_id) {
            $query->where('poli_id', $request->poli_id);
        }

        // Filter by hari
        if ($request->has('hari') && $request->hari) {
            $query->where('hari', $request->hari);
        }

        $jadwals = $query->orderBy('hari')
                        ->orderBy('jam_mulai')
                        ->paginate(15);

        // Get data for filters
        $dokters = Dokter::where('is_active', true)->orderBy('nama_dokter')->get();
        $polis = Poli::where('is_active', true)->orderBy('nama_poli')->get();

        return view('jadwal-dokters.index', compact('jadwals', 'dokters', 'polis'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $dokters = Dokter::where('is_active', true)->orderBy('nama_dokter')->get();
        $polis = Poli::where('is_active', true)->orderBy('nama_poli')->get();

        return view('jadwal-dokters.create', compact('dokters', 'polis'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    Log::info('Store Jadwal Dokter Request:', [
        'request_data' => $request->all(),
        'method' => $request->method(),
    ]);

    try {
        // Validasi
        $validatedData = $request->validate([
            'dokter_id' => 'required|exists:dokters,id',
            'poli_id' => 'required|exists:polis,id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu', // Perbaikan: Kapitalisasi
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'kuota_pasien' => 'required|integer|min:1|max:100', // Perbaikan: Max 100 sesuai view
            'is_active' => 'nullable|boolean',
        ], [
            // Custom error messages
            'dokter_id.required' => 'Dokter harus dipilih.',
            'dokter_id.exists' => 'Dokter yang dipilih tidak valid.',
            'poli_id.required' => 'Poli harus dipilih.',
            'poli_id.exists' => 'Poli yang dipilih tidak valid.',
            'hari.required' => 'Hari harus dipilih.',
            'hari.in' => 'Hari yang dipilih tidak valid.',
            'jam_mulai.required' => 'Jam mulai harus diisi.',
            'jam_mulai.date_format' => 'Format jam mulai tidak valid (HH:MM).',
            'jam_selesai.required' => 'Jam selesai harus diisi.',
            'jam_selesai.date_format' => 'Format jam selesai tidak valid (HH:MM).',
            'jam_selesai.after' => 'Jam selesai harus lebih besar dari jam mulai.',
            'kuota_pasien.required' => 'Kuota pasien harus diisi.',
            'kuota_pasien.integer' => 'Kuota pasien harus berupa angka.',
            'kuota_pasien.min' => 'Kuota pasien minimal 1.',
            'kuota_pasien.max' => 'Kuota pasien maksimal 100.',
        ]);

        // Handle checkbox is_active
        $validatedData['is_active'] = $request->has('is_active') ? true : false; // Perbaikan: boolean sebenarnya

        // Validasi dokter aktif
        $dokter = \App\Dokter::find($validatedData['dokter_id']);
        if (!$dokter || !$dokter->is_active) {
            return back()
                ->with('error', 'Dokter yang dipilih tidak aktif atau tidak ditemukan.')
                ->withInput();
        }

        // Validasi poli aktif
        $poli = \App\Poli::find($validatedData['poli_id']);
        if (!$poli || !$poli->is_active) {
            return back()
                ->with('error', 'Poli yang dipilih tidak aktif atau tidak ditemukan.')
                ->withInput();
        }

        // Validasi durasi minimal (contoh: minimal 1 jam)
        $jamMulai = \Carbon\Carbon::createFromFormat('H:i', $validatedData['jam_mulai']);
        $jamSelesai = \Carbon\Carbon::createFromFormat('H:i', $validatedData['jam_selesai']);
        $durasiMenit = $jamMulai->diffInMinutes($jamSelesai);

        if ($durasiMenit < 60) {
            return back()
                ->with('error', 'Durasi jadwal minimal 1 jam.')
                ->withInput();
        }

        // Perbaikan: Validasi maksimal durasi (contoh: maksimal 12 jam)
        if ($durasiMenit > 720) { // 12 jam = 720 menit
            return back()
                ->with('error', 'Durasi jadwal maksimal 12 jam.')
                ->withInput();
        }

        // Check for duplicate exact schedule (same doctor, day, time)
        $duplicateExact = JadwalDokter::where('dokter_id', $validatedData['dokter_id'])
            ->where('hari', $validatedData['hari'])
            ->where('jam_mulai', $validatedData['jam_mulai'])
            ->where('jam_selesai', $validatedData['jam_selesai'])
            ->exists();

        if ($duplicateExact) {
            return back()
                ->with('error', 'Jadwal yang sama persis sudah ada untuk dokter ini.')
                ->withInput();
        }

        // Perbaikan: Check for time conflicts - lebih akurat
        $conflict = JadwalDokter::where('dokter_id', $validatedData['dokter_id'])
            ->where('hari', $validatedData['hari'])
            ->where('is_active', true)
            ->where(function ($query) use ($validatedData) {
                // Case 1: New schedule starts during existing schedule
                $query->where(function ($q) use ($validatedData) {
                    $q->where('jam_mulai', '<=', $validatedData['jam_mulai'])
                      ->where('jam_selesai', '>', $validatedData['jam_mulai']);
                })
                // Case 2: New schedule ends during existing schedule
                ->orWhere(function ($q) use ($validatedData) {
                    $q->where('jam_mulai', '<', $validatedData['jam_selesai'])
                      ->where('jam_selesai', '>=', $validatedData['jam_selesai']);
                })
                // Case 3: New schedule completely contains existing schedule
                ->orWhere(function ($q) use ($validatedData) {
                    $q->where('jam_mulai', '>=', $validatedData['jam_mulai'])
                      ->where('jam_selesai', '<=', $validatedData['jam_selesai']);
                })
                // Case 4: Existing schedule completely contains new schedule
                ->orWhere(function ($q) use ($validatedData) {
                    $q->where('jam_mulai', '<=', $validatedData['jam_mulai'])
                      ->where('jam_selesai', '>=', $validatedData['jam_selesai']);
                });
            })
            ->exists();

        if ($conflict) {
            // Ambil jadwal yang konflik untuk info lebih detail
            $conflictSchedule = JadwalDokter::with(['dokter', 'poli'])
                ->where('dokter_id', $validatedData['dokter_id'])
                ->where('hari', $validatedData['hari'])
                ->where('is_active', true)
                ->where(function ($query) use ($validatedData) {
                    $query->where(function ($q) use ($validatedData) {
                        $q->where('jam_mulai', '<=', $validatedData['jam_mulai'])
                          ->where('jam_selesai', '>', $validatedData['jam_mulai']);
                    })
                    ->orWhere(function ($q) use ($validatedData) {
                        $q->where('jam_mulai', '<', $validatedData['jam_selesai'])
                          ->where('jam_selesai', '>=', $validatedData['jam_selesai']);
                    })
                    ->orWhere(function ($q) use ($validatedData) {
                        $q->where('jam_mulai', '>=', $validatedData['jam_mulai'])
                          ->where('jam_selesai', '<=', $validatedData['jam_selesai']);
                    })
                    ->orWhere(function ($q) use ($validatedData) {
                        $q->where('jam_mulai', '<=', $validatedData['jam_mulai'])
                          ->where('jam_selesai', '>=', $validatedData['jam_selesai']);
                    });
                })
                ->first();

            $errorMessage = 'Konflik jadwal! Dokter sudah memiliki jadwal pada waktu tersebut.';
            if ($conflictSchedule) {
                $errorMessage .= sprintf(
                    ' Jadwal konflik: %s - %s di %s.',
                    \Carbon\Carbon::createFromFormat('H:i:s', $conflictSchedule->jam_mulai)->format('H:i'),
                    \Carbon\Carbon::createFromFormat('H:i:s', $conflictSchedule->jam_selesai)->format('H:i'),
                    $conflictSchedule->poli->nama_poli ?? 'Poli tidak diketahui'
                );
            }

            return back()
                ->with('error', $errorMessage)
                ->withInput();
        }

        // Convert time format untuk database (tambahkan detik)
        $validatedData['jam_mulai'] = $validatedData['jam_mulai'] . ':00';
        $validatedData['jam_selesai'] = $validatedData['jam_selesai'] . ':00';

        // Create jadwal dokter
        $jadwal = JadwalDokter::create($validatedData);

        Log::info('Jadwal Dokter Created:', [
            'jadwal' => $jadwal->toArray(),
            'dokter' => $jadwal->dokter->nama_dokter ?? 'Unknown',
            'poli' => $jadwal->poli->nama_poli ?? 'Unknown'
        ]);

        // Success message dengan detail
        $successMessage = sprintf(
            'Jadwal dokter berhasil ditambahkan! %s - %s (%s) pada hari %s jam %s-%s.',
            $jadwal->dokter->nama_dokter ?? 'Dokter',
            $jadwal->poli->nama_poli ?? 'Poli',
            $jadwal->dokter->spesialisasi ?? 'Spesialisasi',
            $jadwal->hari,
            \Carbon\Carbon::createFromFormat('H:i:s', $jadwal->jam_mulai)->format('H:i'),
            \Carbon\Carbon::createFromFormat('H:i:s', $jadwal->jam_selesai)->format('H:i')
        );

        return redirect()->route('jadwal-dokters.index')
            ->with('success', $successMessage);

    } catch (\Illuminate\Validation\ValidationException $e) {
        Log::error('Validation Error:', [
            'errors' => $e->errors(),
            'request_data' => $request->all()
        ]);

        return back()
            ->withErrors($e->errors())
            ->withInput();

    } catch (\Exception $e) {
        Log::error('Store Jadwal Dokter Error:', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
            'request_data' => $request->all()
        ]);

        return back()
            ->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi atau hubungi administrator.')
            ->withInput();
    }
}

    /**
     * Display the specified resource.
     */
    public function show(JadwalDokter $jadwalDokter)
    {
        $jadwalDokter->load(['dokter', 'poli']);

        // Add today's quota info if applicable
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

        if ($jadwalDokter->hari === $dayMapping[$dayName]) {
            $jadwalDokter->is_today = true;
            // Add quota methods if exist in model
            if (method_exists($jadwalDokter, 'getKuotaTerpakai')) {
                $jadwalDokter->kuota_terpakai_today = $jadwalDokter->getKuotaTerpakai($today);
                $jadwalDokter->kuota_tersisa_today = $jadwalDokter->getKuotaTersisa($today);
            }
        } else {
            $jadwalDokter->is_today = false;
        }

        Log::info('Show Jadwal Dokter Access:', [
            'jadwal_id' => $jadwalDokter->id,
            'jadwal_data' => $jadwalDokter->toArray()
        ]);

        return view('jadwal-dokters.show', compact('jadwalDokter'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(JadwalDokter $jadwalDokter)
{
    $dokters = Dokter::where('is_active', true)->orderBy('nama_dokter')->get();
    $polis = Poli::where('is_active', true)->orderBy('nama_poli')->get();

    // Kirim sebagai 'dokter' bukan 'jadwalDokter'
    return view('jadwal-dokters.edit', compact('dokters', 'polis'), ['dokter' => $jadwalDokter]);
}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, JadwalDokter $jadwalDokter)
    {
        Log::info('Update Jadwal Dokter Request:', [
            'jadwal_id' => $jadwalDokter->id,
            'request_data' => $request->all(),
        ]);

        try {
            // Validasi
            $validatedData = $request->validate([
                'dokter_id' => 'required|exists:dokters,id',
                'poli_id' => 'required|exists:polis,id',
                'hari' => 'required|in:senin,selasa,rabu,kamis,jumat,sabtu,minggu',
                'jam_mulai' => 'required|date_format:H:i',
                'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
                'kuota_pasien' => 'required|integer|min:1|max:50',
                'is_active' => 'nullable|boolean',
            ]);

            // Handle checkbox is_active
            $validatedData['is_active'] = $request->has('is_active') ? 1 : 0;

            // Check for time conflicts (excluding current schedule)
            $conflict = JadwalDokter::where('dokter_id', $validatedData['dokter_id'])
                ->where('hari', $validatedData['hari'])
                ->where('is_active', true)
                ->where('id', '!=', $jadwalDokter->id)
                ->where(function ($query) use ($validatedData) {
                    $query->whereBetween('jam_mulai', [$validatedData['jam_mulai'], $validatedData['jam_selesai']])
                        ->orWhereBetween('jam_selesai', [$validatedData['jam_mulai'], $validatedData['jam_selesai']])
                        ->orWhere(function ($q) use ($validatedData) {
                            $q->where('jam_mulai', '<=', $validatedData['jam_mulai'])
                              ->where('jam_selesai', '>=', $validatedData['jam_selesai']);
                        });
                })
                ->exists();

            if ($conflict) {
                return back()
                    ->with('error', 'Konflik jadwal! Dokter sudah memiliki jadwal pada waktu tersebut.')
                    ->withInput();
            }

            // Update data
            $updated = $jadwalDokter->update($validatedData);

            Log::info('Update Result:', [
                'success' => $updated,
                'updated_data' => $jadwalDokter->fresh()->toArray()
            ]);

            if ($updated) {
                return redirect()->route('jadwal-dokters.index')
                    ->with('success', 'Jadwal dokter berhasil diupdate!');
            } else {
                return back()
                    ->with('error', 'Gagal mengupdate jadwal dokter.')
                    ->withInput();
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation Error:', $e->errors());
            return back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Update Jadwal Dokter Error:', [
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
    public function destroy(JadwalDokter $jadwalDokter)
    {
        try {
            // Check if jadwal has related kunjungan
            $hasKunjungan = $jadwalDokter->kunjungans()->exists();

            if ($hasKunjungan) {
                return back()->with('error', 'Tidak dapat menghapus jadwal. Masih ada data kunjungan yang terkait.');
            }

            $jadwalDokter->delete();

            Log::info('Jadwal Dokter Deleted:', ['jadwal_id' => $jadwalDokter->id]);

            return redirect()->route('jadwal-dokters.index')
                ->with('success', 'Jadwal dokter berhasil dihapus!');

        } catch (\Exception $e) {
            Log::error('Delete Jadwal Dokter Error:', [
                'message' => $e->getMessage(),
                'jadwal_id' => $jadwalDokter->id
            ]);

            return back()->with('error', 'Terjadi kesalahan saat menghapus jadwal dokter.');
        }
    }

    /**
     * Get weekly schedule for display
     */
    public function weekly(Request $request)
    {
        $dokterId = $request->get('dokter_id');
        $query = JadwalDokter::with(['dokter', 'poli'])->where('is_active', true);

        if ($dokterId) {
            $query->where('dokter_id', $dokterId);
        }

        $jadwals = $query->orderBy('hari')->orderBy('jam_mulai')->get();

        // Group by hari
        $weeklySchedule = $jadwals->groupBy('hari');

        $orderedDays = [
            'senin' => 'Senin',
            'selasa' => 'Selasa',
            'rabu' => 'Rabu',
            'kamis' => 'Kamis',
            'jumat' => 'Jumat',
            'sabtu' => 'Sabtu',
            'minggu' => 'Minggu'
        ];

        $dokters = Dokter::where('is_active', true)->orderBy('nama_dokter')->get();

        return view('jadwal-dokters.weekly', compact('weeklySchedule', 'orderedDays', 'dokters'));
    }

    /**
     * AJAX endpoint for Select2
     */
    public function select2(Request $request)
    {
        $query = JadwalDokter::with(['dokter', 'poli'])->where('is_active', 1);

        if ($request->has('q')) {
            $search = $request->q;
            $query->whereHas('dokter', function ($q) use ($search) {
                $q->where('nama_dokter', 'like', "%{$search}%");
            });
        }

        $jadwals = $query->orderBy('hari')
            ->orderBy('jam_mulai')
            ->limit(20)
            ->get();

        $results = $jadwals->map(function ($jadwal) {
            return [
                'id' => $jadwal->id,
                'text' => $jadwal->dokter->nama_dokter . ' - ' . $jadwal->poli->nama_poli . ' (' . ucfirst($jadwal->hari) . ', ' . $jadwal->jam_mulai . '-' . $jadwal->jam_selesai . ')'
            ];
        });

        return response()->json([
            'results' => $results,
            'pagination' => ['more' => false]
        ]);
    }
}
