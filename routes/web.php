<?php

use App\Http\Controllers\AkunController;
use App\Http\Controllers\BonsaiController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JuriController;
use App\Http\Controllers\KontesController;
use App\Http\Controllers\NilaiController;
use App\Http\Controllers\PendaftaranKontesController;
use App\Http\Controllers\PenilaianController;
use App\Http\Controllers\PesertaController;
use App\Http\Controllers\RekapNilaiController;
use App\Http\Controllers\RiwayatController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
})->middleware('guest');

Route::get('/register', function () {
    return view('auth.register');
})->middleware('guest');

Auth::routes();

Route::middleware(['auth', 'web'])->group(function () {
    Route::resource('home', HomeController::class);
    Route::resource('akun', AkunController::class);

    // ====================ADMIN=========================
    Route::prefix('master')->group(function () {
        Route::resource('kontes', KontesController::class)->parameters([
            'kontes' => 'slug'
        ]);
        Route::resource('juri', JuriController::class)->parameters([
            'juri' => 'slug'
        ]);
        Route::resource('bonsai', BonsaiController::class)->parameters([
            'bonsai' => 'slug'
        ]);
        Route::resource('penilaian', PenilaianController::class)->parameters([
            'penilaian' => 'slug'
        ]);
        // Route::resource('peserta', PesertaController::class);
        Route::resource('bonsai', BonsaiController::class)->parameters([
            'bonsai' => 'slug'
        ]);
        Route::resource('peserta', PesertaController::class)->parameters([
            'peserta' => 'id'
        ]);
    });
    Route::prefix('kontes')->group(function () {
        Route::resource('pendaftaran-peserta', PendaftaranKontesController::class);

        // get bonsai peserta`
        Route::get('get-bonsai-peserta/{id}', [PendaftaranKontesController::class, 'getBonsaiPeserta']);

        Route::resource('rekap-nilai', RekapNilaiController::class);
    });


    //====================JURI=========================
    Route::resource('nilai', NilaiController::class)->parameters([
        'nilai' => 'id'
    ]);
    Route::resource('riwayat', RiwayatController::class)->parameters([
        'riwayat' => 'id'
    ]);
    Route::resource('rekap-nilai', RekapNilaiController::class);
});
