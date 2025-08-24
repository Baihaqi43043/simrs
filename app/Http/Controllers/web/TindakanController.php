<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Kunjungan;
use App\Tindakan;
use App\Dokter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TindakanController extends Controller
{
    public function index($kunjunganId)
    {
        // Cek session user
        $sessionUser = session('user');
        if (!$sessionUser) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        $kunjungan = Kunjungan::with(['pasien', 'dokter', 'poli'])->findOrFail($kunjunganId);
        $tindakans = Tindakan::where('kunjungan_id', $kunjunganId)
                             ->with(['dokter'])
                             ->orderBy('created_at', 'desc')
                             ->get();

        return view('tindakans.index', compact('kunjungan', 'tindakans'));
    }

    public function create($kunjunganId)
    {
        $sessionUser = session('user');
        if (!$sessionUser) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        $kunjungan = Kunjungan::with(['pasien', 'dokter', 'poli'])->findOrFail($kunjunganId);
        $dokters = Dokter::where('is_active', true)->get();

        return view('tindakans.create', compact('kunjungan', 'dokters'));
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

        $userId = $sessionUser['id'] ?? null;

        $validator = Validator::make($request->all(), [
            'kode_tindakan' => 'required|string|max:20',
            'nama_tindakan' => 'required|string|max:255',
            'kategori_tindakan' => 'nullable|string|max:100',
            'jumlah' => 'required|integer|min:1',
            'tarif_satuan' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
            'dikerjakan_oleh' => 'nullable|exists:dokters,id',
            'tanggal_tindakan' => 'required|date',
            'status_tindakan' => 'required|in:rencana,sedang_dikerjakan,selesai,batal'
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

        $kunjungan = Kunjungan::findOrFail($kunjunganId);

        DB::beginTransaction();
        try {
            $tindakan = Tindakan::create([
                'kunjungan_id' => $kunjunganId,
                'kode_tindakan' => $request->kode_tindakan,
                'nama_tindakan' => $request->nama_tindakan,
                'kategori_tindakan' => $request->kategori_tindakan,
                'jumlah' => $request->jumlah,
                'tarif_satuan' => $request->tarif_satuan,
                'keterangan' => $request->keterangan,
                'dikerjakan_oleh' => $request->dikerjakan_oleh,
                'tanggal_tindakan' => $request->tanggal_tindakan,
                'status_tindakan' => $request->status_tindakan
            ]);

            // Update total biaya kunjungan
            $totalTindakan = Tindakan::where('kunjungan_id', $kunjunganId)->sum('total_biaya');
            $kunjungan->update(['total_biaya' => $totalTindakan]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tindakan berhasil ditambahkan',
                    'data' => $tindakan->load('dokter')
                ]);
            }

            return redirect()->route('kunjungans.tindakan.index', $kunjunganId)
                           ->with('success', 'Tindakan berhasil ditambahkan');

        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menambahkan tindakan: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                           ->with('error', 'Gagal menambahkan tindakan: ' . $e->getMessage())
                           ->withInput();
        }
    }

    public function edit($kunjunganId, $tindakanId)
    {
        $sessionUser = session('user');
        if (!$sessionUser) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        $kunjungan = Kunjungan::with(['pasien', 'dokter', 'poli'])->findOrFail($kunjunganId);
        $tindakan = Tindakan::where('kunjungan_id', $kunjunganId)->findOrFail($tindakanId);
        $dokters = Dokter::where('is_active', true)->get();

        return view('tindakans.edit', compact('kunjungan', 'tindakan', 'dokters'));
    }

    public function update(Request $request, $kunjunganId, $tindakanId)
    {
        // Similar to store method but for updating
        $sessionUser = session('user');
        if (!$sessionUser) {
            return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
        }

        $validator = Validator::make($request->all(), [
            'kode_tindakan' => 'required|string|max:20',
            'nama_tindakan' => 'required|string|max:255',
            'kategori_tindakan' => 'nullable|string|max:100',
            'jumlah' => 'required|integer|min:1',
            'tarif_satuan' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
            'dikerjakan_oleh' => 'nullable|exists:dokters,id',
            'tanggal_tindakan' => 'required|date',
            'status_tindakan' => 'required|in:rencana,sedang_dikerjakan,selesai,batal'
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 400);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $tindakan = Tindakan::where('kunjungan_id', $kunjunganId)->findOrFail($tindakanId);
        $kunjungan = Kunjungan::findOrFail($kunjunganId);

        DB::beginTransaction();
        try {
            $tindakan->update($request->except(['_token', '_method']));

            // Update total biaya kunjungan
            $totalTindakan = Tindakan::where('kunjungan_id', $kunjunganId)->sum('total_biaya');
            $kunjungan->update(['total_biaya' => $totalTindakan]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tindakan berhasil diupdate',
                    'data' => $tindakan->fresh()->load('dokter')
                ]);
            }

            return redirect()->route('kunjungans.tindakan.index', $kunjunganId)
                           ->with('success', 'Tindakan berhasil diupdate');

        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengupdate tindakan: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Gagal mengupdate tindakan: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($kunjunganId, $tindakanId)
    {
        $sessionUser = session('user');
        if (!$sessionUser) {
            return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
        }

        $tindakan = Tindakan::where('kunjungan_id', $kunjunganId)->findOrFail($tindakanId);
        $kunjungan = Kunjungan::findOrFail($kunjunganId);

        DB::beginTransaction();
        try {
            $tindakan->delete();

            // Update total biaya kunjungan
            $totalTindakan = Tindakan::where('kunjungan_id', $kunjunganId)->sum('total_biaya');
            $kunjungan->update(['total_biaya' => $totalTindakan]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tindakan berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus tindakan: ' . $e->getMessage()
            ], 500);
        }
    }

    // AJAX method untuk search tindakan
    public function searchTindakan(Request $request)
    {
        $query = $request->get('q', '');

        // Ini bisa dari master tindakan atau manual input
        $tindakans = [
            ['kode' => 'T001', 'nama' => 'Pemeriksaan Fisik Umum', 'kategori' => 'Pemeriksaan', 'tarif' => 50000],
            ['kode' => 'T002', 'nama' => 'Konsultasi Dokter', 'kategori' => 'Konsultasi', 'tarif' => 75000],
            ['kode' => 'T003', 'nama' => 'Injeksi Intramuskular', 'kategori' => 'Tindakan', 'tarif' => 25000],
            ['kode' => 'T004', 'nama' => 'Pemberian Infus', 'kategori' => 'Tindakan', 'tarif' => 100000],
            ['kode' => 'T005', 'nama' => 'Perawatan Luka', 'kategori' => 'Perawatan', 'tarif' => 35000],
        ];

        if ($query) {
            $tindakans = array_filter($tindakans, function($item) use ($query) {
                return stripos($item['nama'], $query) !== false || stripos($item['kode'], $query) !== false;
            });
        }

        return response()->json(array_values($tindakans));
    }

    // Update status tindakan
    public function updateStatus(Request $request, $tindakanId)
    {
        $sessionUser = session('user');
        if (!$sessionUser) {
            return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
        }

        $request->validate([
            'status_tindakan' => 'required|in:rencana,sedang_dikerjakan,selesai,batal'
        ]);

        $tindakan = Tindakan::findOrFail($tindakanId);

        try {
            $tindakan->update([
                'status_tindakan' => $request->status_tindakan
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status tindakan berhasil diubah',
                'data' => $tindakan
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status: ' . $e->getMessage()
            ], 500);
        }
    }
}
