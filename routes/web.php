<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Fixed & Clean Version
|--------------------------------------------------------------------------
*/

// ============================================
// PUBLIC ROUTES
// ============================================

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', 'Web\WebAuthController@showLoginForm')->name('login');
Route::post('/login', 'Web\WebAuthController@login');

// ============================================
// PROTECTED ROUTES
// ============================================

Route::middleware('token.auth')->group(function () {

    // ----------------------------------------
    // DASHBOARD & PROFILE
    // ----------------------------------------
    Route::get('/dashboard', 'Web\WebAuthController@dashboard')->name('dashboard');
    Route::get('/logout', 'Web\WebAuthController@logout')->name('logout');

    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', 'Web\WebAuthController@profile')->name('index');
        Route::put('/', 'Web\WebAuthController@updateProfile')->name('update');
        Route::post('/change-password', 'Web\WebAuthController@changePassword')->name('change-password');
    });

    // ----------------------------------------
    // POLI MANAGEMENT MODULE
    // ----------------------------------------
    Route::prefix('polis')->name('polis.')->group(function () {
        // Routes tanpa parameter terlebih dahulu
        Route::get('/', 'Web\PoliController@index')->name('index');
        Route::get('/create', 'Web\PoliController@create')->name('create');
        Route::post('/', 'Web\PoliController@store')->name('store');
        Route::post('/bulk-action', 'Web\PoliController@bulkAction')->name('bulk-action');

        // Routes dengan parameter
        Route::get('/{poli}', 'Web\PoliController@show')->name('show');
        Route::get('/{poli}/edit', 'Web\PoliController@edit')->name('edit');
        Route::put('/{poli}', 'Web\PoliController@update')->name('update');
        Route::delete('/{poli}', 'Web\PoliController@destroy')->name('destroy');
        Route::get('/{poli}/jadwal-dokters', 'Web\PoliController@jadwalDokters')->name('jadwal-dokters');
    });

    // Export route di luar prefix untuk menghindari konflik
    Route::get('/polis-export', 'Web\PoliController@export')->name('polis.export');

    // ----------------------------------------
    // DOKTER MANAGEMENT MODULE
    // ----------------------------------------
    Route::prefix('dokters')->name('dokters.')->group(function () {
        // Routes tanpa parameter terlebih dahulu
        Route::get('/', 'Web\DokterController@index')->name('index');
        Route::get('/create', 'Web\DokterController@create')->name('create');
        Route::post('/', 'Web\DokterController@store')->name('store');
        Route::post('/bulk-action', 'Web\DokterController@bulkAction')->name('bulk-action');

        // Routes dengan parameter
        Route::get('/{dokter}', 'Web\DokterController@show')->name('show');
        Route::get('/{dokter}/edit', 'Web\DokterController@edit')->name('edit');
        Route::put('/{dokter}', 'Web\DokterController@update')->name('update');
        Route::delete('/{dokter}', 'Web\DokterController@destroy')->name('destroy');
        Route::get('/{dokter}/jadwal-dokters', 'Web\DokterController@jadwalDokters')->name('jadwal-dokters');
        // FIXED: Route untuk API jadwal dokter
        Route::get('/{dokter}/jadwal', 'Web\DokterController@getJadwal')->name('jadwal');
    });

    // Export route di luar prefix
    Route::get('/dokters-export', 'Web\DokterController@export')->name('dokters.export');

    // ----------------------------------------
    // JADWAL DOKTER MODULE
    // ----------------------------------------
    Route::prefix('jadwal-dokters')->name('jadwal-dokters.')->group(function () {
        // Routes tanpa parameter terlebih dahulu
        Route::get('/', 'Web\JadwalDokterController@index')->name('index');
        Route::get('/create', 'Web\JadwalDokterController@create')->name('create');
        Route::get('/weekly/view', 'Web\JadwalDokterController@weekly')->name('weekly');
        Route::post('/', 'Web\JadwalDokterController@store')->name('store');

        // Routes dengan parameter
        Route::get('/{jadwalDokter}', 'Web\JadwalDokterController@show')->name('show');
        Route::get('/{jadwalDokter}/edit', 'Web\JadwalDokterController@edit')->name('edit');
        Route::put('/{jadwalDokter}', 'Web\JadwalDokterController@update')->name('update');
        Route::delete('/{jadwalDokter}', 'Web\JadwalDokterController@destroy')->name('destroy');
    });

    // ----------------------------------------
    // PASIEN MANAGEMENT MODULE
    // ----------------------------------------
    Route::prefix('pasiens')->name('pasiens.')->group(function () {
        // Routes tanpa parameter terlebih dahulu
        Route::get('/', 'Web\PasienController@index')->name('index');
        Route::get('/create', 'Web\PasienController@create')->name('create');
        Route::get('/search', 'Web\PasienController@search')->name('search');
        Route::get('/search/nik/{nik}', 'Web\PasienController@searchByNik')->name('search.nik');
        Route::get('/search/no-rm/{noRm}', 'Web\PasienController@searchByNoRm')->name('search.no-rm');
        Route::post('/', 'Web\PasienController@store')->name('store');
        Route::post('/bulk-action', 'Web\PasienController@bulkAction')->name('bulk-action');

        // Routes dengan parameter
        Route::get('/{pasien}', 'Web\PasienController@show')->name('show');
        Route::get('/{pasien}/edit', 'Web\PasienController@edit')->name('edit');
        Route::put('/{pasien}', 'Web\PasienController@update')->name('update');
        Route::delete('/{pasien}', 'Web\PasienController@destroy')->name('destroy');
        Route::get('/{pasien}/riwayat-kunjungan', 'Web\PasienController@riwayatKunjungan')->name('riwayat-kunjungan');
        Route::get('/{pasien}/check-history', 'Web\PasienController@checkHistory')->name('check-history');
    });

    // ----------------------------------------
    // KUNJUNGAN MANAGEMENT MODULE
    // ----------------------------------------
    Route::prefix('kunjungans')->name('kunjungans.')->group(function () {
        // Routes tanpa parameter terlebih dahulu (FIXED ORDER)
        Route::get('/', 'Web\KunjunganController@index')->name('index');
        Route::get('/today', 'Web\KunjunganController@today')->name('today');
        Route::get('/antrian', 'Web\KunjunganController@antrian')->name('antrian');
        Route::get('/create', 'Web\KunjunganController@create')->name('create');
        Route::get('/search-pasien', 'Web\KunjunganController@searchPasien')->name('search-pasien');
        Route::get('/generate-nomor-antrian', 'Web\KunjunganController@generateNomorAntrian')->name('generate-nomor-antrian');
        Route::post('/', 'Web\KunjunganController@store')->name('store');
        Route::patch('/{kunjungan}/update-status', 'Web\KunjunganController@updateStatus')->name('updateStatus');
        Route::post('/bulk-action', 'Web\KunjunganController@bulkAction')->name('bulk-action');

        // Routes dengan parameter terakhir
        Route::get('/{kunjungan}', 'Web\KunjunganController@show')->name('show');
        Route::get('/{kunjungan}/edit', 'Web\KunjunganController@edit')->name('edit');
        Route::put('/{kunjungan}', 'Web\KunjunganController@update')->name('update');
        Route::delete('/{kunjungan}', 'Web\KunjunganController@destroy')->name('destroy');

          // === TINDAKAN ROUTES ===
        Route::get('/{kunjungan}/tindakan', 'Web\TindakanController@index')->name('tindakan.index');
        Route::get('/{kunjungan}/tindakan/create', 'Web\TindakanController@create')->name('tindakan.create');
        Route::post('/{kunjungan}/tindakan', 'Web\TindakanController@store')->name('tindakan.store');
        Route::get('/{kunjungan}/tindakan/{tindakan}/edit', 'Web\TindakanController@edit')->name('tindakan.edit');
        Route::put('/{kunjungan}/tindakan/{tindakan}', 'Web\TindakanController@update')->name('tindakan.update');
        Route::delete('/{kunjungan}/tindakan/{tindakan}', 'Web\TindakanController@destroy')->name('tindakan.destroy');

        // === DIAGNOSA ROUTES ===
        Route::get('/{kunjungan}/diagnosa', 'Web\DiagnosaController@index')->name('diagnosa.index');
        Route::get('/{kunjungan}/diagnosa/create', 'Web\DiagnosaController@create')->name('diagnosa.create');
        Route::post('/{kunjungan}/diagnosa', 'Web\DiagnosaController@store')->name('diagnosa.store');
        Route::get('/{kunjungan}/diagnosa/{diagnosa}/edit', 'Web\DiagnosaController@edit')->name('diagnosa.edit');
        Route::put('/{kunjungan}/diagnosa/{diagnosa}', 'Web\DiagnosaController@update')->name('diagnosa.update');
        Route::delete('/{kunjungan}/diagnosa/{diagnosa}', 'Web\DiagnosaController@destroy')->name('diagnosa.destroy');
        Route::get('/{kunjungan}/print', 'Web\KunjunganController@print')->name('print');

        // === PELAYANAN GABUNGAN ===
    Route::get('/{kunjungan}/pelayanan', 'Web\KunjunganController@pelayanan')->name('pelayanan');
    Route::post('/{kunjungan}/pelayanan', 'Web\KunjunganController@storePelayanan')->name('storePelayanan');
    });

    // ----------------------------------------
    // TINDAKAN MANAGEMENT MODULE
    // ----------------------------------------
    Route::prefix('tindakans')->name('tindakans.')->group(function () {
        // Routes tanpa parameter terlebih dahulu
        Route::get('/', 'Web\TindakanController@indexAll')->name('index');
        Route::get('/search', 'Web\TindakanController@searchTindakan')->name('search');
        Route::post('/bulk-action', 'Web\TindakanController@bulkAction')->name('bulk-action');

        // Routes dengan parameter
        Route::post('/{tindakan}/update-status', 'Web\TindakanController@updateStatus')->name('update-status');
        Route::get('/{tindakan}', 'Web\TindakanController@show')->name('show');
    });

    // ----------------------------------------
    // DIAGNOSA MANAGEMENT MODULE
    // ----------------------------------------
    Route::prefix('diagnosas')->name('diagnosas.')->group(function () {
        // Routes tanpa parameter terlebih dahulu
        Route::get('/', 'Web\DiagnosaController@indexAll')->name('index');
        Route::get('/search-icd', 'Web\DiagnosaController@searchIcd')->name('search-icd');
        Route::post('/bulk-action', 'Web\DiagnosaController@bulkAction')->name('bulk-action');

        // Routes dengan parameter
        Route::get('/{diagnosa}', 'Web\DiagnosaController@show')->name('show');
    });

    // ----------------------------------------
    // API ROUTES (FIXED)
    // ----------------------------------------
    Route::prefix('api')->name('api.')->group(function () {
        // API untuk mendapatkan jadwal dokter
        Route::get('/dokters/{dokter}/jadwal', 'Web\DokterController@getJadwal')->name('dokters.jadwal');
    });

    // ----------------------------------------
    // AJAX ENDPOINTS (CLEANED UP)
    // ----------------------------------------
    Route::prefix('ajax')->name('ajax.')->group(function () {
        Route::get('/polis/select2', 'Web\PoliController@select2')->name('polis.select2');
        Route::get('/dokters/select2', 'Web\DokterController@select2')->name('dokters.select2');
        Route::get('/jadwal-dokters/select2', 'Web\JadwalDokterController@select2')->name('jadwal-dokters.select2');
        Route::get('/ajax/diagnosas/search-icd', 'Web\DiagnosaController@searchIcd')->name('ajax.diagnosas.search-icd');
    });

    // ----------------------------------------
    // PLACEHOLDER MODULES (COMING SOON)
    // ----------------------------------------

    // User Management Module
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', function() {
            return view('placeholder', [
                'title' => 'Manajemen User',
                'message' => 'Module User Management - Coming Soon',
                'icon' => 'fas fa-users-cog',
                'description' => 'Modul untuk mengelola user, role, dan permission sistem.'
            ]);
        })->name('index');
    });

    // Reports Module
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', function() {
            return view('placeholder', [
                'title' => 'Laporan',
                'message' => 'Module Laporan - Coming Soon',
                'icon' => 'fas fa-chart-bar',
                'description' => 'Modul untuk generate berbagai laporan sistem.'
            ]);
        })->name('index');
    });

    // Settings Module
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', function() {
            return view('placeholder', [
                'title' => 'Pengaturan Sistem',
                'message' => 'Module Settings - Coming Soon',
                'icon' => 'fas fa-cogs',
                'description' => 'Modul untuk konfigurasi dan pengaturan sistem.'
            ]);
        })->name('index');
    });
});
