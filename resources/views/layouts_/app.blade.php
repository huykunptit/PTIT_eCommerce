<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Shop')</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    @stack('styles')
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="text-xl font-bold text-gray-800 hover:text-blue-600 transition duration-200">
                        <i class="fas fa-shopping-bag mr-2"></i>Shop
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    @auth
                        <span class="text-gray-700">Xin chào, {{ Auth::user()->name }}</span>
                        
                        @if(Auth::user()->role === 'admin')
                            <a href="{{ route('admin.dashboard') }}" class="text-red-600 hover:text-red-800 font-medium">
                                <i class="fas fa-crown mr-1"></i>Admin Panel
                            </a>
                        @else
                            <a href="{{ route('auth.dashboard') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                <i class="fas fa-tachometer-alt mr-1"></i>Dashboard
                            </a>
                        @endif
                        
                        <form method="POST" action="{{ route('auth.logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition duration-200">
                                <i class="fas fa-sign-out-alt mr-2"></i>Đăng xuất
                            </button>
                        </form>
                    @else
                        <a href="{{ route('auth.login') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                            <i class="fas fa-sign-in-alt mr-1"></i>Đăng nhập
                        </a>
                        <a href="{{ route('auth.register') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition duration-200">
                            <i class="fas fa-user-plus mr-1"></i>Đăng ký
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="py-6">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-12">
        <div class="max-w-7xl mx-auto py-4 px-4 text-center text-gray-600">
            <p>&copy; 2025 Shop. Tất cả quyền được bảo lưu.</p>
        </div>
    </footer>

    @stack('scripts')
</body>
</html> 