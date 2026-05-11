<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\AdminUser;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create super admin
        AdminUser::create([
            'user_name' => 'superadmin',
            'nama_lengkap' => 'Super Administrator',
            'email' => 'superadmin@greenpoint.com',
            'password' => Hash::make('password'),
            'role' => 'superadmin',
            'status' => 'aktif',
            'no_hp' => '081234567890',
            'alamat' => 'Jakarta',
        ]);

        // Create admin
        AdminUser::create([
            'user_name' => 'admin',
            'nama_lengkap' => 'Administrator',
            'email' => 'admin@greenpoint.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'aktif',
            'no_hp' => '081234567891',
            'alamat' => 'Jakarta',
        ]);

        // Create operator
        AdminUser::create([
            'user_name' => 'operator',
            'nama_lengkap' => 'Operator',
            'email' => 'operator@greenpoint.com',
            'password' => Hash::make('password'),
            'role' => 'operator',
            'status' => 'aktif',
            'no_hp' => '081234567892',
            'alamat' => 'Jakarta',
        ]);
    }
}
