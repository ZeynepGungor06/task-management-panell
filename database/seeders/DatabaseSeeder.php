<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Sistem Yöneticisi (Admin)
        User::create([
            'name' => 'Sistem Yöneticisi',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        // 2. Birim Müdürü (Manager)
        $manager = User::create([
            'name' => 'Ahmet Müdür',
            'email' => 'mudur@test.com',
            'password' => Hash::make('password123'),
            'role' => 'manager',
        ]);

        // 3. Müdüre Bağlı Normal Kullanıcı (User)
        User::create([
            'name' => 'Mehmet Eleman',
            'email' => 'user@test.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'manager_id' => $manager->id, // Ahmet Müdür'e bağlı
        ]);
    }
}