<?php

namespace Database\Seeders;

use App\Models\Bonsai;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BonsaiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Bonsai::create([
            'slug' => 'anting-putri-bonsai20250002',
            'nama_pohon' => 'Anting Putri',
            'nama_lokal' => 'Anting Putri',
            'nama_latin' => 'Wrightia religiosa',
            'ukuran' => 'Small 5 cm',
            'ukuran_1' => 'Small',
            'ukuran_2' => '5',
            'format_ukuran' => 'cm',
            'no_induk_pohon' => 'BONSAI20250002',
            'masa_pemeliharaan' => '2 Tahun',
            'format_masa' => 'tahun',
            'pemilik' => 'Slamet Widodo',
            'no_anggota' => 'ANG20250002',
            'cabang' => 'Yogyakarta',
            'foto' => null,
        ]);
    }
}
