<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-xl font-bold text-gray-800">Shop Dashboard</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-700">Xin chào, {{ Auth::user()->name }}</span>
                    <form method="POST" action="{{ route('auth.logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition duration-200">
                            <i class="fas fa-sign-out-alt mr-2"></i>Đăng xuất
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto py-6 px-4">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        <!-- User Info Card -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">
                <i class="fas fa-user-circle mr-2"></i>Thông tin tài khoản
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-gray-600 mb-2"><strong>Họ và tên:</strong></p>
                    <p class="text-gray-800">{{ Auth::user()->name }}</p>
                </div>
                <div>
                    <p class="text-gray-600 mb-2"><strong>Email:</strong></p>
                    <p class="text-gray-800">{{ Auth::user()->email }}</p>
                </div>
                <div>
                    <p class="text-gray-600 mb-2"><strong>Số điện thoại:</strong></p>
                    <p class="text-gray-800">{{ Auth::user()->phone_number ?: 'Chưa cập nhật' }}</p>
                </div>
                <div>
                    <p class="text-gray-600 mb-2"><strong>Vai trò:</strong></p>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                        {{ Auth::user()->role === 'admin' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800' }}">
                        {{ Auth::user()->role === 'admin' ? 'Admin' : 'User' }}
                    </span>
                </div>
                <div class="md:col-span-2">
                    <p class="text-gray-600 mb-2"><strong>Địa chỉ:</strong></p>
                    <p class="text-gray-800">{{ Auth::user()->address ?: 'Chưa cập nhật' }}</p>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <i class="fas fa-shopping-cart text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-800">Giỏ hàng</h3>
                        <p class="text-gray-600">Quản lý giỏ hàng</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <i class="fas fa-shopping-bag text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-800">Đơn hàng</h3>
                        <p class="text-gray-600">Xem lịch sử đơn hàng</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                        <i class="fas fa-star text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-800">Đánh giá</h3>
                        <p class="text-gray-600">Quản lý đánh giá</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">
                <i class="fas fa-history mr-2"></i>Hoạt động gần đây
            </h2>
            <div class="space-y-4">
                <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                    <div class="p-2 rounded-full bg-blue-100 text-blue-600 mr-4">
                        <i class="fas fa-sign-in-alt"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800">Đăng nhập thành công</p>
                        <p class="text-sm text-gray-600">{{ now()->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
                
                @if(Auth::user()->created_at)
                <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                    <div class="p-2 rounded-full bg-green-100 text-green-600 mr-4">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800">Tài khoản được tạo</p>
                        <p class="text-sm text-gray-600">{{ Auth::user()->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-12">
        <div class="max-w-7xl mx-auto py-4 px-4 text-center text-gray-600">
            <p>&copy; 2025 Shop. Tất cả quyền được bảo lưu.</p>
        </div>
    </footer>
</body>
</html> 