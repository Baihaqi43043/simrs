<?php

namespace App\Http\Middleware;

use Closure;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle($request, Closure $next, $role)
    {
        // Check if user is authenticated
        if (!$request->user()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        $user = $request->user();

        // Check if user is active
        if (!$user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Account is inactive'
            ], 403);
        }

        // Admin can access everything
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Check specific role
        $allowedRoles = explode('|', $role);

        if (!in_array($user->role, $allowedRoles)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access. Required role: ' . $role,
                'user_role' => $user->role,
                'required_roles' => $allowedRoles
            ], 403);
        }

        return $next($request);
    }
}

// ============================================
// Step 8: routes/api.php
// ============================================

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Default Laravel route - bisa dihapus jika tidak perlu
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// SIMRS API Routes
Route::prefix('v1')->group(function () {

    // ============================================
    // PUBLIC ROUTES (No Authentication Required)
    // ============================================
    Route::post('login', 'Api\AuthController@login');

    // Health check endpoint
    Route::get('health', function () {
        return response()->json([
            'success' => true,
            'message' => 'SIMRS API is running',
            'timestamp' => now(),
            'version' => '1.0.0'
        ]);
    });

    // ============================================
    // PROTECTED ROUTES (Authentication Required)
    // ============================================
    Route::middleware('auth:api')->group(function () {

        // ============================================
        // AUTH MANAGEMENT ROUTES
        // ============================================
        Route::post('logout', 'Api\AuthController@logout');
        Route::get('profile', 'Api\AuthController@profile');
        Route::post('change-password', 'Api\AuthController@changePassword');

        // Admin only routes
        Route::middleware('role:admin')->group(function () {
            Route::get('users', 'Api\AuthController@users');
        });

        // ============================================
        // MASTER DATA ROUTES
        // Accessible by admin and pendaftaran
        // ============================================
        Route::middleware('role:admin|pendaftaran')->group(function () {

            // Poli Management
            Route::apiResource('polis', 'Api\PoliController');
            Route::get('polis/{poli}/jadwal-dokters', 'Api\PoliController@jadwalDokters');

            // Dokter Management
            Route::apiResource('dokters', 'Api\DokterController');
            Route::get('dokters/{dokter}/jadwal', 'Api\DokterController@jadwal');
            Route::get('dokters/{dokter}/kunjungans-today', 'Api\DokterController@kunjungansToday');

            // Pasien Management
            Route::apiResource('pasiens', 'Api\PasienController');
            Route::get('pasiens/search/nik/{nik}', 'Api\PasienController@searchByNik');
            Route::get('pasiens/search/no-rm/{no_rm}', 'Api\PasienController@searchByNoRm');
            Route::get('pasiens/{pasien}/riwayat-kunjungan', 'Api\PasienController@riwayatKunjungan');

            // Jadwal Dokter Management
            Route::apiResource('jadwal-dokters', 'Api\JadwalDokterController');
            Route::get('jadwal-dokters/available/{tanggal}', 'Api\JadwalDokterController@getAvailableSchedules');
            Route::get('jadwal-dokters/dokter/{dokter}/hari/{hari}', 'Api\JadwalDokterController@getByDokterHari');
        });

        // ============================================
        // KUNJUNGAN ROUTES
        // Accessible by admin, pendaftaran, and dokter
        // ============================================
        Route::middleware('role:admin|pendaftaran|dokter')->group(function () {

            Route::apiResource('kunjungans', 'Api\KunjunganController');

            // Kunjungan specific routes
            Route::get('kunjungans/today/all', 'Api\KunjunganController@todayAll');
            Route::get('kunjungans/today/by-poli/{poli}', 'Api\KunjunganController@todayByPoli');
            Route::get('kunjungans/today/by-dokter/{dokter}', 'Api\KunjunganController@todayByDokter');
            Route::patch('kunjungans/{kunjungan}/status', 'Api\KunjunganController@updateStatus');
            Route::get('kunjungans/{kunjungan}/antrian-info', 'Api\KunjunganController@antrianInfo');

            // Generate nomor antrian
            Route::post('kunjungans/generate-antrian', 'Api\KunjunganController@generateNomorAntrian');
        });

        // ============================================
        // MEDICAL RECORDS ROUTES
        // Accessible by admin and dokter only
        // ============================================
        Route::middleware('role:admin|dokter')->group(function () {

            // Tindakan Management
            Route::apiResource('tindakans', 'Api\TindakanController');
            Route::get('tindakans/kunjungan/{kunjungan}', 'Api\TindakanController@byKunjungan');
            Route::patch('tindakans/{tindakan}/status', 'Api\TindakanController@updateStatus');

            // Diagnosa Management
            Route::apiResource('diagnosas', 'Api\DiagnosaController');
            Route::get('diagnosas/kunjungan/{kunjungan}', 'Api\DiagnosaController@byKunjungan');
            Route::get('diagnosas/search/icd/{code}', 'Api\DiagnosaController@searchByICD');
        });

        // ============================================
        // REPORTS & PDF ROUTES
        // Accessible by all authenticated users
        // ============================================
        Route::prefix('reports')->group(function () {

            // PDF Reports
            Route::get('bukti-pendaftaran/{kunjungan}', 'Api\ReportController@buktiPendaftaran');
            Route::get('riwayat-kunjungan/{pasien}', 'Api\ReportController@riwayatKunjungan');
            Route::get('resume-medis/{kunjungan}', 'Api\ReportController@resumeMedis');

            // Statistics Reports (Admin only)
            Route::middleware('role:admin')->group(function () {
                Route::get('kunjungan-per-poli', 'Api\ReportController@kunjunganPerPoli');
                Route::get('pasien-tersering', 'Api\ReportController@pasienTersering');
                Route::get('dokter-performance', 'Api\ReportController@dokterPerformance');
                Route::get('revenue-summary', 'Api\ReportController@revenueSummary');
            });
        });

        // ============================================
        // UTILITY ROUTES
        // ============================================
        Route::prefix('utilities')->group(function () {

            // Generate nomor RM baru
            Route::post('generate-no-rm', 'Api\UtilityController@generateNoRM')
                ->middleware('role:admin|pendaftaran');

            // Get next queue number
            Route::get('next-queue/{poli}/{tanggal}', 'Api\UtilityController@getNextQueueNumber')
                ->middleware('role:admin|pendaftaran');

            // Validate schedule availability
            Route::post('validate-schedule', 'Api\UtilityController@validateSchedule')
                ->middleware('role:admin|pendaftaran');
        });

        // ============================================
        // DASHBOARD ROUTES
        // ============================================
        Route::prefix('dashboard')->group(function () {

            // General dashboard (all roles)
            Route::get('summary', 'Api\DashboardController@summary');

            // Role-specific dashboards
            Route::get('admin', 'Api\DashboardController@adminDashboard')
                ->middleware('role:admin');

            Route::get('dokter', 'Api\DashboardController@dokterDashboard')
                ->middleware('role:dokter');

            Route::get('pendaftaran', 'Api\DashboardController@pendaftaranDashboard')
                ->middleware('role:pendaftaran');
        });
    });
});

