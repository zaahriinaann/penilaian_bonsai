<?php

use App\Http\Controllers\BonsaiController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JuriController;
use App\Http\Controllers\KontesController;
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
        Route::resource('peserta', PesertaController::class);
    });
    Route::prefix('kontes')->group(function () {
        Route::resource('bonsai', BonsaiController::class)->parameters([
            'bonsai' => 'slug'
        ]);
        Route::resource('peserta', PesertaController::class);
    });
});
