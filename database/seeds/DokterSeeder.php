<?php

// ============================================
// database/seeds/DokterSeeder.php
// ============================================

use Illuminate\Database\Seeder;
use App\Dokter;

class DokterSeeder extends Seeder
{
    public function run()
    {
        $dokters = [
            [
                'kode_dokter' => 'DR001',
                'nama_dokter' => 'Dr. Ahmad Wijaya, Sp.PD',
                'spesialisasi' => 'Spesialis Penyakit Dalam',
                'no_telepon' => '081234567890',
                'email' => 'ahmad.wijaya@simrs.com',
                'is_active' => true,
            ],
            [
                'kode_dokter' => 'DR002',
                'nama_dokter' => 'Dr. Siti Nurhaliza, Sp.A',
                'spesialisasi' => 'Spesialis Anak',
                'no_telepon' => '081234567891',
                'email' => 'siti.nurhaliza@simrs.com',
                'is_active' => true,
            ],
            [
                'kode_dokter' => 'DR003',
                'nama_dokter' => 'Dr. Budi Santoso, Sp.B',
                'spesialisasi' => 'Spesialis Bedah',
                'no_telepon' => '081234567892',
                'email' => 'budi.santoso@simrs.com',
                'is_active' => true,
            ],
            [
                'kode_dokter' => 'DR004',
                'nama_dokter' => 'Dr. Maya Sari',
                'spesialisasi' => 'Dokter Umum',
                'no_telepon' => '081234567893',
                'email' => 'maya.sari@simrs.com',
                'is_active' => true,
            ],
            [
                'kode_dokter' => 'DR005',
                'nama_dokter' => 'Dr. Andi Pratama, Sp.M',
                'spesialisasi' => 'Spesialis Mata',
                'no_telepon' => '081234567894',
                'email' => 'andi.pratama@simrs.com',
                'is_active' => true,
            ],
            [
                'kode_dokter' => 'DR006',
                'nama_dokter' => 'drg. Lisa Anggraini',
                'spesialisasi' => 'Dokter Gigi',
                'no_telepon' => '081234567895',
                'email' => 'lisa.anggraini@simrs.com',
                'is_active' => true,
            ],
        ];

        foreach ($dokters as $dokter) {
            Dokter::create($dokter);
        }

        $this->command->info('Dokters seeded successfully!');
    }
}
