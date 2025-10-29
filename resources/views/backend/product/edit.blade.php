@extends('backend.layouts.master')

@section('main-content')

<div class="row create-user-wrapper">
    <div class="col-lg-6">
        <div class="card form-half">
            <h5 class="card-header">Chỉnh sửa sản phẩm</h5>
            <div class="card-body">
                <form method="post" action="{{ route('admin.products.update', $product->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="form-group floating-group">
                        <input type="text" class="form-control modern-input" id="name" name="name" placeholder=" " value="{{ old('name', $product->name) }}">
                        <label for="name" class="col-form-label">Tên sản phẩm <span class="text-danger">*</span></label>
                        @error('name')
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>

                    <div class="form-group floating-group">
                        <textarea class="form-control modern-input" id="description" name="description" placeholder=" " style="height: 100px">{{ old('description', $product->description) }}</textarea>
                        <label for="description" class="col-form-label">Mô tả sản phẩm</label>
                        @error('description')
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>

                    <div class="form-group floating-group">
                        <input type="number" class="form-control modern-input" id="price" name="price" placeholder=" " step="0.01" value="{{ old('price', $product->price) }}">
                        <label for="price" class="col-form-label">Giá (VNĐ) <span class="text-danger">*</span></label>
                        @error('price')
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>

                    <div class="form-group floating-group">
                        <input type="number" class="form-control modern-input" id="quantity" name="quantity" placeholder=" " min="0" value="{{ old('quantity', $product->quantity) }}">
                        <label for="quantity" class="col-form-label">Số lượng <span class="text-danger">*</span></label>
                        @error('quantity')
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="seller_id" class="col-form-label">Người bán <span class="text-danger">*</span></label>
                        <select class="form-control" id="seller_id" name="seller_id">
                            <option value="">-- Chọn người bán --</option>
                            @if(isset($sellers))
                                @foreach($sellers as $seller)
                                    <option value="{{$seller->id}}" {{ old('seller_id', $product->seller_id) == $seller->id ? 'selected' : '' }}>{{$seller->name}}</option>
                                @endforeach
                            @endif
                        </select>
                        @error('seller_id')
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="category_id" class="col-form-label">Danh mục <span class="text-danger">*</span></label>
                        <select class="form-control" id="category_id" name="category_id">
                            <option value="">-- Chọn danh mục --</option>
                            @if(isset($categories))
                                @foreach($categories as $category)
                                    <option value="{{$category->id}}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{$category->name}}</option>
                                @endforeach
                            @endif
                        </select>
                        @error('category_id')
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="image_url" class="col-form-label">Hình ảnh sản phẩm</label>
                        <input type="file" class="form-control" id="image_url" name="image_url" accept="image/*">
                        @if($product->image_url)
                            <small class="text-muted">Ảnh hiện tại: <a href="{{ $product->image_url }}" target="_blank">Xem</a></small>
                        @endif
                        @error('image_url')
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="image_link" class="col-form-label">Hoặc dán URL ảnh</label>
                        <input type="url" class="form-control" id="image_link" name="image_link" value="{{old('image_link')}}" placeholder="https://...">
                        @error('image_link')
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                        <small class="text-muted">Chọn một trong hai: tải file hoặc nhập URL.</small>
                    </div>

                    <div class="form-group">
                        <label for="status" class="col-form-label">Trạng thái <span class="text-danger">*</span></label>
                        <select class="form-control" id="status" name="status">
                            <option value="active" {{ old('status', $product->status) == 'active' ? 'selected' : '' }}>Hoạt động</option>
                            <option value="inactive" {{ old('status', $product->status) == 'inactive' ? 'selected' : '' }}>Không hoạt động</option>
                        </select>
                        @error('status')
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <button type="reset" class="btn btn-warning">Đặt lại</button>
                        <button class="btn btn-success" type="submit">Cập nhật sản phẩm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body text-center">
                <h5 class="card-title">Xem trước sản phẩm</h5>
                <div class="preview-container">
                    <img id="imagePreview" src="{{ $product->image_url ? asset($product->image_url) : 'https://via.placeholder.com/300x200?text=Chưa+chọn+hình+ảnh' }}" alt="Preview" class="img-fluid rounded mb-3" style="max-height: 200px;">
                    <h6 id="namePreview">{{ old('name', $product->name) ?? 'Tên sản phẩm' }}</h6>
                    <p class="text-muted mb-1" id="pricePreview">Giá: {{ number_format(old('price', $product->price ?? 0), 0, ',', '.') }} VNĐ</p>
                    <p class="text-muted mb-1" id="quantityPreview">Số lượng: {{ old('quantity', $product->quantity ?? 0) }}</p>
                    <p class="text-muted mb-1" id="sellerPreview">Người bán: {{ optional($product->seller)->name ?? 'Chưa chọn' }}</p>
                    <p class="text-muted mb-1" id="categoryPreview">Danh mục: {{ optional($product->category)->name ?? 'Chưa chọn' }}</p>
                    <p class="text-muted mb-1" id="statusPreview">Trạng thái: {{ $product->status == 'inactive' ? 'Không hoạt động' : 'Hoạt động' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Live preview functionality
    $('#name').on('input', function() {
        $('#namePreview').text($(this).val() || 'Tên sản phẩm');
    });

    $('#price').on('input', function() {
        const price = $(this).val() || '0';
        const formatted = new Intl.NumberFormat('vi-VN').format(price);
        $('#pricePreview').text('Giá: ' + formatted + ' VNĐ');
    });

    $('#quantity').on('input', function() {
        $('#quantityPreview').text('Số lượng: ' + ($(this).val() || '0'));
    });

    $('#seller_id').on('change', function() {
        const selectedText = $(this).find('option:selected').text();
        $('#sellerPreview').text('Người bán: ' + (selectedText.includes('--') ? 'Chưa chọn' : selectedText));
    });

    $('#category_id').on('change', function() {
        const selectedText = $(this).find('option:selected').text();
        $('#categoryPreview').text('Danh mục: ' + (selectedText.includes('--') ? 'Chưa chọn' : selectedText));
    });

    $('#status').on('change', function() {
        const statusText = $(this).find('option:selected').text();
        $('#statusPreview').text('Trạng thái: ' + statusText);
    });

    $('#image_url').on('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#imagePreview').attr('src', e.target.result);
            };
            reader.readAsDataURL(file);
        }
    });
    $('#image_link').on('input', function(){
        const url = $(this).val();
        if (url) { $('#imagePreview').attr('src', url); }
    });
});
</script>
@endpush