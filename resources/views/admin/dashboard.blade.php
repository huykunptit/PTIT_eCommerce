@extends('layouts.app')

@section('title', 'Admin Dashboard - Shop')

@section('content')
<div class="max-w-7xl mx-auto px-4">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Admin Dashboard</h1>
        <p class="text-gray-600">Quản lý hệ thống shop</p>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            {{ session('error') }}
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-users text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-800">{{ $stats['total_users'] }}</h3>
                    <p class="text-gray-600">Tổng người dùng</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-box text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-800">{{ $stats['total_products'] }}</h3>
                    <p class="text-gray-600">Tổng sản phẩm</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <i class="fas fa-tags text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-800">{{ $stats['total_categories'] }}</h3>
                    <p class="text-gray-600">Tổng danh mục</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fas fa-shopping-cart text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-800">{{ $stats['total_orders'] }}</h3>
                    <p class="text-gray-600">Tổng đơn hàng</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <a href="{{ route('admin.users') }}" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition duration-200">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-user-cog text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-800">Quản lý Users</h3>
                    <p class="text-gray-600">Thêm, sửa, xóa người dùng</p>
                </div>
            </div>
        </a>

        <a href="{{ route('admin.categories') }}" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition duration-200">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-tags text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-800">Quản lý Danh mục</h3>
                    <p class="text-gray-600">Thêm, sửa, xóa danh mục</p>
                </div>
            </div>
        </a>

        <a href="{{ route('admin.products') }}" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition duration-200">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <i class="fas fa-box text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-800">Quản lý Sản phẩm</h3>
                    <p class="text-gray-600">Thêm, sửa, xóa sản phẩm</p>
                </div>
            </div>
        </a>

        <a href="{{ route('admin.orders') }}" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition duration-200">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fas fa-shopping-cart text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-800">Quản lý Đơn hàng</h3>
                    <p class="text-gray-600">Xem và cập nhật đơn hàng</p>
                </div>
            </div>
        </a>
    </div>

    <!-- Recent Data -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Orders -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">
                <i class="fas fa-clock mr-2"></i>Đơn hàng gần đây
            </h2>
            @if($stats['recent_orders']->count() > 0)
                <div class="space-y-4">
                    @foreach($stats['recent_orders'] as $order)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-800">#{{ $order->id }}</p>
                                <p class="text-sm text-gray-600">{{ $order->user->name ?? 'N/A' }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-medium text-gray-800">{{ number_format($order->total_amount) }} VNĐ</p>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                       ($order->status === 'processing' ? 'bg-blue-100 text-blue-800' : 
                                       ($order->status === 'shipped' ? 'bg-purple-100 text-purple-800' : 
                                       ($order->status === 'delivered' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'))) }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 text-center">
                    <a href="{{ route('admin.orders') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                        Xem tất cả đơn hàng
                    </a>
                </div>
            @else
                <p class="text-gray-500 text-center py-4">Chưa có đơn hàng nào</p>
            @endif
        </div>

        <!-- Recent Users -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">
                <i class="fas fa-user-plus mr-2"></i>Người dùng mới
            </h2>
            @if($stats['recent_users']->count() > 0)
                <div class="space-y-4">
                    @foreach($stats['recent_users'] as $user)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-blue-600"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="font-medium text-gray-800">{{ $user->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $user->email }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-500">{{ $user->created_at->format('d/m/Y') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 text-center">
                    <a href="{{ route('admin.users') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                        Xem tất cả người dùng
                    </a>
                </div>
            @else
                <p class="text-gray-500 text-center py-4">Chưa có người dùng nào</p>
            @endif
        </div>
    </div>
</div>
@endsection 