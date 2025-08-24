<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Kunjungan;
use App\Diagnosa;
use App\Dokter;
use App\Tindakan;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class DiagnosaController extends Controller
{
    public function index($kunjunganId)
    {
        $sessionUser = session('user');
        if (!$sessionUser) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        $kunjungan = Kunjungan::with(['pasien', 'dokter', 'poli'])->findOrFail($kunjunganId);
        $diagnosas = Diagnosa::where('kunjungan_id', $kunjunganId)
                             ->with(['dokter'])
                             ->orderBy('jenis_diagnosa', 'asc')
                             ->orderBy('created_at', 'desc')
                             ->get();

        return view('diagnosas.index', compact('kunjungan', 'diagnosas'));
    }

    public function create($kunjunganId)
    {
        $sessionUser = session('user');
        if (!$sessionUser) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        $kunjungan = Kunjungan::with(['pasien', 'dokter', 'poli'])->findOrFail($kunjunganId);
        $dokters = Dokter::where('is_active', true)->get();

        return view('diagnosas.create', compact('kunjungan', 'dokters'));
    }

    public function store(Request $request, $kunjunganId)
    {
        $sessionUser = session('user');
        if (!$sessionUser) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'jenis_diagnosa' => 'required|in:utama,sekunder',
            'kode_icd' => 'required|string|max:10',
            'nama_diagnosa' => 'required|string|max:500',
            'deskripsi' => 'nullable|string',
            'didiagnosa_oleh' => 'nullable|exists:dokters,id',
            'tanggal_diagnosa' => 'required|date'
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 400);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Validasi: Hanya boleh ada 1 diagnosa utama
        if ($request->jenis_diagnosa === 'utama') {
            $existingUtama = Diagnosa::where('kunjungan_id', $kunjunganId)
                                    ->where('jenis_diagnosa', 'utama')
                                    ->exists();

            if ($existingUtama) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Diagnosa utama sudah ada. Silakan edit yang sudah ada atau tambah sebagai diagnosa sekunder.'
                    ], 400);
                }

                return redirect()->back()
                               ->with('error', 'Diagnosa utama sudah ada. Silakan edit yang sudah ada atau tambah sebagai diagnosa sekunder.')
                               ->withInput();
            }
        }

        $kunjungan = Kunjungan::findOrFail($kunjunganId);

        try {
            $diagnosa = Diagnosa::create([
                'kunjungan_id' => $kunjunganId,
                'jenis_diagnosa' => $request->jenis_diagnosa,
                'kode_icd' => $request->kode_icd,
                'nama_diagnosa' => $request->nama_diagnosa,
                'deskripsi' => $request->deskripsi,
                'didiagnosa_oleh' => $request->didiagnosa_oleh,
                'tanggal_diagnosa' => $request->tanggal_diagnosa
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Diagnosa berhasil ditambahkan',
                    'data' => $diagnosa->load('dokter')
                ]);
            }

            return redirect()->route('kunjungans.diagnosa.index', $kunjunganId)
                           ->with('success', 'Diagnosa berhasil ditambahkan');

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menambahkan diagnosa: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                           ->with('error', 'Gagal menambahkan diagnosa: ' . $e->getMessage())
                           ->withInput();
        }
    }

    public function edit($kunjunganId, $diagnosaId)
    {
        $sessionUser = session('user');
        if (!$sessionUser) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        $kunjungan = Kunjungan::with(['pasien', 'dokter', 'poli'])->findOrFail($kunjunganId);
        $diagnosa = Diagnosa::where('kunjungan_id', $kunjunganId)->findOrFail($diagnosaId);
        $dokters = Dokter::where('is_active', true)->get();

        return view('diagnosas.edit', compact('kunjungan', 'diagnosa', 'dokters'));
    }

    public function update(Request $request, $kunjunganId, $diagnosaId)
    {
        $sessionUser = session('user');
        if (!$sessionUser) {
            return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
        }

        $validator = Validator::make($request->all(), [
            'jenis_diagnosa' => 'required|in:utama,sekunder',
            'kode_icd' => 'required|string|max:10',
            'nama_diagnosa' => 'required|string|max:500',
            'deskripsi' => 'nullable|string',
            'didiagnosa_oleh' => 'nullable|exists:dokters,id',
            'tanggal_diagnosa' => 'required|date'
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 400);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $diagnosa = Diagnosa::where('kunjungan_id', $kunjunganId)->findOrFail($diagnosaId);

        // Validasi: Hanya boleh ada 1 diagnosa utama (kecuali yang sedang diedit)
        if ($request->jenis_diagnosa === 'utama') {
            $existingUtama = Diagnosa::where('kunjungan_id', $kunjunganId)
                                    ->where('jenis_diagnosa', 'utama')
                                    ->where('id', '!=', $diagnosaId)
                                    ->exists();

            if ($existingUtama) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Diagnosa utama sudah ada untuk kunjungan ini.'
                    ], 400);
                }

                return redirect()->back()
                               ->with('error', 'Diagnosa utama sudah ada untuk kunjungan ini.')
                               ->withInput();
            }
        }

        try {
            $diagnosa->update($request->except(['_token', '_method']));

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Diagnosa berhasil diupdate',
                    'data' => $diagnosa->fresh()->load('dokter')
                ]);
            }

            return redirect()->route('kunjungans.diagnosa.index', $kunjunganId)
                           ->with('success', 'Diagnosa berhasil diupdate');

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengupdate diagnosa: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Gagal mengupdate diagnosa: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($kunjunganId, $diagnosaId)
    {
        $sessionUser = session('user');
        if (!$sessionUser) {
            return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
        }

        $diagnosa = Diagnosa::where('kunjungan_id', $kunjunganId)->findOrFail($diagnosaId);

        try {
            $diagnosa->delete();

            return response()->json([
                'success' => true,
                'message' => 'Diagnosa berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus diagnosa: ' . $e->getMessage()
            ], 500);
        }
    }

    // AJAX method untuk search ICD codes
    public function searchIcd(Request $request)
    {
        $query = $request->get('q', '');

        // Sample ICD-10 codes - dalam implementasi nyata bisa dari database atau API
        $icdCodes = [
            ['kode' => 'A09.9', 'nama' => 'Gastroenteritis and colitis of unspecified origin'],
            ['kode' => 'B34.9', 'nama' => 'Viral infection, unspecified'],
            ['kode' => 'E11.9', 'nama' => 'Type 2 diabetes mellitus without complications'],
            ['kode' => 'I10', 'nama' => 'Essential (primary) hypertension'],
            ['kode' => 'J00', 'nama' => 'Acute nasopharyngitis [common cold]'],
            ['kode' => 'J06.9', 'nama' => 'Acute upper respiratory infection, unspecified'],
            ['kode' => 'K29.9', 'nama' => 'Gastroduodenitis, unspecified'],
            ['kode' => 'M79.3', 'nama' => 'Panniculitis, unspecified'],
            ['kode' => 'R50.9', 'nama' => 'Fever, unspecified'],
            ['kode' => 'Z00.0', 'nama' => 'General adult medical examination'],
        ];

        if ($query) {
            $icdCodes = array_filter($icdCodes, function($item) use ($query) {
                return stripos($item['nama'], $query) !== false || stripos($item['kode'], $query) !== false;
            });
        }

        return response()->json(array_values($icdCodes));
    }

    public function pelayanan($kunjunganId)
{
    $sessionUser = session('user');
    if (!$sessionUser) {
        return redirect()->route('login')->with('error', 'Please login first');
    }

    $kunjungan = Kunjungan::with(['pasien', 'dokter', 'poli'])->findOrFail($kunjunganId);

    // Cek apakah kunjungan bisa dilayani
    if (!in_array($kunjungan->status, ['menunggu', 'sedang_dilayani'])) {
        return redirect()->route('kunjungans.show', $kunjunganId)
                        ->with('error', 'Kunjungan tidak dapat dilayani. Status: ' . $kunjungan->status);
    }

    // Get existing tindakan dan diagnosa
    $tindakans = Tindakan::where('kunjungan_id', $kunjunganId)
                         ->with(['dokter'])
                         ->orderBy('created_at', 'desc')
                         ->get();

    $diagnosas = Diagnosa::where('kunjungan_id', $kunjunganId)
                         ->with(['dokter'])
                         ->orderBy('jenis_diagnosa', 'asc')
                         ->orderBy('created_at', 'desc')
                         ->get();

    $dokters = Dokter::where('is_active', true)->get();

    return view('kunjungans.pelayanan', compact('kunjungan', 'tindakans', 'diagnosas', 'dokters'));
}

/**
 * Store pelayanan (tindakan & diagnosa sekaligus)
 */
public function storePelayanan(Request $request, $kunjunganId)
{
    $sessionUser = session('user');
    if (!$sessionUser) {
        return response()->json([
            'success' => false,
            'message' => 'User not authenticated'
        ], 401);
    }

    $userId = $sessionUser['id'] ?? null;
    $kunjungan = Kunjungan::findOrFail($kunjunganId);

    // Validasi input
    $validator = Validator::make($request->all(), [
        // Tindakan validation
        'tindakans' => 'nullable|array',
        'tindakans.*.kode_tindakan' => 'required|string|max:20',
        'tindakans.*.nama_tindakan' => 'required|string|max:255',
        'tindakans.*.kategori_tindakan' => 'nullable|string|max:100',
        'tindakans.*.jumlah' => 'required|integer|min:1',
        'tindakans.*.tarif_satuan' => 'required|numeric|min:0',
        'tindakans.*.keterangan' => 'nullable|string',
        'tindakans.*.dikerjakan_oleh' => 'nullable|exists:dokters,id',
        'tindakans.*.status_tindakan' => 'required|in:rencana,sedang_dikerjakan,selesai,batal',

        // Diagnosa validation
        'diagnosas' => 'nullable|array',
        'diagnosas.*.jenis_diagnosa' => 'required|in:utama,sekunder',
        'diagnosas.*.kode_icd' => 'required|string|max:10',
        'diagnosas.*.nama_diagnosa' => 'required|string|max:500',
        'diagnosas.*.deskripsi' => 'nullable|string',
        'diagnosas.*.didiagnosa_oleh' => 'nullable|exists:dokters,id',

        // Catatan kunjungan
        'catatan_kunjungan' => 'nullable|string',
        'status_kunjungan' => 'required|in:menunggu,sedang_dilayani,selesai'
    ]);

    if ($validator->fails()) {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }
        return redirect()->back()->withErrors($validator)->withInput();
    }

    DB::beginTransaction();
    try {
        // Process Tindakan
        if ($request->has('tindakans') && is_array($request->tindakans)) {
            foreach ($request->tindakans as $tindakanData) {
                Tindakan::create([
                    'kunjungan_id' => $kunjunganId,
                    'kode_tindakan' => $tindakanData['kode_tindakan'],
                    'nama_tindakan' => $tindakanData['nama_tindakan'],
                    'kategori_tindakan' => $tindakanData['kategori_tindakan'] ?? null,
                    'jumlah' => $tindakanData['jumlah'],
                    'tarif_satuan' => $tindakanData['tarif_satuan'],
                    'keterangan' => $tindakanData['keterangan'] ?? null,
                    'dikerjakan_oleh' => $tindakanData['dikerjakan_oleh'] ?? null,
                    'tanggal_tindakan' => now(),
                    'status_tindakan' => $tindakanData['status_tindakan']
                ]);
            }
        }

        // Process Diagnosa
        if ($request->has('diagnosas') && is_array($request->diagnosas)) {
            // Validasi: Hanya boleh ada 1 diagnosa utama
            $utamaCount = 0;
            foreach ($request->diagnosas as $diagnosaData) {
                if ($diagnosaData['jenis_diagnosa'] === 'utama') {
                    $utamaCount++;
                }
            }

            $existingUtama = Diagnosa::where('kunjungan_id', $kunjunganId)
                                    ->where('jenis_diagnosa', 'utama')
                                    ->exists();

            if (($utamaCount > 1) || ($existingUtama && $utamaCount > 0)) {
                DB::rollBack();
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Hanya boleh ada satu diagnosa utama'
                    ], 400);
                }
                return redirect()->back()->with('error', 'Hanya boleh ada satu diagnosa utama')->withInput();
            }

            foreach ($request->diagnosas as $diagnosaData) {
                Diagnosa::create([
                    'kunjungan_id' => $kunjunganId,
                    'jenis_diagnosa' => $diagnosaData['jenis_diagnosa'],
                    'kode_icd' => $diagnosaData['kode_icd'],
                    'nama_diagnosa' => $diagnosaData['nama_diagnosa'],
                    'deskripsi' => $diagnosaData['deskripsi'] ?? null,
                    'didiagnosa_oleh' => $diagnosaData['didiagnosa_oleh'] ?? null,
                    'tanggal_diagnosa' => now()
                ]);
            }
        }

        // Update kunjungan
        $updateData = [];
        if ($request->catatan_kunjungan) {
            $updateData['catatan'] = $request->catatan_kunjungan;
        }
        if ($request->status_kunjungan) {
            $updateData['status'] = $request->status_kunjungan;
        }

        // Update total biaya
        $totalTindakan = Tindakan::where('kunjungan_id', $kunjunganId)->sum('total_biaya');
        $updateData['total_biaya'] = $totalTindakan;

        if (!empty($updateData)) {
            $kunjungan->update($updateData);
        }

        DB::commit();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Pelayanan berhasil disimpan',
                'data' => [
                    'kunjungan' => $kunjungan->fresh(),
                    'total_tindakan' => Tindakan::where('kunjungan_id', $kunjunganId)->count(),
                    'total_diagnosa' => Diagnosa::where('kunjungan_id', $kunjunganId)->count()
                ]
            ]);
        }

        return redirect()->route('kunjungans.show', $kunjunganId)
                        ->with('success', 'Pelayanan berhasil disimpan');

    } catch (\Exception $e) {
        DB::rollBack();

        \Log::error('Failed to store pelayanan', [
            'kunjungan_id' => $kunjunganId,
            'user_id' => $userId,
            'error' => $e->getMessage(),
            'request_data' => $request->all()
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan pelayanan: ' . $e->getMessage()
            ], 500);
        }

        return redirect()->back()
                        ->with('error', 'Gagal menyimpan pelayanan: ' . $e->getMessage())
                        ->withInput();
    }
}
}
