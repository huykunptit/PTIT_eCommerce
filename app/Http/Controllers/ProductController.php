<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use App\Models\User;
use App\Models\ProductVariant;
use App\Models\Tag;

class ProductController extends Controller
{
        // Management Products
        public function products()
        {
            $products = Product::with(['category','seller'])->paginate(10);
            $brands  = Brand::all();
            return view('backend.product.index', compact('products', 'brands'));
        }
    
            public function createProduct()
        {
            $categories = Category::all();
            $brands = Brand::all();
            $sellers = User::where('role_id', '<>', 1)->get();
            $tags = Tag::all();
            return view('backend.product.create', compact('categories','brands','sellers','tags'));
        }
    
        public function storeProduct(Request $request)
        {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'quantity' => 'required|integer|min:0',
                'category_id' => 'required|exists:categories,id',
                'image_url' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:5120', // 5MB
                'image_link' => 'nullable|url',
                'seller_id' => 'required|exists:users,id',
                'status' => 'required|in:active,inactive',
                'variant_image.*' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:2048', // 2MB per variant
            ]);
    
            $imagePath = null;
            if ($request->hasFile('image_url')) {
                $file = $request->file('image_url');
                $filename = now()->format('YmdHis') . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
                $destination = public_path('uploads/img/product');
                if (!is_dir($destination)) {
                    mkdir($destination, 0755, true);
                }
                
                // Save original first
                $tempPath = $destination . '/temp_' . $filename;
                $file->move($destination, 'temp_' . $filename);
                
                // Resize and optimize
                $finalPath = $destination . '/' . $filename;
                \App\Helpers\ImageHelper::resizeImage($tempPath, $finalPath, 800, 800, 85);
                
                // Delete temp file
                if (file_exists($tempPath)) {
                    unlink($tempPath);
                }
                
                $imagePath = 'uploads/img/product/' . $filename;
            } elseif (!empty($validated['image_link'])) {
                $imagePath = $validated['image_link'];
            }
    
            $data = [
                'name' => $validated['name'],
                'description' => $validated['description'] ?? '',
                'price' => $validated['price'],
                'quantity' => $validated['quantity'],
                'category_id' => $validated['category_id'],
                'image_url' => $imagePath,
                'seller_id' => $validated['seller_id'],
                'status' => $validated['status'],
            ];
    
            $product = Product::create($data);

            // Sync tags if provided
            if ($request->has('tags')) {
                $tagIds = is_array($request->tags) ? $request->tags : [];
                $product->tags()->sync($tagIds);
            }

            // Variants (optional): arrays variant_sku[], variant_price[], variant_stock[], variant_size[], variant_option[]
            $variantSkus = (array) $request->input('variant_sku', []);
            $variantPrices = (array) $request->input('variant_price', []);
            $variantStocks = (array) $request->input('variant_stock', []);
            $variantSizes = (array) $request->input('variant_size', []);
            $variantOptions = (array) $request->input('variant_option', []);
            
            // Validate total stock
            if (!empty($variantStocks)) {
                $totalVariantStock = 0;
                foreach ($variantStocks as $stock) {
                    $totalVariantStock += (int) ($stock ?? 0);
                }
                
                if ($totalVariantStock > $validated['quantity']) {
                    return back()->withErrors([
                        'variants' => "Tổng số lượng các biến thể ({$totalVariantStock}) không được vượt quá số lượng sản phẩm ({$validated['quantity']})"
                    ])->withInput();
                }
            }
            
            // Handle variant images
            $variantImages = $request->hasFile('variant_image') ? $request->file('variant_image') : [];
            $destination = public_path('uploads/img/product/variants');
            if (!is_dir($destination)) {
                mkdir($destination, 0755, true);
            }

            foreach ($variantPrices as $idx => $price) {
                if ($price === null || $price === '') { continue; }
                $attributes = [];
                if (isset($variantSizes[$idx]) && $variantSizes[$idx] !== '') { $attributes['size'] = $variantSizes[$idx]; }
                if (isset($variantOptions[$idx]) && $variantOptions[$idx] !== '') { $attributes['option'] = $variantOptions[$idx]; }
                
                // Handle variant image
                $variantImagePath = null;
                if (isset($variantImages[$idx]) && $variantImages[$idx]) {
                    $file = $variantImages[$idx];
                    $filename = now()->format('YmdHis') . '_' . $idx . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
                    
                    // Save original first
                    $tempPath = $destination . '/temp_' . $filename;
                    $file->move($destination, 'temp_' . $filename);
                    
                    // Resize and optimize (smaller for variants)
                    $finalPath = $destination . '/' . $filename;
                    \App\Helpers\ImageHelper::resizeImage($tempPath, $finalPath, 600, 600, 85);
                    
                    // Delete temp file
                    if (file_exists($tempPath)) {
                        unlink($tempPath);
                    }
                    
                    $variantImagePath = 'uploads/img/product/variants/' . $filename;
                }
                
                $variantData = [
                    'product_id' => $product->id,
                    'sku' => $variantSkus[$idx] ?? null,
                    'attributes' => $attributes,
                    'price' => (float) $price,
                    'stock' => (int) ($variantStocks[$idx] ?? 0),
                    'status' => 'active',
                ];
                
                // Only add image if column exists and has value
                if ($variantImagePath) {
                    $variantData['image'] = $variantImagePath;
                }
                
                ProductVariant::create($variantData);
            }
    
            return redirect()->route('admin.products.index')->with('success', 'Product created successfully!');
        }
    
        public function editProduct($id)
        {
            $product = Product::with('tags')->findOrFail($id);
            $categories = Category::all();
            $brands = Brand::all();
            $sellers = User::where('role_id', '<>', 1)->get();
            $tags = Tag::all();

            return view('backend.product.edit', compact('product', 'categories','brands','sellers','tags'));
        }
    
        public function updateProduct(Request $request, $id)
        {
            $product = Product::findOrFail($id);
    
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'quantity' => 'required|integer|min:0', // fixed: use quantity
                'category_id' => 'required|exists:categories,id',
                'image_url' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:5120', // 5MB
                'image_link' => 'nullable|url',
                'status' => 'required|in:active,inactive',
                'variant_image.*' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:2048', // 2MB per variant
            ]);
    
            $imagePath = $product->image_url; // keep existing by default
            if ($request->hasFile('image_url')) {
                $file = $request->file('image_url');
                $filename = now()->format('YmdHis') . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
                $destination = public_path('uploads/img/product');
                if (!is_dir($destination)) {
                    mkdir($destination, 0755, true);
                }
                
                // Save original first
                $tempPath = $destination . '/temp_' . $filename;
                $file->move($destination, 'temp_' . $filename);
                
                // Resize and optimize
                $finalPath = $destination . '/' . $filename;
                \App\Helpers\ImageHelper::resizeImage($tempPath, $finalPath, 800, 800, 85);
                
                // Delete temp file
                if (file_exists($tempPath)) {
                    unlink($tempPath);
                }
                
                $imagePath = 'uploads/img/product/' . $filename;
    
                // optional: delete old file
                // if ($product->image_url && file_exists(public_path($product->image_url))) { unlink(public_path($product->image_url)); }
            } elseif (!empty($validated['image_link'])) {
                $imagePath = $validated['image_link'];
            }
    
            $data = [
                'name' => $validated['name'],
                'description' => $validated['description'] ?? '',
                'price' => $validated['price'],
                'quantity' => $validated['quantity'],
                'category_id' => $validated['category_id'],
                'image_url' => $imagePath,
                'status' => $validated['status'],
            ];
    
            $product->update($data);

            // Replace variants if provided
            if ($request->hasAny(['variant_price','variant_stock','variant_size','variant_option','variant_sku'])) {
                $variantSkus = (array) $request->input('variant_sku', []);
                $variantPrices = (array) $request->input('variant_price', []);
                $variantStocks = (array) $request->input('variant_stock', []);
                $variantSizes = (array) $request->input('variant_size', []);
                $variantOptions = (array) $request->input('variant_option', []);
                $variantImageExisting = (array) $request->input('variant_image_existing', []);
                
                // Validate total stock
                $totalVariantStock = 0;
                foreach ($variantStocks as $stock) {
                    $totalVariantStock += (int) ($stock ?? 0);
                }
                
                if ($totalVariantStock > $validated['quantity']) {
                    return back()->withErrors([
                        'variants' => "Tổng số lượng các biến thể ({$totalVariantStock}) không được vượt quá số lượng sản phẩm ({$validated['quantity']})"
                    ])->withInput();
                }
                
                // Delete old variants
                $oldVariants = ProductVariant::where('product_id', $product->id)->get();
                foreach ($oldVariants as $oldVariant) {
                    // Delete old image if exists
                    if ($oldVariant->image && file_exists(public_path($oldVariant->image))) {
                        unlink(public_path($oldVariant->image));
                    }
                }
                ProductVariant::where('product_id', $product->id)->delete();
                
                // Create new variants
                $variantImages = $request->hasFile('variant_image') ? $request->file('variant_image') : [];
                $destination = public_path('uploads/img/product/variants');
                if (!is_dir($destination)) {
                    mkdir($destination, 0755, true);
                }
                
                foreach ($variantPrices as $idx => $price) {
                    if ($price === null || $price === '') { continue; }
                    $attributes = [];
                    if (isset($variantSizes[$idx]) && $variantSizes[$idx] !== '') { $attributes['size'] = $variantSizes[$idx]; }
                    if (isset($variantOptions[$idx]) && $variantOptions[$idx] !== '') { $attributes['option'] = $variantOptions[$idx]; }
                    
                    // Handle variant image
                    $variantImagePath = null;
                    if (isset($variantImages[$idx]) && $variantImages[$idx]) {
                        $file = $variantImages[$idx];
                        $filename = now()->format('YmdHis') . '_' . $idx . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
                        
                        // Save original first
                        $tempPath = $destination . '/temp_' . $filename;
                        $file->move($destination, 'temp_' . $filename);
                        
                        // Resize and optimize (smaller for variants)
                        $finalPath = $destination . '/' . $filename;
                        \App\Helpers\ImageHelper::resizeImage($tempPath, $finalPath, 600, 600, 85);
                        
                        // Delete temp file
                        if (file_exists($tempPath)) {
                            unlink($tempPath);
                        }
                        
                        $variantImagePath = 'uploads/img/product/variants/' . $filename;
                    } elseif (isset($variantImageExisting[$idx]) && !empty($variantImageExisting[$idx])) {
                        $variantImagePath = $variantImageExisting[$idx];
                    }
                    
                    try {
                        $variantData = [
                            'product_id' => $product->id,
                            'sku' => $variantSkus[$idx] ?? null,
                            'attributes' => $attributes,
                            'price' => (float) $price,
                            'stock' => (int) ($variantStocks[$idx] ?? 0),
                            'status' => 'active',
                        ];
                        
                        // Only add image if column exists (check schema)
                        if ($variantImagePath && Schema::hasColumn('product_variants', 'image')) {
                            $variantData['image'] = $variantImagePath;
                        }
                        
                        ProductVariant::create($variantData);
                    } catch (\Illuminate\Database\QueryException $e) {
                        // If image column doesn't exist, create without image
                        if (str_contains($e->getMessage(), "Unknown column 'image'")) {
                            ProductVariant::create([
                                'product_id' => $product->id,
                                'sku' => $variantSkus[$idx] ?? null,
                                'attributes' => $attributes,
                                'price' => (float) $price,
                                'stock' => (int) ($variantStocks[$idx] ?? 0),
                                'status' => 'active',
                            ]);
                        } else {
                            throw $e;
                        }
                    }
                }
            }

            // Sync tags if provided
            if ($request->has('tags')) {
                $tagIds = is_array($request->tags) ? $request->tags : [];
                $product->tags()->sync($tagIds);
            } else {
                // If tags not provided, keep existing tags
                // Or remove all tags if you want: $product->tags()->detach();
            }
    
            return redirect()->route('admin.products.index')->with('success', 'Product updated successfully!');
        }
    
        public function deleteProduct($id)
        {
            $product = Product::findOrFail($id);
            $product->delete();
    
            return redirect()->route('admin.products.index')->with('success', 'Product deleted  '.$product->name.' successfully!');
        }
}
