<?php

namespace Database\Seeders;

use App\Models\Juri;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JuriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Juri::create([
            'no_induk_juri' => 'JURI20250001',
            'nama_juri' => 'Juri Dummy',
            'email' => 'juri.dummy@gmail.com',
            'username' => 'juri.dummy',
            'password' => bcrypt('juri123'),
            'slug' => 'juri-dummy-juri-dummy',
            'no_telepon' => '0123456789',
            'status' => '1',
            'role' => 'juri',
            'foto' => null,
        ]);
    }
}
