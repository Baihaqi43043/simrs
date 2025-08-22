<?php

// ============================================
// database/seeds/PoliSeeder.php
// ============================================

use Illuminate\Database\Seeder;
use App\Poli;

class PoliSeeder extends Seeder
{
    public function run()
    {
        $polis = [
            [
                'kode_poli' => 'P001',
                'nama_poli' => 'Poli Umum',
                'deskripsi' => 'Pelayanan kesehatan umum untuk berbagai keluhan',
                'is_active' => true,
            ],
            [
                'kode_poli' => 'P002',
                'nama_poli' => 'Poli Anak',
                'deskripsi' => 'Pelayanan kesehatan khusus untuk anak-anak',
                'is_active' => true,
            ],
            [
                'kode_poli' => 'P003',
                'nama_poli' => 'Poli Penyakit Dalam',
                'deskripsi' => 'Pelayanan spesialis penyakit dalam',
                'is_active' => true,
            ],
            [
                'kode_poli' => 'P004',
                'nama_poli' => 'Poli Bedah',
                'deskripsi' => 'Pelayanan spesialis bedah',
                'is_active' => true,
            ],
            [
                'kode_poli' => 'P005',
                'nama_poli' => 'Poli Mata',
                'deskripsi' => 'Pelayanan spesialis mata',
                'is_active' => true,
            ],
            [
                'kode_poli' => 'P006',
                'nama_poli' => 'Poli Gigi',
                'deskripsi' => 'Pelayanan kesehatan gigi dan mulut',
                'is_active' => true,
            ],
        ];

        foreach ($polis as $poli) {
            Poli::create($poli);
        }

        $this->command->info('Polis seeded successfully!');
    }
}

