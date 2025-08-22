<?php

use Illuminate\Database\Seeder;
use App\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'name' => 'Administrator',
                'email' => 'admin@simrs.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
            ],
            [
                'name' => 'Dr. Ahmad Wijaya',
                'email' => 'ahmad.wijaya@simrs.com',
                'password' => Hash::make('password'),
                'role' => 'dokter',
                'is_active' => true,
            ],
            [
                'name' => 'Dr. Siti Nurhaliza',
                'email' => 'siti.nurhaliza@simrs.com',
                'password' => Hash::make('password'),
                'role' => 'dokter',
                'is_active' => true,
            ],
            [
                'name' => 'Dr. Budi Santoso',
                'email' => 'budi.santoso@simrs.com',
                'password' => Hash::make('password'),
                'role' => 'dokter',
                'is_active' => true,
            ],
            [
                'name' => 'Rina Pendaftaran',
                'email' => 'rina@simrs.com',
                'password' => Hash::make('password'),
                'role' => 'pendaftaran',
                'is_active' => true,
            ],
            [
                'name' => 'Sari Pendaftaran',
                'email' => 'sari@simrs.com',
                'password' => Hash::make('password'),
                'role' => 'pendaftaran',
                'is_active' => true,
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }

        $this->command->info('Users seeded successfully!');
    }
}
