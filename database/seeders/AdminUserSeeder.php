<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::updateOrCreate(
        ['email' => 'admin@admin.de'],
        [
            'name' => 'Admin',
            'first_name' => 'Admin',
            'last_name' => 'User',
            'password' => Hash::make('password'),
            'is_admin' => true,
            'is_active' => true,
            'must_change_password' => true,
            'email_verified_at' => now(),
        ]
        );
    }
}
