<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Kunjungan;
use App\Pasien;
use App\Dokter;
use App\Poli;
use App\JadwalDokter;
use Carbon\Carbon;

class KunjunganController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Kunjungan::with(['pasien', 'dokter', 'poli', 'jadwalDokter']);

        // Filter by tanggal
        if ($request->has('tanggal_kunjungan') && $request->tanggal_kunjungan) {
            $query->whereDate('tanggal_kunjungan', $request->tanggal_kunjungan);
        }

        // Filter by date range
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('tanggal_kunjungan', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('tanggal_kunjungan', '<=', $request->end_date);
        }

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filter by dokter
        if ($request->has('dokter_id') && $request->dokter_id) {
            $query->where('dokter_id', $request->dokter_id);
        }

        // Filter by poli
        if ($request->has('poli_id') && $request->poli_id) {
            $query->where('poli_id', $request->poli_id);
        }

        // Filter by jenis kunjungan
        if ($request->has('jenis_kunjungan') && $request->jenis_kunjungan !== '') {
            $query->where('jenis_kunjungan', $request->jenis_kunjungan);
        }

        // Search by patient name or no_kunjungan
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_kunjungan', 'like', "%{$search}%")
                  ->orWhereHas('pasien', function ($subQ) use ($search) {
                      $subQ->where('nama', 'like', "%{$search}%")
                           ->orWhere('no_rm', 'like', "%{$search}%");
                  });
            });
        }

        $kunjungans = $query->orderBy('tanggal_kunjungan', 'desc')
                           ->orderBy('no_antrian')
                           ->paginate(15);

        // Get data for filters
        $dokters = Dokter::where('is_active', 1)->orderBy('nama_dokter')->get();
        $polis = Poli::where('is_active', 1)->orderBy('nama_poli')->get();

        return view('kunjungans.index', compact('kunjungans', 'dokters', 'polis'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $dokters = Dokter::where('is_active', 1)->orderBy('nama_dokter')->get();
        $polis = Poli::where('is_active', 1)->orderBy('nama_poli')->get();

        // Get selected pasien if from pasien detail
        $selectedPasien = null;
        if ($request->has('pasien_id')) {
            $selectedPasien = Pasien::find($request->pasien_id);
        }

        return view('kunjungans.create', compact('dokters', 'polis', 'selectedPasien'));
    }

    /**
     * Store a newly created resource in storage.
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
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
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
                    return redirect()->back()
                        ->with('error', 'Tanggal yang dipilih tidak sesuai dengan jadwal dokter')
                        ->withInput();
                }

                // Check quota availability
                if (!$jadwal->isKuotaAvailable($tanggal)) {
                    DB::rollBack();
                    return redirect()->back()
                        ->with('error', 'Kuota dokter sudah penuh untuk tanggal ini')
                        ->withInput();
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
                'created_by' => auth()->user()->id
            ]);

            DB::commit();

            return redirect()->route('kunjungans.show', $kunjungan->id)
                ->with('success', 'Kunjungan berhasil didaftarkan dengan nomor antrian: ' . $noAntrian);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Create Kunjungan Error: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Gagal mendaftarkan kunjungan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $kunjungan = Kunjungan::with([
            'pasien', 'dokter', 'poli', 'jadwalDokter',
            'tindakans', 'diagnosas', 'createdBy'
        ])->findOrFail($id);

        // Get queue information
        $antrianInfo = $this->getAntrianInfo($kunjungan);

        return view('kunjungans.show', compact('kunjungan', 'antrianInfo'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $kunjungan = Kunjungan::findOrFail($id);

        // Check if kunjungan can be updated
        if (!$kunjungan->canBeUpdated()) {
            return redirect()->back()
                ->with('error', 'Kunjungan tidak dapat diubah. Status saat ini: ' . $kunjungan->status);
        }

        $dokters = Dokter::where('is_active', 1)->orderBy('nama_dokter')->get();
        $polis = Poli::where('is_active', 1)->orderBy('nama_poli')->get();

        return view('kunjungans.edit', compact('kunjungan', 'dokters', 'polis'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $kunjungan = Kunjungan::findOrFail($id);

        // Check if kunjungan can be updated
        if (!$kunjungan->canBeUpdated()) {
            return redirect()->back()
                ->with('error', 'Kunjungan tidak dapat diubah. Status saat ini: ' . $kunjungan->status);
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
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
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

            DB::commit();

            return redirect()->route('kunjungans.show', $kunjungan->id)
                ->with('success', 'Data kunjungan berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update Kunjungan Error: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Gagal memperbarui kunjungan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $kunjungan = Kunjungan::findOrFail($id);

        // Check if kunjungan can be cancelled
        if (!$kunjungan->canBeCancelled()) {
            return redirect()->back()
                ->with('error', 'Kunjungan tidak dapat dibatalkan. Status saat ini: ' . $kunjungan->status);
        }

        // Check if kunjungan has related medical records
        $hasTindakan = $kunjungan->tindakans()->exists();
        $hasDiagnosa = $kunjungan->diagnosas()->exists();

        if ($hasTindakan || $hasDiagnosa) {
            return redirect()->back()
                ->with('error', 'Tidak dapat menghapus kunjungan. Masih ada catatan medis terkait (tindakan/diagnosa).');
        }

        $kunjungan->delete();

        return redirect()->route('kunjungans.index')
            ->with('success', 'Kunjungan berhasil dihapus');
    }

    /**
     * Update kunjungan status
     */
    public function updateStatus(Request $request, $id)
    {
        $kunjungan = Kunjungan::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:menunggu,sedang_dilayani,selesai,batal'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->with('error', 'Status tidak valid');
        }

        // Validate status transition
        $currentStatus = $kunjungan->status;
        $newStatus = $request->status;

        $validTransitions = [
            'menunggu' => ['sedang_dilayani', 'batal'],
            'sedang_dilayani' => ['selesai', 'menunggu'],
            'selesai' => [],
            'batal' => []
        ];

        if (!in_array($newStatus, $validTransitions[$currentStatus])) {
            return redirect()->back()
                ->with('error', "Tidak dapat mengubah status dari {$currentStatus} ke {$newStatus}");
        }

        $kunjungan->update(['status' => $newStatus]);

        return redirect()->back()
            ->with('success', 'Status kunjungan berhasil diperbarui');
    }


    /**
     * Show today's kunjungans
     */
    // Di KunjunganController@today
public function today(Request $request)
{
    $query = Kunjungan::whereDate('created_at', today())
                      ->with(['pasien', 'poli', 'dokter']);

    // Apply filters
    if ($request->poli) {
        $query->where('poli_id', $request->poli);
    }
    if ($request->dokter) {
        $query->where('dokter_id', $request->dokter);
    }
    if ($request->status) {
        $query->where('status', $request->status);
    }

    $kunjungans = $query->paginate(20);

    // Statistics
    $totalKunjungan = Kunjungan::whereDate('created_at', today())->count();
    $menunggu = Kunjungan::whereDate('created_at', today())->where('status', 'menunggu')->count();
    $selesai = Kunjungan::whereDate('created_at', today())->where('status', 'selesai')->count();
    $batal = Kunjungan::whereDate('created_at', today())->where('status', 'batal')->count();

    $polis = Poli::all();
    $dokters = Dokter::all();
    // dd($polis, $dokters);

    return view('kunjungans.today', compact(
        'kunjungans', 'totalKunjungan', 'menunggu', 'selesai', 'batal', 'polis', 'dokters'
    ));
}

    /**
     * Show antrian information
     */
    public function antrian($id)
    {
        $kunjungan = Kunjungan::with(['pasien', 'dokter', 'poli'])->findOrFail($id);
        $antrianInfo = $this->getAntrianInfo($kunjungan);

        return view('kunjungans.antrian', compact('kunjungan', 'antrianInfo'));
    }

    /**
     * Bulk actions
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,update_status',
            'selected_ids' => 'required|array',
            'selected_ids.*' => 'exists:kunjungans,id',
            'bulk_status' => 'required_if:action,update_status|in:menunggu,sedang_dilayani,selesai,batal'
        ]);

        $selectedIds = $request->selected_ids;
        $action = $request->action;

        try {
            switch ($action) {
                case 'delete':
                    $deletedCount = 0;
                    foreach ($selectedIds as $id) {
                        $kunjungan = Kunjungan::find($id);
                        if ($kunjungan && $kunjungan->canBeCancelled()) {
                            $kunjungan->delete();
                            $deletedCount++;
                        }
                    }
                    $message = "Berhasil menghapus {$deletedCount} kunjungan.";
                    break;

                case 'update_status':
                    $updatedCount = 0;
                    foreach ($selectedIds as $id) {
                        $kunjungan = Kunjungan::find($id);
                        if ($kunjungan) {
                            $kunjungan->update(['status' => $request->bulk_status]);
                            $updatedCount++;
                        }
                    }
                    $message = "Berhasil mengupdate status {$updatedCount} kunjungan.";
                    break;
            }

            return back()->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Bulk Action Error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat melakukan aksi bulk.');
        }
    }

    /**
     * Search pasien for kunjungan
     */
    // public function searchPasien(Request $request)
    // {
    //     $query = Pasien::query();

    //     if ($request->has('q')) {
    //         $search = $request->q;
    //         $query->where(function ($q) use ($search) {
    //             $q->where('nama', 'like', "%{$search}%")
    //               ->orWhere('no_rm', 'like', "%{$search}%")
    //               ->orWhere('nik', 'like', "%{$search}%");
    //         });
    //     }

    //     $pasiens = $query->limit(10)->get();

    //     $results = $pasiens->map(function ($pasien) {
    //         return [
    //             'id' => $pasien->id,
    //             'text' => $pasien->no_rm . ' - ' . $pasien->nama . ' (' . $pasien->nik . ')',
    //             'no_rm' => $pasien->no_rm,
    //             'nama' => $pasien->nama,
    //             'nik' => $pasien->nik
    //         ];
    //     });

    //     return response()->json($results);
    // }

    // Di KunjunganController.php
public function searchPasien(Request $request)
{
    $term = $request->get('q', ''); // Default empty string

    $query = Pasien::query();

    if (!empty($term)) {
        $query->where(function($q) use ($term) {
            $q->where('nama', 'LIKE', "%{$term}%")
              ->orWhere('no_rm', 'LIKE', "%{$term}%")
              ->orWhere('nik', 'LIKE', "%{$term}%");
        });
    }

    $pasiens = $query->orderBy('created_at', 'desc')
                   ->limit(10)
                   ->get()
                   ->map(function($pasien) {
                       return [
                           'id' => $pasien->id,
                           'no_rm' => $pasien->no_rm,
                           'nama' => $pasien->nama,
                           'nik' => $pasien->nik,
                           'jenis_kelamin' => $pasien->jenis_kelamin,
                           'tanggal_lahir' => $pasien->tanggal_lahir,
                           'alamat' => $pasien->alamat,
                           'telepon' => $pasien->telepon
                       ];
                   });

    return response()->json($pasiens);
}

// Di DokterController.php
public function getJadwal(Request $request, $dokterId)
{
    $hari = $request->get('hari');
    $tanggal = $request->get('tanggal');

    $query = JadwalDokter::where('dokter_id', $dokterId);

    if ($hari) {
        $query->where('hari', $hari);
    }

    if ($tanggal) {
        // Optional: filter berdasarkan tanggal aktif jika ada
        $query->whereDate('tanggal_berlaku_dari', '<=', $tanggal)
              ->whereDate('tanggal_berlaku_sampai', '>=', $tanggal);
    }

    $jadwals = $query->with(['poli', 'dokter'])
                    ->orderBy('jam_mulai')
                    ->get()
                    ->map(function($jadwal) {
                        return [
                            'id' => $jadwal->id,
                            'hari' => $jadwal->hari,
                            'jam_mulai' => $jadwal->jam_mulai,
                            'jam_selesai' => $jadwal->jam_selesai,
                            'poli' => [
                                'id' => $jadwal->poli->id ?? '',
                                'nama_poli' => $jadwal->poli->nama ?? $jadwal->poli->nama_poli ?? ''
                            ]
                        ];
                    });

    return response()->json([
        'success' => true,
        'data' => $jadwals
    ]);
}

    /**
     * Get antrian info for kunjungan
     */
    private function getAntrianInfo($kunjungan)
    {
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

        return [
            'no_antrian' => $currentPosition,
            'status' => $kunjungan->status,
            'before_me' => $beforeMe,
            'after_me' => $afterMe,
            'estimated_waiting' => $beforeMe * 15,
            'currently_served' => $currentlyServed ? [
                'no_antrian' => $currentlyServed->no_antrian,
                'pasien' => $currentlyServed->pasien->nama
            ] : null
        ];
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
    public function generateNomorAntrian(Request $request)
{
    $poliId = $request->get('poli_id');
    $tanggal = $request->get('tanggal');

    if (!$poliId || !$tanggal) {
        return response()->json([
            'success' => false,
            'message' => 'Poli ID dan tanggal harus diisi'
        ]);
    }

    // Hitung nomor antrian berdasarkan jumlah kunjungan hari itu di poli tersebut
    $lastAntrian = \App\Kunjungan::where('poli_id', $poliId)
                                       ->whereDate('tanggal_kunjungan', $tanggal)
                                       ->count();

    $nomorAntrian = str_pad($lastAntrian + 1, 3, '0', STR_PAD_LEFT);

    $poli = \App\Poli::find($poliId);

    return response()->json([
        'success' => true,
        'data' => [
            'no_antrian' => $nomorAntrian,
            'poli_nama' => $poli->nama ?? $poli->nama_poli ?? '',
            'tanggal' => $tanggal
        ]
    ]);
}

private function generateNoAntrian($poliId, $tanggal)
    {
        $lastAntrian = Kunjungan::where('poli_id', $poliId)
            ->whereDate('tanggal_kunjungan', $tanggal)
            ->max('no_antrian');

        return ($lastAntrian ?? 0) + 1;
    }
}
