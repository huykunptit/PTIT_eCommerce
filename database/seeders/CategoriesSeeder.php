<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Kính', 'description' => 'Kính thời trang'],
            ['name' => 'Nhẫn', 'description' => 'Nhẫn thời trang'],
            ['name' => 'Đồng hồ', 'description' => 'Đồng hồ thời trang'],
            ['name' => 'Vòng tay', 'description' => 'Vòng tay phụ kiện'],
            ['name' => 'Dây chuyền', 'description' => 'Dây chuyền phụ kiện'],
            ['name' => 'Phụ kiện khác', 'description' => 'Phụ kiện thời trang'],
        ];

        foreach ($categories as $c) {
            Category::firstOrCreate(['name' => $c['name']], [
                'description' => $c['description'] ?? null,
                'image' => null,
                'parent_category_id' => null,
            ]);
        }
    }
}


