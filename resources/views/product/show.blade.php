@extends('frontend.layouts.master')
@section('title','Product Detail')
@section('main-content')
@php
    use App\Models\ProductPost;
    use App\Models\ProductReview;
    use Illuminate\Support\Str;

    $productId = request()->route('id');
    $product = DB::table('products')->where('id', $productId)->first();
    $variants = \App\Models\ProductVariant::where('product_id', $productId)->where('status','active')->get();

    // Collect all product images
    $photos = array_filter(array_map('trim', explode(',', (string)($product->image_url ?? ''))));
    if(empty($photos)) {
        $photos = [asset('backend/img/thumbnail-default.jpg')];
    }
    $allPhotos = [];
    foreach($photos as $photo) {
        $imgSrc = $photo && Str::startsWith($photo, ['http://','https://']) 
            ? $photo 
            : ($photo ? asset($photo) : asset('backend/img/thumbnail-default.jpg'));
        $allPhotos[] = $imgSrc;
    }

    // Add variant images to gallery
    foreach($variants as $variant) {
        if(isset($variant->image) && $variant->image) {
            $variantImg = Str::startsWith($variant->image, ['http://','https://']) 
                ? $variant->image 
                : asset($variant->image);
            if(!in_array($variantImg, $allPhotos)) {
                $allPhotos[] = $variantImg;
            }
        }
    }

    $mainImage = $allPhotos[0] ?? asset('backend/img/thumbnail-default.jpg');

    // Build size-option map
    $sizeOptionMap = [];
    foreach($variants as $v) {
        $attrs = is_array($v->attributes) ? $v->attributes : (is_string($v->attributes) ? json_decode($v->attributes, true) : []);
        $size = $attrs['size'] ?? null;
        $option = $attrs['option'] ?? null;
        if ($size && $option) {
            if(!isset($sizeOptionMap[$size])) $sizeOptionMap[$size] = [];
            $sizeOptionMap[$size][$option] = [
                'id' => $v->id,
                'price' => (float)$v->price,
                'stock' => (int)$v->stock,
                'sku' => $v->sku ?? null,
                'image' => isset($v->image) && $v->image ? (Str::startsWith($v->image, ['http://','https://']) ? $v->image : asset($v->image)) : null,
            ];
        }
    }

    $sizes = array_keys($sizeOptionMap);

    // Base product info
    $baseProductStock = $product->quantity ?? 0;
    $baseProductPrice = $product->price ?? 0;

    // Get product post
    $productPost = ProductPost::where('product_id', $productId)->where('status', 'published')->first();

    // Get reviews
    $reviews = ProductReview::where('product_id', $productId)->where('status', 'approved')->orderBy('created_at', 'desc')->get();
    $avgRating = $reviews->avg('rating') ?? 0;
    $reviewCount = $reviews->count();

    // Get related products (same category)
    $relatedProducts = collect();
    if ($product && isset($product->category_id)) {
        $relatedProducts = DB::table('products')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $productId)
            ->where('status', 'active')
            ->limit(5)
            ->get();
    }

    $seller = null;
    if ($product && isset($product->seller_id) && $product->seller_id) {
        $seller = DB::table('users')->where('id', $product->seller_id)->first();
    }
@endphp

<!-- Breadcrumb -->
<section class="breadcrumb-section" style="padding:20px 0;background:#f8f9fa;border-bottom:1px solid #eee;">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb" style="margin:0;background:transparent;">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" style="color:#666;">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $product->name ?? 'Sản phẩm' }}</li>
            </ol>
        </nav>
    </div>
</section>

<section class="product-detail-section" style="padding:40px 0;background:#fff;">
    <div class="container">
        @if($product)
        <!-- Product Main Info -->
        <div class="row mb-5">
            <!-- Image Gallery Slider -->
            <div class="col-lg-6 col-md-6">
                <div class="product-gallery">
                    @if(count($allPhotos) > 0)
                    <div id="productImageSlider" class="carousel slide" data-ride="carousel" data-interval="false" style="margin-bottom:15px;">
                        <div class="carousel-inner" style="border:1px solid #eee;border-radius:8px;overflow:hidden;background:#fff;">
                            @foreach($allPhotos as $index => $photo)
                            <div class="carousel-item {{$index==0 ? 'active' : ''}}" data-image="{{$photo}}">
                                <img src="{{$photo}}" referrerpolicy="no-referrer" class="d-block w-100" alt="{{$product->name}} - {{$index+1}}" style="width:100%;height:auto;min-height:400px;object-fit:contain;background:#fff;" id="main-product-image">
                            </div>
                            @endforeach
                        </div>
                        @if(count($allPhotos) > 1)
                        <a class="carousel-control-prev" href="#productImageSlider" role="button" data-slide="prev" style="width:50px;background:rgba(0,0,0,0.3);border-radius:4px 0 0 4px;">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="sr-only">Previous</span>
                        </a>
                        <a class="carousel-control-next" href="#productImageSlider" role="button" data-slide="next" style="width:50px;background:rgba(0,0,0,0.3);border-radius:0 4px 4px 0;">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="sr-only">Next</span>
                        </a>
                        <!-- Thumbnail Navigation -->
                        <div class="thumbnail-gallery mt-3" style="display:flex;gap:10px;flex-wrap:wrap;justify-content:center;">
                            @foreach($allPhotos as $index => $photo)
                            <div class="thumbnail-item" data-slide-to="{{$index}}" data-image="{{$photo}}" style="width:80px;height:80px;border:2px solid {{$index==0 ? '#D4AF37' : '#ddd'}};border-radius:4px;overflow:hidden;cursor:pointer;transition:all 0.3s;">
                                <img src="{{$photo}}" referrerpolicy="no-referrer" alt="Thumbnail {{$index+1}}" style="width:100%;height:100%;object-fit:cover;">
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                    @else
                    <div style="border:1px solid #eee;border-radius:8px;overflow:hidden;background:#fff;padding:100px;text-align:center;">
                        <img src="{{asset('backend/img/thumbnail-default.jpg')}}" referrerpolicy="no-referrer" class="img-fluid" alt="{{$product->name}}" style="max-width:100%;height:auto;">
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Product Info -->
            <div class="col-lg-6 col-md-6">
                <h1 style="font-size:28px;font-weight:700;margin-bottom:15px;color:#1a1a1a;">{{$product->name}}</h1>
                
                <!-- Rating -->
                @if($reviewCount > 0)
                <div class="product-rating mb-3" style="display:flex;align-items:center;gap:10px;">
                    <div class="stars" style="color:#ffc107;">
                        @for($i=1; $i<=5; $i++)
                            <i class="fa fa-star{{$i <= round($avgRating) ? '' : '-o'}}"></i>
                        @endfor
                    </div>
                    <span style="color:#666;font-size:14px;">({{$reviewCount}} đánh giá)</span>
                </div>
                @endif
                
                <!-- Price -->
                <div class="product-price mb-4">
                    <h3 class="text-danger" id="priceDisplay" style="font-size:32px;font-weight:700;margin:0;">
                        @if(isset($product->price))
                            {{ number_format($product->price, 0, ',', '.') }}₫
                        @else
                            Liên hệ
                        @endif
                    </h3>
                </div>
                
                <!-- Summary -->
                <p class="product-summary mb-4" style="color:#666;line-height:1.6;">{{$product->summary ?? ''}}</p>

                <!-- Variants -->
                @if(count($sizes) > 0)
                <div class="product-variants mb-4">
                    <label style="font-weight:600;margin-bottom:8px;display:block;">Phân loại:</label>

                    <div class="variant-btn-groups" style="display:grid;gap:12px;">
                        <div>
                            <div style="color:#666;font-size:14px;margin-bottom:8px;">Kích thước</div>
                            <div class="variant-size-buttons" style="display:flex;flex-wrap:wrap;gap:10px;">
                                @foreach($sizes as $s)
                                    <button type="button" class="btn btn-sm btn-outline-secondary variant-size-btn" data-size="{{$s}}" style="padding:8px 12px;">{{$s}}</button>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <div style="color:#666;font-size:14px;margin-bottom:8px;">Tùy chọn</div>
                            <div id="optionButtons" class="variant-option-buttons" style="display:flex;flex-wrap:wrap;gap:10px;">
                                <span style="color:#999;font-size:14px;">Vui lòng chọn kích thước</span>
                            </div>
                        </div>

                        <!-- Keep original selects for compatibility with existing JS logic -->
                        <div style="display:none;">
                            <select id="selectSize" class="form-control variant-select">
                                <option value="">-- Chọn kích thước --</option>
                                @foreach($sizes as $s)
                                    <option value="{{$s}}">{{$s}}</option>
                                @endforeach
                            </select>
                            <select id="selectOption" class="form-control variant-select" disabled>
                                <option value="">-- Chọn tùy chọn --</option>
                            </select>
                        </div>

                    <div id="variant-info" class="mt-3" style="display:none;padding:15px;background:#f8f9fa;border-radius:4px;border-left:3px solid #D4AF37;">
                        <div class="row">
                            <div class="col-6">
                                <strong>Giá:</strong> <span id="variant-price" style="color:#D4AF37;font-weight:700;font-size:18px;">-</span>
                            </div>
                            <div class="col-6">
                                <strong>Tồn kho:</strong> <span id="variant-stock" style="color:#28a745;font-weight:600;">-</span>
                            </div>
                        </div>
                        <div class="mt-2">
                            <strong>SKU:</strong> <span id="variant-sku">-</span>
                        </div>
                    </div>
                    <div id="base-stock-info" class="mt-3" style="padding:10px;background:#f0f0f0;border-radius:4px;">
                        <strong>Số lượng còn lại (Sản phẩm gốc):</strong> <span id="base-stock">{{ $baseProductStock }}</span>
                    </div>
                </div>
                @endif
                
                <!-- Quantity & Actions -->
                <div class="product-actions">
                    <div class="quantity-control mb-4">
                        <label style="font-weight:600;margin-bottom:8px;display:block;">Số lượng:</label>
                        <div class="d-flex align-items-center" style="gap: 10px;">
                            <button type="button" class="btn btn-outline-secondary qty-minus" style="width:40px;height:40px;border:1px solid #ddd;">-</button>
                            <input type="number" id="productQuantity" class="form-control" value="1" min="1" style="width: 80px; text-align: center;height:40px;">
                            <button type="button" class="btn btn-outline-secondary qty-plus" style="width:40px;height:40px;border:1px solid #ddd;">+</button>
                        </div>
                    </div>
                    <div class="d-flex gap-2" style="gap:10px;">
                        <button type="button" class="btn btn-primary btn-add-to-cart" data-product-id="{{ $product->id }}" style="flex:1;padding:12px 20px;background:#D4AF37;border:none;color:#1a1a1a;font-weight:600;border-radius:4px;">
                            <i class="ti-bag mr-2"></i>Thêm vào giỏ
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-buy-now" data-product-id="{{ $product->id }}" style="flex:1;padding:12px 20px;border:1px solid #ddd;color:#1a1a1a;background:transparent;font-weight:600;border-radius:4px;">
                            Mua ngay
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-add-to-wishlist" data-product-id="{{ $product->id }}" style="padding:12px 20px;border:1px solid #dc3545;color:#dc3545;background:transparent;border-radius:4px;">
                            <i class="fa fa-heart"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Shipping Info -->
                <div class="shipping-info mt-4" style="padding:15px;background:#f8f9fa;border-radius:4px;">
                    <p style="margin:0;color:#28a745;font-weight:600;"><i class="ti-truck mr-2"></i>Miễn phí vận chuyển cho tất cả đơn hàng</p>
                    <p style="margin:5px 0 0 0;color:#666;font-size:14px;">Giao hàng trong 3-5 ngày làm việc</p>
                </div>
            </div>
        </div>

        <!-- Seller / Shop Info (simple, based on seller_id) -->
        <div class="mb-5" style="padding:20px;border:1px solid #eee;border-radius:8px;background:#fff;">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div style="display:flex;align-items:center;gap:12px;">
                        <div style="width:48px;height:48px;border-radius:50%;background:#f8f9fa;border:1px solid #eee;display:flex;align-items:center;justify-content:center;overflow:hidden;">
                            @if($seller && isset($seller->avatar) && $seller->avatar)
                                <img src="{{ Str::startsWith($seller->avatar, ['http://','https://']) ? $seller->avatar : asset($seller->avatar) }}" referrerpolicy="no-referrer" alt="Seller" style="width:100%;height:100%;object-fit:cover;">
                            @else
                                <i class="fa fa-user" style="color:#999;"></i>
                            @endif
                        </div>
                        <div>
                            <div style="font-weight:700;color:#1a1a1a;">{{ $seller->name ?? 'Người bán' }}</div>
                            <div style="color:#666;font-size:14px;">Thông tin người bán</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4" style="display:flex;gap:10px;justify-content:flex-end;">
                    <a href="{{ route('contact') }}" class="btn btn-outline-secondary" style="border-radius:4px;">Chat</a>
                    <a href="{{ route('home') }}" class="btn btn-outline-secondary" style="border-radius:4px;">Xem shop</a>
                </div>
            </div>
        </div>
        
        <!-- Product Description / Post -->
        @if($productPost)
        <div class="product-post-section mb-5" style="padding:30px;background:#f8f9fa;border-radius:8px;">
            <h3 style="font-size:24px;font-weight:700;margin-bottom:20px;color:#1a1a1a;">Mô tả sản phẩm</h3>
            <div class="product-post-content" style="color:#666;line-height:1.8;">
                {!! $productPost->content !!}
            </div>
        </div>
        @endif
        
        <!-- Reviews Section -->
        <div class="reviews-section mb-5">
            <h3 style="font-size:24px;font-weight:700;margin-bottom:30px;color:#1a1a1a;">Đánh giá khách hàng</h3>
            
            @if($reviewCount > 0)
            <!-- Rating Summary -->
            <div class="rating-summary mb-4" style="display:flex;gap:40px;padding:20px;background:#f8f9fa;border-radius:8px;">
                <div class="rating-overview" style="text-align:center;">
                    <div style="font-size:48px;font-weight:700;color:#D4AF37;">{{number_format($avgRating, 1)}}</div>
                    <div class="stars mb-2" style="color:#ffc107;font-size:20px;">
                        @for($i=1; $i<=5; $i++)
                            <i class="fa fa-star{{$i <= round($avgRating) ? '' : '-o'}}"></i>
                        @endfor
                    </div>
                    <div style="color:#666;font-size:14px;">Dựa trên {{$reviewCount}} đánh giá</div>
                </div>
                <div class="rating-breakdown" style="flex:1;">
                    @for($star=5; $star>=1; $star--)
                        @php
                            $count = $reviews->where('rating', $star)->count();
                            $percent = $reviewCount > 0 ? ($count / $reviewCount * 100) : 0;
                        @endphp
                        <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
                            <span style="width:60px;color:#666;">{{$star}} sao</span>
                            <div style="flex:1;height:8px;background:#e0e0e0;border-radius:4px;overflow:hidden;">
                                <div style="height:100%;background:#ffc107;width:{{$percent}}%;"></div>
                            </div>
                            <span style="width:40px;text-align:right;color:#666;font-size:14px;">{{$count}}</span>
                        </div>
                    @endfor
                </div>
            </div>
            
            <!-- Reviews List -->
            <div class="review-filters mb-3" style="display:flex;flex-wrap:wrap;gap:10px;">
                <button type="button" class="btn btn-sm btn-outline-secondary review-filter-btn active" data-filter="all">Tất cả ({{ $reviewCount }})</button>
                @for($s=5; $s>=1; $s--)
                    <button type="button" class="btn btn-sm btn-outline-secondary review-filter-btn" data-filter="{{ $s }}">{{ $s }} Sao ({{ $reviews->where('rating', $s)->count() }})</button>
                @endfor
                <button type="button" class="btn btn-sm btn-outline-secondary review-filter-btn" data-filter="comment">Có bình luận ({{ $reviews->filter(fn($r) => (bool)($r->comment))->count() }})</button>
            </div>

            <div class="reviews-list">
                @foreach($reviews as $review)
                <div class="review-item" data-rating="{{ (int)$review->rating }}" data-has-comment="{{ $review->comment ? '1' : '0' }}" style="padding:20px;border-bottom:1px solid #eee;margin-bottom:20px;">
                    <div style="display:flex;justify-content:space-between;align-items:start;margin-bottom:10px;">
                        <div>
                            <div style="font-weight:600;margin-bottom:5px;">{{$review->name ?? ($review->user ? $review->user->name : 'Khách')}}</div>
                            <div class="stars" style="color:#ffc107;margin-bottom:5px;">
                                @for($i=1; $i<=5; $i++)
                                    <i class="fa fa-star{{$i <= $review->rating ? '' : '-o'}}"></i>
                                @endfor
                            </div>
                        </div>
                        <div style="color:#666;font-size:14px;">{{$review->created_at->format('d/m/Y')}}</div>
                    </div>
                    @if($review->comment)
                    <p style="color:#666;line-height:1.6;margin:0;">{{$review->comment}}</p>
                    @endif
                </div>
                @endforeach
            </div>
            @else
            <div style="text-align:center;padding:40px;color:#666;">
                <p>Chưa có đánh giá nào cho sản phẩm này.</p>
            </div>
            @endif
        </div>
        
        <!-- Related Products -->
        @if(count($relatedProducts) > 0)
        <div class="related-products-section">
            <h3 style="font-size:24px;font-weight:700;margin-bottom:30px;color:#1a1a1a;">Sản phẩm tương tự</h3>
            <div class="row">
                @foreach($relatedProducts as $related)
                    @php
                        $relatedPhotos = array_filter(array_map('trim', explode(',', (string)($related->image_url ?? ''))));
                        $relatedImg = !empty($relatedPhotos) ? $relatedPhotos[0] : '';
                        $relatedImgSrc = $relatedImg && Str::startsWith($relatedImg, ['http://','https://']) 
                            ? $relatedImg 
                            : ($relatedImg ? asset($relatedImg) : asset('backend/img/thumbnail-default.jpg'));
                    @endphp
                    <div class="col-lg-2 col-md-4 col-6 mb-4">
                        <div class="product-card" style="border:1px solid #eee;border-radius:8px;overflow:hidden;transition:all 0.3s;background:#fff;" onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.1)'" onmouseout="this.style.boxShadow='none'">
                            <a href="{{ route('product.show', $related->id) }}" style="text-decoration:none;color:inherit;">
                                <div style="position:relative;padding-top:100%;overflow:hidden;">
                                    <img src="{{$relatedImgSrc}}" referrerpolicy="no-referrer" alt="{{$related->name}}" style="position:absolute;top:0;left:0;width:100%;height:100%;object-fit:cover;">
                                </div>
                                <div style="padding:15px;">
                                    <h5 style="font-size:14px;font-weight:600;margin:0 0 8px 0;color:#1a1a1a;line-height:1.4;height:40px;overflow:hidden;">{{$related->name}}</h5>
                                    <div style="color:#D4AF37;font-weight:700;font-size:16px;">{{number_format($related->price ?? 0, 0, ',', '.')}}₫</div>
                                </div>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        @else
            <div class="alert alert-warning">Không tìm thấy sản phẩm.</div>
        @endif
    </div>
</section>
@endsection

@push('scripts')
<style>
    .variant-size-btn.active,
    .variant-option-btn.active,
    .review-filter-btn.active {
        border-color: #D4AF37 !important;
        color: #1a1a1a !important;
        background: rgba(212, 175, 55, 0.15) !important;
    }

    .variant-option-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        border-radius: 4px;
    }

    .variant-option-thumb {
        width: 26px;
        height: 26px;
        border-radius: 4px;
        overflow: hidden;
        border: 1px solid #eee;
        background: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .variant-option-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Đảm bảo dropdown Size/Option luôn hiển thị phía trên các nút bên dưới */
    .product-variants {
        position: relative;
        z-index: 5;
        margin-bottom: 10px;
    }
    .product-variants select.variant-select {
        cursor: pointer;
        position: relative;
        z-index: 10;
        width: 100%;
    }
    .product-actions {
        position: relative;
        z-index: 1;
    }
    .product-variants select.variant-select:disabled {
        cursor: not-allowed;
        opacity: 0.6;
    }
</style>
<script>
    (function(){
        // Thumbnail click updates main image
        $('.thumbnail-item').on('click', function() {
            const newImage = $(this).data('image');
            $('#main-product-image').attr('src', newImage);
            $('.thumbnail-item').css('border-color', '#ddd');
            $(this).css('border-color', '#D4AF37');
        });

        var sizeOptionMap = @json($sizeOptionMap);
        var baseProductPrice = {{ $baseProductPrice }};
        var baseProductStock = {{ $baseProductStock }};
        
        // Khi chọn size, chỉ cập nhật options phù hợp (KHÔNG thay đổi Size dropdown)
        $('#selectSize').on('change', function(){
            var selectedSize = $(this).val();
            var optionSelect = $('#selectOption');
            var optionButtons = $('#optionButtons');
            
            // Reset Option dropdown
            optionSelect.empty();
            optionSelect.append('<option value="">-- Chọn tùy chọn --</option>');

            // Reset option buttons
            optionButtons.empty();
            
            if(selectedSize && sizeOptionMap[selectedSize]){
                // Hiển thị các option tương ứng với size đã chọn
                var options = sizeOptionMap[selectedSize];
                for(var opt in options){
                    if(options.hasOwnProperty(opt)){
                        optionSelect.append('<option value="'+opt+'">'+opt+'</option>');
                    }
                }

                // Render Shopee-like option buttons
                for(var optBtn in options){
                    if(!options.hasOwnProperty(optBtn)) continue;
                    var v = options[optBtn];
                    var thumb = v && v.image ? ('<span class="variant-option-thumb"><img src="'+v.image+'" referrerpolicy="no-referrer" alt="" /></span>') : '';
                    optionButtons.append(
                        '<button type="button" class="btn btn-sm btn-outline-secondary variant-option-btn" data-option="'+optBtn+'">'+thumb+'<span>'+optBtn+'</span></button>'
                    );
                }

                optionSelect.prop('disabled', false);
                // Ẩn thông tin tồn kho sản phẩm gốc
                $('#base-stock-info').hide();
                $('#variant-info').hide();
                // Reset active state
                $('.variant-option-btn').removeClass('active');
                // Reset giá về giá gốc
                $('#priceDisplay').html(new Intl.NumberFormat('vi-VN').format(baseProductPrice) + '₫');
            } else {
                // Nếu không chọn size hoặc size không hợp lệ
                optionSelect.prop('disabled', true);
                $('#base-stock-info').show();
                $('#variant-info').hide();
                optionButtons.html('<span style="color:#999;font-size:14px;">Vui lòng chọn kích thước</span>');
                // Hiển thị giá gốc
                $('#priceDisplay').html(new Intl.NumberFormat('vi-VN').format(baseProductPrice) + '₫');
            }
        });

        // Size button click -> update hidden select
        $(document).on('click', '.variant-size-btn', function(){
            var size = $(this).data('size');
            $('.variant-size-btn').removeClass('active');
            $(this).addClass('active');
            $('#selectSize').val(size).trigger('change');
            // Reset option select
            $('#selectOption').val('').trigger('change');
        });

        // Option button click -> update hidden select
        $(document).on('click', '.variant-option-btn', function(){
            var opt = $(this).data('option');
            $('.variant-option-btn').removeClass('active');
            $(this).addClass('active');
            $('#selectOption').val(opt).trigger('change');
        });

        // Khi chọn option, hiển thị giá và tồn kho của variant
        $('#selectOption').on('change', function(){
            var selectedSize = $('#selectSize').val();
            var selectedOption = $(this).val();
            
            if(selectedSize && selectedOption && sizeOptionMap[selectedSize] && sizeOptionMap[selectedSize][selectedOption]){
                var variant = sizeOptionMap[selectedSize][selectedOption];
                
                // Cập nhật thông tin variant
                $('#variant-price').text(new Intl.NumberFormat('vi-VN').format(variant.price) + '₫');
                $('#variant-stock').text(variant.stock + ' sản phẩm');
                $('#variant-sku').text(variant.sku || '-');
                $('#variant-info').show();
                $('#base-stock-info').hide();
                
                // Cập nhật giá hiển thị chính
                $('#priceDisplay').html('<span style="color:#D4AF37;">' + new Intl.NumberFormat('vi-VN').format(variant.price) + '₫</span>');

                // Cập nhật ảnh chính nếu variant có ảnh
                if(variant.image){
                    $('#main-product-image').attr('src', variant.image);
                    // Tô viền thumbnail tương ứng nếu có
                    $('.thumbnail-item').each(function(){
                        if($(this).data('image') === variant.image){
                            $('.thumbnail-item').css('border-color', '#ddd');
                            $(this).css('border-color', '#D4AF37');
                        }
                    });
                }
            } else {
                // Nếu không chọn option hoặc option không hợp lệ
                $('#variant-info').hide();
                $('#base-stock-info').show();
                $('#variant-sku').text('-');
                // Hiển thị giá gốc
                $('#priceDisplay').html(new Intl.NumberFormat('vi-VN').format(baseProductPrice) + '₫');
                // Trả lại ảnh chính sản phẩm gốc
                $('#main-product-image').attr('src', '{{ $mainImage }}');
                $('.thumbnail-item').css('border-color', '#ddd');
                $('.thumbnail-item').first().css('border-color', '#D4AF37');
            }
        });

        // Khi load trang, chỉ hiện số lượng tồn kho sản phẩm gốc, disable tùy chọn option
        // QUAN TRỌNG: KHÔNG BAO GIỜ modify Size dropdown (#selectSize)
        $('#selectOption').prop('disabled', true);
        $('#variant-info').hide();
        $('#base-stock-info').show();
        
        // Đảm bảo Size dropdown luôn giữ nguyên tất cả options
        var sizeSelect = $('#selectSize');
        if(sizeSelect.length > 0){
            // Lưu lại số lượng options ban đầu để kiểm tra
            var initialSizeOptionsCount = sizeSelect.find('option').length;
            
            // Monitor để đảm bảo không có code nào xóa options
            setInterval(function(){
                var currentOptionsCount = sizeSelect.find('option').length;
                if(currentOptionsCount < initialSizeOptionsCount){
                    console.warn('Warning: Size dropdown options were modified!');
                    // Có thể reload page hoặc restore options nếu cần
                }
            }, 1000);
        }

        // Các nút tăng giảm số lượng
        $('.qty-minus').on('click', function () {
            var qtyInput = $('#productQuantity');
            var currentQty = parseInt(qtyInput.val()) || 1;
            if(currentQty > 1){
                qtyInput.val(currentQty - 1);
            }
        });

        $('.qty-plus').on('click', function () {
            var qtyInput = $('#productQuantity');
            var currentQty = parseInt(qtyInput.val()) || 1;
            var maxStock;
            var selectedSize = $('#selectSize').val();
            var selectedOption = $('#selectOption').val();
            if(selectedSize && selectedOption && sizeOptionMap[selectedSize] && sizeOptionMap[selectedSize][selectedOption]){
                maxStock = sizeOptionMap[selectedSize][selectedOption].stock;
            } else {
                maxStock = baseProductStock;
            }
            if(currentQty < maxStock){
                qtyInput.val(currentQty + 1);
            } else {
                alert('Số lượng sản phẩm không đủ');
            }
        });

        // Validate số lượng nhập tay
        $('#productQuantity').on('change', function(){
            var qty = parseInt($(this).val()) || 1;
            var maxStock;
            var selectedSize = $('#selectSize').val();
            var selectedOption = $('#selectOption').val();
            if(selectedSize && selectedOption && sizeOptionMap[selectedSize] && sizeOptionMap[selectedSize][selectedOption]){
                maxStock = sizeOptionMap[selectedSize][selectedOption].stock;
            } else {
                maxStock = baseProductStock;
            }
            if(qty > maxStock){
                $(this).val(maxStock);
                alert('Số lượng tối đa: ' + maxStock);
            } else if(qty < 1){
                $(this).val(1);
            }
        });

        // Add to cart
        $('.btn-add-to-cart').on('click', function(e){
            e.preventDefault();
            e.stopPropagation();
            var productId = $(this).data('product-id');
            var quantity = parseInt($('#productQuantity').val()) || 1;
            var selectedSize = $('#selectSize').val();
            var selectedOption = $('#selectOption').val();
            var variantId = null;
            if(selectedSize && selectedOption && sizeOptionMap[selectedSize] && sizeOptionMap[selectedSize][selectedOption]){
                variantId = sizeOptionMap[selectedSize][selectedOption].id;
            }
            $.ajax({
                url: '{{ route("cart.add") }}',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    product_id: productId,
                    quantity: quantity,
                    variant_id: variantId
                },
                success: function(response){
                    if(response.success){
                        if(typeof loadCartData === "function"){
                            loadCartData();
                        }
                        // Hiện modal thành công (nếu có)
                        if(typeof showLuxuryModal === "function"){
                            showLuxuryModal('cart', 'Đã thêm vào giỏ!', 'Sản phẩm đã được thêm vào giỏ hàng của bạn', {
                                primaryBtn: {
                                    text: 'Xem giỏ hàng',
                                    action: function() { if(typeof openMiniCart === "function"){openMiniCart();} }
                                },
                                secondaryBtn: { text: 'Tiếp tục mua', action: function(){} },
                                autoClose: false
                            });
                        } else if(typeof openMiniCart === "function"){
                            openMiniCart();
                        }
                    }
                },
                error: function(xhr){
                    var errMsg = (xhr.responseJSON && xhr.responseJSON.error) ? xhr.responseJSON.error : 'Có lỗi xảy ra';
                    alert(errMsg);
                }
            });
            return false;
        });

        // Buy now = add to cart then go checkout
        $('.btn-buy-now').on('click', function(e){
            e.preventDefault();
            e.stopPropagation();
            var productId = $(this).data('product-id');
            var quantity = parseInt($('#productQuantity').val()) || 1;
            var selectedSize = $('#selectSize').val();
            var selectedOption = $('#selectOption').val();
            var variantId = null;
            if(selectedSize && selectedOption && sizeOptionMap[selectedSize] && sizeOptionMap[selectedSize][selectedOption]){
                variantId = sizeOptionMap[selectedSize][selectedOption].id;
            }
            $.ajax({
                url: '{{ route("cart.add") }}',
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                data: { product_id: productId, quantity: quantity, variant_id: variantId },
                success: function(response){
                    if(response.success){
                        window.location.href = '{{ route("checkout.index") }}';
                    }
                },
                error: function(xhr){
                    var errMsg = (xhr.responseJSON && xhr.responseJSON.error) ? xhr.responseJSON.error : 'Có lỗi xảy ra';
                    alert(errMsg);
                }
            });
            return false;
        });

        // Add to wishlist
        $('.btn-add-to-wishlist').on('click', function(e){
            e.preventDefault();
            e.stopPropagation();
            var productId = $(this).data('product-id');
            var btn = $(this);
            $.ajax({
                url: '{{ route("wishlist.add") }}',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: { product_id: productId },
                success: function(response){
                    if(response.success){
                        btn.find('i').removeClass('fa-heart-o').addClass('fa-heart');
                        btn.addClass('active');
                        alert('Đã thêm vào yêu thích!');
                        if(typeof loadWishlistData === "function") loadWishlistData();
                    }
                },
                error: function(xhr){
                    var errMsg = (xhr.responseJSON && xhr.responseJSON.error) ? xhr.responseJSON.error : 'Có lỗi xảy ra';
                    alert(errMsg);
                }
            });
            return false;
        });

        // Review filters
        $('.review-filter-btn').on('click', function(){
            var filter = $(this).data('filter');
            $('.review-filter-btn').removeClass('active');
            $(this).addClass('active');

            $('.review-item').each(function(){
                var rating = parseInt($(this).data('rating'), 10);
                var hasComment = $(this).data('has-comment') == 1;
                var show = true;

                if(filter === 'all') {
                    show = true;
                } else if(filter === 'comment') {
                    show = hasComment;
                } else {
                    show = (rating === parseInt(filter, 10));
                }

                $(this).toggle(show);
            });
        });
    })();
</script>
@endpush


