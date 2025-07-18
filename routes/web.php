<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\{
    AkunController,
    BonsaiController,
    FuzzyRuleController,
    HomeController,
    JuriController,
    KontesController,
    NilaiController,
    PendaftaranKontesController,
    PenilaianController,
    PesertaController,
    RekapNilaiController,
    RiwayatController
};

// ==================== Guest Routes ====================
Route::middleware('guest')->group(function () {
    Route::view('/', 'auth.login');
    Route::view('/register', 'auth.register');
});

Auth::routes();

// ==================== Authenticated Routes ====================
Route::middleware(['auth', 'web'])->group(function () {

    // Dashboard & Akun
    Route::resource('home', HomeController::class);
    Route::resource('akun', AkunController::class);

    // ==================== ADMIN ====================
    Route::prefix('master')->group(function () {
        Route::resource('kontes', KontesController::class)->parameters(['kontes' => 'slug']);
        Route::resource('juri', JuriController::class)->parameters(['juri' => 'slug']);
        Route::resource('bonsai', BonsaiController::class)->parameters(['bonsai' => 'slug']);
        Route::resource('penilaian', PenilaianController::class)->parameters(['penilaian' => 'slug']);
        Route::resource('peserta', PesertaController::class)->parameters(['peserta' => 'id']);
    });

    // Admin: Daftar Juri Aktif
    Route::get('/admin/nilai', [NilaiController::class, 'indexAdmin'])->name('admin.nilai.index');

    // Admin: Daftar peserta yang dinilai oleh juri tertentu
    Route::get('/admin/nilai/{juriId}', [NilaiController::class, 'showAdmin'])->name('admin.nilai.show');

    // Admin: Detail nilai peserta dari juri tertentu
    Route::get('/admin/nilai/{juriId}/bonsai/{bonsaiId}', [NilaiController::class, 'detailAdmin'])->name('admin.nilai.detail');


    // ==================== KONTESTAN ====================
    Route::prefix('kontes')->group(function () {
        Route::resource('pendaftaran-peserta', PendaftaranKontesController::class);
        Route::get('get-bonsai-peserta/{id}', [PendaftaranKontesController::class, 'getBonsaiPeserta']);
        Route::resource('rekap-nilai', RekapNilaiController::class);
    });

    // ==================== JURI ====================
    Route::resource('nilai', NilaiController::class)->parameters(['nilai' => 'id']);
    Route::get('nilai/{id}/form', [NilaiController::class, 'formPenilaian'])->name('nilai.form');
    Route::get('nilai/{id}/hasil', [NilaiController::class, 'show'])->name('nilai.hasil');

    Route::resource('riwayat', RiwayatController::class)->parameters(['riwayat' => 'id']);

    // ==================== REKAP NILAI ====================
    Route::resource('rekap-nilai', RekapNilaiController::class);
    Route::get('/rekap/{nama_pohon}/{nomor_juri}', [RekapNilaiController::class, 'show'])->name('rekap.show');
    Route::get('/rekap/export/{nama_pohon}', [RekapNilaiController::class, 'exportPdf'])->name('rekap.export');
    Route::get('/rekap/cetak', [RekapNilaiController::class, 'cetak'])->name('rekap.cetak');

    // ==================== FUZZY RULES ====================
    Route::prefix('admin/penilaian')->group(function () {
        Route::get('fuzzy-rules', [FuzzyRuleController::class, 'index'])->name('fuzzy-rules.index');
        Route::post('fuzzy-rules/auto-generate', [FuzzyRuleController::class, 'autoGenerate'])->name('fuzzy-rules.auto-generate');
    });

    // ==================== RIWAYAT PENILAIAN ====================
    Route::prefix('riwayat')->name('riwayat.')->group(function () {
        Route::get('/', [RiwayatController::class, 'index'])->name('index');
        Route::get('/{kontes}', [RiwayatController::class, 'show'])->name('show');
        Route::get('/{kontes}/{bonsai}', [RiwayatController::class, 'detail'])->name('detail');
    });
});
