<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Str;

class PesertaSeeder extends Seeder
{
    public function run(): void
    {
        $namaList = [
            'Andi Saputra',
            'Budi Santoso',
            'Citra Ayu',
            'Dewi Lestari',
            'Eka Prasetya',
            'Fajar Nugraha',
            'Gilang Ramadhan',
            'Hani Puspita',
            'Indra Wijaya',
            'Joko Susilo',
            'Kartika Sari',
            'Lutfi Hidayat',
            'Mega Sari',
            'Nanda Pratama',
            'Oki Setiawan',
            'Putri Amelia',
            'Rizky Aditya',
            'Siti Aisyah',
            'Taufik Hidayat'
        ];

        $cabangLain = [
            'KABUPATEN INDRAMAYU',
            'KABUPATEN MAJALENGKA',
            'KABUPATEN KUNINGAN',
            'KOTA BANDUNG',
            'KOTA JAKARTA'
        ];

        $jalanList = [
            'Jl. Raya Sukasari',
            'Jl. Merdeka',
            'Jl. Ahmad Yani',
            'Jl. Gatot Subroto',
            'Jl. Sudirman',
            'Jl. Siliwangi',
            'Jl. Diponegoro',
            'Jl. Pahlawan',
            'Jl. Cendrawasih',
            'Jl. Anggrek',
            'Jl. Melati',
            'Jl. Kenanga'
        ];

        $prefixHp = ['0812', '0813', '0821', '0822', '0852', '0853', '0877', '0882'];

        $pesertas = [
            [
                'name' => 'Purwawidada',
                'username' => 'purwa',
                'no_anggota' => '20240113123456',
                'cabang' => 'KOTA CIREBON',
                'no_hp' => $prefixHp[array_rand($prefixHp)] . rand(1000000, 9999999),
                'alamat' => $jalanList[array_rand($jalanList)] . ' No.' . rand(1, 300) . ', Kota Cirebon',
                'email' => 'purwa26@gmail.com',
                'password' => Hash::make('purwa'),
                'created_at' => Carbon::create(2025, 8, 3, 14, 45, 41),
            ],
        ];

        for ($i = 0; $i < 19; $i++) {
            $name = $namaList[$i];
            $baseUsername = Str::slug(explode(' ', strtolower($name))[0]);
            $username = $baseUsername;
            $counter = 1;

            while (DB::table('users')->where('username', $username)->exists()) {
                $username = $baseUsername . $counter;
                $counter++;
            }

            $isCirebon = rand(1, 100) <= 70;
            $cabang = $isCirebon ? 'KOTA CIREBON' : $cabangLain[array_rand($cabangLain)];
            $alamat = $jalanList[array_rand($jalanList)] . ' No.' . rand(1, 300) . ', ' . ucwords(strtolower($cabang));
            $noHp = $prefixHp[array_rand($prefixHp)] . rand(1000000, 9999999);

            $pesertas[] = [
                'name' => $name,
                'username' => $username,
                'no_anggota' => date('Y') . str_pad($i + 1, 8, '0', STR_PAD_LEFT),
                'cabang' => $cabang,
                'no_hp' => $noHp,
                'alamat' => $alamat,
                'email' => $username . '@gmail.com',
                'password' => Hash::make($username),
                'created_at' => Carbon::now()->subDays(rand(1, 30)),
            ];
        }

        foreach ($pesertas as $peserta) {
            DB::table('users')->insert([
                'name' => $peserta['name'],
                'username' => $peserta['username'],
                'no_anggota' => $peserta['no_anggota'],
                'cabang' => $peserta['cabang'],
                'no_hp' => $peserta['no_hp'],
                'alamat' => $peserta['alamat'],
                'foto' => null,
                'email' => $peserta['email'],
                'email_verified_at' => null,
                'password' => $peserta['password'],
                'role' => 'anggota',
                'remember_token' => null,
                'created_at' => $peserta['created_at'],
                'updated_at' => $peserta['created_at'],
                'deleted_at' => null,
            ]);
        }
    }
}
