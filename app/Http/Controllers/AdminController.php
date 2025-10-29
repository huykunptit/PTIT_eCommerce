<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\BannerController;
class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    // Dashboard Admin
    public function dashboard()
    {
        $stats = [
            'total_users' => User::where('role', 'user')->count(),
            'total_products' => Product::count(),
            'total_categories' => Category::count(),
            'total_orders' => Order::count(),
            'recent_orders' => Order::with('user')->latest()->take(5)->get(),
            'recent_users' => User::where('role', 'user')->latest()->take(5)->get(),
        ];

        return view('admin.index', compact('stats'));
    }


    public function users()
    {
        $users = User::where('role', 'user')->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function createUser()
    {
        return view('admin.users.create');
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:4096',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $datePrefix = now()->format('Ymd');
            $file = $request->file('photo');
            $filename = $datePrefix.'_'.$file->getClientOriginalName();
            $destination = public_path('uploads/img/user');
            if (!is_dir($destination)) {
                mkdir($destination, 0755, true);
            }
            $file->move($destination, $filename);
            $photoPath = 'uploads/img/user/'.$filename;
        }

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'photo' => $photoPath,
            'phone_number' => $validated['phone_number'] ?? null,
            'address' => $validated['address'] ?? null,
            'role' => $request->input('role', 'user'),
            'status' => $request->input('status', 'active'),
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully!');
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:4096',
            'role' => 'nullable|in:admin,user',
            'status' => 'nullable|in:active,inactive',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $request->input('role', $user->role),
            'status' => $request->input('status', $user->status),
            'phone_number' => $validated['phone_number'] ?? $user->phone_number,
            'address' => $validated['address'] ?? $user->address,
        ];

        if (!empty($validated['password'])) {
            $data['password'] = bcrypt($validated['password']);
        }

        if ($request->hasFile('photo')) {
            $datePrefix = now()->format('Ymd');
            $file = $request->file('photo');
            $filename = $datePrefix.'_'.$file->getClientOriginalName();
            $destination = public_path('uploads/img/user');
            if (!is_dir($destination)) { mkdir($destination, 0755, true); }
            $file->move($destination, $filename);
            $data['photo'] = 'uploads/img/user/'.$filename;
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'Cập nhật người dùng '.$user->name.' thành công!');
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Xoá người dùng '.$user->name.' thành công!');
    }

    //Management Categories
    public function categories()
    {
        $categories = Category::paginate(10);
        return view('admin.category.index', compact('categories'));
    }

    public function createCategory()
    {
        return view('admin.category.create');
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_category_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:4096',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = now()->format('YmdHis') . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $destination = public_path('uploads/img/category');
            if (!is_dir($destination)) { mkdir($destination, 0755, true); }
            $file->move($destination, $filename);
            $imagePath = 'uploads/img/category/' . $filename;
        }

        Category::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'parent_category_id' => $validated['parent_category_id'] ?? null,
            'image' => $imagePath,
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully!');
    }

    public function editCategory($id)
    {
        $category = Category::findOrFail($id);
        return view('admin.category.edit', compact('category'));
    }

    public function updateCategory(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_category_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:4096',
        ]);

        $data = [
            'name' => $validated['name'],
            'description' => $validated['description'] ?? $category->description,
            'parent_category_id' => $validated['parent_category_id'] ?? $category->parent_category_id,
        ];

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = now()->format('YmdHis') . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $destination = public_path('uploads/img/category');
            if (!is_dir($destination)) { mkdir($destination, 0755, true); }
            $file->move($destination, $filename);
            $data['image'] = 'uploads/img/category/' . $filename;
        }

        $category->update($data);

        return redirect()->route('admin.categories.index')->with('success', 'Category updated successfully!');
    }

    public function deleteCategory($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Category deleted successfully!');
    }

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
        $sellers = User::where('role', 'user')->get();
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
        $sellers = User::where('role', 'user')->get();
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

    // Management Orders
    public function orders()
    {
        $orders = Order::with(['user', 'items.product'])->paginate(10);
        return view('admin.orders.index', compact('orders'));
    }

    public function showOrder($id)
    {
        $order = Order::with(['user', 'items.product'])->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    public function updateOrderStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
        ]);

        $order->update($validated);

        return redirect()->route('admin.orders')->with('success', 'Order status updated successfully!');
    }
} 