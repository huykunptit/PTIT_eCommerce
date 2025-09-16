@extends('layouts.app')

@section('title', 'Thêm Product - Admin')

@section('content')
<div class="max-w-4xl mx-auto px-4">
	<!-- Header -->
	<div class="mb-8">
		<h1 class="text-3xl font-bold text-gray-800">Thêm Product Mới</h1>
		<p class="text-gray-600">Tạo sản phẩm mới</p>
	</div>

	@if($errors->any())
		<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
			<ul class="list-disc list-inside">
				@foreach($errors->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
			</ul>
		</div>
	@endif

	<!-- Form -->
	<div class="bg-white rounded-lg shadow-md p-6">
		<form method="POST" action="{{ route('admin.products.store') }}" class="space-y-6">
			@csrf

			<div>
				<label for="name" class="block text-sm font-medium text-gray-700 mb-2">
					<i class="fas fa-box mr-2"></i>Tên sản phẩm
				</label>
				<input type="text"
					   id="name"
					   name="name"
					   value="{{ old('name') }}"
					   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
					   placeholder="Nhập tên sản phẩm"
					   required>
			</div>

			<div>
				<label for="description" class="block text-sm font-medium text-gray-700 mb-2">
					<i class="fas fa-align-left mr-2"></i>Mô tả
				</label>
				<textarea id="description"
					  name="description"
					  rows="4"
					  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
					  placeholder="Nhập mô tả sản phẩm">{{ old('description') }}</textarea>
			</div>

			<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
				<div>
					<label for="price" class="block text-sm font-medium text-gray-700 mb-2">
						<i class="fas fa-dollar-sign mr-2"></i>Giá
					</label>
					<input type="number" step="0.01" min="0"
						   id="price"
						   name="price"
						   value="{{ old('price') }}"
						   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
						   placeholder="Nhập giá"
						   required>
				</div>

				<div>
					<label for="stock" class="block text-sm font-medium text-gray-700 mb-2">
						<i class="fas fa-warehouse mr-2"></i>Tồn kho
					</label>
					<input type="number" min="0"
						   id="stock"
						   name="stock"
						   value="{{ old('stock') }}"
						   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
						   placeholder="Nhập số lượng tồn"
						   required>
				</div>
			</div>

			<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
				<div>
					<label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
						<i class="fas fa-list mr-2"></i>Danh mục
					</label>
					<select id="category_id" name="category_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent" required>
						<option value="">-- Chọn danh mục --</option>
						@foreach($categories as $category)
							<option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
								{{ $category->name }}
							</option>
						@endforeach
					</select>
				</div>

				<div>
					<label for="image" class="block text-sm font-medium text-gray-700 mb-2">
						<i class="fas fa-image mr-2"></i>Ảnh (URL)
					</label>
					<input type="text"
						   id="image"
						   name="image"
						   value="{{ old('image') }}"
						   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
						   placeholder="https://...">
				</div>
			</div>

			<div class="flex justify-end space-x-4 pt-6">
				<a href="{{ route('admin.products') }}" class="bg-gray-500 text-white px-6 py-2 rounded-md hover:bg-gray-600 transition duration-200">
					<i class="fas fa-arrow-left mr-2"></i>Quay lại
				</a>
				<button type="submit" class="bg-purple-600 text-white px-6 py-2 rounded-md hover:bg-purple-700 transition duration-200">
					<i class="fas fa-save mr-2"></i>Lưu
				</button>
			</div>
		</form>
	</div>
</div>
@endsection 