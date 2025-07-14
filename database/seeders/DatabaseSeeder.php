<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\JuriSeeder;
use Database\Seeders\KontesSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // Akun Admin
        $this->call([
            UserSeeder::class,
            //JuriSeeder::class,
            //KontesSeeder::class,
            //BonsaiSeeder::class,
            HelperKriteriaSeeder::class,
            //PenilaianSeeder::class,
            //PendaftaranKontesSeeder::class,
        ]);
    }
}
