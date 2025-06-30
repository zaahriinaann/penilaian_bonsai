<?php

use App\Http\Controllers\AkunController;
use App\Http\Controllers\BonsaiController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JuriController;
use App\Http\Controllers\KontesController;
use App\Http\Controllers\PendaftaranKontesController;
use App\Http\Controllers\PenilaianController;
use App\Http\Controllers\PesertaController;
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
            // kalau ini gak di ubah, id/slug ga keambil sesuai parameter yang kita mau
            // contoh yang lain pake slug, disini aku pake id,
            // jadi kalau mau pake slug, ganti id jadi slug
            // kalau mau pake id, ganti slug jadi id
            // kalau mau pake bawaan, cek nya pake command php artisan route:list
            // liat parameternya bener atau gak, kalau gak bener tinggal ganti
            // kayak contoh disini, atau di atasnya
            // contoh bawaan pake slug master/kontes/{slug}
            // jadi nanti yang di lempar parameternya adalah slug, kalau {id}, ya ganti aja jadi id
            // defaultnya laravel itu pake id, jadi harusnya gausah di ubah gapapa
            // tapi kalau gak bisa ya di ubah aja manual seperti contoh lainnya
            'peserta' => 'id'
        ]);
    });
    Route::prefix('kontes')->group(function () {
        Route::resource('pendaftaran-peserta', PendaftaranKontesController::class);

        // get bonsai peserta
        Route::get('get-bonsai-peserta/{id}', [PendaftaranKontesController::class, 'getBonsaiPeserta']);
    });
});
