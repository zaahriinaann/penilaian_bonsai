<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class JuriSeeder extends Seeder
{
    public function run(): void
    {
        $juris = [
            [
                'nama_juri' => 'Suryadi',
                'username' => 'suryadi',
                'email' => 'suryadi01@gmail.com',
                'no_induk_juri' => 'JURI202578965078',
                'sertifikat' => '019-SK-Juri utama-Suryadi.pdf',
                'no_telepon' => '08123457896',
                'created_at' => Carbon::create(2025, 8, 3, 14, 36, 35),
            ],
            [
                'nama_juri' => 'Budi Santoso',
                'username' => 'budi',
                'email' => 'budi@gmail.com',
                'no_induk_juri' => 'JURI202578965079',
                'sertifikat' => '019-SK-Juri utama-Suryadi.pdf',
                'no_telepon' => '08123457897',
                'created_at' => Carbon::create(2025, 8, 4, 10, 0, 0),
            ],
            [
                'nama_juri' => 'Ahmad Fauzi',
                'username' => 'ahmad',
                'email' => 'ahmad@gmail.com',
                'no_induk_juri' => 'JURI202578965080',
                'sertifikat' => '019-SK-Juri utama-Suryadi.pdf',
                'no_telepon' => '08123457898',
                'created_at' => Carbon::create(2025, 8, 5, 10, 0, 0),
            ],
        ];

        foreach ($juris as $index => $juri) {
            // Insert ke tabel users
            $userId = DB::table('users')->insertGetId([
                'name' => $juri['nama_juri'],
                'username' => $juri['username'],
                'no_anggota' => $juri['no_induk_juri'],
                'cabang' => null,
                'no_hp' => null,
                'alamat' => null,
                'foto' => null,
                'email' => $juri['email'],
                'email_verified_at' => null,
                'password' => Hash::make($juri['username']), // password = username
                'role' => 'juri',
                'remember_token' => null,
                'created_at' => $juri['created_at'],
                'updated_at' => $juri['created_at'],
                'deleted_at' => null,
            ]);

            // Insert ke tabel juri
            DB::table('juri')->insert([
                'user_id' => $userId,
                'slug' => Str::slug($juri['nama_juri'] . '-' . $juri['username'] . $juri['no_induk_juri']),
                'no_induk_juri' => $juri['no_induk_juri'],
                'nama_juri' => $juri['nama_juri'],
                'foto' => null,
                'sertifikat' => $juri['sertifikat'],
                'no_telepon' => $juri['no_telepon'],
                'email' => $juri['email'],
                'username' => $juri['username'],
                'password' => Hash::make($juri['username']), // password = username
                'status' => '1',
                'role' => 'juri',
                'created_at' => $juri['created_at'],
                'updated_at' => $juri['created_at'],
                'deleted_at' => null,
            ]);
        }
    }
}
