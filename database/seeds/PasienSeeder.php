<?php

// ============================================
// database/seeds/PasienSeeder.php
// ============================================

use Illuminate\Database\Seeder;
use App\Pasien;
use Carbon\Carbon;

class PasienSeeder extends Seeder
{
    public function run()
    {
        $pasiens = [
            [
                'no_rm' => 'RM-20241201-0001',
                'nik' => '3301010101900001',
                'nama' => 'John Doe',
                'tanggal_lahir' => '1990-01-01',
                'tempat_lahir' => 'Jakarta',
                'jenis_kelamin' => 'L',
                'alamat' => 'Jl. Merdeka No. 1, Jakarta',
                'no_telepon' => '081234567801',
                'no_telepon_darurat' => '081234567901',
                'nama_kontak_darurat' => 'Jane Doe',
            ],
            [
                'no_rm' => 'RM-20241201-0002',
                'nik' => '3301010201920002',
                'nama' => 'Jane Smith',
                'tanggal_lahir' => '1992-02-15',
                'tempat_lahir' => 'Bandung',
                'jenis_kelamin' => 'P',
                'alamat' => 'Jl. Sudirman No. 2, Bandung',
                'no_telepon' => '081234567802',
                'no_telepon_darurat' => '081234567902',
                'nama_kontak_darurat' => 'John Smith',
            ],
            [
                'no_rm' => 'RM-20241201-0003',
                'nik' => '3301010301850003',
                'nama' => 'Ahmad Rahman',
                'tanggal_lahir' => '1985-03-20',
                'tempat_lahir' => 'Surabaya',
                'jenis_kelamin' => 'L',
                'alamat' => 'Jl. Diponegoro No. 3, Surabaya',
                'no_telepon' => '081234567803',
                'no_telepon_darurat' => '081234567903',
                'nama_kontak_darurat' => 'Siti Rahman',
            ],
            [
                'no_rm' => 'RM-20241201-0004',
                'nik' => '3301010401880004',
                'nama' => 'Siti Aisyah',
                'tanggal_lahir' => '1988-04-10',
                'tempat_lahir' => 'Medan',
                'jenis_kelamin' => 'P',
                'alamat' => 'Jl. Gajah Mada No. 4, Medan',
                'no_telepon' => '081234567804',
                'no_telepon_darurat' => '081234567904',
                'nama_kontak_darurat' => 'Ahmad Aisyah',
            ],
            [
                'no_rm' => 'RM-20241201-0005',
                'nik' => '3301010501950005',
                'nama' => 'Budi Hartono',
                'tanggal_lahir' => '1995-05-25',
                'tempat_lahir' => 'Yogyakarta',
                'jenis_kelamin' => 'L',
                'alamat' => 'Jl. Malioboro No. 5, Yogyakarta',
                'no_telepon' => '081234567805',
                'no_telepon_darurat' => '081234567905',
                'nama_kontak_darurat' => 'Sri Hartono',
            ],
            [
                'no_rm' => 'RM-20241201-0006',
                'nik' => '3301010601870006',
                'nama' => 'Maya Kusuma',
                'tanggal_lahir' => '1987-06-12',
                'tempat_lahir' => 'Semarang',
                'jenis_kelamin' => 'P',
                'alamat' => 'Jl. Pemuda No. 6, Semarang',
                'no_telepon' => '081234567806',
                'no_telepon_darurat' => '081234567906',
                'nama_kontak_darurat' => 'Rudi Kusuma',
            ],
            [
                'no_rm' => 'RM-20241201-0007',
                'nik' => '3301010701930007',
                'nama' => 'Andi Pratama',
                'tanggal_lahir' => '1993-07-08',
                'tempat_lahir' => 'Makassar',
                'jenis_kelamin' => 'L',
                'alamat' => 'Jl. Veteran No. 7, Makassar',
                'no_telepon' => '081234567807',
                'no_telepon_darurat' => '081234567907',
                'nama_kontak_darurat' => 'Dewi Pratama',
            ],
            [
                'no_rm' => 'RM-20241201-0008',
                'nik' => '3301010801910008',
                'nama' => 'Lisa Anggraini',
                'tanggal_lahir' => '1991-08-30',
                'tempat_lahir' => 'Palembang',
                'jenis_kelamin' => 'P',
                'alamat' => 'Jl. Sudirman No. 8, Palembang',
                'no_telepon' => '081234567808',
                'no_telepon_darurat' => '081234567908',
                'nama_kontak_darurat' => 'Eko Anggraini',
            ],
        ];

        foreach ($pasiens as $pasien) {
            Pasien::create($pasien);
        }

        $this->command->info('Pasiens seeded successfully!');
    }
}
