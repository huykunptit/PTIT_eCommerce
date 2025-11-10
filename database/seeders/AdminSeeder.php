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
            'email' => 'admin@ptit.com',
            'password' => Hash::make('Demo@1234'),
            'phone_number' => '0123456789',
            'address' => '123 Admin Street, Admin City',
            'role_id' => '1',
            'status' => 'active',
        ]);

        $this->command->info('Admin account created successfully!');
        $this->command->info('Email: admin@ptit.com');
        $this->command->info('Password: Demo@1234');
    }
} 