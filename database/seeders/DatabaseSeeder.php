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
        // 1. Admin Kullanıcısı Oluşturuyoruz
        User::create([
            'name' => 'Sistem Yöneticisi',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'), // Giriş yaparken kullanacağımız şifre
            'role' => 'admin',
        ]);

        // 2. Normal Kullanıcı Oluşturuyoruz
        User::create([
            'name' => 'Normal Kullanıcı',
            'email' => 'user@test.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
        ]);
    }
}