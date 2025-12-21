<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use OpenApi\Annotations as OA;

class AdminController extends Controller
{
    public function __construct()
    {
        // Middleware đã được áp dụng ở route level
    }

    // ========== DASHBOARD API ==========
    
    /**
     * @OA\Get(
     *     path="/api/admin/dashboard",
     *     tags={"Admin"},
     *     summary="Tổng quan dashboard admin",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Thống kê tổng quan",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_products' => Product::count(),
            'total_categories' => Category::count(),
            'total_orders' => Order::count(),
            'recent_orders' => Order::with('user')->latest()->take(5)->get(),
            'recent_users' => User::where('role', 'user')->latest()->take(5)->get(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    // ========== USERS API ==========
    /**
     * @OA\Get(
     *     path="/api/admin/users",
     *     tags={"Admin - Users"},
     *     summary="Danh sách người dùng (role=user)",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Tìm theo tên hoặc email",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="OK")
     * )
     */
    public function getUsers(Request $request)
    {
        $users = User::where('role', 'user')
            ->when($request->search, function($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            })
            ->paginate($request->per_page ?? 10);

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/admin/users/{id}",
     *     tags={"Admin - Users"},
     *     summary="Chi tiết người dùng",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID người dùng",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="OK"),
     *     @OA\Response(response=404, description="Không tìm thấy")
     * )
     */
    public function getUser($id)
    {
        $user = User::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/users",
     *     tags={"Admin - Users"},
     *     summary="Tạo mới người dùng",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password"},
     *             @OA\Property(property="name", type="string", example="Nguyễn Văn A"),
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password"),
     *             @OA\Property(property="phone_number", type="string", example="0123456789"),
     *             @OA\Property(property="address", type="string", example="Hà Nội")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Tạo thành công"),
     *     @OA\Response(response=422, description="Dữ liệu không hợp lệ")
     * )
     */
    public function createUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone_number' => $validated['phone_number'] ?? null,
            'address' => $validated['address'] ?? null,
            'role' => 'user',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => $user
        ], 201);
    }

    /**
     * @OA\Put(
     *     path="/api/admin/users/{id}",
     *     tags={"Admin - Users"},
     *     summary="Cập nhật người dùng",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID người dùng",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email"},
     *             @OA\Property(property="name", type="string", example="Nguyễn Văn B"),
     *             @OA\Property(property="email", type="string", format="email", example="new@example.com"),
     *             @OA\Property(property="phone_number", type="string", example="0987654321"),
     *             @OA\Property(property="address", type="string", example="TP.HCM")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Cập nhật thành công"),
     *     @OA\Response(response=404, description="Không tìm thấy")
     * )
     */
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($id)],
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data' => $user
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/users/{id}",
     *     tags={"Admin - Users"},
     *     summary="Xóa người dùng",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID người dùng",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Xóa thành công"),
     *     @OA\Response(response=404, description="Không tìm thấy")
     * )
     */
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    }

    // ========== CATEGORIES API ==========
    /**
     * @OA\Get(
     *     path="/api/admin/categories",
     *     tags={"Admin - Categories"},
     *     summary="Danh sách danh mục sản phẩm",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Tìm theo tên danh mục",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="OK")
     * )
     */
    public function getCategories(Request $request)
    {
        $categories = Category::when($request->search, function($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->withCount('products')
            ->paginate($request->per_page ?? 10);

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/admin/categories/{id}",
     *     tags={"Admin - Categories"},
     *     summary="Chi tiết danh mục",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID danh mục",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="OK"),
     *     @OA\Response(response=404, description="Không tìm thấy")
     * )
     */
    public function getCategory($id)
    {
        $category = Category::withCount('products')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $category
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/categories",
     *     tags={"Admin - Categories"},
     *     summary="Tạo danh mục mới",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Điện thoại"),
     *             @OA\Property(property="description", type="string", example="Danh mục điện thoại")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Tạo thành công"),
     *     @OA\Response(response=422, description="Dữ liệu không hợp lệ")
     * )
     */
    public function createCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string',
        ]);

        $category = Category::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully',
            'data' => $category
        ], 201);
    }

    /**
     * @OA\Put(
     *     path="/api/admin/categories/{id}",
     *     tags={"Admin - Categories"},
     *     summary="Cập nhật danh mục",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID danh mục",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Điện thoại - cập nhật"),
     *             @OA\Property(property="description", type="string", example="Mô tả mới")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Cập nhật thành công"),
     *     @OA\Response(response=404, description="Không tìm thấy")
     * )
     */
    public function updateCategory(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('categories')->ignore($id)],
            'description' => 'nullable|string',
        ]);

        $category->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully',
            'data' => $category
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/categories/{id}",
     *     tags={"Admin - Categories"},
     *     summary="Xóa danh mục",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID danh mục",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Xóa thành công"),
     *     @OA\Response(response=404, description="Không tìm thấy")
     * )
     */
    public function deleteCategory($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully'
        ]);
    }

    // ========== PRODUCTS API ==========
    
    /**
     * @OA\Get(
     *     path="/api/admin/products",
     *     tags={"Admin - Products"},
     *     summary="Danh sách sản phẩm (quản trị)",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Tìm theo tên sản phẩm",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="category_id",
     *         in="query",
     *         description="Lọc theo danh mục",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="OK")
     * )
     */
    public function getProducts(Request $request)
    {
        $products = Product::with('category')
            ->when($request->search, function($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->when($request->category_id, function($query, $categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->paginate($request->per_page ?? 10);

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/admin/products/{id}",
     *     tags={"Admin - Products"},
     *     summary="Chi tiết sản phẩm",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID sản phẩm",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="OK"),
     *     @OA\Response(response=404, description="Không tìm thấy")
     * )
     */
    public function getProduct($id)
    {
        $product = Product::with('category')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $product
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/products",
     *     tags={"Admin - Products"},
     *     summary="Tạo sản phẩm mới",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","price","stock","category_id"},
     *             @OA\Property(property="name", type="string", example="iPhone 16"),
     *             @OA\Property(property="description", type="string", example="Mô tả sản phẩm"),
     *             @OA\Property(property="price", type="number", format="float", example=19990000),
     *             @OA\Property(property="stock", type="integer", example=10),
     *             @OA\Property(property="category_id", type="integer", example=1),
     *             @OA\Property(property="image", type="string", example="uploads/products/iphone16.jpg")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Tạo thành công"),
     *     @OA\Response(response=422, description="Dữ liệu không hợp lệ")
     * )
     */
    public function createProduct(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|string',
        ]);

        $validated['seller_id'] = Auth::id();
        $product = Product::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'data' => $product->load('category')
        ], 201);
    }

    /**
     * @OA\Put(
     *     path="/api/admin/products/{id}",
     *     tags={"Admin - Products"},
     *     summary="Cập nhật sản phẩm",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID sản phẩm",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","price","stock","category_id"},
     *             @OA\Property(property="name", type="string", example="iPhone 16 Pro"),
     *             @OA\Property(property="description", type="string", example="Mô tả mới"),
     *             @OA\Property(property="price", type="number", format="float", example=25990000),
     *             @OA\Property(property="stock", type="integer", example=5),
     *             @OA\Property(property="category_id", type="integer", example=1),
     *             @OA\Property(property="image", type="string", example="uploads/products/iphone16-pro.jpg")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Cập nhật thành công"),
     *     @OA\Response(response=404, description="Không tìm thấy")
     * )
     */
    public function updateProduct(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|string',
        ]);

        $product->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'data' => $product->load('category')
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/products/{id}",
     *     tags={"Admin - Products"},
     *     summary="Xóa sản phẩm",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID sản phẩm",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Xóa thành công"),
     *     @OA\Response(response=404, description="Không tìm thấy")
     * )
     */
    public function deleteProduct($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully'
        ]);
    }

    // ========== ORDERS API ==========
    
    /**
     * @OA\Get(
     *     path="/api/admin/orders",
     *     tags={"Admin - Orders"},
     *     summary="Danh sách đơn hàng",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Lọc trạng thái đơn hàng",
     *         @OA\Schema(type="string", enum={"pending","processing","shipped","delivered","cancelled"})
     *     ),
     *     @OA\Response(response=200, description="OK")
     * )
     */
    public function getOrders(Request $request)
    {
        $orders = Order::with(['user', 'items.product'])
            ->when($request->status, function($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->search, function($query, $search) {
                $query->whereHas('user', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->paginate($request->per_page ?? 10);

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/admin/orders/{id}",
     *     tags={"Admin - Orders"},
     *     summary="Chi tiết đơn hàng",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID đơn hàng",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="OK"),
     *     @OA\Response(response=404, description="Không tìm thấy")
     * )
     */
    public function getOrder($id)
    {
        $order = Order::with(['user', 'items.product'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/admin/orders/{id}/status",
     *     tags={"Admin - Orders"},
     *     summary="Cập nhật trạng thái đơn hàng",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID đơn hàng",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"status"},
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 enum={"pending","processing","shipped","delivered","cancelled"},
     *                 example="processing"
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Cập nhật thành công"),
     *     @OA\Response(response=404, description="Không tìm thấy")
     * )
     */
    public function updateOrderStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
        ]);

        $order->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully',
            'data' => $order->load(['user', 'items.product'])
        ]);
    }

    // ========== STATISTICS API ==========
    
    /**
     * @OA\Get(
     *     path="/api/admin/statistics",
     *     tags={"Admin - Orders"},
     *     summary="Thống kê đơn hàng & người dùng",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response=200, description="OK")
     * )
     */
    public function getStatistics()
    {
        $stats = [
            'total_users' => User::count(),
            'total_products' => Product::count(),
            'total_categories' => Category::count(),
            'total_orders' => Order::count(),
            'orders_by_status' => [
                'pending' => Order::where('status', 'pending')->count(),
                'processing' => Order::where('status', 'processing')->count(),
                'shipped' => Order::where('status', 'shipped')->count(),
                'delivered' => Order::where('status', 'delivered')->count(),
                'cancelled' => Order::where('status', 'cancelled')->count(),
            ],
            'recent_orders' => Order::with('user')->latest()->take(5)->get(),
            'recent_users' => User::where('role', 'user')->latest()->take(5)->get(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    // ========== PERMISSIONS / RBAC API ==========

    /**
     * @OA\Get(
     *     path="/api/admin/employees/{id}/permissions",
     *     tags={"Admin - Permissions"},
     *     summary="Xem danh sách quyền chi tiết của một nhân viên",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID người dùng (nhân viên)",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(type="string", example="orders.manage")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Không tìm thấy người dùng")
     * )
     */
    public function getEmployeePermissions(int $id)
    {
        $user = User::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $user->permissions ?? [],
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/admin/employees/{id}/permissions",
     *     tags={"Admin - Permissions"},
     *     summary="Cập nhật quyền chi tiết cho một nhân viên",
     *     description="Ví dụ các quyền: employee.access, orders.manage, shipping.update, categories.manage, banners.manage, posts.manage, coupons.manage, system.settings",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID người dùng (nhân viên)",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"permissions"},
     *             @OA\Property(
     *                 property="permissions",
     *                 type="array",
     *                 @OA\Items(type="string", example="orders.manage")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cập nhật thành công",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Không tìm thấy người dùng"),
     *     @OA\Response(response=422, description="Dữ liệu không hợp lệ")
     * )
     */
    public function updateEmployeePermissions(Request $request, int $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'string',
        ]);

        $user->permissions = array_values(array_unique($validated['permissions']));
        $user->save();

        return response()->json([
            'success' => true,
            'data' => $user->permissions,
        ]);
    }
} 