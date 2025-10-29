<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Roles;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Roles::where('role_code', 'admin')->first();
        $userRole = Roles::where('role_code', 'user')->first();
        $salesRole = Roles::where('role_code', 'sales')->first();
        $shipperRole = Roles::where('role_code', 'shipper')->first();
        $auditorRole = Roles::where('role_code', 'auditor')->first();
        $packerRole = Roles::where('role_code', 'packer')->first();

        // Ensure at least one admin
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => bcrypt('password'),
                'role_id' => optional($adminRole)->id,
                'status' => 'active',
            ]
        );

        $names = [
            'Nguyễn Văn A','Trần Thị B','Lê Hoàng C','Phạm Văn D','Vũ Thị E',
            'Đỗ Minh F','Phan Thị G','Bùi Văn H','Hoàng Thị I','Đinh Văn K'
        ];

        $rolesCycle = [
            $userRole, $salesRole, $shipperRole, $auditorRole, $packerRole,
            $userRole, $salesRole, $shipperRole, $auditorRole
        ];

        for ($i = 0; $i < 9; $i++) {
            $name = $names[$i] ?? ('User '.$i);
            $email = 'user'.$i.'@example.com';
            User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'password' => bcrypt('password'),
                    'role_id' => optional($rolesCycle[$i] ?? $userRole)->id,
                    'status' => 'active',
                ]
            );
        }
    }
}


