<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ProductsSeeder extends Seeder
{
    public function run(): void
    {
        // Xóa dữ liệu cũ (tạm tắt ràng buộc khóa ngoại để TRUNCATE an toàn)
        Schema::disableForeignKeyConstraints();
        DB::table('product_variants')->truncate();
        DB::table('products')->truncate();
        Schema::enableForeignKeyConstraints();

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

        // Các size và option mẫu
        $sizes = ['S', 'M', 'L', 'XL'];
        $colors = ['Đỏ', 'Xanh', 'Vàng', 'Đen', 'Trắng', 'Hồng', 'Xám', 'Nâu'];
        $materials = ['Bạc', 'Vàng', 'Da', 'Nhựa', 'Kim loại', 'Gỗ', 'Đá', 'Vải'];

        for ($i = 0; $i < 20; $i++) {
            $basePrice = rand(100000, 5000000) / 100;
            
            $product = Product::create([
                'name' => $names[$i] ?? ('Sản phẩm '.$i),
                'description' => 'Mô tả sản phẩm mẫu '.$i,
                'price' => $basePrice, // Giá mặc định
                'quantity' => rand(1, 200),
                'seller_id' => $sellers[array_rand($sellers)],
                'category_id' => $categories[array_rand($categories)],
                'image_url' => 'https://trangsuchas.com/wp-content/uploads/2021/01/trang-suc-dep-nhan-cap-dinh-da.jpg',
                'status' => 'active',
            ]);

            // Tạo variants cho mỗi product (2-4 variants ngẫu nhiên)
            $variantCount = rand(2, 4);
            $usedCombinations = [];

            for ($v = 0; $v < $variantCount; $v++) {
                // Chọn size và option ngẫu nhiên, tránh trùng
                $size = $sizes[array_rand($sizes)];
                $option = (rand(0, 1) === 0) 
                    ? $colors[array_rand($colors)] 
                    : $materials[array_rand($materials)];
                
                $combo = $size . '_' . $option;
                if (in_array($combo, $usedCombinations)) {
                    continue; // Bỏ qua nếu đã có
                }
                $usedCombinations[] = $combo;

                // Giá variant có thể khác giá base (±20%)
                $variantPrice = $basePrice * (1 + (rand(-20, 20) / 100));
                $variantPrice = max(10000, round($variantPrice, 2)); // Tối thiểu 10k

                ProductVariant::create([
                    'product_id' => $product->id,
                    'sku' => 'SKU-' . $product->id . '-' . ($v + 1),
                    'attributes' => [
                        'size' => $size,
                        'option' => $option,
                    ],
                    'price' => $variantPrice,
                    'stock' => rand(5, 100),
                    'status' => 'active',
                ]);
            }
        }
    }
}


