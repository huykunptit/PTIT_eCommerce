<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Banner;

class BannersSeeder extends Seeder
{
    public function run(): void
    {
        $banners = [
            ['title' => 'Khuyến mãi Kính', 'description' => 'Giảm giá hấp dẫn cho kính thời trang'],
            ['title' => 'Nhẫn cao cấp', 'description' => 'Bộ sưu tập nhẫn mới'],
            ['title' => 'Đồng hồ hot', 'description' => 'Xu hướng đồng hồ 2025'],
            ['title' => 'Vòng tay cá tính', 'description' => 'Phong cách mỗi ngày'],
            ['title' => 'Dây chuyền thanh lịch', 'description' => 'Tinh tế và sang trọng'],
        ];

        foreach ($banners as $b) {
            Banner::firstOrCreate(['title' => $b['title']], [
                'description' => $b['description'],
                'slug' => str()->slug($b['title']).'-'.uniqid(),
                'photo' => 'backend/img/banner.png',
                'status' => 'active',
            ]);
        }
    }
}


