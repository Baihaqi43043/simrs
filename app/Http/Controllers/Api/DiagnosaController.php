<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Diagnosa;
use App\Kunjungan;
use App\Dokter;
use Carbon\Carbon;

class DiagnosaController extends Controller
{
    /**
     * Display a listing of diagnosas
     */
    public function index(Request $request)
    {
        $query = Diagnosa::with(['kunjungan.pasien', 'didiagnosa']);

        // Filter by kunjungan
        if ($request->has('kunjungan_id')) {
            $query->where('kunjungan_id', $request->kunjungan_id);
        }

        // Filter by jenis diagnosa
        if ($request->has('jenis_diagnosa')) {
            $query->where('jenis_diagnosa', $request->jenis_diagnosa);
        }

        // Filter by dokter
        if ($request->has('didiagnosa_oleh')) {
            $query->where('didiagnosa_oleh', $request->didiagnosa_oleh);
        }

        // Filter by date range
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('tanggal_diagnosa', [$request->start_date, $request->end_date]);
        }

        // Filter by tanggal
        if ($request->has('tanggal_diagnosa')) {
            $query->whereDate('tanggal_diagnosa', $request->tanggal_diagnosa);
        }

        // Search by ICD code or nama diagnosa
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_icd', 'like', "%{$search}%")
                  ->orWhere('nama_diagnosa', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%")
                  ->orWhereHas('kunjungan.pasien', function ($subQ) use ($search) {
                      $subQ->where('nama', 'like', "%{$search}%")
                           ->orWhere('no_rm', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by ICD code pattern
        if ($request->has('icd_code')) {
            $query->where('kode_icd', 'like', "%{$request->icd_code}%");
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $diagnosas = $query->orderBy('tanggal_diagnosa', 'desc')
                          ->orderBy('jenis_diagnosa', 'asc') // utama first
                          ->orderBy('created_at', 'desc')
                          ->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Diagnosas retrieved successfully',
            'data' => $diagnosas->items(),
            'meta' => [
                'current_page' => $diagnosas->currentPage(),
                'last_page' => $diagnosas->lastPage(),
                'per_page' => $diagnosas->perPage(),
                'total' => $diagnosas->total(),
            ]
        ]);
    }

    /**
     * Store a newly created diagnosa
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kunjungan_id' => 'required|exists:kunjungans,id',
            'jenis_diagnosa' => 'required|in:utama,sekunder',
            'kode_icd' => 'required|string|max:10|regex:/^[A-Z]\d{2}(\.\d)?$/',
            'nama_diagnosa' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:1000',
            'didiagnosa_oleh' => 'required|exists:dokters,id',
            'tanggal_diagnosa' => 'nullable|date|after_or_equal:today'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 400);
        }

        DB::beginTransaction();

        try {
            // Check if kunjungan exists and can accept diagnosa
            $kunjungan = Kunjungan::find($request->kunjungan_id);

            if (!$kunjungan->canAddDiagnosa()) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot add diagnosa. Kunjungan status: ' . $kunjungan->status
                ], 400);
            }

            // Check if utama diagnosa already exists for this kunjungan
            if ($request->jenis_diagnosa === 'utama') {
                $existingUtama = Diagnosa::where('kunjungan_id', $request->kunjungan_id)
                    ->where('jenis_diagnosa', 'utama')
                    ->first();

                if ($existingUtama) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Diagnosa utama already exists for this kunjungan'
                    ], 400);
                }
            }

            // Check for duplicate ICD code in same kunjungan
            $existingIcd = Diagnosa::where('kunjungan_id', $request->kunjungan_id)
                ->where('kode_icd', $request->kode_icd)
                ->first();

            if ($existingIcd) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Diagnosa with this ICD code already exists for this kunjungan'
                ], 400);
            }

            // Create diagnosa
            $diagnosa = Diagnosa::create([
                'kunjungan_id' => $request->kunjungan_id,
                'jenis_diagnosa' => $request->jenis_diagnosa,
                'kode_icd' => strtoupper($request->kode_icd),
                'nama_diagnosa' => $request->nama_diagnosa,
                'deskripsi' => $request->deskripsi,
                'didiagnosa_oleh' => $request->didiagnosa_oleh,
                'tanggal_diagnosa' => $request->tanggal_diagnosa ?? now()
            ]);

            $diagnosa->load(['kunjungan.pasien', 'didiagnosa']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Diagnosa created successfully',
                'data' => $diagnosa
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create diagnosa: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified diagnosa
     */
    public function show($id)
    {
        $diagnosa = Diagnosa::with([
            'kunjungan.pasien',
            'kunjungan.dokter',
            'kunjungan.poli',
            'didiagnosa'
        ])->find($id);

        if (!$diagnosa) {
            return response()->json([
                'success' => false,
                'message' => 'Diagnosa not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Diagnosa retrieved successfully',
            'data' => $diagnosa
        ]);
    }

    /**
     * Update the specified diagnosa
     */
    public function update(Request $request, $id)
    {
        $diagnosa = Diagnosa::find($id);

        if (!$diagnosa) {
            return response()->json([
                'success' => false,
                'message' => 'Diagnosa not found'
            ], 404);
        }

        // Check if diagnosa can be updated
        if (!$diagnosa->canBeUpdated()) {
            return response()->json([
                'success' => false,
                'message' => 'Diagnosa cannot be updated. Kunjungan status: ' . $diagnosa->kunjungan->status
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'jenis_diagnosa' => 'sometimes|in:utama,sekunder',
            'kode_icd' => 'sometimes|string|max:10|regex:/^[A-Z]\d{2}(\.\d)?$/',
            'nama_diagnosa' => 'sometimes|string|max:255',
            'deskripsi' => 'nullable|string|max:1000',
            'didiagnosa_oleh' => 'sometimes|exists:dokters,id',
            'tanggal_diagnosa' => 'sometimes|date'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 400);
        }

        DB::beginTransaction();

        try {
            // Check if changing to utama when utama already exists
            if ($request->has('jenis_diagnosa') && $request->jenis_diagnosa === 'utama' && $diagnosa->jenis_diagnosa !== 'utama') {
                $existingUtama = Diagnosa::where('kunjungan_id', $diagnosa->kunjungan_id)
                    ->where('jenis_diagnosa', 'utama')
                    ->where('id', '!=', $id)
                    ->first();

                if ($existingUtama) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Diagnosa utama already exists for this kunjungan'
                    ], 400);
                }
            }

            // Check for duplicate ICD code if kode_icd is being updated
            if ($request->has('kode_icd') && $request->kode_icd !== $diagnosa->kode_icd) {
                $existingIcd = Diagnosa::where('kunjungan_id', $diagnosa->kunjungan_id)
                    ->where('kode_icd', strtoupper($request->kode_icd))
                    ->where('id', '!=', $id)
                    ->first();

                if ($existingIcd) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Diagnosa with this ICD code already exists for this kunjungan'
                    ], 400);
                }
            }

            // Update diagnosa
            $updateData = $request->only([
                'jenis_diagnosa', 'kode_icd', 'nama_diagnosa',
                'deskripsi', 'didiagnosa_oleh', 'tanggal_diagnosa'
            ]);

            // Uppercase ICD code
            if (isset($updateData['kode_icd'])) {
                $updateData['kode_icd'] = strtoupper($updateData['kode_icd']);
            }

            $diagnosa->update($updateData);
            $diagnosa->load(['kunjungan.pasien', 'didiagnosa']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Diagnosa updated successfully',
                'data' => $diagnosa
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update diagnosa: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified diagnosa
     */
    public function destroy($id)
    {
        $diagnosa = Diagnosa::find($id);

        if (!$diagnosa) {
            return response()->json([
                'success' => false,
                'message' => 'Diagnosa not found'
            ], 404);
        }

        // Check if diagnosa can be deleted
        if (!$diagnosa->canBeDeleted()) {
            return response()->json([
                'success' => false,
                'message' => 'Diagnosa cannot be deleted. Kunjungan status: ' . $diagnosa->kunjungan->status
            ], 400);
        }

        $diagnosa->delete();

        return response()->json([
            'success' => true,
            'message' => 'Diagnosa deleted successfully'
        ]);
    }

    /**
     * Get diagnosas by kunjungan
     */
    public function byKunjungan($kunjunganId)
    {
        $kunjungan = Kunjungan::with('pasien')->find($kunjunganId);

        if (!$kunjungan) {
            return response()->json([
                'success' => false,
                'message' => 'Kunjungan not found'
            ], 404);
        }

        $diagnosas = Diagnosa::with('didiagnosa')
            ->where('kunjungan_id', $kunjunganId)
            ->orderBy('jenis_diagnosa', 'asc') // utama first
            ->orderBy('tanggal_diagnosa')
            ->orderBy('created_at')
            ->get();

        $diagnosUtama = $diagnosas->where('jenis_diagnosa', 'utama')->first();
        $diagnosSekunder = $diagnosas->where('jenis_diagnosa', 'sekunder');

        return response()->json([
            'success' => true,
            'message' => 'Diagnosas by kunjungan retrieved successfully',
            'data' => [
                'kunjungan' => $kunjungan,
                'total_diagnosa' => $diagnosas->count(),
                'diagnosa_utama' => $diagnosUtama,
                'diagnosa_sekunder' => $diagnosSekunder->values(),
                'all_diagnosas' => $diagnosas
            ]
        ]);
    }

    /**
     * Get diagnosas by ICD category
     */
    public function byIcdCategory(Request $request, $category)
    {
        // ICD-10 categories (first letter)
        $validCategories = [
            'A', 'B', // Infectious diseases
            'C', 'D', // Neoplasms
            'E', // Endocrine diseases
            'F', // Mental disorders
            'G', // Nervous system
            'H', // Eye and ear
            'I', // Circulatory system
            'J', // Respiratory system
            'K', // Digestive system
            'L', // Skin diseases
            'M', // Musculoskeletal
            'N', // Genitourinary
            'O', 'P', 'Q', // Pregnancy, perinatal, congenital
            'R', // Symptoms and signs
            'S', 'T', // Injury and poisoning
            'V', 'W', 'X', 'Y', 'Z' // External causes
        ];

        $category = strtoupper($category);

        if (!in_array($category, $validCategories)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid ICD category'
            ], 400);
        }

        $query = Diagnosa::with(['kunjungan.pasien', 'didiagnosa'])
            ->where('kode_icd', 'like', $category . '%');

        // Filter by date if provided
        if ($request->has('tanggal')) {
            $query->whereDate('tanggal_diagnosa', $request->tanggal);
        }

        // Filter by jenis if provided
        if ($request->has('jenis')) {
            $query->where('jenis_diagnosa', $request->jenis);
        }

        $diagnosas = $query->orderBy('tanggal_diagnosa', 'desc')->get();

        return response()->json([
            'success' => true,
            'message' => 'Diagnosas by ICD category retrieved successfully',
            'data' => [
                'icd_category' => $category,
                'total_diagnosa' => $diagnosas->count(),
                'diagnosas' => $diagnosas
            ]
        ]);
    }

    /**
     * Get today's diagnosas
     */
    public function todayDiagnosas(Request $request)
    {
        $query = Diagnosa::with(['kunjungan.pasien', 'didiagnosa'])
            ->whereDate('tanggal_diagnosa', today());

        // Filter by jenis if provided
        if ($request->has('jenis')) {
            $query->where('jenis_diagnosa', $request->jenis);
        }

        $diagnosas = $query->orderBy('created_at')->get();

        // Group by jenis for summary
        $summary = [
            'utama' => $diagnosas->where('jenis_diagnosa', 'utama')->count(),
            'sekunder' => $diagnosas->where('jenis_diagnosa', 'sekunder')->count(),
        ];

        // Top ICD codes today
        $topIcdCodes = $diagnosas->groupBy('kode_icd')
            ->map(function ($group) {
                return [
                    'kode_icd' => $group->first()->kode_icd,
                    'nama_diagnosa' => $group->first()->nama_diagnosa,
                    'count' => $group->count()
                ];
            })
            ->sortByDesc('count')
            ->take(10)
            ->values();

        return response()->json([
            'success' => true,
            'message' => 'Today diagnosas retrieved successfully',
            'data' => [
                'tanggal' => today()->format('Y-m-d'),
                'total_diagnosa' => $diagnosas->count(),
                'summary' => $summary,
                'top_icd_codes' => $topIcdCodes,
                'diagnosas' => $diagnosas
            ]
        ]);
    }

    /**
     * Search ICD codes
     */
    public function searchIcd(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'search' => 'required|string|min:2'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 400);
        }

        $search = $request->search;

        // Search in existing diagnosas
        $results = Diagnosa::select('kode_icd', 'nama_diagnosa')
            ->where(function ($q) use ($search) {
                $q->where('kode_icd', 'like', "%{$search}%")
                  ->orWhere('nama_diagnosa', 'like', "%{$search}%");
            })
            ->groupBy('kode_icd', 'nama_diagnosa')
            ->orderBy('kode_icd')
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'ICD search results retrieved successfully',
            'data' => [
                'search_term' => $search,
                'total_results' => $results->count(),
                'results' => $results
            ]
        ]);
    }

    /**
     * Get diagnosa statistics
     */
    public function statistics(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        $query = Diagnosa::whereBetween('tanggal_diagnosa', [$startDate, $endDate]);

        $totalDiagnosas = $query->count();
        $diagnosUtama = $query->where('jenis_diagnosa', 'utama')->count();
        $diagnosSekunder = $query->where('jenis_diagnosa', 'sekunder')->count();

        // Top 10 most common diagnoses
        $topDiagnoses = Diagnosa::select('kode_icd', 'nama_diagnosa', DB::raw('count(*) as total'))
            ->whereBetween('tanggal_diagnosa', [$startDate, $endDate])
            ->groupBy('kode_icd', 'nama_diagnosa')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        // Diagnoses by ICD category
        $byCategoryQuery = Diagnosa::select(DB::raw('LEFT(kode_icd, 1) as category'), DB::raw('count(*) as total'))
            ->whereBetween('tanggal_diagnosa', [$startDate, $endDate])
            ->groupBy(DB::raw('LEFT(kode_icd, 1)'))
            ->orderBy('total', 'desc')
            ->get();

        // Daily trend
        $dailyTrend = Diagnosa::select(DB::raw('DATE(tanggal_diagnosa) as date'), DB::raw('count(*) as total'))
            ->whereBetween('tanggal_diagnosa', [$startDate, $endDate])
            ->groupBy(DB::raw('DATE(tanggal_diagnosa)'))
            ->orderBy('date')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Diagnosa statistics retrieved successfully',
            'data' => [
                'period' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ],
                'summary' => [
                    'total_diagnosas' => $totalDiagnosas,
                    'diagnosa_utama' => $diagnosUtama,
                    'diagnosa_sekunder' => $diagnosSekunder
                ],
                'top_diagnoses' => $topDiagnoses,
                'by_category' => $byCategoryQuery,
                'daily_trend' => $dailyTrend
            ]
        ]);
    }

    /**
     * Validate ICD code format
     */
    public function validateIcd(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_icd' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 400);
        }

        $kodeIcd = strtoupper($request->kode_icd);

        // ICD-10 pattern validation
        $isValid = preg_match('/^[A-Z]\d{2}(\.\d)?$/', $kodeIcd);

        $response = [
            'kode_icd' => $kodeIcd,
            'is_valid' => $isValid,
            'format' => 'ICD-10 (Letter + 2 digits + optional .digit)'
        ];

        if ($isValid) {
            // Get category info
            $category = substr($kodeIcd, 0, 1);
            $categoryNames = [
                'A' => 'Certain infectious and parasitic diseases (A00-B99)',
                'B' => 'Certain infectious and parasitic diseases (A00-B99)',
                'C' => 'Neoplasms (C00-D49)',
                'D' => 'Neoplasms / Blood disorders (D00-D89)',
                'E' => 'Endocrine, nutritional and metabolic diseases (E00-E89)',
                'F' => 'Mental and behavioural disorders (F00-F99)',
                'G' => 'Diseases of the nervous system (G00-G99)',
                'H' => 'Diseases of the eye/ear (H00-H95)',
                'I' => 'Diseases of the circulatory system (I00-I99)',
                'J' => 'Diseases of the respiratory system (J00-J99)',
                'K' => 'Diseases of the digestive system (K00-K95)',
                'L' => 'Diseases of the skin (L00-L99)',
                'M' => 'Diseases of the musculoskeletal system (M00-M99)',
                'N' => 'Diseases of the genitourinary system (N00-N99)',
                'O' => 'Pregnancy, childbirth and the puerperium (O00-O9A)',
                'P' => 'Certain conditions originating in the perinatal period (P00-P96)',
                'Q' => 'Congenital malformations (Q00-Q99)',
                'R' => 'Symptoms, signs and abnormal findings (R00-R99)',
                'S' => 'Injury, poisoning (S00-T88)',
                'T' => 'Injury, poisoning (S00-T88)',
                'V' => 'External causes of morbidity (V00-Y99)',
                'W' => 'External causes of morbidity (V00-Y99)',
                'X' => 'External causes of morbidity (V00-Y99)',
                'Y' => 'External causes of morbidity (V00-Y99)',
                'Z' => 'Factors influencing health status (Z00-Z99)'
            ];

            $response['category'] = $category;
            $response['category_description'] = $categoryNames[$category] ?? 'Unknown category';
        }

        return response()->json([
            'success' => true,
            'message' => 'ICD code validation completed',
            'data' => $response
        ]);
    }
}
