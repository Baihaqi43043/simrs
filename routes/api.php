<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // ============================================
    // PUBLIC ROUTES (No Authentication Required)
    // ============================================

    // Health check endpoint
    Route::get('health', function () {
        return response()->json([
            'success' => true,
            'message' => 'SIMRS API is running',
            'timestamp' => now(),
            'version' => '1.0.0',
            'laravel_version' => app()->version()
        ]);
    });

    // Basic info
    Route::get('info', function () {
        return response()->json([
            'success' => true,
            'message' => 'SIMRS Hospital Management System API',
            'features' => [
                'Patient Registration',
                'Doctor Scheduling',
                'Medical Records',
                'Reports & PDF Generation'
            ]
        ]);
    });

    // Login endpoint (SINGLE - no duplicate)
    Route::post('login', 'Api\AuthController@login');

    // ============================================
    // PROTECTED ROUTES (Authentication Required)
    // ============================================
    Route::middleware('auth:api')->group(function () {

        // Auth management routes
        Route::get('profile', 'Api\AuthController@profile');
        Route::post('logout', 'Api\AuthController@logout');
        Route::post('change-password', 'Api\AuthController@changePassword');

        // Test authenticated endpoint
        Route::get('test-auth', function (Request $request) {
            $user = $request->user();
            return response()->json([
                'success' => true,
                'message' => 'Authentication working!',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ],
                'timestamp' => now()
            ]);
        });

        // Admin only routes
        Route::middleware('role:admin')->group(function () {
            Route::get('users', 'Api\AuthController@users');
            Route::get('test-admin', function () {
                return response()->json([
                    'success' => true,
                    'message' => 'Admin access working!',
                    'timestamp' => now()
                ]);
            });
        });

        // Master data routes (accessible by admin and pendaftaran)
        Route::middleware('role:admin|pendaftaran')->group(function () {

            // Poli routes
            Route::apiResource('polis', 'Api\PoliController');
            Route::get('polis/{poli}/jadwal-dokters', 'Api\PoliController@jadwalDokters');

            // Dokter routes
            Route::apiResource('dokters', 'Api\DokterController');
            Route::get('dokters/{dokter}/jadwal', 'Api\DokterController@jadwal');
            Route::get('dokters/{dokter}/kunjungans-today', 'Api\DokterController@kunjungansToday');

            // Pasien routes
            Route::apiResource('pasiens', 'Api\PasienController');
            Route::get('pasiens/search/nik/{nik}', 'Api\PasienController@searchByNik');
            Route::get('pasiens/search/no-rm/{no_rm}', 'Api\PasienController@searchByNoRm');
            Route::get('pasiens/{pasien}/riwayat-kunjungan', 'Api\PasienController@riwayatKunjungan');

            Route::apiResource('jadwal-dokters', 'Api\JadwalDokterController');
            Route::get('jadwal-dokters/available/{tanggal}', 'Api\JadwalDokterController@getAvailableSchedules');
            Route::get('jadwal-dokters/dokter/{dokter}/hari/{hari}', 'Api\JadwalDokterController@getByDokterHari');
            Route::get('jadwal-dokters/dokter/{dokter}/weekly', 'Api\JadwalDokterController@getWeeklySchedule');
            Route::post('jadwal-dokters/check-availability', 'Api\JadwalDokterController@checkAvailability');
            Route::get('jadwal-dokters/statistics', 'Api\JadwalDokterController@getStatistics');

            // Basic CRUD
            Route::apiResource('kunjungans', 'Api\KunjunganController');

            // Status management
            Route::patch('kunjungans/{kunjungan}/status', 'Api\KunjunganController@updateStatus');

            // Today's visits
            Route::get('kunjungans/today/all', 'Api\KunjunganController@todayAll');
            Route::get('kunjungans/today/by-poli/{poli}', 'Api\KunjunganController@todayByPoli');
            Route::get('kunjungans/today/by-dokter/{dokter}', 'Api\KunjunganController@todayByDokter');

            // Queue management
            Route::get('kunjungans/{kunjungan}/antrian-info', 'Api\KunjunganController@antrianInfo');
            Route::post('kunjungans/generate-antrian', 'Api\KunjunganController@generateNomorAntrian');
        });

        // Doctor access routes
        Route::middleware('role:admin|dokter')->group(function () {
            Route::get('test-doctor', function (Request $request) {
                return response()->json([
                    'success' => true,
                    'message' => 'Doctor access working!',
                    'user_role' => $request->user()->role,
                    'timestamp' => now()
                ]);
            });
        });
    });
});
