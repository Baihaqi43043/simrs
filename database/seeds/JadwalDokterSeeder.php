<?php

// ============================================
// database/seeds/JadwalDokterSeeder.php
// ============================================

use Illuminate\Database\Seeder;
use App\JadwalDokter;

class JadwalDokterSeeder extends Seeder
{
    public function run()
    {
        $jadwals = [
            // Dr. Ahmad Wijaya (Penyakit Dalam)
            [
                'dokter_id' => 1,
                'poli_id' => 3,
                'hari' => 'senin',
                'jam_mulai' => '08:00:00',
                'jam_selesai' => '12:00:00',
                'kuota_pasien' => 20,
                'is_active' => true,
            ],
            [
                'dokter_id' => 1,
                'poli_id' => 3,
                'hari' => 'rabu',
                'jam_mulai' => '13:00:00',
                'jam_selesai' => '17:00:00',
                'kuota_pasien' => 15,
                'is_active' => true,
            ],

            // Dr. Siti Nurhaliza (Anak)
            [
                'dokter_id' => 2,
                'poli_id' => 2,
                'hari' => 'selasa',
                'jam_mulai' => '08:00:00',
                'jam_selesai' => '12:00:00',
                'kuota_pasien' => 25,
                'is_active' => true,
            ],
            [
                'dokter_id' => 2,
                'poli_id' => 2,
                'hari' => 'kamis',
                'jam_mulai' => '13:00:00',
                'jam_selesai' => '17:00:00',
                'kuota_pasien' => 20,
                'is_active' => true,
            ],

            // Dr. Budi Santoso (Bedah)
            [
                'dokter_id' => 3,
                'poli_id' => 4,
                'hari' => 'senin',
                'jam_mulai' => '13:00:00',
                'jam_selesai' => '17:00:00',
                'kuota_pasien' => 15,
                'is_active' => true,
            ],
            [
                'dokter_id' => 3,
                'poli_id' => 4,
                'hari' => 'jumat',
                'jam_mulai' => '08:00:00',
                'jam_selesai' => '12:00:00',
                'kuota_pasien' => 12,
                'is_active' => true,
            ],

            // Dr. Maya Sari (Umum)
            [
                'dokter_id' => 4,
                'poli_id' => 1,
                'hari' => 'senin',
                'jam_mulai' => '08:00:00',
                'jam_selesai' => '12:00:00',
                'kuota_pasien' => 30,
                'is_active' => true,
            ],
            [
                'dokter_id' => 4,
                'poli_id' => 1,
                'hari' => 'rabu',
                'jam_mulai' => '08:00:00',
                'jam_selesai' => '12:00:00',
                'kuota_pasien' => 30,
                'is_active' => true,
            ],
            [
                'dokter_id' => 4,
                'poli_id' => 1,
                'hari' => 'jumat',
                'jam_mulai' => '13:00:00',
                'jam_selesai' => '17:00:00',
                'kuota_pasien' => 25,
                'is_active' => true,
            ],

            // Dr. Andi Pratama (Mata)
            [
                'dokter_id' => 5,
                'poli_id' => 5,
                'hari' => 'selasa',
                'jam_mulai' => '13:00:00',
                'jam_selesai' => '17:00:00',
                'kuota_pasien' => 18,
                'is_active' => true,
            ],
            [
                'dokter_id' => 5,
                'poli_id' => 5,
                'hari' => 'kamis',
                'jam_mulai' => '08:00:00',
                'jam_selesai' => '12:00:00',
                'kuota_pasien' => 20,
                'is_active' => true,
            ],

            // drg. Lisa Anggraini (Gigi)
            [
                'dokter_id' => 6,
                'poli_id' => 6,
                'hari' => 'senin',
                'jam_mulai' => '08:00:00',
                'jam_selesai' => '16:00:00',
                'kuota_pasien' => 15,
                'is_active' => true,
            ],
            [
                'dokter_id' => 6,
                'poli_id' => 6,
                'hari' => 'rabu',
                'jam_mulai' => '08:00:00',
                'jam_selesai' => '16:00:00',
                'kuota_pasien' => 15,
                'is_active' => true,
            ],
            [
                'dokter_id' => 6,
                'poli_id' => 6,
                'hari' => 'jumat',
                'jam_mulai' => '08:00:00',
                'jam_selesai' => '16:00:00',
                'kuota_pasien' => 15,
                'is_active' => true,
            ],
        ];

        foreach ($jadwals as $jadwal) {
            JadwalDokter::create($jadwal);
        }

        $this->command->info('Jadwal Dokters seeded successfully!');
    }
}
