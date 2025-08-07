<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class KontesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kontes = [
            // Tahun 2024
            [
                'nama_kontes' => 'Kontes Bonsai Madya Cirebon 2024',
                'slug' => Str::slug('Kontes Bonsai Madya Cirebon 2024'),
                'tempat_kontes' => 'Gor Bima Cirebon',
                'tingkat_kontes' => 'Madya',
                'link_gmaps' => null,
                'tanggal_mulai_kontes' => $mulai = Carbon::create(2024, 3, 10, 9, 0, 0),
                'tanggal_selesai_kontes' => Carbon::create(2024, 3, 15, 21, 0, 0),
                'jumlah_peserta' => 80,
                'limit_peserta' => 100,
                'harga_tiket_kontes' => 50000,
                'status' => '1',
                'poster_kontes' => null,
                'created_at' => $mulai->copy()->subDays(30),
                'updated_at' => $mulai->copy()->subDays(30),
                'deleted_at' => null,
            ],
            [
                'nama_kontes' => 'Kontes Bonsai Pratama Cirebon 2024',
                'slug' => Str::slug('Kontes Bonsai Pratama Cirebon 2024'),
                'tempat_kontes' => 'Alun-Alun Kejaksan Cirebon',
                'tingkat_kontes' => 'Pratama',
                'link_gmaps' => null,
                'tanggal_mulai_kontes' => $mulai = Carbon::create(2024, 9, 5, 9, 0, 0),
                'tanggal_selesai_kontes' => Carbon::create(2024, 9, 10, 21, 0, 0),
                'jumlah_peserta' => 60,
                'limit_peserta' => 80,
                'harga_tiket_kontes' => 30000,
                'status' => '1',
                'poster_kontes' => null,
                'created_at' => $mulai->copy()->subDays(30),
                'updated_at' => $mulai->copy()->subDays(30),
                'deleted_at' => null,
            ],

            // Tahun 2025
            [
                'nama_kontes' => 'Kontes Bonsai Madya Cirebon 2025',
                'slug' => Str::slug('Kontes Bonsai Madya Cirebon 2025'),
                'tempat_kontes' => 'Gedung Wali Kota Cirebon',
                'tingkat_kontes' => 'Madya',
                'link_gmaps' => null,
                'tanggal_mulai_kontes' => $mulai = Carbon::create(2025, 8, 11, 10, 21, 0),
                'tanggal_selesai_kontes' => Carbon::create(2025, 8, 16, 21, 21, 0),
                'jumlah_peserta' => 100,
                'limit_peserta' => 100,
                'harga_tiket_kontes' => null,
                'status' => '1',
                'poster_kontes' => null,
                'created_at' => $mulai->copy()->subDays(30),
                'updated_at' => $mulai->copy()->subDays(30),
                'deleted_at' => null,
            ],
            [
                'nama_kontes' => 'Kontes Bonsai Madya Cirebon 2025 - Edisi April',
                'slug' => Str::slug('Kontes Bonsai Madya Cirebon 2025 - Edisi April'),
                'tempat_kontes' => 'Gor Bima Cirebon',
                'tingkat_kontes' => 'Madya',
                'link_gmaps' => null,
                'tanggal_mulai_kontes' => $mulai = Carbon::create(2025, 4, 15, 9, 0, 0),
                'tanggal_selesai_kontes' => Carbon::create(2025, 4, 20, 21, 0, 0),
                'jumlah_peserta' => 90,
                'limit_peserta' => 100,
                'harga_tiket_kontes' => 50000,
                'status' => '1',
                'poster_kontes' => null,
                'created_at' => $mulai->copy()->subDays(30),
                'updated_at' => $mulai->copy()->subDays(30),
                'deleted_at' => null,
            ],
            [
                'nama_kontes' => 'Kontes Bonsai Pratama Cirebon 2025',
                'slug' => Str::slug('Kontes Bonsai Pratama Cirebon 2025'),
                'tempat_kontes' => 'Taman Sari Cirebon',
                'tingkat_kontes' => 'Pratama',
                'link_gmaps' => null,
                'tanggal_mulai_kontes' => $mulai = Carbon::create(2025, 11, 2, 9, 0, 0),
                'tanggal_selesai_kontes' => Carbon::create(2025, 11, 6, 21, 0, 0),
                'jumlah_peserta' => 50,
                'limit_peserta' => 60,
                'harga_tiket_kontes' => 20000,
                'status' => '1',
                'poster_kontes' => null,
                'created_at' => $mulai->copy()->subDays(30),
                'updated_at' => $mulai->copy()->subDays(30),
                'deleted_at' => null,
            ],
        ];

        DB::table('kontes')->insert($kontes);
    }
}
