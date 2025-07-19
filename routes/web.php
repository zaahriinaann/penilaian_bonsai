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
    Route::prefix('master')->name('master.')->group(function () {
        Route::resource('kontes', KontesController::class)->parameters(['kontes' => 'slug']);
        Route::resource('juri', JuriController::class)->parameters(['juri' => 'slug']);
        Route::resource('bonsai', BonsaiController::class)->parameters(['bonsai' => 'slug']);
        Route::resource('penilaian', PenilaianController::class)->parameters(['penilaian' => 'slug']);
        Route::resource('peserta', PesertaController::class)->parameters(['peserta' => 'id']);
    });

    // ==================== KONTESTAN ====================
    Route::prefix('kontes')->name('kontes.')->group(function () {
        Route::resource('pendaftaran-peserta', PendaftaranKontesController::class);
        Route::get('get-bonsai-peserta/{id}', [PendaftaranKontesController::class, 'getBonsaiPeserta']);

        // Resource rekap-nilai (nama route otomatis prefiks 'kontes.')
        Route::resource('rekap-nilai', RekapNilaiController::class);

        // Contoh route custom lain (jika perlu)
        // Route::get('rekap-khusus', [RekapNilaiController::class, 'khusus'])->name('rekap-khusus');
    });

    // ==================== PENILAIAN JURI ====================
    Route::resource('nilai', NilaiController::class)->parameters(['nilai' => 'id']);
    Route::get('nilai/{id}/form', [NilaiController::class, 'formPenilaian'])->name('nilai.form');
    Route::get('nilai/{id}/hasil', [NilaiController::class, 'show'])->name('nilai.hasil');

    // ==================== REKAP NILAI LAINNYA ====================
    // Semua nama route harus beda dan jelas!
    Route::get('/rekap/{nama_pohon}/{nomor_juri}', [RekapNilaiController::class, 'show'])->name('rekap.show');
    Route::get('/rekap/export/{nama_pohon}', [RekapNilaiController::class, 'exportPdf'])->name('rekap.export');
    Route::get('/rekap-nilai/{id}/cetak-rekap', [RekapNilaiController::class, 'cetakRekapPerBonsai'])->name('rekap.cetak-per-bonsai');

    // ==================== FUZZY RULES ====================
    Route::prefix('admin/penilaian')->name('admin.penilaian.')->group(function () {
        Route::get('fuzzy-rules', [FuzzyRuleController::class, 'index'])->name('fuzzy-rules.index');
        Route::post('fuzzy-rules/auto-generate', [FuzzyRuleController::class, 'autoGenerate'])->name('fuzzy-rules.auto-generate');
    });

    // ==================== RIWAYAT PENILAIAN (Opsional) ====================
    // Kalau mau pakai, pastikan prefix dan name unik
    // Route::prefix('riwayat')->name('riwayat.')->group(function () {
    //     Route::get('/', [RiwayatController::class, 'index'])->name('index');
    //     Route::get('/{kontes}', [RiwayatController::class, 'show'])->name('show');
    //     Route::get('/{kontes}/{bonsai}', [RiwayatController::class, 'detail'])->name('detail');
    // });
});
