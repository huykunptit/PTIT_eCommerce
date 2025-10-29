<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use App\Models\User;

class ProductController extends Controller
{
        // Management Products
        public function products()
        {
            $products = Product::with('category')->paginate(10);
            $brands  = Brand::all();
            return view('backend.product.index', compact('products', 'brands'));
        }
    
            public function createProduct()
        {
            $categories = Category::all();
            $brands = Brand::all();
            $sellers = User::where('role_id', '<>', 1)->get();
            return view('backend.product.create', compact('categories','brands','sellers'));
        }
    
        public function storeProduct(Request $request)
        {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'quantity' => 'required|integer|min:0',
                'category_id' => 'required|exists:categories,id',
                'image_url' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:4096',
                'image_link' => 'nullable|url',
                'seller_id' => 'required|exists:users,id',
                'status' => 'required|in:active,inactive',
            ]);
    
            $imagePath = null;
            if ($request->hasFile('image_url')) {
                $file = $request->file('image_url');
                $filename = now()->format('YmdHis') . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
                $destination = public_path('uploads/img/product');
                if (!is_dir($destination)) {
                    mkdir($destination, 0755, true);
                }
                $file->move($destination, $filename);
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
    
            Product::create($data);
    
            return redirect()->route('admin.products.index')->with('success', 'Product created successfully!');
        }
    
        public function editProduct($id)
        {
            $product = Product::findOrFail($id);
            $categories = Category::all();
            $brands = Brand::all();
            $sellers = User::where('role_id', '<>', 1)->get();

            return view('backend.product.edit', compact('product', 'categories','brands','sellers'));
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
                'image_url' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:4096',
                'image_link' => 'nullable|url',
                'status' => 'required|in:active,inactive',
            ]);
    
            $imagePath = $product->image_url; // keep existing by default
            if ($request->hasFile('image_url')) {
                $file = $request->file('image_url');
                $filename = now()->format('YmdHis') . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
                $destination = public_path('uploads/img/product');
                if (!is_dir($destination)) {
                    mkdir($destination, 0755, true);
                }
                $file->move($destination, $filename);
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
    
            return redirect()->route('admin.products.index')->with('success', 'Product updated successfully!');
        }
    
        public function deleteProduct($id)
        {
            $product = Product::findOrFail($id);
            $product->delete();
    
            return redirect()->route('admin.products.index')->with('success', 'Product deleted  '.$product->name.' successfully!');
        }
}
