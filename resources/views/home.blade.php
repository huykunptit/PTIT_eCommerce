@extends('frontend.layouts.master')
@section('title','PTIT || Trang Sức Cao Cấp')
@section('main-content')
@php
  use Illuminate\Support\Facades\DB;
  
  // Load banners from database (DB::table) + robust ID parsing
  if (!isset($banners) || !count($banners)) {
      $bannerIdsRaw = \App\Models\SystemSetting::get('home_banner_ids');
      $bannerIds = [];
      if (!empty($bannerIdsRaw)) {
          $bannerIds = array_values(array_filter(array_map('intval', preg_split('/[\s,]+/', (string)$bannerIdsRaw)), function($v){ return $v > 0; }));
      }

      if (!empty($bannerIds)) {
          $banners = DB::table('banners')
              ->whereIn('id', $bannerIds)
              ->where('status', 'active')
              ->orderByRaw('FIELD(id, ' . implode(',', $bannerIds) . ')')
              ->get();
      } else {
          $banners = DB::table('banners')->where('status','active')->orderByDesc('id')->get();
      }

      if (!$banners || (is_object($banners) && method_exists($banners,'count') && $banners->count() == 0) || (is_array($banners) && count($banners) == 0)) {
          $banners = collect([
              (object) ['title' => 'Bộ Sưu Tập Mới', 'description' => 'Khám phá vẻ đẹp vượt thời gian', 'photo' => 'https://picsum.photos/1200/550?random=11'],
          ]);
      }
  }
  
  $categories = DB::table('categories')->select('id','name')->orderBy('name')->get();
  $selectedCategory = request()->query('category');
  $search = trim((string) request()->query('q', ''));
  
  $products = DB::table('products')
      ->when($selectedCategory, function ($q) use ($selectedCategory) {
          return $q->where('category_id', $selectedCategory);
      })
      ->when($search !== '', function ($q) use ($search) {
          return $q->where(function($w) use ($search){
              $w->where('name', 'like', '%'.$search.'%')
                ->orWhere('description', 'like', '%'.$search.'%');
          });
      })
      ->orderByDesc('id')
      ->paginate(12)
      ->appends(request()->query());
@endphp

<!-- Hero Slider Section -->
@if(count($banners)>0)
<section class="hero-slider">
    <div id="heroCarousel" class="carousel slide carousel-fade" data-ride="carousel" data-interval="5000">
        <div class="carousel-indicators">
            @foreach($banners as $key=>$banner)
            <button type="button" data-target="#heroCarousel" data-slide-to="{{$key}}" class="{{$key==0 ? 'active' : ''}}" aria-label="Slide {{$key+1}}"></button>
            @endforeach
                    </div>
        
        <div class="carousel-inner">
            @foreach($banners as $key=>$banner)
            @php
                $bSrc = isset($banner->photo) && \Illuminate\Support\Str::startsWith($banner->photo, ['http://','https://'])
                    ? $banner->photo
                    : asset($banner->photo ?? 'backend/img/thumbnail-default.jpg');
            @endphp
            <div class="carousel-item {{$key==0 ? 'active' : ''}}">
                <div class="hero-image" style="background-image: url('{{$bSrc}}')"></div>
                <div class="hero-overlay"></div>
                <div class="container h-100">
                    <div class="row h-100 align-items-center">
                        <div class="col-lg-7">
                            <div class="hero-content">
                                <span class="hero-subtitle">Bộ Sưu Tập Mới</span>
                                <h1 class="hero-title">{{$banner->title ?? 'Trang Sức Cao Cấp'}}</h1>
                                <p class="hero-description">{!! html_entity_decode($banner->description ?? 'Khám phá vẻ đẹp vượt thời gian') !!}</p>
                                <a href="#products" class="btn-hero">
                                    Khám Phá Ngay
                                    <i class="fas fa-arrow-right ml-2"></i>
                                </a>
                                                </div>
                                                </div>
                                            </div>
                                    </div>
                                </div>
                                @endforeach
                        </div>
        
        <button class="carousel-control-prev" type="button" data-target="#heroCarousel" data-slide="prev">
            <i class="fas fa-chevron-left"></i>
            <span class="sr-only">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-target="#heroCarousel" data-slide="next">
            <i class="fas fa-chevron-right"></i>
            <span class="sr-only">Next</span>
        </button>
    </div>
</section>
@endif

<!-- Features Section -->
<section class="features-section">
    <div class="container">
        <div class="row">
            <div class="col-md-3 col-6">
                <div class="feature-box">
                    <div class="feature-icon">
                        <i class="fas fa-shipping-fast"></i>
                </div>
                    <h4>Miễn Phí Vận Chuyển</h4>
                    <p>Cho đơn hàng trên 2 triệu</p>
            </div>
        </div>
            <div class="col-md-3 col-6">
                <div class="feature-box">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                                    </div>
                    <h4>Bảo Hành Trọn Đời</h4>
                    <p>Cam kết chất lượng vàng</p>
                                    </div>
                                </div>
            <div class="col-md-3 col-6">
                <div class="feature-box">
                    <div class="feature-icon">
                        <i class="fas fa-gem"></i>
                            </div>
                    <h4>Chất Lượng Cao Cấp</h4>
                    <p>Vàng 24K chính hãng</p>
                            </div>
                        </div>
            <div class="col-md-3 col-6">
                <div class="feature-box">
                    <div class="feature-icon">
                        <i class="fas fa-headset"></i>
                </div>
                    <h4>Hỗ Trợ 24/7</h4>
                    <p>Tư vấn nhiệt tình</p>
            </div>
        </div>
    </div>
</div>
</section>

<!-- Products Section -->
<section class="products-section" id="products">
    <div class="container">
        <!-- Section Header -->
        <div class="section-header text-center">
            <span class="section-subtitle">Bộ Sưu Tập</span>
            <h2 class="section-title">Trang Sức Cao Cấp</h2>
            <p class="section-description">Khám phá những thiết kế trang sức tinh tế và sang trọng</p>
                        </div>

        <!-- Filter Section -->
        <div class="filter-container">
            <form method="GET" action="{{ route('home') }}" id="filterForm">
                <div class="row align-items-end justify-content-center">
                    <div class="col-lg-3 col-md-4 mb-3">
                        <div class="filter-group">
                            <label><i class="fas fa-layer-group mr-2"></i>Danh Mục</label>
                            <select name="category" class="custom-select" onchange="document.getElementById('filterForm').submit()">
                                <option value="">Tất cả danh mục</option>
                                @foreach($categories as $cat)
                                    <option value="{{$cat->id}}" {{ (string)$selectedCategory === (string)$cat->id ? 'selected' : '' }}>
                                        {{$cat->name}}
                                    </option>
                    @endforeach
                            </select>
                </div>
            </div>
                    <div class="col-lg-5 col-md-6 mb-3">
                        <div class="filter-group">
                            <label><i class="fas fa-search mr-2"></i>Tìm Kiếm</label>
                            <div class="search-box">
                                <input type="text" name="q" value="{{ $search }}" class="form-control" placeholder="Tìm kiếm sản phẩm...">
                                <button type="submit" class="search-btn">
                                    <i class="fas fa-search"></i>
                                </button>
        </div>
    </div>
                </div>
                    @if($selectedCategory || $search)
                    <div class="col-lg-2 col-md-2 mb-3">
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-block">
                            <i class="fas fa-redo mr-2"></i>Đặt lại
                        </a>
            </div>
            @endif
        </div>
            </form>
    </div>

        <!-- Products Grid -->
        <div class="products-grid">
        <div class="row">
                @foreach($products as $product)
                    @php
                        $photos = explode(',', (string)($product->image_url ?? ''));
                        $img = trim($photos[0] ?? '');
                        $imgSrc = $img && \Illuminate\Support\Str::startsWith($img, ['http://','https://']) 
                            ? $img 
                            : ($img ? asset($img) : asset('backend/img/thumbnail-default.jpg'));
                                                @endphp
                    <div class="col-lg-3 col-md-4 col-6 mb-4">
                        <div class="product-card">
                            <div class="product-badge">
                                <span class="badge badge-new">Mới</span>
                                                    </div>
                            <div class="product-image">
                                <a href="{{ url('/product/'.$product->id) }}">
                                    <img src="{{$imgSrc}}" referrerpolicy="no-referrer" alt="{{$product->name}}">
                                </a>
                                <div class="product-overlay">
                                    <div class="overlay-actions">
                                        <a href="{{ url('/product/'.$product->id) }}" class="action-btn" title="Xem chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button type="button" class="action-btn btn-add-wishlist" data-product-id="{{ $product->id }}" title="Yêu thích">
                                            <i class="far fa-heart"></i>
                                                        </button>
                                                    </div>
                                    <button type="button" class="btn-add-cart" data-product-id="{{ $product->id }}">
                                        <i class="fas fa-shopping-bag mr-2"></i>
                                        Thêm vào giỏ
                                                        </button>
                                                    </div>
                                                </div>
                            <div class="product-info">
                                <div class="product-category">Trang Sức</div>
                                <h3 class="product-title">
                                    <a href="{{ url('/product/'.$product->id) }}">{{$product->name}}</a>
                                </h3>
                                @if(isset($product->price))
                                <div class="product-price">
                                    <span class="current-price">{{ number_format($product->price, 0, ',', '.') }}₫</span>
                                            </div>
                                @endif
                                <div class="product-rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <span>(5.0)</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
    @endforeach
                            </div>
                        </div>

        <!-- Pagination -->
        @if($products->hasPages())
        <div class="pagination-wrapper">
            {{ $products->links() }}
                    </div>
@endif
    </div>
</section>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
/* ===== General Styles ===== */
:root {
    --primary-color: #D4AF37;
    --secondary-color: #1a1a1a;
    --text-color: #333;
    --light-bg: #f8f9fa;
    --white: #ffffff;
    --transition: all 0.3s ease;
}

/* ===== Hero Slider ===== */
.hero-slider {
    position: relative;
    overflow: hidden;
}

.hero-slider .carousel-item {
    height: 650px;
    position: relative;
}

.hero-image {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
}

.hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.3) 100%);
}

.hero-content {
    position: relative;
    z-index: 2;
    color: var(--white);
    animation: fadeInUp 1s ease;
}

.hero-subtitle {
    display: inline-block;
    color: var(--primary-color);
    font-size: 14px;
    font-weight: 600;
    letter-spacing: 2px;
    text-transform: uppercase;
    margin-bottom: 15px;
    position: relative;
    padding-left: 60px;
}

.hero-subtitle::before {
    content: '';
    position: absolute;
    left: 0;
    top: 50%;
    width: 50px;
    height: 2px;
    background: var(--primary-color);
}

.hero-title {
    font-size: 64px;
    font-weight: 700;
    line-height: 1.2;
    margin-bottom: 20px;
    color: var(--white);
    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
}

.hero-description {
        font-size: 18px;
    line-height: 1.6;
    margin-bottom: 35px;
    color: rgba(255,255,255,0.9);
    max-width: 500px;
}

.btn-hero {
    display: inline-flex;
    align-items: center;
    padding: 16px 40px;
    background: var(--primary-color);
    color: var(--secondary-color);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    border-radius: 0;
    transition: var(--transition);
    text-decoration: none;
    font-size: 14px;
}

.btn-hero:hover {
    background: var(--white);
    color: var(--secondary-color);
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(212,175,55,0.3);
    text-decoration: none;
}

.carousel-control-prev,
.carousel-control-next {
    width: 60px;
    height: 60px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
    top: 50%;
    transform: translateY(-50%);
    opacity: 1;
    transition: var(--transition);
}

.carousel-control-prev {
    left: 30px;
}

.carousel-control-next {
    right: 30px;
}

.carousel-control-prev:hover,
.carousel-control-next:hover {
    background: var(--primary-color);
}

.carousel-control-prev i,
.carousel-control-next i {
    font-size: 20px;
    color: var(--white);
}

.carousel-indicators {
    bottom: 30px;
}

.carousel-indicators button {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: rgba(255,255,255,0.5);
    border: 2px solid transparent;
    margin: 0 5px;
}

.carousel-indicators button.active {
    background: var(--primary-color);
    border-color: var(--white);
}

/* ===== Features Section ===== */
.features-section {
    padding: 80px 0;
    background: var(--white);
    border-bottom: 1px solid #eee;
}

.feature-box {
    text-align: center;
    padding: 30px 20px;
    transition: var(--transition);
}

.feature-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, var(--primary-color) 0%, #C4A037 100%);
    color: var(--white);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    border-radius: 50%;
    font-size: 32px;
    transition: var(--transition);
}

.feature-box:hover .feature-icon {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(212,175,55,0.3);
}

.feature-box h4 {
    font-size: 16px;
    font-weight: 600;
    color: var(--secondary-color);
    margin-bottom: 8px;
}

.feature-box p {
    font-size: 14px;
    color: #666;
    margin: 0;
}

/* ===== Products Section ===== */
.products-section {
    padding: 80px 0;
    background: var(--light-bg);
}

.section-header {
    margin-bottom: 60px;
}

.section-subtitle {
    display: inline-block;
    color: var(--primary-color);
    font-size: 14px;
    font-weight: 600;
    letter-spacing: 2px;
    text-transform: uppercase;
    margin-bottom: 10px;
}

.section-title {
    font-size: 42px;
    font-weight: 700;
    color: var(--secondary-color);
    margin-bottom: 15px;
}

.section-description {
    font-size: 16px;
    color: #666;
    max-width: 600px;
    margin: 0 auto;
}

/* ===== Filter Container ===== */
.filter-container {
    background: var(--white);
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 2px 15px rgba(0,0,0,0.08);
    margin-bottom: 50px;
}

.filter-group label {
    font-size: 13px;
    font-weight: 600;
    color: var(--text-color);
    margin-bottom: 10px;
    display: block;
}

.filter-group .custom-select {
    height: 50px;
    border: 2px solid #eee;
    border-radius: 4px;
    font-size: 14px;
    transition: var(--transition);
}

.filter-group .custom-select:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(212,175,55,0.1);
}

.search-box {
    position: relative;
}

.search-box .form-control {
    height: 50px;
    padding-right: 50px;
    border: 2px solid #eee;
    border-radius: 4px;
    transition: var(--transition);
}

.search-box .form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(212,175,55,0.1);
}

.search-btn {
    position: absolute;
    right: 0;
    top: 0;
    height: 50px;
    width: 50px;
    background: var(--primary-color);
    border: none;
    color: var(--white);
    cursor: pointer;
    transition: var(--transition);
    border-radius: 0 4px 4px 0;
}

.search-btn:hover {
    background: var(--secondary-color);
}

/* ===== Product Card ===== */
.product-card {
    background: var(--white);
    border-radius: 8px;
    overflow: hidden;
    transition: var(--transition);
    height: 100%;
    display: flex;
    flex-direction: column;
}

.product-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.12);
}

.product-image {
    position: relative;
    overflow: hidden;
    padding-top: 100%;
    background: #f5f5f5;
}

.product-image img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: var(--transition);
}

.product-card:hover .product-image img {
    transform: scale(1.1);
}

.product-badge {
    position: absolute;
    top: 15px;
    left: 15px;
    z-index: 2;
}

.badge-new {
    background: var(--primary-color);
    color: var(--secondary-color);
    padding: 6px 12px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    border-radius: 3px;
}

.product-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.7);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: var(--transition);
}

.product-card:hover .product-overlay {
    opacity: 1;
}

.overlay-actions {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
}

.action-btn {
    width: 45px;
    height: 45px;
    background: var(--white);
    color: var(--secondary-color);
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: var(--transition);
    transform: translateY(20px);
    opacity: 0;
    text-decoration: none;
}

.product-card:hover .action-btn {
    transform: translateY(0);
    opacity: 1;
}

.action-btn:nth-child(1) {
    transition-delay: 0.1s;
}

.action-btn:nth-child(2) {
    transition-delay: 0.15s;
}

.action-btn:nth-child(3) {
    transition-delay: 0.2s;
}

.action-btn:hover {
    background: var(--primary-color);
    color: var(--white);
    transform: translateY(-3px);
}

.btn-add-cart {
    background: var(--primary-color);
    color: var(--secondary-color);
    border: none;
    padding: 12px 30px;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 13px;
    letter-spacing: 1px;
    cursor: pointer;
    transition: var(--transition);
    transform: translateY(20px);
    opacity: 0;
    border-radius: 25px;
}

.product-card:hover .btn-add-cart {
    transform: translateY(0);
    opacity: 1;
    transition-delay: 0.25s;
}

.btn-add-cart:hover {
    background: var(--white);
    color: var(--secondary-color);
}

.product-info {
    padding: 20px;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
}

.product-category {
    font-size: 12px;
    color: var(--primary-color);
    text-transform: uppercase;
    letter-spacing: 1px;
    font-weight: 600;
    margin-bottom: 8px;
}

.product-title {
    font-size: 15px;
    font-weight: 600;
    margin-bottom: 10px;
    line-height: 1.4;
}

.product-title a {
    color: var(--secondary-color);
    text-decoration: none;
    transition: var(--transition);
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.product-title a:hover {
    color: var(--primary-color);
}

.product-price {
    margin-bottom: 10px;
}

.current-price {
    font-size: 20px;
    font-weight: 700;
    color: var(--primary-color);
}

.product-rating {
    display: flex;
    align-items: center;
    gap: 3px;
    font-size: 13px;
    color: #FFB800;
}

.product-rating span {
    color: #999;
    margin-left: 5px;
}

/* ===== Pagination ===== */
.pagination-wrapper {
    margin-top: 50px;
    display: flex;
    justify-content: center;
}

.pagination-wrapper .pagination {
    gap: 5px;
}

.pagination-wrapper .page-link {
    border: 2px solid #eee;
    color: var(--text-color);
    padding: 10px 16px;
    border-radius: 4px;
    transition: var(--transition);
}

.pagination-wrapper .page-link:hover {
    background: var(--primary-color);
    border-color: var(--primary-color);
    color: var(--secondary-color);
}

.pagination-wrapper .page-item.active .page-link {
    background: var(--primary-color);
    border-color: var(--primary-color);
    color: var(--secondary-color);
}

/* ===== Animations ===== */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* ===== Responsive ===== */
@media (max-width: 991px) {
    .hero-slider .carousel-item {
        height: 500px;
    }
    
    .hero-title {
        font-size: 42px;
    }
    
    .section-title {
        font-size: 32px;
    }
}

@media (max-width: 767px) {
    .hero-slider .carousel-item {
        height: 450px;
    }
    
    .hero-title {
        font-size: 32px;
    }
    
    .hero-description {
        font-size: 16px;
    }
    
    .section-title {
        font-size: 28px;
    }
    
    .features-section {
        padding: 50px 0;
    }
    
    .products-section {
        padding: 50px 0;
    }
    
    .filter-container {
        padding: 20px;
    }
    
    .carousel-control-prev,
    .carousel-control-next {
        width: 45px;
        height: 45px;
    }
    
    .carousel-control-prev {
        left: 15px;
    }
    
    .carousel-control-next {
        right: 15px;
    }
}

@media (max-width: 575px) {
    .product-title {
        font-size: 13px;
    }
    
    .current-price {
        font-size: 16px;
    }
    
    .hero-subtitle {
        font-size: 12px;
        padding-left: 40px;
    }
    
    .hero-subtitle::before {
        width: 30px;
    }
        }
    </style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script>
$(document).ready(function() {
    // Smooth scroll to products
    $('a[href="#products"]').on('click', function(e) {
        e.preventDefault();
        $('html, body').animate({
            scrollTop: $('#products').offset().top - 80
        }, 800);
    });
    
    // Add to cart with animation
    $('.btn-add-cart').on('click', function(e) {
        e.preventDefault();
        var productId = $(this).data('product-id');
        var btn = $(this);
        
        $.ajax({
            url: '{{ route("cart.add") }}',
            method: 'POST',
            timeout: 5000,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            data: {
                product_id: productId,
                quantity: 1
            },
            success: function(response) {
                if (response.success) {
                    if (typeof loadCartData === 'function') {
                        loadCartData();
                    }
                    
                    if (typeof showLuxuryModal === 'function') {
                        showLuxuryModal('cart', 'Đã thêm vào giỏ!', 'Sản phẩm đã được thêm vào giỏ hàng của bạn', {
                            secondaryBtn: {
                                text: 'Tiếp tục mua',
                                action: function() {}
                            },
                            primaryBtn: {
                                text: 'Xem giỏ hàng',
                                action: function() {
                                    if (typeof openMiniCart === 'function') {
                                        openMiniCart();
                                    }
                                }
                            },
                            autoClose: false
                        });
                    } else if (typeof openMiniCart === 'function') {
                        openMiniCart();
                    }
                }
            },
            error: function(xhr) {
                const errorMsg = xhr.responseJSON?.error || 'Có lỗi xảy ra';
                if (typeof showLuxuryModal === 'function') {
                    showLuxuryModal('error', 'Lỗi!', errorMsg, {
                        autoClose: 3000
                    });
                } else {
                    swal({
                        title: "Lỗi!",
                        text: errorMsg,
                        icon: "error",
                        button: "Đóng",
                    });
                }
            }
            });
        });

    // Add to wishlist
    $('.btn-add-wishlist').on('click', function(e) {
        e.preventDefault();
        var productId = $(this).data('product-id');
        var btn = $(this);
        
        $.ajax({
            url: '{{ route("wishlist.add") }}',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            data: {
                product_id: productId
            },
            success: function(response) {
                if (response.success) {
                    btn.find('i').removeClass('far').addClass('fas');
                    btn.addClass('active');
                    
                    if (typeof showLuxuryModal === 'function') {
                        showLuxuryModal('wishlist', 'Đã thêm vào yêu thích!', 'Sản phẩm đã được lưu vào danh sách yêu thích của bạn', {
                            secondaryBtn: {
                                text: 'Đóng',
                                action: function() {}
                            },
                            primaryBtn: {
                                text: 'Xem yêu thích',
                                action: function() {
                                    window.location.href = '{{ route("wishlist.index") }}';
                                }
                            },
                            autoClose: false
                        });
                    } else {
                        swal({
                            title: "Thành công!",
                            text: response.message,
                            icon: "success",
                            button: "Đóng",
                        });
                    }
                    
                    if (typeof loadWishlistData === 'function') {
                        loadWishlistData();
                    }
                }
            },
            error: function(xhr) {
                const errorMsg = xhr.responseJSON?.error || 'Có lỗi xảy ra';
                if (typeof showLuxuryModal === 'function') {
                    showLuxuryModal('error', 'Lỗi!', errorMsg, {
                        autoClose: 3000
                    });
                } else {
                    swal({
                        title: "Lỗi!",
                        text: errorMsg,
                        icon: "error",
                        button: "Đóng",
                    });
                }
            }
        });
    });
    
    // Product card entrance animation
    $('.product-card').each(function(index) {
        $(this).css({
            'animation': 'fadeInUp 0.6s ease forwards',
            'animation-delay': (index * 0.05) + 's',
            'opacity': '0'
        });
    });
    
    // Hero carousel auto-pause on hover
    $('#heroCarousel').hover(
        function() {
            $(this).carousel('pause');
        },
        function() {
            $(this).carousel('cycle');
        }
    );
});
    </script>
@endpush
@endsection