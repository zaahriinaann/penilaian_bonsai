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
    // update deploy
    // Dashboard & Akun
    Route::resource('home', HomeController::class);
    Route::resource('akun', AkunController::class);

    // ==================== [ADMIN] MASTER ====================
    Route::prefix('master')->name('master.')->group(function () {
        Route::resource('kontes', KontesController::class)->parameters(['kontes' => 'slug']);
        Route::resource('juri', JuriController::class)->parameters(['juri' => 'slug']);
        Route::resource('bonsai', BonsaiController::class)->parameters(['bonsai' => 'slug']);
        Route::resource('penilaian', PenilaianController::class)->parameters(['penilaian' => 'slug']);
        Route::resource('peserta', PesertaController::class)->parameters(['peserta' => 'id']);
    });

    // ==================== [ADMIN] KONTESTAN: PENDAFTARAN ====================
    Route::prefix('kontes')->name('kontes.')->group(function () {
        Route::resource('pendaftaran-peserta', PendaftaranKontesController::class);
        Route::get('get-bonsai-peserta/{id}', [PendaftaranKontesController::class, 'getBonsaiPeserta'])->name('get-bonsai-peserta');
    });

    // ==================== [ADMIN] ====================
    Route::prefix('admin')->name('admin.')->group(function () {
        // ==================== [ADMIN] NILAI ====================
        Route::get('nilai', [NilaiController::class, 'indexAdmin'])->name('nilai.index');
        Route::get('nilai/{juriId}', [NilaiController::class, 'showAdmin'])->name('nilai.show');
        Route::get('nilai/{juriId}/bonsai/{bonsaiId}', [NilaiController::class, 'detailAdmin'])->name('nilai.detail');
        // ==================== [ADMIN] RIWAYAT ====================
        Route::prefix('riwayat')->name('riwayat.')->group(function () {
            Route::get('/kontes', [RiwayatController::class, 'riwayatAdminIndex'])->name('index');
            // Route::get('/{kontes}/cetak-laporan-rekap', [RiwayatController::class, 'cetakLaporan'])->name('cetak');
            Route::get('/{kontes}/juri', [RiwayatController::class, 'riwayatAdminJuri'])->name('juri');
            Route::get('/{kontes}/{juri}/peserta', [RiwayatController::class, 'riwayatAdminPeserta'])->name('peserta');
            Route::get('/{kontes}/{juri}/peserta/{bonsai}', [RiwayatController::class, 'riwayatAdminDetail'])->name('detail');
        });
        // ==================== [ADMIN] KELOLA RULES (menu master | di folder penilaian) ====================
        Route::prefix('penilaian')->name('penilaian.')->group(function () {
            Route::get('fuzzy-rules', [FuzzyRuleController::class, 'index'])->name('fuzzy-rules.index');
            Route::post('fuzzy-rules/auto-generate', [FuzzyRuleController::class, 'autoGenerate'])->name('fuzzy-rules.auto-generate');
        });
    });

    // ==================== [JURI] ====================
    Route::prefix('juri')->name('juri.')->group(function () {
        // ==================== [JURI] NILAI ====================
        Route::resource('nilai', NilaiController::class)->parameters(['nilai' => 'id']);
        Route::get('nilai/{id}/form', [NilaiController::class, 'formPenilaian'])->name('nilai.form');

        // ==================== [JURI] RIWAYAT ====================
        Route::prefix('riwayat')->name('riwayat.')->group(function () {
            Route::get('/kontes', [RiwayatController::class, 'riwayatJuriIndex'])->name('index');
            Route::get('/{kontes}/peserta', [RiwayatController::class, 'riwayatJuriPeserta'])->name('peserta');
            Route::get('/{kontes}/peserta/detail/{bonsai}', [RiwayatController::class, 'riwayatJuriDetail'])->name('detail');
        });
    });

    Route::prefix('rekap-nilai')->name('rekap-nilai.')->group(function () {
        Route::resource('/', RekapNilaiController::class)->except(['create', 'store', 'update', 'destroy']);
        Route::get('/{id}', [RekapNilaiController::class, 'show'])->name('show');
        Route::get('/cetak/{kontesId}', [RekapNilaiController::class, 'cetakLaporan'])->name('cetak-laporan');
        Route::get('/{id}/cetak-rekap', [RekapNilaiController::class, 'cetakRekapPerBonsai'])->name('cetak-per-bonsai');
        // Route::get('/export/{nama_pohon}', [RekapNilaiController::class, 'exportPdf'])->name('export');
    });

    // // ==================== RIWAYAT PENILAIAN UMUM ====================
    // Route::prefix('riwayat')->name('riwayat.')->group(function () {
    //     Route::get('/', [RiwayatController::class, 'index'])->name('index');
    //     Route::get('/{kontes}', [RiwayatController::class, 'show'])->name('show');
    //     Route::get('/{kontes}/{bonsai}', [RiwayatController::class, 'detail'])->name('detail');
    //     Route::get('/rekap-nilai/{id_bonsai}', [RiwayatController::class, 'rekap'])->name('rekap-nilai');
    // });

    // ==================== [ANGGOTA/PESERTA] RIWAYAT ====================
    Route::prefix('peserta')->name('peserta.')->group(function () {
        // Daftar nilai bonsai anggota pada kontes aktif
        Route::get('/nilai', [RekapNilaiController::class, 'indexAnggota'])->name('nilai.index');

        // Riwayat kontes yang pernah diikuti anggota
        Route::get('/riwayat/kontes', [RiwayatController::class, 'riwayatAnggotaIndex'])->name('riwayat.index');

        // Daftar bonsai peserta dalam kontes tertentu
        Route::get('/riwayat/kontes/{kontes}/bonsai', [RiwayatController::class, 'riwayatAnggotaBonsai'])->name('riwayat.bonsai');
    });
});
