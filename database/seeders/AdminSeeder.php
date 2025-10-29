<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@shop.com',
            'password' => Hash::make('admin123'),
            'phone_number' => '0123456789',
            'address' => '123 Admin Street, Admin City',
            'role' => 'admin',
        ]);

        $this->command->info('Admin account created successfully!');
        $this->command->info('Email: admin@shop.com');
        $this->command->info('Password: admin123');
    }
} 