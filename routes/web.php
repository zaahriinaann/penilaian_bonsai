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

    // PENILAIAN ROLE ADMIN
    Route::get('/admin/nilai', [NilaiController::class, 'indexAdmin'])->name('admin.nilai.index');
    Route::get('/admin/nilai/{juriId}', [NilaiController::class, 'showAdmin'])->name('admin.nilai.show');
    Route::get('/admin/nilai/{juriId}/bonsai/{bonsaiId}', [NilaiController::class, 'detailAdmin'])->name('admin.nilai.detail');

    // RIWAYAT PENILAIAN ADMIN
    Route::prefix('admin/riwayat')->name('admin.riwayat.')->group(function () {
        Route::get('/', [NilaiController::class, 'riwayatIndex'])->name('index');
        Route::get('/{kontes}/cetak', [NilaiController::class, 'cetakLaporan'])->name('cetak');
        Route::get('/{kontes}', [NilaiController::class, 'riwayatJuri'])->name('juri');
        Route::get('/{kontes}/{juri}', [NilaiController::class, 'riwayatPeserta'])->name('peserta');
        Route::get('/{kontes}/{juri}/{bonsai}', [NilaiController::class, 'riwayatDetail'])->name('detail');
    });

    // ==================== RIWAYAT PENILAIAN JURI ====================
    Route::prefix('juri/riwayat')->name('juri.riwayat.')->group(function () {
        Route::get('/', [NilaiController::class, 'riwayatJuriIndex'])->name('index');
        Route::get('/{kontes}', [NilaiController::class, 'riwayatJuriPeserta'])->name('peserta');
        Route::get('/{kontes}/{bonsai}', [NilaiController::class, 'riwayatJuriDetail'])->name('detail');
    });

    // ==================== PENDAFTARAN KONTESTAN ====================
    Route::prefix('kontes')->group(function () {
        Route::resource('pendaftaran-peserta', PendaftaranKontesController::class);
        Route::get('get-bonsai-peserta/{id}', [PendaftaranKontesController::class, 'getBonsaiPeserta']);
    });

    // ==================== PENILAIAN JURI ====================
    Route::resource('nilai', NilaiController::class)->parameters(['nilai' => 'id']);
    Route::get('nilai/{id}/form', [NilaiController::class, 'formPenilaian'])->name('nilai.form');
    Route::get('nilai/{id}/hasil', [NilaiController::class, 'show'])->name('nilai.hasil');

    // ==================== REKAP NILAI ====================
    Route::resource('rekap-nilai', RekapNilaiController::class);
    Route::get('/rekap/cetak/{kontesId}', [RekapNilaiController::class, 'cetakLaporan'])->name('rekap.cetak');
    Route::get('/rekap/{nama_pohon}/{nomor_juri}', [RekapNilaiController::class, 'show'])->name('rekap.show');
    Route::get('/rekap/export/{nama_pohon}', [RekapNilaiController::class, 'exportPdf'])->name('rekap.export');
    Route::get('/rekap-nilai/{id}/cetak-rekap', [RekapNilaiController::class, 'cetakRekapPerBonsai'])->name('rekap.cetak-per-bonsai');

    // ==================== FUZZY RULES ====================
    Route::prefix('admin/penilaian')->group(function () {
        Route::get('fuzzy-rules', [FuzzyRuleController::class, 'index'])->name('fuzzy-rules.index');
        Route::post('fuzzy-rules/auto-generate', [FuzzyRuleController::class, 'autoGenerate'])->name('fuzzy-rules.auto-generate');
    });

    // ==================== RIWAYAT PENILAIAN BIASA ====================
    Route::prefix('riwayat')->name('riwayat.')->group(function () {
        Route::get('/', [RiwayatController::class, 'index'])->name('index');
        Route::get('/{kontes}', [RiwayatController::class, 'show'])->name('show');
        Route::get('/{kontes}/{bonsai}', [RiwayatController::class, 'detail'])->name('detail');
    });
});
