<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\KontesController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
})->middleware('guest');

Auth::routes();

Route::middleware(['auth', 'web'])->group(function () {
    Route::resource('home', HomeController::class);
    Route::resource('kontes', KontesController::class)->parameters([
        'kontes' => 'slug'
    ]);
});
