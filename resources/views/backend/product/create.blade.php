@extends('backend.layouts.master')

@section('main-content')

<div class="row create-user-wrapper">
    <div class="col-lg-6">
        <div class="card form-half">
            <h5 class="card-header">Thêm sản phẩm</h5>
            <div class="card-body">
      <form method="post" action="{{route('admin.products.store')}}" enctype="multipart/form-data">
                    {{csrf_field()}}
                      @csrf
                    <div class="form-group floating-group">
                        <input type="text" class="form-control modern-input" id="name" name="name" placeholder=" " value="{{old('name')}}">
                        <label for="name" class="col-form-label">Tên sản phẩm <span class="text-danger">*</span></label>
                        @error('name')
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>

                    <div class="form-group floating-group">
                        <textarea class="form-control modern-input" id="description" name="description" placeholder=" " style="height: 100px">{{old('description')}}</textarea>
                        <label for="description" class="col-form-label">Mô tả sản phẩm</label>
                        @error('description')
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>

                    <div class="form-group floating-group">
                        <input type="number" class="form-control modern-input" id="price" name="price" placeholder=" " step="0.01" value="{{old('price')}}">
                        <label for="price" class="col-form-label">Giá (VNĐ) <span class="text-danger">*</span></label>
                        @error('price')
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>

                    <div class="form-group floating-group">
                        <input type="number" class="form-control modern-input" id="quantity" name="quantity" placeholder=" " min="0" value="{{old('quantity')}}">
                        <label for="quantity" class="col-form-label">Số lượng <span class="text-danger">*</span></label>
                        @error('quantity')
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="tags" class="col-form-label">Tags</label>
                        <select class="form-control select2" id="tags" name="tags[]" multiple>
                            @if(isset($tags))
                                @foreach($tags as $tag)
                                    <option value="{{ $tag->id }}" style="color: {{ $tag->color }};">
                                        {{ $tag->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        <small class="form-text text-muted">Chọn các tags cho sản phẩm (có thể chọn nhiều)</small>
                    </div>

                    <div class="form-group">
                        <label for="seller_id" class="col-form-label">Người bán <span class="text-danger">*</span></label>
                        <select class="form-control" id="seller_id" name="seller_id">
                            <option value="">-- Chọn người bán --</option>
                            @if(isset($sellers))
                                @foreach($sellers as $seller)
                                    <option value="{{$seller->id}}">{{$seller->name}}</option>
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
                                    <option value="{{$category->id}}">{{$category->name}}</option>
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
                            <option value="active">Hoạt động</option>
                            <option value="inactive">Không hoạt động</option>
                        </select>
                        @error('status')
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>

                    <hr>
                    <h5 class="mb-3">Biến thể (Size/Option/Price)</h5>
                    <div class="alert alert-info" id="stock-warning" style="display:none;">
                        <strong>Cảnh báo:</strong> Tổng số lượng các biến thể (<span id="total-variant-stock">0</span>) không được vượt quá số lượng sản phẩm (<span id="product-quantity">0</span>)
                    </div>
                    <div id="variant-rows"></div>
                    <button id="add-variant" class="btn btn-outline-primary btn-sm mb-3">+ Thêm biến thể</button>

                    <div class="form-group mb-3">
                        <button type="reset" class="btn btn-warning">Đặt lại</button>
                        <button class="btn btn-success" type="submit">Thêm sản phẩm</button>
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
                    <img id="imagePreview" src="https://via.placeholder.com/300x200?text=Chưa+chọn+hình+ảnh" alt="Preview" class="img-fluid rounded mb-3" style="max-height: 200px;">
                    <h6 id="namePreview">Tên sản phẩm</h6>
                    <p class="text-muted mb-1" id="pricePreview">Giá: 0 VNĐ</p>
                    <p class="text-muted mb-1" id="quantityPreview">Số lượng: 0</p>
                    <p class="text-muted mb-1" id="sellerPreview">Người bán: Chưa chọn</p>
                    <p class="text-muted mb-1" id="categoryPreview">Danh mục: Chưa chọn</p>
                    <p class="text-muted mb-1" id="statusPreview">Trạng thái: Hoạt động</p>
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
        $('#pricePreview').text('Giá: ' + new Intl.NumberFormat('vi-VN').format(price) + ' VNĐ');
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
// Variants repeater (create)
document.addEventListener('DOMContentLoaded', function(){
  const container = document.getElementById('variant-rows');
  const addBtn = document.getElementById('add-variant');
  const stockWarning = document.getElementById('stock-warning');
  const totalStockSpan = document.getElementById('total-variant-stock');
  const productQuantitySpan = document.getElementById('product-quantity');
  const productQuantityInput = document.getElementById('quantity');
  
  // Dữ liệu động: thu thập từ các hàng đã nhập
  let sizeOptionMap = {}; // Map Size -> [Options]
  let variantPriceMap = {}; // Map "Size|Option" -> Price
  let allSizes = [];
  
  if (!container || !addBtn) return;
  
  // Thu thập tất cả Size và Option từ các hàng hiện có
  function collectVariantData() {
    sizeOptionMap = {};
    variantPriceMap = {};
    allSizes = [];
    
    container.querySelectorAll('.variant-row').forEach(row => {
      const sizeInput = row.querySelector('.variant-size-select');
      const optionInput = row.querySelector('.variant-option-select');
      const priceInput = row.querySelector('.variant-price-input');
      
      if (sizeInput && optionInput && priceInput) {
        const size = sizeInput.value;
        const option = optionInput.value;
        const price = parseFloat(priceInput.value) || 0;
        
        // Ghi nhận Size/Option ngay cả khi chưa nhập giá để không mất lựa chọn mới tạo
        if (size) {
          if (!sizeOptionMap[size]) {
            sizeOptionMap[size] = [];
            if (!allSizes.includes(size)) {
              allSizes.push(size);
            }
          }
          if (option && option !== '__new__' && !sizeOptionMap[size].includes(option)) {
            sizeOptionMap[size].push(option);
          }
        }
        // Chỉ lưu giá khi đầy đủ size + option + price
        if (size && option && price > 0) {
          variantPriceMap[size + '|' + option] = price;
        }
      }
    });
  }
  
  // Cập nhật tất cả Size dropdowns
  function updateAllSizeDropdowns() {
    collectVariantData();
    container.querySelectorAll('.variant-size-select').forEach(select => {
      const currentValue = select.value;
      const existingOptions = Array.from(select.options).map(opt => opt.value);
      const hasNewOption = existingOptions.includes('__new__');
      
      // Xóa tất cả options trừ option đầu tiên (placeholder)
      select.innerHTML = '<option value="">-- Chọn Size --</option>';
      
      // Thêm các Size mới
      allSizes.forEach(size => {
        const option = document.createElement('option');
        option.value = size;
        option.textContent = size;
        select.appendChild(option);
      });
      
      // Thêm option "Thêm mới..." vào cuối
      const newOption = document.createElement('option');
      newOption.value = '__new__';
      newOption.textContent = '+ Thêm Size mới...';
      select.appendChild(newOption);
      
      // Khôi phục giá trị đã chọn
      if (currentValue && currentValue !== '__new__') {
        select.value = currentValue;
      }
    });
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
    
    // Thêm option "Thêm mới..." vào cuối
    const newOption = document.createElement('option');
    newOption.value = '__new__';
    newOption.textContent = '+ Thêm Option mới...';
    optionSelect.appendChild(newOption);
    
    // Nếu option hiện tại không còn trong danh sách, reset
    if (currentOption && currentOption !== '__new__' && (!selectedSize || !sizeOptionMap[selectedSize] || !sizeOptionMap[selectedSize].includes(currentOption))) {
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
    const priceInput = row.querySelector('.variant-price-input');
    
    if (sizeSelect) {
      sizeSelect.addEventListener('change', function() {
        if (this.value === '__new__') {
          const newSize = prompt('Nhập Size mới:');
          if (newSize && newSize.trim()) {
            const trimmedSize = newSize.trim();
            // Thêm Size mới vào dropdown
            const option = document.createElement('option');
            option.value = trimmedSize;
            option.textContent = trimmedSize;
            option.selected = true;
            this.insertBefore(option, this.querySelector('option[value="__new__"]'));
            this.value = trimmedSize;
            
            if (!allSizes.includes(trimmedSize)) {
              allSizes.push(trimmedSize);
              updateAllSizeDropdowns();
            }
          } else {
            this.value = '';
          }
        } else {
          updateOptionDropdown(this, optionSelect);
        }
      });
    }
    
    if (optionSelect) {
      optionSelect.addEventListener('change', function() {
        if (this.value === '__new__') {
          const selectedSize = sizeSelect.value;
          if (!selectedSize) {
            alert('Vui lòng chọn Size trước!');
            this.value = '';
            return;
          }
          const newOption = prompt('Nhập Option mới:');
          if (newOption && newOption.trim()) {
            const trimmedOption = newOption.trim();
            // Thêm Option mới vào dropdown
            const option = document.createElement('option');
            option.value = trimmedOption;
            option.textContent = trimmedOption;
            option.selected = true;
            this.insertBefore(option, this.querySelector('option[value="__new__"]'));
            this.value = trimmedOption;
            
            if (!sizeOptionMap[selectedSize]) {
              sizeOptionMap[selectedSize] = [];
            }
            if (!sizeOptionMap[selectedSize].includes(trimmedOption)) {
              sizeOptionMap[selectedSize].push(trimmedOption);
              // Cập nhật Option dropdown cho các hàng khác có cùng Size
              container.querySelectorAll('.variant-row').forEach(otherRow => {
                if (otherRow !== row) {
                  const otherSizeSelect = otherRow.querySelector('.variant-size-select');
                  const otherOptionSelect = otherRow.querySelector('.variant-option-select');
                  if (otherSizeSelect && otherSizeSelect.value === selectedSize) {
                    updateOptionDropdown(otherSizeSelect, otherOptionSelect);
                  }
                }
              });
            }
          } else {
            this.value = '';
          }
        } else {
          updatePriceFromVariant(sizeSelect, this);
        }
      });
    }
    
    // Khi nhập giá, cập nhật variantPriceMap
    if (priceInput) {
      priceInput.addEventListener('blur', function() {
        const size = sizeSelect.value;
        const option = optionSelect.value;
        const price = parseFloat(this.value) || 0;
        
        if (size && option && price > 0 && size !== '__new__' && option !== '__new__') {
          variantPriceMap[size + '|' + option] = price;
          
          // Cập nhật sizeOptionMap
          if (!sizeOptionMap[size]) {
            sizeOptionMap[size] = [];
            if (!allSizes.includes(size)) {
              allSizes.push(size);
              updateAllSizeDropdowns();
            }
          }
          if (!sizeOptionMap[size].includes(option)) {
            sizeOptionMap[size].push(option);
            // Cập nhật Option dropdown cho các hàng khác có cùng Size
            container.querySelectorAll('.variant-row').forEach(otherRow => {
              if (otherRow !== row) {
                const otherSizeSelect = otherRow.querySelector('.variant-size-select');
                const otherOptionSelect = otherRow.querySelector('.variant-option-select');
                if (otherSizeSelect && otherSizeSelect.value === size) {
                  updateOptionDropdown(otherSizeSelect, otherOptionSelect);
                }
              }
            });
          }
        }
      });
    }
  }
  
  // Calculate total stock
  function calculateTotalStock() {
    const productQty = parseInt(productQuantityInput?.value) || 0;
    const stockInputs = container.querySelectorAll('.variant-stock-input');
    let total = 0;
    stockInputs.forEach(input => {
      const val = parseInt(input.value) || 0;
      total += val;
    });
    totalStockSpan.textContent = total;
    productQuantitySpan.textContent = productQty;
    
    if (total > productQty && productQty > 0) {
      stockWarning.style.display = 'block';
      stockWarning.className = 'alert alert-danger';
      return false;
    } else {
      stockWarning.style.display = 'none';
      return true;
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
  
  // Monitor product quantity changes
  if (productQuantityInput) {
    productQuantityInput.addEventListener('input', calculateTotalStock);
  }
  
  function makeRow() {
    const row = document.createElement('div');
    row.className = 'form-row align-items-end mb-3 variant-row';
    row.style.border = '1px solid #ddd';
    row.style.padding = '15px';
    row.style.borderRadius = '5px';
    row.innerHTML = `
      <div class="col-md-12 mb-3">
        <label><strong>Ảnh biến thể</strong></label>
        <input type="file" name="variant_image[]" class="form-control variant-image" accept="image/*">
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
      <div class="col-md-2 col-6 mb-2"><label>Tồn kho</label><input name="variant_stock[]" type="number" min="0" class="form-control variant-stock-input" value="0"></div>
      <div class="col-md-2 col-6 mb-2"><label>&nbsp;</label><button type="button" class="btn btn-outline-danger btn-sm btn-block remove-variant">Xóa</button></div>
    `;
    
    // Populate Size dropdown với các Size đã có
    collectVariantData();
    const sizeSelect = row.querySelector('.variant-size-select');
    allSizes.forEach(size => {
      const option = document.createElement('option');
      option.value = size;
      option.textContent = size;
      sizeSelect.appendChild(option);
    });
    
    // Thêm option "Thêm mới..." vào cuối Size dropdown
    const newSizeOption = document.createElement('option');
    newSizeOption.value = '__new__';
    newSizeOption.textContent = '+ Thêm Size mới...';
    sizeSelect.appendChild(newSizeOption);
    
    // Thêm option "Thêm mới..." vào Option dropdown
    const optionSelect = row.querySelector('.variant-option-select');
    const newOptionOption = document.createElement('option');
    newOptionOption.value = '__new__';
    newOptionOption.textContent = '+ Thêm Option mới...';
    optionSelect.appendChild(newOptionOption);
    
    // Setup event listeners
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
    
    row.querySelector('.remove-variant').addEventListener('click', function(ev){ 
      ev.preventDefault(); 
      row.remove(); 
      calculateTotalStock();
      collectVariantData(); // Cập nhật lại dữ liệu sau khi xóa
    });
    
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
});
</script>
@endpush