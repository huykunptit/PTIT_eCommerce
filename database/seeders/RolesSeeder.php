<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Roles;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['role_name' => 'Admin', 'role_code' => 'admin'],
            ['role_name' => 'Người sử dụng', 'role_code' => 'user'],
            ['role_name' => 'Nhân viên bán hàng', 'role_code' => 'sales'],
            ['role_name' => 'Nhân viên giao hàng', 'role_code' => 'shipper'],
            ['role_name' => 'Nhân viên kiểm toán', 'role_code' => 'auditor'],
            ['role_name' => 'Nhân viên đóng hàng', 'role_code' => 'packer'],
        ];

        foreach ($roles as $role) {
            Roles::firstOrCreate(['role_code' => $role['role_code']], $role);
        }
    }
}


