<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class PeringkatNilaiService
{
    /**
     * Generate dan simpan peringkat di tabel rekap_nilai untuk satu kontes.
     *
     * @param int $kontesId
     * @return void
     */
    public function updateRanking(int $kontesId): void
    {
        DB::statement(
            "UPDATE rekap_nilai AS r
             JOIN (
               SELECT
                 id,
                 ROW_NUMBER() OVER (
                   PARTITION BY id_kontes
                   ORDER BY skor_akhir DESC
                 ) AS rn
               FROM rekap_nilai
               WHERE id_kontes = ?
             ) AS sub ON r.id = sub.id
             SET r.peringkat = sub.rn",
            [$kontesId]
        );
    }
}
