<?php

namespace Database\Seeders;

use App\Models\Kontes;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KontesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Kontes::create([
            'nama_kontes' => 'Kontes Dummy',
            'slug' => 'kontes-dummy',
            'tempat_kontes' => 'Tempat Dummy',
            'tingkat_kontes' => 'Madya',
            'link_gmaps' => 'https://www.google.co.id/',
            'tanggal_mulai_kontes' => '2025-05-01 08:00:00',
            'tanggal_selesai_kontes' => '2025-05-05 17:00:00',
            'jumlah_peserta' => 50,
            'limit_peserta' => 50,
            'harga_tiket_kontes' => 100000,
            'status' => 1,
        ]);
    }
}
