<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;

class ProductsSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::pluck('id')->all();
        $sellers = User::whereHas('getRole', function ($q) {
            $q->whereIn('role_code', ['sales','user']);
        })->pluck('id')->all();

        if (empty($categories) || empty($sellers)) {
            return; // prerequisites not ready
        }

        $names = [
            'Kính râm Classic','Nhẫn bạc tối giản','Đồng hồ dây da','Vòng tay hạt đá',
            'Dây chuyền trái tim','Kính mắt vuông','Nhẫn vàng sang trọng','Đồng hồ thể thao',
            'Vòng tay charm','Dây chuyền ngọc trai','Kính thời trang unisex','Nhẫn cặp đôi',
            'Đồng hồ vintage','Vòng tay da','Dây chuyền chữ cái','Kính phi công','Nhẫn kim cương',
            'Đồng hồ thông minh','Vòng tay bạc','Dây chuyền mảnh'
        ];

        for ($i = 0; $i < 20; $i++) {
            Product::create([
                'name' => $names[$i] ?? ('Sản phẩm '.$i),
                'description' => 'Mô tả sản phẩm mẫu '.$i,
                'price' => rand(100000, 5000000) / 100,
                'quantity' => rand(1, 200),
                'seller_id' => $sellers[array_rand($sellers)],
                'category_id' => $categories[array_rand($categories)],
                'image_url' => 'https://picsum.photos/seed/product'.$i.'/600/400',
                'status' => 'active',
            ]);
        }
    }
}


