<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'nama_user' => 'Administrator Sistem',
                'email' => 'admin@kapuas.go.id',
                'password' => Hash::make('123'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ],
            [
                'nama_user' => 'Budi Santoso (Operator)',
                'email' => 'operator@kapuas.go.id',
                'password' => Hash::make('123'),
                'role' => 'operator',
                'email_verified_at' => now(),
            ],
            [
                'nama_user' => 'Siti Rahma (Viewer)',
                'email' => 'viewer@kapuas.go.id',
                'password' => Hash::make('123'),
                'role' => 'viewer',
                'email_verified_at' => now(),
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }
}
