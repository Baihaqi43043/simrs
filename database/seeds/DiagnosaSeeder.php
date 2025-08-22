<?php

// ============================================
// database/seeds/DiagnosaSeeder.php
// ============================================

use Illuminate\Database\Seeder;
use App\Diagnosa;
use Carbon\Carbon;

class DiagnosaSeeder extends Seeder
{
    public function run()
    {
        $yesterday = Carbon::yesterday();
        $today = Carbon::today();
        $twoDaysAgo = Carbon::today()->subDays(2);

        $diagnosas = [
            // Diagnosa untuk kunjungan 1 (John Doe - demam batuk) - SELESAI
            [
                'kunjungan_id' => 1,
                'jenis_diagnosa' => 'utama',
                'kode_icd' => 'J06.9',
                'nama_diagnosa' => 'Acute upper respiratory infection, unspecified',
                'deskripsi' => 'Infeksi saluran pernapasan atas akut dengan gejala demam dan batuk produktif. Pasien mengalami gejala selama 3 hari.',
                'didiagnosa_oleh' => 4, // Dr. Maya Sari
                'tanggal_diagnosa' => $today->copy()->setTime(9, 15),
            ],
            [
                'kunjungan_id' => 1,
                'jenis_diagnosa' => 'sekunder',
                'kode_icd' => 'R50.9',
                'nama_diagnosa' => 'Fever, unspecified',
                'deskripsi' => 'Demam subfebris yang menyertai infeksi saluran pernapasan, suhu 37.8°C',
                'didiagnosa_oleh' => 4,
                'tanggal_diagnosa' => $today->copy()->setTime(9, 15),
            ],

            // Diagnosa untuk kunjungan 5 (Siti Aisyah - sakit kepala) - SELESAI KEMARIN
            [
                'kunjungan_id' => 5,
                'jenis_diagnosa' => 'utama',
                'kode_icd' => 'G44.1',
                'nama_diagnosa' => 'Vascular headache, not elsewhere classified',
                'deskripsi' => 'Sakit kepala vaskular dengan gejala pusing, mual, dan fotofobia. Hasil CT Scan kepala dalam batas normal.',
                'didiagnosa_oleh' => 4, // Dr. Maya Sari
                'tanggal_diagnosa' => $yesterday->copy()->setTime(11, 0),
            ],
            [
                'kunjungan_id' => 5,
                'jenis_diagnosa' => 'sekunder',
                'kode_icd' => 'R11',
                'nama_diagnosa' => 'Nausea and vomiting',
                'deskripsi' => 'Mual dan muntah yang menyertai sakit kepala, kemungkinan akibat migren',
                'didiagnosa_oleh' => 4,
                'tanggal_diagnosa' => $yesterday->copy()->setTime(11, 0),
            ],

            // Diagnosa untuk kunjungan 6 (Budi Hartono - gigi) - SELESAI KEMARIN
            [
                'kunjungan_id' => 6,
                'jenis_diagnosa' => 'utama',
                'kode_icd' => 'K02.9',
                'nama_diagnosa' => 'Dental caries, unspecified',
                'deskripsi' => 'Karies gigi pada molar 1 dan 2 kanan rahang bawah dengan kavitas sedang hingga dalam',
                'didiagnosa_oleh' => 6, // drg. Lisa Anggraini
                'tanggal_diagnosa' => $yesterday->copy()->setTime(9, 30),
            ],
            [
                'kunjungan_id' => 6,
                'jenis_diagnosa' => 'sekunder',
                'kode_icd' => 'K03.6',
                'nama_diagnosa' => 'Deposits [accretions] on teeth',
                'deskripsi' => 'Penumpukan plak dan karang gigi terutama di regio anterior dan posterior, memerlukan scaling',
                'didiagnosa_oleh' => 6,
                'tanggal_diagnosa' => $yesterday->copy()->setTime(9, 30),
            ],

            // Diagnosa untuk kunjungan 7 (Lisa Anggraini - nyeri dada) - SELESAI KEMARIN
            [
                'kunjungan_id' => 7,
                'jenis_diagnosa' => 'utama',
                'kode_icd' => 'R06.00',
                'nama_diagnosa' => 'Dyspnea, unspecified',
                'deskripsi' => 'Sesak napas saat beraktivitas dengan nyeri dada kiri. EKG dan rontgen thorax normal, kemungkinan anxiety disorder.',
                'didiagnosa_oleh' => 1, // Dr. Ahmad Wijaya
                'tanggal_diagnosa' => $yesterday->copy()->setTime(15, 30),
            ],
            [
                'kunjungan_id' => 7,
                'jenis_diagnosa' => 'sekunder',
                'kode_icd' => 'R07.89',
                'nama_diagnosa' => 'Other chest pain',
                'deskripsi' => 'Nyeri dada kiri non-kardiak, kemungkinan akibat stress dan kecemasan',
                'didiagnosa_oleh' => 1,
                'tanggal_diagnosa' => $yesterday->copy()->setTime(15, 30),
            ],
            [
                'kunjungan_id' => 7,
                'jenis_diagnosa' => 'sekunder',
                'kode_icd' => 'F41.9',
                'nama_diagnosa' => 'Anxiety disorder, unspecified',
                'deskripsi' => 'Gangguan cemas yang memicu gejala somatik berupa sesak napas dan nyeri dada',
                'didiagnosa_oleh' => 1,
                'tanggal_diagnosa' => $yesterday->copy()->setTime(15, 30),
            ],

            // Diagnosa untuk kunjungan 8 (Maya Kusuma - post op) - SELESAI 2 HARI LALU
            [
                'kunjungan_id' => 8,
                'jenis_diagnosa' => 'utama',
                'kode_icd' => 'Z48.00',
                'nama_diagnosa' => 'Encounter for change or removal of nonsurgical wound dressing',
                'deskripsi' => 'Kontrol pasca operasi appendektomi hari ke-7. Luka operasi baik, tidak ada tanda infeksi.',
                'didiagnosa_oleh' => 3, // Dr. Budi Santoso
                'tanggal_diagnosa' => $twoDaysAgo->copy()->setTime(9, 0),
            ],
            [
                'kunjungan_id' => 8,
                'jenis_diagnosa' => 'sekunder',
                'kode_icd' => 'K35.9',
                'nama_diagnosa' => 'Acute appendicitis, unspecified',
                'deskripsi' => 'Riwayat appendisitis akut yang telah dilakukan appendektomi 1 minggu yang lalu',
                'didiagnosa_oleh' => 3,
                'tanggal_diagnosa' => $twoDaysAgo->copy()->setTime(9, 0),
            ],
        ];

        foreach ($diagnosas as $diagnosa) {
            Diagnosa::create($diagnosa);
        }

        $this->command->info('Diagnosas seeded successfully! Total: ' . count($diagnosas) . ' records');
        $this->command->info('Diagnosa distribution:');
        $this->command->info('- Utama: ' . collect($diagnosas)->where('jenis_diagnosa', 'utama')->count());
        $this->command->info('- Sekunder: ' . collect($diagnosas)->where('jenis_diagnosa', 'sekunder')->count());
    }
}
