<?php

// ============================================
// database/seeds/DatabaseSeeder.php
// ============================================

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UserSeeder::class,
            PoliSeeder::class,
            DokterSeeder::class,
            PasienSeeder::class,
            JadwalDokterSeeder::class,
            KunjunganSeeder::class,
            TindakanSeeder::class,
            DiagnosaSeeder::class,
        ]);
    }
}
