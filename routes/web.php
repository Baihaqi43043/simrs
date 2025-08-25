<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Role-based Implementation
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
Route::get('/logout', 'Web\WebAuthController@logout')->name('logout');

// ============================================
// DEBUG ROUTES (HAPUS SETELAH TESTING)
// ============================================

Route::middleware('token.auth')->group(function () {
    Route::get('/debug-session', function() {
        return response()->json([
            'user' => session('user'),
            'user_role' => session('user.role'),
            'token' => session('token') ? 'EXISTS' : 'MISSING'
        ]);
    });

    Route::get('/test-admin', function() {
        return 'Admin Only - Access Granted! Role: ' . session('user.role');
    })->middleware('web.role:admin');

    Route::get('/test-dokter', function() {
        return 'Dokter Only - Access Granted! Role: ' . session('user.role');
    })->middleware('web.role:dokter');

    Route::get('/test-pendaftaran', function() {
        return 'Pendaftaran Only - Access Granted! Role: ' . session('user.role');
    })->middleware('web.role:pendaftaran');
});

// ============================================
// PROTECTED ROUTES
// ============================================

Route::middleware('token.auth')->group(function () {

    // ----------------------------------------
    // DASHBOARD & PROFILE - All authenticated users
    // ----------------------------------------
    Route::get('/dashboard', 'Web\WebAuthController@dashboard')->name('dashboard');

    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', 'Web\WebAuthController@profile')->name('index');
        Route::put('/', 'Web\WebAuthController@updateProfile')->name('update');
        Route::post('/change-password', 'Web\WebAuthController@changePassword')->name('change-password');
    });

    // ----------------------------------------
    // ADMIN ONLY MODULES
    // ----------------------------------------
    Route::middleware('web.role:admin')->group(function () {

        // POLI MANAGEMENT
        Route::prefix('polis')->name('polis.')->group(function () {
            Route::get('/', 'Web\PoliController@index')->name('index');
            Route::get('/create', 'Web\PoliController@create')->name('create');
            Route::post('/', 'Web\PoliController@store')->name('store');
            Route::post('/bulk-action', 'Web\PoliController@bulkAction')->name('bulk-action');
            Route::get('/{poli}', 'Web\PoliController@show')->name('show');
            Route::get('/{poli}/edit', 'Web\PoliController@edit')->name('edit');
            Route::put('/{poli}', 'Web\PoliController@update')->name('update');
            Route::delete('/{poli}', 'Web\PoliController@destroy')->name('destroy');
            Route::get('/{poli}/jadwal-dokters', 'Web\PoliController@jadwalDokters')->name('jadwal-dokters');
        });

        // DOKTER MANAGEMENT
        Route::prefix('dokters')->name('dokters.')->group(function () {
            Route::get('/', 'Web\DokterController@index')->name('index');
            Route::get('/create', 'Web\DokterController@create')->name('create');
            Route::post('/', 'Web\DokterController@store')->name('store');
            Route::post('/bulk-action', 'Web\DokterController@bulkAction')->name('bulk-action');
            Route::get('/{dokter}', 'Web\DokterController@show')->name('show');
            Route::get('/{dokter}/edit', 'Web\DokterController@edit')->name('edit');
            Route::put('/{dokter}', 'Web\DokterController@update')->name('update');
            Route::delete('/{dokter}', 'Web\DokterController@destroy')->name('destroy');
            Route::get('/{dokter}/jadwal-dokters', 'Web\DokterController@jadwalDokters')->name('jadwal-dokters');
            Route::get('/{dokter}/jadwal', 'Web\DokterController@getJadwal')->name('jadwal');
        });

        // JADWAL DOKTER MANAGEMENT
        Route::prefix('jadwal-dokters')->name('jadwal-dokters.')->group(function () {
            Route::get('/', 'Web\JadwalDokterController@index')->name('index');
            Route::get('/create', 'Web\JadwalDokterController@create')->name('create');
            Route::get('/weekly/view', 'Web\JadwalDokterController@weekly')->name('weekly');
            Route::post('/', 'Web\JadwalDokterController@store')->name('store');
            Route::get('/{jadwalDokter}', 'Web\JadwalDokterController@show')->name('show');
            Route::get('/{jadwalDokter}/edit', 'Web\JadwalDokterController@edit')->name('edit');
            Route::put('/{jadwalDokter}', 'Web\JadwalDokterController@update')->name('update');
            Route::delete('/{jadwalDokter}', 'Web\JadwalDokterController@destroy')->name('destroy');
        });
    });

    // ----------------------------------------
    // ADMIN & PENDAFTARAN MODULES
    // ----------------------------------------
    Route::middleware('web.role:admin|pendaftaran')->group(function () {

        // PASIEN MANAGEMENT
        Route::prefix('pasiens')->name('pasiens.')->group(function () {
            Route::get('/', 'Web\PasienController@index')->name('index');
            Route::get('/create', 'Web\PasienController@create')->name('create');
            Route::get('/search', 'Web\PasienController@search')->name('search');
            Route::get('/search/nik/{nik}', 'Web\PasienController@searchByNik')->name('search.nik');
            Route::get('/search/no-rm/{noRm}', 'Web\PasienController@searchByNoRm')->name('search.no-rm');
            Route::post('/', 'Web\PasienController@store')->name('store');
            Route::post('/bulk-action', 'Web\PasienController@bulkAction')->name('bulk-action');
            Route::get('/{pasien}', 'Web\PasienController@show')->name('show');
            Route::get('/{pasien}/edit', 'Web\PasienController@edit')->name('edit');
            Route::put('/{pasien}', 'Web\PasienController@update')->name('update');
            Route::delete('/{pasien}', 'Web\PasienController@destroy')->name('destroy');
            Route::get('/{pasien}/riwayat-kunjungan', 'Web\PasienController@riwayatKunjungan')->name('riwayat-kunjungan');
            Route::get('/{pasien}/check-history', 'Web\PasienController@checkHistory')->name('check-history');
        });

        // KUNJUNGAN CREATE/EDIT
        Route::prefix('kunjungans')->name('kunjungans.')->group(function () {
            Route::get('/create', 'Web\KunjunganController@create')->name('create');
            Route::get('/search-pasien', 'Web\KunjunganController@searchPasien')->name('search-pasien');
            Route::get('/generate-nomor-antrian', 'Web\KunjunganController@generateNomorAntrian')->name('generate-nomor-antrian');
            Route::post('/', 'Web\KunjunganController@store')->name('store');
            Route::patch('/{kunjungan}/update-status', 'Web\KunjunganController@updateStatus')->name('updateStatus');
            Route::post('/bulk-action', 'Web\KunjunganController@bulkAction')->name('bulk-action');
            Route::get('/{kunjungan}/edit', 'Web\KunjunganController@edit')->name('edit');
            Route::put('/{kunjungan}', 'Web\KunjunganController@update')->name('update');
            Route::delete('/{kunjungan}', 'Web\KunjunganController@destroy')->name('destroy');
            Route::get('/{kunjungan}/pelayanan', 'Web\KunjunganController@pelayanan')->name('pelayanan');
            Route::post('/{kunjungan}/pelayanan', 'Web\KunjunganController@storePelayanan')->name('storePelayanan');
        });
    });

    // ----------------------------------------
    // ALL ROLES - KUNJUNGAN READ ACCESS
    // ----------------------------------------
    Route::prefix('kunjungans')->name('kunjungans.')->group(function () {
        Route::get('/', 'Web\KunjunganController@index')->name('index');
        Route::get('/today', 'Web\KunjunganController@today')->name('today');
        Route::get('/antrian', 'Web\KunjunganController@antrian')->name('antrian');
        Route::get('/{kunjungan}', 'Web\KunjunganController@show')->name('show');
        Route::get('/{kunjungan}/print', 'Web\KunjunganController@print')->name('print');
    });

    // ----------------------------------------
    // ADMIN & DOKTER - MEDICAL RECORDS
    // ----------------------------------------
    Route::middleware('web.role:admin|dokter')->group(function () {

        // TINDAKAN & DIAGNOSA
        Route::prefix('kunjungans')->name('kunjungans.')->group(function () {
            Route::get('/{kunjungan}/tindakan', 'Web\TindakanController@index')->name('tindakan.index');
            Route::get('/{kunjungan}/tindakan/create', 'Web\TindakanController@create')->name('tindakan.create');
            Route::post('/{kunjungan}/tindakan', 'Web\TindakanController@store')->name('tindakan.store');
            Route::get('/{kunjungan}/tindakan/{tindakan}/edit', 'Web\TindakanController@edit')->name('tindakan.edit');
            Route::put('/{kunjungan}/tindakan/{tindakan}', 'Web\TindakanController@update')->name('tindakan.update');
            Route::delete('/{kunjungan}/tindakan/{tindakan}', 'Web\TindakanController@destroy')->name('tindakan.destroy');

            Route::get('/{kunjungan}/diagnosa', 'Web\DiagnosaController@index')->name('diagnosa.index');
            Route::get('/{kunjungan}/diagnosa/create', 'Web\DiagnosaController@create')->name('diagnosa.create');
            Route::post('/{kunjungan}/diagnosa', 'Web\DiagnosaController@store')->name('diagnosa.store');
            Route::get('/{kunjungan}/diagnosa/{diagnosa}/edit', 'Web\DiagnosaController@edit')->name('diagnosa.edit');
            Route::put('/{kunjungan}/diagnosa/{diagnosa}', 'Web\DiagnosaController@update')->name('diagnosa.update');
            Route::delete('/{kunjungan}/diagnosa/{diagnosa}', 'Web\DiagnosaController@destroy')->name('diagnosa.destroy');
        });

        // TINDAKAN & DIAGNOSA MANAGEMENT
        Route::prefix('tindakans')->name('tindakans.')->group(function () {
            Route::get('/', 'Web\TindakanController@indexAll')->name('index');
            Route::get('/search', 'Web\TindakanController@searchTindakan')->name('search');
            Route::post('/bulk-action', 'Web\TindakanController@bulkAction')->name('bulk-action');
            Route::post('/{tindakan}/update-status', 'Web\TindakanController@updateStatus')->name('update-status');
            Route::get('/{tindakan}', 'Web\TindakanController@show')->name('show');
        });

        Route::prefix('diagnosas')->name('diagnosas.')->group(function () {
            Route::get('/', 'Web\DiagnosaController@indexAll')->name('index');
            Route::get('/search-icd', 'Web\DiagnosaController@searchIcd')->name('search-icd');
            Route::post('/bulk-action', 'Web\DiagnosaController@bulkAction')->name('bulk-action');
            Route::get('/{diagnosa}', 'Web\DiagnosaController@show')->name('show');
        });
    });

    // ----------------------------------------
    // API & AJAX ROUTES
    // ----------------------------------------
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/dokters/{dokter}/jadwal', 'Web\DokterController@getJadwal')->name('dokters.jadwal');
    });

    Route::prefix('ajax')->name('ajax.')->group(function () {
        Route::get('/polis/select2', 'Web\PoliController@select2')->name('polis.select2');
        Route::get('/dokters/select2', 'Web\DokterController@select2')->name('dokters.select2');
        Route::get('/jadwal-dokters/select2', 'Web\JadwalDokterController@select2')->name('jadwal-dokters.select2');
        Route::get('/ajax/diagnosas/search-icd', 'Web\DiagnosaController@searchIcd')->name('ajax.diagnosas.search-icd');
    });

    // Export routes
    Route::get('/polis-export', 'Web\PoliController@export')->name('polis.export');
    Route::get('/dokters-export', 'Web\DokterController@export')->name('dokters.export');

});
