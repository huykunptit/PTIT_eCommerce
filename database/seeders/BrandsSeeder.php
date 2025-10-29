<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Brand;
use Illuminate\Support\Str;

class BrandsSeeder extends Seeder
{
    public function run(): void
    {
        $brands = ['Kính', 'Nhẫn', 'Đồng hồ', 'Vòng tay', 'Dây chuyền', 'Phụ kiện'];
        foreach ($brands as $title) {
            $slugBase = Str::slug($title);
            $slug = $slugBase;
            $i = 1;
            while (Brand::where('slug', $slug)->exists()) {
                $slug = $slugBase.'-'.$i++;
            }
            Brand::firstOrCreate(['title' => $title], [
                'slug' => $slug,
                'status' => 'active',
            ]);
        }
    }
}


