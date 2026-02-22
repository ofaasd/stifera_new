<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Buat Role SuperAdmin jika belum ada di table roles
        $role = Role::firstOrCreate(['name' => 'SuperAdmin']);

        // 2. Buat User atau update jika email sudah ada
        $user = User::updateOrCreate(
            ['email' => 'admin@stifera.ac.id'], // Unsur unik untuk pengecekan
            [
                'name' => 'Admin Stifera',
                'password' => Hash::make('lazyprogrammer'), // Sesuai permintaan Anda
            ]
        );

        // 3. Assign Role ke User menggunakan logika Spatie
        $user->assignRole($role);
    }
}