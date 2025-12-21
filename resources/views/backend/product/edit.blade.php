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
                        <label for="tags" class="col-form-label">Tags</label>
                        <select class="form-control select2" id="tags" name="tags[]" multiple>
                            @if(isset($tags))
                                @foreach($tags as $tag)
                                    <option value="{{ $tag->id }}" 
                                            {{ $product->tags->contains($tag->id) ? 'selected' : '' }}
                                            style="color: {{ $tag->color }};">
                                        {{ $tag->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        <small class="form-text text-muted">Chọn các tags cho sản phẩm (có thể chọn nhiều)</small>
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

                    @php
                        $existingVariants = \App\Models\ProductVariant::where('product_id', $product->id)->get();
                        // Thu thập tất cả các Size và Option từ các biến thể
                        $allSizes = [];
                        $allOptions = [];
                        $sizeOptionMap = []; // Map Size -> [Options]
                        $variantPriceMap = []; // Map "Size|Option" -> Price
                        
                        foreach($existingVariants as $v) {
                            $attrs = is_array($v->attributes) ? $v->attributes : (is_string($v->attributes) ? json_decode($v->attributes, true) : []);
                            if (!is_array($attrs)) $attrs = [];
                            
                            $size = $attrs['size'] ?? '';
                            $option = $attrs['option'] ?? '';
                            
                            if ($size && !in_array($size, $allSizes)) {
                                $allSizes[] = $size;
                            }
                            if ($option && !in_array($option, $allOptions)) {
                                $allOptions[] = $option;
                            }
                            
                            if ($size && $option) {
                                if (!isset($sizeOptionMap[$size])) {
                                    $sizeOptionMap[$size] = [];
                                }
                                if (!in_array($option, $sizeOptionMap[$size])) {
                                    $sizeOptionMap[$size][] = $option;
                                }
                                $variantPriceMap[$size . '|' . $option] = $v->price;
                            }
                        }
                    @endphp
                    <hr>
                    <h5 class="mb-3">Biến thể (Size/Option/Price)</h5>
                    <div class="alert alert-danger" id="stock-warning" style="display:none;">
                        <strong><i class="fa fa-exclamation-triangle"></i> Cảnh báo:</strong> Tổng số lượng các biến thể (<span id="total-variant-stock" style="font-weight:bold;color:#e53e3e;">0</span>) không được vượt quá số lượng sản phẩm (<span id="product-quantity" style="font-weight:bold;">{{ $product->quantity }}</span>)
                    </div>
                    @error('variants')
                        <div class="alert alert-danger">
                            {{ $message }}
                        </div>
                    @enderror
                    <div id="variant-rows">
                        @foreach($existingVariants as $v)
                            @php 
                                // Model tự động cast attributes thành array
                                $attrs = is_array($v->attributes) ? $v->attributes : (is_string($v->attributes) ? json_decode($v->attributes, true) : []);
                                if (!is_array($attrs)) $attrs = [];
                            @endphp
                            <div class="form-row align-items-end mb-3 variant-row" style="border: 1px solid #ddd; padding: 15px; border-radius: 5px; background: #f9f9f9;">
                                <div class="col-md-12 mb-3">
                                    <label><strong>Ảnh biến thể</strong></label>
                                    <div class="d-flex align-items-center gap-3">
                                        @if(isset($v->image) && $v->image)
                                            <div class="mr-3">
                                                <img src="{{ asset($v->image) }}" alt="Current" style="max-width: 80px; max-height: 80px; border: 1px solid #ddd; border-radius: 4px; object-fit: cover;">
                                                <br><small class="text-muted">Ảnh hiện tại</small>
                                            </div>
                                        @endif
                                        <div class="flex-grow-1">
                                            <input type="file" name="variant_image[]" class="form-control variant-image" accept="image/*">
                                            @if(isset($v->image) && $v->image)
                                                <input type="hidden" name="variant_image_existing[]" value="{{ $v->image }}">
                                            @else
                                                <input type="hidden" name="variant_image_existing[]" value="">
                                            @endif
                                            <div class="variant-image-preview mt-2" style="display:none;">
                                                <img src="" alt="Preview" style="max-width: 80px; max-height: 80px; border: 1px solid #ddd; border-radius: 4px; object-fit: cover;">
                                                <br><small class="text-muted">Ảnh mới</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 col-6 mb-2"><label>SKU</label><input name="variant_sku[]" class="form-control" value="{{$v->sku}}"></div>
                                <div class="col-md-2 col-6 mb-2">
                                    <label>Size</label>
                                    <select name="variant_size[]" class="form-control variant-size-select">
                                        <option value="">-- Chọn Size --</option>
                                        @foreach($allSizes as $size)
                                            <option value="{{ $size }}" {{ ($attrs['size'] ?? '') == $size ? 'selected' : '' }}>{{ $size }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2 col-6 mb-2">
                                    <label>Option</label>
                                    <select name="variant_option[]" class="form-control variant-option-select">
                                        <option value="">-- Chọn Option --</option>
                                        @php
                                            $currentSize = $attrs['size'] ?? '';
                                            $availableOptions = $currentSize && isset($sizeOptionMap[$currentSize]) ? $sizeOptionMap[$currentSize] : [];
                                        @endphp
                                        @foreach($availableOptions as $opt)
                                            <option value="{{ $opt }}" {{ ($attrs['option'] ?? '') == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2 col-6 mb-2"><label>Giá</label><input name="variant_price[]" type="number" step="0.01" min="0" class="form-control variant-price-input" value="{{$v->price}}" required></div>
                                <div class="col-md-2 col-6 mb-2"><label>Tồn kho</label><input name="variant_stock[]" type="number" min="0" class="form-control variant-stock-input" value="{{$v->stock}}" data-product-quantity="{{ $product->quantity }}"></div>
                                <div class="col-md-2 col-6 mb-2"><label>&nbsp;</label><button type="button" class="btn btn-outline-danger btn-sm btn-block remove-variant">Xóa</button></div>
                            </div>
                        @endforeach
                    </div>
                    <button id="add-variant" class="btn btn-outline-primary btn-sm mb-3">+ Thêm biến thể</button>

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

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
.select2-container--default .select2-selection--multiple {
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    min-height: 38px;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice {
    background-color: #D4AF37;
    border: none;
    color: #1a1a1a;
    padding: 5px 10px;
    margin: 3px;
    border-radius: 4px;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('#tags').select2({
        placeholder: 'Chọn tags...',
        allowClear: true,
        width: '100%'
    });
});
</script>
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
<script>
// Variants repeater (edit)
document.addEventListener('DOMContentLoaded', function(){
  const container = document.getElementById('variant-rows');
  const addBtn = document.getElementById('add-variant');
  const productQuantity = {{ $product->quantity }};
  const stockWarning = document.getElementById('stock-warning');
  const totalStockSpan = document.getElementById('total-variant-stock');
  const productQuantitySpan = document.getElementById('product-quantity');
  
  // Dữ liệu từ PHP
  const allSizes = @json($allSizes);
  const sizeOptionMap = @json($sizeOptionMap);
  const variantPriceMap = @json($variantPriceMap);
  
  if (!container || !addBtn) return;
  
  // Calculate total stock
  function calculateTotalStock() {
    const stockInputs = container.querySelectorAll('.variant-stock-input');
    let total = 0;
    stockInputs.forEach(input => {
      const val = parseInt(input.value) || 0;
      total += val;
    });
    totalStockSpan.textContent = total;
    productQuantitySpan.textContent = productQuantity;
    
    if (total > productQuantity) {
      stockWarning.style.display = 'block';
      stockWarning.className = 'alert alert-danger';
      return false;
    } else {
      stockWarning.style.display = 'none';
      return true;
    }
  }
  
  // Cập nhật Option dropdown khi chọn Size
  function updateOptionDropdown(sizeSelect, optionSelect) {
    const selectedSize = sizeSelect.value;
    const currentOption = optionSelect.value;
    
    // Xóa tất cả options trừ option đầu tiên (placeholder)
    optionSelect.innerHTML = '<option value="">-- Chọn Option --</option>';
    
    if (selectedSize && sizeOptionMap[selectedSize]) {
      const availableOptions = sizeOptionMap[selectedSize];
      availableOptions.forEach(option => {
        const optionElement = document.createElement('option');
        optionElement.value = option;
        optionElement.textContent = option;
        if (option === currentOption) {
          optionElement.selected = true;
        }
        optionSelect.appendChild(optionElement);
      });
    }
    
    // Nếu option hiện tại không còn trong danh sách, reset
    if (currentOption && (!selectedSize || !sizeOptionMap[selectedSize] || !sizeOptionMap[selectedSize].includes(currentOption))) {
      optionSelect.value = '';
    }
    
    // Tự động cập nhật giá
    updatePriceFromVariant(sizeSelect, optionSelect);
  }
  
  // Tự động cập nhật giá từ Size/Option
  function updatePriceFromVariant(sizeSelect, optionSelect) {
    const row = sizeSelect.closest('.variant-row');
    const priceInput = row.querySelector('.variant-price-input');
    const selectedSize = sizeSelect.value;
    const selectedOption = optionSelect.value;
    
    if (selectedSize && selectedOption && variantPriceMap[selectedSize + '|' + selectedOption]) {
      priceInput.value = variantPriceMap[selectedSize + '|' + selectedOption];
    }
  }
  
  // Setup event listeners cho Size và Option selects
  function setupVariantSelects(row) {
    const sizeSelect = row.querySelector('.variant-size-select');
    const optionSelect = row.querySelector('.variant-option-select');
    
    if (sizeSelect) {
      sizeSelect.addEventListener('change', function() {
        updateOptionDropdown(this, optionSelect);
      });
    }
    
    if (optionSelect) {
      optionSelect.addEventListener('change', function() {
        updatePriceFromVariant(sizeSelect, this);
      });
    }
  }
  
  // Image preview
  function setupImagePreview(input) {
    if (input.files && input.files[0]) {
      const reader = new FileReader();
      const preview = input.closest('.variant-row').querySelector('.variant-image-preview');
      const img = preview.querySelector('img');
      reader.onload = function(e) {
        img.src = e.target.result;
        preview.style.display = 'block';
      };
      reader.readAsDataURL(input.files[0]);
    }
  }
  
  // Setup image preview for existing inputs
  container.querySelectorAll('.variant-image').forEach(input => {
    input.addEventListener('change', function() {
      setupImagePreview(this);
    });
  });
  
  // Setup stock validation for existing inputs
  container.querySelectorAll('.variant-stock-input').forEach(input => {
    input.addEventListener('input', calculateTotalStock);
    input.addEventListener('blur', function() {
      const val = parseInt(this.value) || 0;
      if (val < 0) this.value = 0;
      calculateTotalStock();
    });
  });
  
  // Setup variant selects for existing rows
  container.querySelectorAll('.variant-row').forEach(row => {
    setupVariantSelects(row);
  });
  
  // Monitor product quantity changes
  const productQuantityInput = document.getElementById('quantity');
  if (productQuantityInput) {
    productQuantityInput.addEventListener('input', function() {
      const newQty = parseInt(this.value) || 0;
      productQuantitySpan.textContent = newQty;
      container.querySelectorAll('.variant-stock-input').forEach(input => {
        input.setAttribute('data-product-quantity', newQty);
      });
      calculateTotalStock();
    });
  }
  
  function wireRemove(node){
    const btn = node.querySelector('.remove-variant');
    if (btn) {
      btn.addEventListener('click', function(ev){ 
        ev.preventDefault(); 
        node.remove(); 
        calculateTotalStock();
      });
    }
  }
  
  Array.from(container.querySelectorAll('.variant-row')).forEach(wireRemove);
  
  function makeRow(){
    const row = document.createElement('div');
    row.className = 'form-row align-items-end mb-3 variant-row';
    row.style.border = '1px solid #ddd';
    row.style.padding = '15px';
    row.style.borderRadius = '5px';
    row.innerHTML = `
      <div class="col-md-12 mb-3">
        <label><strong>Ảnh biến thể</strong></label>
        <input type="file" name="variant_image[]" class="form-control variant-image" accept="image/*">
        <input type="hidden" name="variant_image_existing[]" value="">
        <div class="variant-image-preview mt-2" style="display:none;">
          <img src="" alt="Preview" style="max-width: 80px; max-height: 80px; border: 1px solid #ddd; border-radius: 4px; object-fit: cover;">
          <br><small class="text-muted">Ảnh mới</small>
        </div>
      </div>
      <div class="col-md-2 col-6 mb-2"><label>SKU</label><input name="variant_sku[]" class="form-control"></div>
      <div class="col-md-2 col-6 mb-2">
        <label>Size</label>
        <select name="variant_size[]" class="form-control variant-size-select">
          <option value="">-- Chọn Size --</option>
        </select>
      </div>
      <div class="col-md-2 col-6 mb-2">
        <label>Option</label>
        <select name="variant_option[]" class="form-control variant-option-select">
          <option value="">-- Chọn Option --</option>
        </select>
      </div>
      <div class="col-md-2 col-6 mb-2"><label>Giá</label><input name="variant_price[]" type="number" step="0.01" min="0" class="form-control variant-price-input" required></div>
      <div class="col-md-2 col-6 mb-2"><label>Tồn kho</label><input name="variant_stock[]" type="number" min="0" class="form-control variant-stock-input" value="0" data-product-quantity="${productQuantity}"></div>
      <div class="col-md-2 col-6 mb-2"><label>&nbsp;</label><button type="button" class="btn btn-outline-danger btn-sm btn-block remove-variant">Xóa</button></div>
    `;
    
    // Populate Size dropdown
    const sizeSelect = row.querySelector('.variant-size-select');
    allSizes.forEach(size => {
      const option = document.createElement('option');
      option.value = size;
      option.textContent = size;
      sizeSelect.appendChild(option);
    });
    
    // Setup event listeners for new row
    const imageInput = row.querySelector('.variant-image');
    imageInput.addEventListener('change', function() {
      setupImagePreview(this);
    });
    
    const stockInput = row.querySelector('.variant-stock-input');
    stockInput.addEventListener('input', calculateTotalStock);
    stockInput.addEventListener('blur', function() {
      const val = parseInt(this.value) || 0;
      if (val < 0) this.value = 0;
      calculateTotalStock();
    });
    
    // Setup variant selects
    setupVariantSelects(row);
    
    wireRemove(row);
    return row;
  }
  
  addBtn.addEventListener('click', function(e){ 
    e.preventDefault(); 
    container.appendChild(makeRow()); 
  });
  
  // Form submission validation
  document.querySelector('form').addEventListener('submit', function(e) {
    if (!calculateTotalStock()) {
      e.preventDefault();
      alert('Tổng số lượng các biến thể không được vượt quá số lượng sản phẩm!');
      return false;
    }
  });
  
  // Initial calculation
  calculateTotalStock();
});
</script>
@endpush