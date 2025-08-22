<?php

// ============================================
// database/seeds/KunjunganSeeder.php
// ============================================

use Illuminate\Database\Seeder;
use App\Kunjungan;
use Carbon\Carbon;

class KunjunganSeeder extends Seeder
{
    public function run()
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $tomorrow = Carbon::tomorrow();
        $twoDaysAgo = Carbon::today()->subDays(2);

        $kunjungans = [
            // Kunjungan hari ini
            [
                'no_kunjungan' => 'KJ-' . $today->format('Ymd') . '-0001',
                'pasien_id' => 1, // John Doe
                'dokter_id' => 4, // Dr. Maya Sari (Umum)
                'poli_id' => 1,   // Poli Umum
                'jadwal_dokter_id' => 7,
                'tanggal_kunjungan' => $today,
                'jam_kunjungan' => '08:30:00',
                'no_antrian' => 1,
                'jenis_kunjungan' => 'lama',
                'cara_bayar' => 'umum',
                'keluhan_utama' => 'Demam dan batuk sejak 3 hari yang tidak kunjung sembuh',
                'status' => 'selesai',
                'total_biaya' => 150000,
                'catatan' => 'Pasien sudah berobat sebelumnya, kondisi membaik',
                'created_by' => 5, // Rina Pendaftaran
            ],
            [
                'no_kunjungan' => 'KJ-' . $today->format('Ymd') . '-0002',
                'pasien_id' => 2, // Jane Smith
                'dokter_id' => 2, // Dr. Siti Nurhaliza (Anak)
                'poli_id' => 2,   // Poli Anak
                'jadwal_dokter_id' => 3,
                'tanggal_kunjungan' => $today,
                'jam_kunjungan' => '09:00:00',
                'no_antrian' => 1,
                'jenis_kunjungan' => 'baru',
                'cara_bayar' => 'bpjs',
                'keluhan_utama' => 'Anak demam tinggi 39°C sejak kemarin malam, disertai muntah',
                'status' => 'sedang_dilayani',
                'total_biaya' => 0,
                'catatan' => 'Pasien baru, sedang dalam pemeriksaan',
                'created_by' => 6, // Sari Pendaftaran
            ],
            [
                'no_kunjungan' => 'KJ-' . $today->format('Ymd') . '-0003',
                'pasien_id' => 3, // Ahmad Rahman
                'dokter_id' => 1, // Dr. Ahmad Wijaya (Penyakit Dalam)
                'poli_id' => 3,   // Poli Penyakit Dalam
                'jadwal_dokter_id' => 1,
                'tanggal_kunjungan' => $today,
                'jam_kunjungan' => '10:00:00',
                'no_antrian' => 1,
                'jenis_kunjungan' => 'lama',
                'cara_bayar' => 'umum',
                'keluhan_utama' => 'Kontrol rutin diabetes melitus, cek gula darah',
                'status' => 'menunggu',
                'total_biaya' => 0,
                'catatan' => 'Kontrol rutin bulanan',
                'created_by' => 5,
            ],
            [
                'no_kunjungan' => 'KJ-' . $today->format('Ymd') . '-0004',
                'pasien_id' => 7, // Andi Pratama
                'dokter_id' => 5, // Dr. Andi Pratama (Mata)
                'poli_id' => 5,   // Poli Mata
                'jadwal_dokter_id' => 11,
                'tanggal_kunjungan' => $today,
                'jam_kunjungan' => '10:30:00',
                'no_antrian' => 1,
                'jenis_kunjungan' => 'lama',
                'cara_bayar' => 'asuransi',
                'keluhan_utama' => 'Mata kabur dan sering berair, terutama mata kanan',
                'status' => 'menunggu',
                'total_biaya' => 0,
                'catatan' => 'Rujukan dari poli umum',
                'created_by' => 6,
            ],

            // Kunjungan kemarin
            [
                'no_kunjungan' => 'KJ-' . $yesterday->format('Ymd') . '-0001',
                'pasien_id' => 4, // Siti Aisyah
                'dokter_id' => 4, // Dr. Maya Sari (Umum)
                'poli_id' => 1,   // Poli Umum
                'jadwal_dokter_id' => 7,
                'tanggal_kunjungan' => $yesterday,
                'jam_kunjungan' => '08:15:00',
                'no_antrian' => 1,
                'jenis_kunjungan' => 'baru',
                'cara_bayar' => 'umum',
                'keluhan_utama' => 'Sakit kepala berkepanjangan, pusing dan mual',
                'status' => 'selesai',
                'total_biaya' => 250000,
                'catatan' => 'Pasien dirujuk untuk CT Scan, sudah selesai perawatan',
                'created_by' => 5,
            ],
            [
                'no_kunjungan' => 'KJ-' . $yesterday->format('Ymd') . '-0002',
                'pasien_id' => 5, // Budi Hartono
                'dokter_id' => 6, // drg. Lisa Anggraini (Gigi)
                'poli_id' => 6,   // Poli Gigi
                'jadwal_dokter_id' => 12,
                'tanggal_kunjungan' => $yesterday,
                'jam_kunjungan' => '09:00:00',
                'no_antrian' => 1,
                'jenis_kunjungan' => 'lama',
                'cara_bayar' => 'asuransi',
                'keluhan_utama' => 'Sakit gigi geraham kanan, nyeri berdenyut',
                'status' => 'selesai',
                'total_biaya' => 500000,
                'catatan' => 'Sudah dilakukan penambalan dan scaling',
                'created_by' => 6,
            ],
            [
                'no_kunjungan' => 'KJ-' . $yesterday->format('Ymd') . '-0003',
                'pasien_id' => 8, // Lisa Anggraini
                'dokter_id' => 1, // Dr. Ahmad Wijaya (Penyakit Dalam)
                'poli_id' => 3,   // Poli Penyakit Dalam
                'jadwal_dokter_id' => 2,
                'tanggal_kunjungan' => $yesterday,
                'jam_kunjungan' => '14:00:00',
                'no_antrian' => 1,
                'jenis_kunjungan' => 'baru',
                'cara_bayar' => 'bpjs',
                'keluhan_utama' => 'Nyeri dada kiri, sesak napas saat beraktivitas',
                'status' => 'selesai',
                'total_biaya' => 350000,
                'catatan' => 'Dilakukan EKG dan rontgen thorax',
                'created_by' => 5,
            ],

            // Kunjungan 2 hari lalu
            [
                'no_kunjungan' => 'KJ-' . $twoDaysAgo->format('Ymd') . '-0001',
                'pasien_id' => 6, // Maya Kusuma
                'dokter_id' => 3, // Dr. Budi Santoso (Bedah)
                'poli_id' => 4,   // Poli Bedah
                'jadwal_dokter_id' => 6,
                'tanggal_kunjungan' => $twoDaysAgo,
                'jam_kunjungan' => '08:30:00',
                'no_antrian' => 1,
                'jenis_kunjungan' => 'lama',
                'cara_bayar' => 'umum',
                'keluhan_utama' => 'Kontrol pasca operasi usus buntu (appendektomi)',
                'status' => 'selesai',
                'total_biaya' => 200000,
                'catatan' => 'Kondisi luka baik, jahitan akan dibuka minggu depan',
                'created_by' => 6,
            ],

            // Kunjungan besok (appointment)
            [
                'no_kunjungan' => 'KJ-' . $tomorrow->format('Ymd') . '-0001',
                'pasien_id' => 2, // Jane Smith (follow up)
                'dokter_id' => 2, // Dr. Siti Nurhaliza (Anak)
                'poli_id' => 2,   // Poli Anak
                'jadwal_dokter_id' => 4,
                'tanggal_kunjungan' => $tomorrow,
                'jam_kunjungan' => '14:00:00',
                'no_antrian' => 1,
                'jenis_kunjungan' => 'lama',
                'cara_bayar' => 'bpjs',
                'keluhan_utama' => 'Kontrol demam anak, evaluasi pengobatan',
                'status' => 'menunggu',
                'total_biaya' => 0,
                'catatan' => 'Appointment follow up dari kunjungan sebelumnya',
                'created_by' => 5,
            ],
            [
                'no_kunjungan' => 'KJ-' . $tomorrow->format('Ymd') . '-0002',
                'pasien_id' => 1, // John Doe (follow up)
                'dokter_id' => 4, // Dr. Maya Sari
                'poli_id' => 1,   // Poli Umum
                'jadwal_dokter_id' => 9,
                'tanggal_kunjungan' => $tomorrow,
                'jam_kunjungan' => '15:00:00',
                'no_antrian' => 1,
                'jenis_kunjungan' => 'lama',
                'cara_bayar' => 'umum',
                'keluhan_utama' => 'Kontrol hasil lab darah',
                'status' => 'menunggu',
                'total_biaya' => 0,
                'catatan' => 'Kontrol hasil pemeriksaan laboratorium',
                'created_by' => 6,
            ],
        ];

        foreach ($kunjungans as $kunjungan) {
            Kunjungan::create($kunjungan);
        }

        $this->command->info('Kunjungans seeded successfully! Total: ' . count($kunjungans) . ' records');
    }
}
