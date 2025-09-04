@extends('layouts.app')

@section('title', 'Trang chủ - Shop')

@section('content')
<div class="max-w-7xl mx-auto px-4">
    <!-- Hero Section -->
    <div class="text-center py-16">
        <h1 class="text-4xl md:text-6xl font-bold text-gray-800 mb-6">
            Chào mừng đến với <span class="text-blue-600">Shop</span>
        </h1>
        <p class="text-xl text-gray-600 mb-8 max-w-3xl mx-auto">
            Khám phá bộ sưu tập sản phẩm đa dạng với chất lượng cao và giá cả hợp lý
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    @auth
                <a href="{{ route('auth.dashboard') }}" class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 transition duration-200 text-lg font-semibold">
                    <i class="fas fa-tachometer-alt mr-2"></i>Vào Dashboard
                </a>
                    @else
                <a href="{{ route('auth.login') }}" class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 transition duration-200 text-lg font-semibold">
                    <i class="fas fa-sign-in-alt mr-2"></i>Đăng nhập
                </a>
                <a href="{{ route('auth.register') }}" class="bg-green-600 text-white px-8 py-3 rounded-lg hover:bg-green-700 transition duration-200 text-lg font-semibold">
                    <i class="fas fa-user-plus mr-2"></i>Đăng ký
                </a>
                    @endauth
                </div>
                </div>

    <!-- Features Section -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 py-16">
        <div class="text-center">
            <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-shipping-fast text-2xl text-blue-600"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-800 mb-2">Giao hàng nhanh</h3>
            <p class="text-gray-600">Giao hàng toàn quốc với thời gian nhanh chóng</p>
                                </div>

        <div class="text-center">
            <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-shield-alt text-2xl text-green-600"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-800 mb-2">Chất lượng đảm bảo</h3>
            <p class="text-gray-600">Sản phẩm chất lượng cao với chính sách bảo hành</p>
                            </div>

        <div class="text-center">
            <div class="bg-purple-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-headset text-2xl text-purple-600"></i>
                                </div>
            <h3 class="text-xl font-semibold text-gray-800 mb-2">Hỗ trợ 24/7</h3>
            <p class="text-gray-600">Đội ngũ hỗ trợ khách hàng chuyên nghiệp</p>
                            </div>
                                </div>

    <!-- About Section -->
    <div class="bg-white rounded-lg shadow-lg p-8 mb-16">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-800 mb-4">Về chúng tôi</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">
                Shop là nơi cung cấp các sản phẩm chất lượng cao với giá cả hợp lý. 
                Chúng tôi cam kết mang đến trải nghiệm mua sắm tốt nhất cho khách hàng.
                                </p>
                            </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Tại sao chọn chúng tôi?</h3>
                <ul class="space-y-2 text-gray-600">
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-2"></i>
                        Sản phẩm đa dạng, chất lượng cao
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-2"></i>
                        Giá cả cạnh tranh, minh bạch
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-2"></i>
                        Dịch vụ khách hàng chuyên nghiệp
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-2"></i>
                        Giao hàng nhanh chóng, an toàn
                    </li>
                </ul>
                                </div>

            <div>
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Thông tin liên hệ</h3>
                <div class="space-y-3 text-gray-600">
                    <div class="flex items-center">
                        <i class="fas fa-map-marker-alt text-blue-500 mr-3 w-5"></i>
                        <span>123 Đường ABC, Quận XYZ, TP.HCM</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-phone text-blue-500 mr-3 w-5"></i>
                        <span>0123 456 789</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-envelope text-blue-500 mr-3 w-5"></i>
                        <span>info@shop.com</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
