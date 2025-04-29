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
        // Create Super Admin
        User::create([
            'name' => 'DHH Platform Owner',
            'email' => 'superadmin@dhh.dev',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
        ]);

        // Create Admin users
        User::factory()
            ->count(2)
            ->admin()
            ->create();

        // Create Staff users
        User::factory()
            ->count(4)
            ->staff()
            ->create();
    }
} 