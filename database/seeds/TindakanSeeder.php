<?php

// ============================================
// database/seeds/TindakanSeeder.php
// ============================================

use Illuminate\Database\Seeder;
use App\Tindakan;
use Carbon\Carbon;

class TindakanSeeder extends Seeder
{
    public function run()
    {
        $tindakans = [
            // Tindakan untuk kunjungan 1 (John Doe - selesai)
            [
                'kunjungan_id' => 1,
                'kode_tindakan' => 'T001',
                'nama_tindakan' => 'Konsultasi Dokter Umum',
                'kategori_tindakan' => 'Konsultasi',
                'jumlah' => 1,
                'tarif_satuan' => 50000,
                'keterangan' => 'Pemeriksaan umum dan konsultasi',
                'dikerjakan_oleh' => 4,
                'tanggal_tindakan' => Carbon::today()->setTime(8, 45),
                'status_tindakan' => 'selesai',
            ],
            [
                'kunjungan_id' => 5,
                'kode_tindakan' => 'T006',
                'nama_tindakan' => 'Scaling Gigi',
                'kategori_tindakan' => 'Dental',
                'jumlah' => 1,
                'tarif_satuan' => 175000,
                'keterangan' => 'Pembersihan karang gigi',
                'dikerjakan_oleh' => 6,
                'tanggal_tindakan' => Carbon::yesterday()->setTime(10, 30),
                'status_tindakan' => 'selesai',
            ],
        ];

        foreach ($tindakans as $tindakan) {
            Tindakan::create($tindakan);
        }

        $this->command->info('Tindakans seeded successfully!');
    }
}
