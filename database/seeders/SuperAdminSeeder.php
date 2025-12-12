<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cek apakah super admin sudah ada
        if (!User::where('role', 'super_admin')->exists()) {
            User::create([
                'name' => 'Super Admin',
                'username' => 'superadmin',
                'email' => 'superadmin@politala.ac.id',
                'nim' => 'SUPERADMIN', // Placeholder NIM
                'role' => 'super_admin',
                'password' => Hash::make('password'), // Sebaiknya diganti nanti
                'telepon' => '6280000000000',
            ]);
            $this->command->info('Super Admin user created successfully.');
        } else {
            $this->command->info('Super Admin user already exists.');
        }
    }
}
