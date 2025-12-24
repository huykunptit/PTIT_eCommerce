@extends('frontend.layouts.master')
@section('title','PTIT || Trang Sức Cao Cấp')
@section('main-content')
@php
  use Illuminate\Support\Facades\DB;
  
  // Load banners from database
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
    $categoryMap = $categories->pluck('name', 'id');
  
  // Load products for client-side filtering.
  // IMPORTANT: do NOT load the entire products table in-memory (can OOM on small RAM dynos).
  // Keep this reasonably bounded; UI still supports filtering within this window.
  $maxProducts = (int) (env('HOME_PRODUCTS_MAX') ?: 200);
  if ($maxProducts <= 0) { $maxProducts = 200; }
  if ($maxProducts > 500) { $maxProducts = 500; }

  $allProducts = DB::table('products')
      ->select(['id','name','price','quantity','image_url','category_id','description'])
      ->where('status', 'active')
      ->orderByDesc('id')
      ->limit($maxProducts)
      ->get();
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
                <div class="hero-image">
                    <img src="{{ $bSrc }}" alt="{{ $banner->title ?? 'Banner' }}" referrerpolicy="no-referrer">
                </div>
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
                                    <i class="fa fa-arrow-right ml-2"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <button class="carousel-control-prev" type="button" data-target="#heroCarousel" data-slide="prev">
            <i class="fa fa-chevron-left"></i>
            <span class="sr-only">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-target="#heroCarousel" data-slide="next">
            <i class="fa fa-chevron-right"></i>
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
                        <i class="fa fa-shipping-fast"></i>
                    </div>
                    <h4>Miễn Phí Vận Chuyển</h4>
                    <p>Cho đơn hàng trên 2 triệu</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="feature-box">
                    <div class="feature-icon">
                        <i class="fa fa-shield-alt"></i>
                    </div>
                    <h4>Bảo Hành Trọn Đời</h4>
                    <p>Cam kết chất lượng vàng</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="feature-box">
                    <div class="feature-icon">
                        <i class="fa fa-gem"></i>
                    </div>
                    <h4>Chất Lượng Cao Cấp</h4>
                    <p>Vàng 24K chính hãng</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="feature-box">
                    <div class="feature-icon">
                        <i class="fa fa-headset"></i>
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

        <!-- Filter Section - New Design -->
        <div class="filter-wrapper-new">
            <div class="filter-box">
                <!-- Category Filter -->
                <div class="filter-item">
                    <label class="filter-label">
                        <i class="fa fa-list mr-2"></i>Danh mục
                    </label>
                    <select id="categoryFilter" class="filter-control">
                        <option value="">Tất cả danh mục</option>
                        @foreach($categories as $cat)
                            <option value="{{$cat->id}}">{{$cat->name}}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Search Input -->
                <div class="filter-item filter-item-search">
                    <label class="filter-label">
                        <i class="fa fa-search mr-2"></i>Tìm kiếm
                    </label>
                    <div class="search-box">
                        <input type="text" id="searchInput" class="filter-control" placeholder="Nhập tên sản phẩm...">
                        <button id="clearSearch" class="search-clear" style="display: none;" type="button">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>

                <!-- Sort Filter -->
                <div class="filter-item">
                    <label class="filter-label">
                        <i class="fa fa-sort mr-2"></i>Sắp xếp
                    </label>
                    <select id="sortFilter" class="filter-control">
                        <option value="newest">Mới nhất</option>
                        <option value="price-asc">Giá tăng dần</option>
                        <option value="price-desc">Giá giảm dần</option>
                        <option value="name">Tên A-Z</option>
                    </select>
                </div>

                <!-- Reset Button -->
                <div class="filter-item filter-item-btn">
                    <label class="filter-label" style="opacity: 0;">&nbsp;</label>
                    <button id="resetFilters" class="btn-filter-reset" type="button">
                        <i class="fa fa-redo mr-2"></i>Đặt lại
                    </button>
                </div>
            </div>
            
            <!-- Filter Status -->
            <div id="filterStatus" class="filter-result-info" style="display: none;">
                <i class="fa fa-info-circle mr-2"></i>
                <span id="resultCount">0</span> sản phẩm được tìm thấy
            </div>
        </div>

        <!-- Products Grid -->
        <div id="productsContainer" class="products-grid">
            <!-- Skeleton Loaders -->
            <div id="skeletonLoaders" class="row" style="display: none;">
                @for($i = 0; $i < 8; $i++)
                <div class="col-lg-3 col-md-4 col-6 mb-4">
                    <div class="product-card skeleton-card">
                        <div class="skeleton-image"></div>
                        <div class="skeleton-content">
                            <div class="skeleton-line skeleton-title"></div>
                            <div class="skeleton-line skeleton-price"></div>
                            <div class="skeleton-line skeleton-rating"></div>
                        </div>
                    </div>
                </div>
                @endfor
            </div>
            
            <div class="row" id="productsRow">
                @foreach($allProducts as $product)
                    @php
                        $photos = explode(',', (string)($product->image_url ?? ''));
                        $img = trim($photos[0] ?? '');
                        $imgLocal = $img && !\Illuminate\Support\Str::startsWith($img, ['http://','https://']) ? public_path($img) : null;
                        $imgSrc = $img && \Illuminate\Support\Str::startsWith($img, ['http://','https://'])
                            ? $img
                            : (($imgLocal && file_exists($imgLocal)) ? asset($img) : asset('backend/img/thumbnail-default.jpg'));
                        $categoryName = (string)($categoryMap[$product->category_id] ?? '');
                    @endphp
                    <div class="col-lg-3 col-md-4 col-6 mb-4 product-item" 
                         data-id="{{$product->id}}"
                         data-name="{{strtolower($product->name)}}"
                         data-category="{{$product->category_id}}"
                         data-category-name="{{strtolower($categoryName)}}"
                         data-price="{{$product->price ?? 0}}"
                         data-description="{{strtolower($product->description ?? '')}}">
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
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <button type="button" class="action-btn btn-add-wishlist" data-product-id="{{ $product->id }}" title="Yêu thích">
                                            <i class="far fa-heart"></i>
                                        </button>
                                    </div>
                                    <button type="button" class="btn-add-cart" data-product-id="{{ $product->id }}">
                                        <i class="fa fa-shopping-bag mr-2"></i>
                                        Thêm vào giỏ
                                    </button>
                                </div>
                            </div>
                            <div class="product-info">
                                <div class="product-category">{{ $categoryName !== '' ? $categoryName : 'Danh mục' }}</div>
                                <h3 class="product-title">
                                    <a href="{{ url('/product/'.$product->id) }}">{{$product->name}}</a>
                                </h3>
                                @if(isset($product->price))
                                <div class="product-price">
                                    <span class="current-price">{{ number_format($product->price, 0, ',', '.') }}₫</span>
                                </div>
                                @endif
                                <div class="product-rating">
                                    <i class="fa fa-star"></i>
                                    <i class="fa fa-star"></i>
                                    <i class="fa fa-star"></i>
                                    <i class="fa fa-star"></i>
                                    <i class="fa fa-star"></i>
                                    <span>(5.0)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- No Results Message -->
        <div id="noResults" class="no-results" style="display: none;">
            <i class="fa fa-search"></i>
            <h3>Không tìm thấy sản phẩm</h3>
            <p>Vui lòng thử tìm kiếm với từ khóa khác hoặc điều chỉnh bộ lọc</p>
        </div>

        <!-- Loading Indicator -->
        <div id="loadingIndicator" class="loading-indicator" style="display: none;">
            <div class="spinner"></div>
            <p>Đang tải...</p>
        </div>

        <!-- Pagination -->
        <div id="paginationContainer" class="pagination-container mt-4">
            <nav aria-label="Product pagination">
                <ul class="pagination justify-content-center" id="paginationList">
                    <!-- Pagination items will be generated by JavaScript -->
                </ul>
            </nav>
        </div>
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

/* ===== Hero Slider (Compact) ===== */
.hero-slider {
    position: relative;
    overflow: hidden;
}

.hero-slider .carousel-item {
    height: 450px;
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
}

.hero-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
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
}

.hero-subtitle {
    display: inline-block;
    color: var(--primary-color);
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 2px;
    text-transform: uppercase;
    margin-bottom: 10px;
}

.hero-title {
    font-size: 48px;
    font-weight: 700;
    line-height: 1.2;
    margin-bottom: 15px;
    color: var(--white);
}

.hero-description {
    font-size: 16px;
    line-height: 1.6;
    margin-bottom: 25px;
    color: rgba(255,255,255,0.9);
    max-width: 500px;
}

.btn-hero {
    display: inline-flex;
    align-items: center;
    padding: 14px 35px;
    background: var(--primary-color);
    color: var(--secondary-color);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    transition: var(--transition);
    text-decoration: none;
    font-size: 13px;
}

.btn-hero:hover {
    background: var(--white);
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(212,175,55,0.3);
    text-decoration: none;
}

.carousel-control-prev,
.carousel-control-next {
    width: 50px;
    height: 50px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
    top: 50%;
    transform: translateY(-50%);
    opacity: 1;
    transition: var(--transition);
}

.carousel-control-prev {
    left: 20px;
}

.carousel-control-next {
    right: 20px;
}

.carousel-control-prev:hover,
.carousel-control-next:hover {
    background: var(--primary-color);
}

.carousel-indicators {
    bottom: 20px;
}

.carousel-indicators button {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: rgba(255,255,255,0.5);
    margin: 0 4px;
}

.carousel-indicators button.active {
    background: var(--primary-color);
}

/* ===== Features Section (Compact) ===== */
.features-section {
    padding: 50px 0;
    background: var(--white);
    border-bottom: 1px solid #eee;
}

.feature-box {
    text-align: center;
    padding: 20px 15px;
    transition: var(--transition);
}

.feature-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, var(--primary-color) 0%, #C4A037 100%);
    color: var(--white);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 15px;
    border-radius: 50%;
    font-size: 24px;
    transition: var(--transition);
}

.feature-box:hover .feature-icon {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(212,175,55,0.3);
}

.feature-box h4 {
    font-size: 14px;
    font-weight: 600;
    color: var(--secondary-color);
    margin-bottom: 5px;
}

.feature-box p {
    font-size: 13px;
    color: #666;
    margin: 0;
}

/* ===== Products Section ===== */
.products-section {
    padding: 50px 0;
    background: var(--light-bg);
}

.section-header {
    margin-bottom: 40px;
}

.section-subtitle {
    display: inline-block;
    color: var(--primary-color);
    font-size: 13px;
    font-weight: 600;
    letter-spacing: 2px;
    text-transform: uppercase;
    margin-bottom: 8px;
}

.section-title {
    font-size: 36px;
    font-weight: 700;
    color: var(--secondary-color);
    margin-bottom: 10px;
}

.section-description {
    font-size: 15px;
    color: #666;
    max-width: 600px;
    margin: 0 auto;
}

/* ===== New Filter Design ===== */
.filter-wrapper-new {
    margin-bottom: 30px;
}

.filter-box {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    background: #fff;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    align-items: flex-end;
}

.filter-item {
    flex: 1;
    min-width: 180px;
    display: flex;
    flex-direction: column;
}

.filter-item-search {
    flex: 2;
    min-width: 250px;
    margin-bottom: 15px;
}

.filter-item-btn {
    flex: 0 0 auto;
    min-width: 120px;
    margin-bottom: 15px;
}

.filter-label {
    font-size: 13px;
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
}

.filter-control {
    width: 100%;
    height: 44px;
    padding: 0 15px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 14px;
    background: #fff;
    transition: all 0.3s ease;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
}

.filter-control:focus {
    outline: none;
    border-color: #D4AF37;
    box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.1);
}

.filter-control option {
    padding: 10px;
}

/* Search Box */
.search-box {
    position: relative;
    display: flex;
    align-items: center;
}

.search-box .filter-control {
    padding-right: 40px;
}

.search-clear {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #999;
    cursor: pointer;
    padding: 5px 8px;
    font-size: 14px;
    transition: color 0.3s ease;
    z-index: 10;
}

.search-clear:hover {
    color: #D4AF37;
}

/* Reset Button */
.btn-filter-reset {
    width: 100%;
    height: 44px;
    background: #1a1a1a;
    color: #fff;
    border: none;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-filter-reset:hover {
    background: #D4AF37;
    color: #1a1a1a;
}

/* Filter Result Info */
.filter-result-info {
    margin-top: 15px;
    padding: 12px 20px;
    background: #f8f9fa;
    border-radius: 6px;
    font-size: 14px;
    color: #666;
    display: flex;
    align-items: center;
}

.filter-result-info i {
    color: #D4AF37;
}

#resultCount {
    font-weight: 600;
    color: #D4AF37;
    margin: 0 5px;
}

/* Responsive */
@media (max-width: 991px) {
    .filter-box {
        flex-direction: column;
    }
    
    .filter-item,
    .filter-item-search,
    .filter-item-btn {
        width: 100%;
        min-width: 100%;
    }
}

@media (max-width: 575px) {
    .filter-box {
        padding: 15px;
        gap: 12px;
    }
    
    .filter-control,
    .btn-filter-reset {
        height: 42px;
        font-size: 13px;
    }
}

/* ===== Product Card ===== */
.product-card {
    background: var(--white);
    border-radius: 8px;
    overflow: hidden;
    transition: var(--transition);
    height: 100%;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
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
    transform: scale(1.08);
}

.product-badge {
    position: absolute;
    top: 12px;
    left: 12px;
    z-index: 2;
}

.badge-new {
    background: var(--primary-color);
    color: var(--secondary-color);
    padding: 5px 10px;
    font-size: 10px;
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
    gap: 8px;
    margin-bottom: 15px;
}

.action-btn {
    width: 40px;
    height: 40px;
    background: var(--white);
    color: var(--secondary-color);
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: var(--transition);
    text-decoration: none;
    border: none;
    cursor: pointer;
}

.action-btn:hover {
    background: var(--primary-color);
    color: var(--white);
    transform: translateY(-2px);
}

.btn-add-cart {
    background: var(--primary-color);
    color: var(--secondary-color);
    border: none;
    padding: 10px 25px;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 12px;
    letter-spacing: 1px;
    cursor: pointer;
    transition: var(--transition);
    border-radius: 20px;
}

.btn-add-cart:hover {
    background: var(--white);
    color: var(--secondary-color);
}

.product-info {
    padding: 18px;
}

.product-category {
    font-size: 11px;
    color: var(--primary-color);
    text-transform: uppercase;
    letter-spacing: 1px;
    font-weight: 600;
    margin-bottom: 6px;
}

.product-title {
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 8px;
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
    margin-bottom: 8px;
}

.current-price {
    font-size: 18px;
    font-weight: 700;
    color: var(--primary-color);
}

.product-rating {
    display: flex;
    align-items: center;
    gap: 2px;
    font-size: 12px;
    color: #FFB800;
}

.product-rating span {
    color: #999;
    margin-left: 4px;
}

/* ===== No Results ===== */
.no-results {
    text-align: center;
    padding: 60px 20px;
    color: #999;
}

.no-results i {
    font-size: 64px;
    color: #ddd;
    margin-bottom: 20px;
}

.no-results h3 {
    font-size: 24px;
    color: var(--text-color);
    margin-bottom: 10px;
}

.no-results p {
    font-size: 15px;
    color: #999;
}

/* ===== Loading Indicator ===== */
.loading-indicator {
    text-align: center;
    padding: 40px;
}

.spinner {
    width: 40px;
    height: 40px;
    border: 3px solid #f3f3f3;
    border-top: 3px solid var(--primary-color);
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 15px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* ===== Skeleton Loaders ===== */
.skeleton-card {
    background: var(--white);
    border-radius: 8px;
    overflow: hidden;
    padding: 0;
}

.skeleton-image {
    width: 100%;
    padding-top: 100%;
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: skeleton-loading 1.5s ease-in-out infinite;
}

.skeleton-content {
    padding: 18px;
}

.skeleton-line {
    height: 12px;
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: skeleton-loading 1.5s ease-in-out infinite;
    border-radius: 4px;
    margin-bottom: 10px;
}

.skeleton-title {
    width: 80%;
    height: 16px;
}

.skeleton-price {
    width: 60%;
    height: 20px;
    margin-top: 8px;
}

.skeleton-rating {
    width: 40%;
    height: 14px;
    margin-top: 8px;
}

@keyframes skeleton-loading {
    0% {
        background-position: 200% 0;
    }
    100% {
        background-position: -200% 0;
    }
}

/* ===== Pagination ===== */
.pagination-container {
    margin-top: 40px;
    padding-top: 30px;
    border-top: 1px solid #eee;
}

.pagination {
    display: flex;
    list-style: none;
    padding: 0;
    margin: 0;
}

.pagination li {
    margin: 0 5px;
}

.pagination a,
.pagination span {
    display: inline-block;
    padding: 10px 15px;
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    color: var(--text-color);
    text-decoration: none;
    transition: var(--transition);
    min-width: 40px;
    text-align: center;
}

.pagination a:hover {
    background: var(--primary-color);
    color: var(--white);
    border-color: var(--primary-color);
}

.pagination .active span {
    background: var(--primary-color);
    color: var(--white);
    border-color: var(--primary-color);
}

.pagination .disabled span {
    opacity: 0.5;
    cursor: not-allowed;
}

/* ===== Fade Animation ===== */
.product-item {
    transition: opacity 0.3s ease, transform 0.3s ease;
}

.product-item.hiding {
    opacity: 0;
    transform: scale(0.95);
}

/* ===== Responsive ===== */
@media (max-width: 991px) {
    .hero-slider .carousel-item {
        height: 400px;
    }
    
    .hero-title {
        font-size: 36px;
    }
    
    .section-title {
        font-size: 28px;
    }
}

@media (max-width: 767px) {
    .hero-slider .carousel-item {
        height: 350px;
    }
    
    .hero-title {
        font-size: 28px;
    }
    
    .section-title {
        font-size: 24px;
    }
    
    .features-section {
        padding: 40px 0;
    }
    
    .products-section {
        padding: 40px 0;
    }
    
    .filter-container-compact {
        padding: 12px;
    }
    
    .filter-select,
    .filter-input,
    .btn-reset {
        height: 38px;
        font-size: 13px;
    }
}

@media (max-width: 575px) {
    .product-title {
        font-size: 13px;
    }
    
    .current-price {
        font-size: 16px;
    }
}
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
<script>
$(document).ready(function() {
    // Realtime Filter & Search System
    let filterTimeout;
    const $productsContainer = $('#productsRow');
    const $allProducts = $('.product-item');
    const $noResults = $('#noResults');
    const $filterStatus = $('#filterStatus');
    const $resultCount = $('#resultCount');
    const $categoryFilter = $('#categoryFilter');
    const $searchInput = $('#searchInput');
    const $sortFilter = $('#sortFilter');
    const $clearSearch = $('#clearSearch');
    const $resetFilters = $('#resetFilters');
    const $skeletonLoaders = $('#skeletonLoaders');
    const $paginationContainer = $('#paginationContainer');
    const $paginationList = $('#paginationList');
    
    // Pagination settings
    let currentPage = 1;
    const itemsPerPage = 12;
    let filteredProducts = [];

    function normalizeText(value) {
        if (value === null || value === undefined) return '';
        return String(value)
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/đ/g, 'd')
            .trim();
    }
    
    // Filter function with debounce
    function filterProducts() {
        clearTimeout(filterTimeout);
        filterTimeout = setTimeout(function() {
            const category = $categoryFilter.val();
            const searchTerm = normalizeText($searchInput.val());
            const sortBy = $sortFilter.val();
            
            // Show/hide clear button
            $clearSearch.toggle(searchTerm.length > 0);
            
            let visibleProducts = $allProducts.filter(function() {
                const $item = $(this);
                const matchCategory = !category || $item.data('category') == category;
                const productName = normalizeText($item.data('name'));
                const productDesc = normalizeText($item.data('description'));
                const productCategoryName = normalizeText($item.data('category-name'));
                const matchSearch = !searchTerm || 
                    productName.includes(searchTerm) || 
                    productDesc.includes(searchTerm) ||
                    productCategoryName.includes(searchTerm);
                
                return matchCategory && matchSearch;
            });
            
            // Sort products
            if (sortBy !== 'newest') {
                visibleProducts = visibleProducts.sort(function(a, b) {
                    const $a = $(a);
                    const $b = $(b);
                    
                    switch(sortBy) {
                        case 'price-asc':
                            return parseFloat($a.data('price')) - parseFloat($b.data('price'));
                        case 'price-desc':
                            return parseFloat($b.data('price')) - parseFloat($a.data('price'));
                        case 'name':
                            return normalizeText($a.data('name')).localeCompare(normalizeText($b.data('name')));
                        default:
                            return 0;
                    }
                });
            }
            
            // Store filtered products
            filteredProducts = visibleProducts.toArray();
            currentPage = 1;
            
            // Show skeleton loaders
            $skeletonLoaders.show();
            $productsContainer.hide();
            
            setTimeout(function() {
                $skeletonLoaders.hide();
                renderProducts();
                renderPagination();
                
                if (filteredProducts.length > 0) {
                    $noResults.hide();
                    $filterStatus.show();
                    $resultCount.text(filteredProducts.length);
                } else {
                    $noResults.show();
                    $filterStatus.hide();
                    $paginationContainer.hide();
                }
            }, 500);
            
        }, 300); // Debounce delay
    }
    
    // Event listeners
    $categoryFilter.on('change', filterProducts);
    $searchInput.on('input', filterProducts);
    $sortFilter.on('change', filterProducts);
    
    $clearSearch.on('click', function() {
        $searchInput.val('').focus();
        filterProducts();
    });
    
    $resetFilters.on('click', function() {
        $categoryFilter.val('');
        $searchInput.val('');
        $sortFilter.val('newest');
        $clearSearch.hide();
        filterProducts();
    });
    
    // Render products for current page
    function renderProducts() {
        $allProducts.hide();
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        const productsToShow = filteredProducts.slice(startIndex, endIndex);
        
        productsToShow.forEach(function(product, index) {
            const $item = $(product);
            setTimeout(function() {
                $item.show().css({
                    'animation': 'fadeInUp 0.4s ease forwards',
                    'animation-delay': (index * 0.03) + 's'
                });
            }, 50);
        });
        
        $productsContainer.show();
        
        // Scroll to products section
        if (currentPage > 1) {
            $('html, body').animate({
                scrollTop: $('#products').offset().top - 70
            }, 300);
        }
    }
    
    // Render pagination
    function renderPagination() {
        const totalPages = Math.ceil(filteredProducts.length / itemsPerPage);
        
        if (totalPages <= 1) {
            $paginationContainer.hide();
            return;
        }
        
        $paginationContainer.show();
        $paginationList.empty();
        
        // Previous button
        const prevDisabled = currentPage === 1 ? 'disabled' : '';
        $paginationList.append(`
            <li class="page-item ${prevDisabled}">
                <a class="page-link" href="#" data-page="${currentPage - 1}" ${prevDisabled ? 'tabindex="-1"' : ''}>
                    <i class="fa fa-chevron-left"></i>
                </a>
            </li>
        `);
        
        // Page numbers
        let startPage = Math.max(1, currentPage - 2);
        let endPage = Math.min(totalPages, currentPage + 2);
        
        if (startPage > 1) {
            $paginationList.append(`<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>`);
            if (startPage > 2) {
                $paginationList.append(`<li class="page-item disabled"><span class="page-link">...</span></li>`);
            }
        }
        
        for (let i = startPage; i <= endPage; i++) {
            const active = i === currentPage ? 'active' : '';
            $paginationList.append(`
                <li class="page-item ${active}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>
            `);
        }
        
        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                $paginationList.append(`<li class="page-item disabled"><span class="page-link">...</span></li>`);
            }
            $paginationList.append(`<li class="page-item"><a class="page-link" href="#" data-page="${totalPages}">${totalPages}</a></li>`);
        }
        
        // Next button
        const nextDisabled = currentPage === totalPages ? 'disabled' : '';
        $paginationList.append(`
            <li class="page-item ${nextDisabled}">
                <a class="page-link" href="#" data-page="${currentPage + 1}" ${nextDisabled ? 'tabindex="-1"' : ''}>
                    <i class="fa fa-chevron-right"></i>
                </a>
            </li>
        `);
    }
    
    // Pagination click handler
    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        const page = parseInt($(this).data('page'));
        if (page && page !== currentPage && page >= 1) {
            currentPage = page;
            renderProducts();
            renderPagination();
        }
    });
    
    // Initial load
    filteredProducts = $allProducts.toArray();
    renderProducts();
    renderPagination();
    $filterStatus.show();
    $resultCount.text($allProducts.length);
    
    // Smooth scroll to products
    $('a[href="#products"]').on('click', function(e) {
        e.preventDefault();
        $('html, body').animate({
            scrollTop: $('#products').offset().top - 70
        }, 600);
    });
    
    // Add to cart
    $(document).on('click', '.btn-add-cart', function(e) {
        e.preventDefault();
        const productId = $(this).data('product-id');
        
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
                    } else {
                        swal("Thành công!", "Đã thêm vào giỏ hàng", "success");
                    }
                }
            },
            error: function(xhr) {
                const errorMsg = xhr.responseJSON?.error || 'Có lỗi xảy ra';
                swal("Lỗi!", errorMsg, "error");
            }
        });
    });

    // Add to wishlist
    $(document).on('click', '.btn-add-wishlist', function(e) {
        e.preventDefault();
        const productId = $(this).data('product-id');
        const $btn = $(this);
        
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
                    $btn.find('i').removeClass('far').addClass('fas');
                    $btn.addClass('active');
                    
                    if (typeof showLuxuryModal === 'function') {
                        showLuxuryModal('wishlist', 'Đã thêm vào yêu thích!', 'Sản phẩm đã được lưu vào danh sách yêu thích', {
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
                        swal("Thành công!", response.message, "success");
                    }
                    
                    if (typeof loadWishlistData === 'function') {
                        loadWishlistData();
                    }
                }
            },
            error: function(xhr) {
                const errorMsg = xhr.responseJSON?.error || 'Có lỗi xảy ra';
                swal("Lỗi!", errorMsg, "error");
            }
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