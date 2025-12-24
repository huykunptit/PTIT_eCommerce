<header class="header shop">
    <!-- Topbar -->
    <div class="topbar">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-12 col-12">
                    <!-- Top Left -->
                    <div class="top-left">
                        <ul class="list-main">
                            <li><i class="ti-headphone-alt"></i> 0123-456-789</li>
                            <li><i class="ti-email"></i> support@example.com</li>
                        </ul>
                    </div>
                    <!--/ End Top Left -->
                </div>
                <div class="col-lg-6 col-md-12 col-12">
                    <!-- Top Right -->
                    <div class="right-content">
                        <ul class="list-main">
                            <li><i class="ti-location-pin"></i> <span>Địa chỉ: PTIT</span></li>
                            <li><i class="ti-alarm-clock"></i> <span>Giờ làm việc: 08:00 - 17:30</span></li>
                            {{-- <li><i class="ti-alarm-clock"></i> <a href="#">Daily deal</a></li> --}}
                            @auth 
                                @if(Auth::user()->role=='admin')
                                    <li><i class="ti-user"></i> <a href="{{ route('admin.dashboard') }}"  target="_blank">Dashboard</a></li>
                                @endif
                                <li><i class="ti-power-off"></i> <a href="{{ route('auth.logout') }}">Logout</a></li>

                            @else
                                <li><i class="ti-power-off"></i><a href="{{ route('auth.login') }}">Login /</a> <a href="{{ route('auth.register') }}">Register</a></li>
                            @endauth
                        </ul>
                    </div>
                    <!-- End Top Right -->
                </div>
            </div>
        </div>
    </div>
    <!-- End Topbar -->
    <div class="middle-inner">
        <div class="container">
            <div class="row">
                <div class="col-lg-2 col-md-2 col-12">
                    <!-- Logo -->
                    <div class="logo">
                        <a href="{{ route('home') }}" style="display:inline-flex;align-items:center;">
                            <img src="{{ asset('images/logoden.png') }}" alt="logo" style="max-height:60px;width:auto;object-fit:contain;">
                        </a>
                    </div>
                    <!--/ End Logo -->
                    <!-- Search Form -->
                    <div class="search-top">
                        <div class="top-search"><a href="#0"><i class="ti-search"></i></a></div>
                        <!-- Search Form -->
                        <form class="search-form">
                            <input type="text" placeholder="Search here..." name="search">
                            <button value="search" type="submit"><i class="ti-search"></i></button>
                        </form>
                        <!--/ End Search Form -->
                    </div>
                    <!--/ End Search Form -->
                    <div class="mobile-nav"></div>
                </div>
                <div class="col-lg-8 col-md-7 col-12">
                    <div class="search-bar-top">
                        <div class="search-bar" style="position:relative;">
                            <select id="search-category" style="display:none;">
                                <option value="">All Category</option>
                                @php
                                    $categories = DB::table('categories')->select('id','name')->orderBy('name')->get();
                                @endphp
                                @foreach($categories as $cat)
                                <option value="{{$cat->id}}">{{$cat->name}}</option>
                                @endforeach
                            </select>
                            <form id="search-form" method="GET" action="{{ route('home') }}" style="position:relative;">
                                <input id="search-input" name="search" placeholder="Search Products Here....." type="search" autocomplete="off">
                                <button class="btnn" type="submit"><i class="ti-search"></i></button>
                            </form>
                            <div id="search-results" style="display:none;position:absolute;top:100%;left:0;right:0;background:#fff;border:1px solid #ddd;border-radius:4px;box-shadow:0 4px 12px rgba(0,0,0,0.15);z-index:1000;max-height:500px;overflow-y:auto;margin-top:5px;">
                                <div class="search-results-content"></div>
                                <div class="search-results-footer" style="padding:10px;border-top:1px solid #eee;text-align:center;">
                                    <a href="#" id="search-view-all" style="color:#D4AF37;text-decoration:none;font-weight:600;">Xem tất cả kết quả</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-3 col-12">
                    <div class="right-bar">
                        <!-- Wishlist -->
                        <div class="sinlge-bar shopping wishlist-dropdown">
                            <a href="{{ route('wishlist.index') }}" class="single-icon">
                                <i class="fa fa-heart"></i> 
                                <span class="total-count wishlist-count">0</span>
                            </a>
                            <!-- Shopping Item -->
                            <div class="shopping-item wishlist-preview">
                                <div class="dropdown-cart-header">
                                    <span class="wishlist-items-count">0 Sản phẩm</span>
                                    <a href="{{ route('wishlist.index') }}">Xem tất cả</a>
                                </div>
                                <ul class="shopping-list wishlist-items-list">
                                    <li class="empty-message">Danh sách yêu thích trống</li>
                                </ul>
                                <div class="bottom">
                                    <a href="{{ route('wishlist.index') }}" class="btn animate">Xem yêu thích</a>
                                </div>
                            </div>
                            <!--/ End Shopping Item -->
                        </div>
                        
                        <!-- Cart -->
                        <div class="sinlge-bar shopping cart-dropdown">
                            <a href="{{ route('cart.index') }}" class="single-icon">
                                <i class="ti-bag"></i> 
                                <span class="total-count cart-count">0</span>
                            </a>
                            <!-- Shopping Item -->
                            <div class="shopping-item cart-preview">
                                <div class="dropdown-cart-header">
                                    <span class="cart-items-count">0 Sản phẩm</span>
                                    <a href="{{ route('cart.index') }}">Xem giỏ hàng</a>
                                </div>
                                <ul class="shopping-list cart-items-list">
                                    <li class="empty-message">Giỏ hàng trống</li>
                                </ul>
                                <div class="bottom">
                                    <div class="total">
                                        <span>Tổng cộng</span>
                                        <span class="total-amount cart-total">0₫</span>
                                    </div>
                                    <a href="{{ route('checkout.index') }}" class="btn animate">Thanh toán</a>
                                </div>
                            </div>
                            <!--/ End Shopping Item -->
                        </div>
                        
                        <!-- User Avatar Dropdown -->
                        @auth
                        <div class="sinlge-bar user-dropdown">
                            <a href="#" class="single-icon user-avatar-toggle" style="position:relative;">
                                @if(Auth::user()->avatar)
                                    <img src="{{ asset(Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}" style="width:30px;height:30px;border-radius:50%;object-fit:cover;">
                                @else
                                    <i class="ti-user" style="font-size:20px;"></i>
                                @endif
                            </a>
                            <!-- User Dropdown -->
                            <div class="shopping-item user-menu" style="display:none;position:absolute;right:0;top:100%;background:#fff;border:1px solid #ddd;border-radius:4px;box-shadow:0 4px 12px rgba(0,0,0,0.15);z-index:1000;min-width:200px;margin-top:10px;">
                                <div class="dropdown-cart-header" style="padding:15px;border-bottom:1px solid #eee;">
                                    <div style="display:flex;align-items:center;gap:10px;">
                                        @if(Auth::user()->avatar)
                                            <img src="{{ asset(Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}" style="width:40px;height:40px;border-radius:50%;object-fit:cover;">
                                        @else
                                            <div style="width:40px;height:40px;border-radius:50%;background:var(--primary-color, #667eea);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:bold;">
                                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <div style="font-weight:600;color:#333;">{{ Auth::user()->name }}</div>
                                            <div style="font-size:12px;color:#666;">{{ Auth::user()->email }}</div>
                                        </div>
                                    </div>
                                </div>
                                <ul class="shopping-list" style="list-style:none;padding:0;margin:0;">
                                    <li style="border-bottom:1px solid #eee;">
                                        <a href="#" class="orders-dropdown-toggle" style="display:flex;align-items:center;padding:12px 15px;color:#333;text-decoration:none;transition:background 0.3s;">
                                            <i class="ti-shopping-cart" style="margin-right:10px;color:var(--primary-color, #667eea);"></i>
                                            <span>Đơn hàng của tôi</span>
                                            <i class="ti-angle-down" style="margin-left:auto;font-size:12px;"></i>
                                        </a>
                                        <!-- Orders Dropdown -->
                                        <div class="orders-dropdown" style="display:none;background:#fff;border-top:1px solid #eee;max-height:400px;overflow-y:auto;">
                                            <div class="orders-dropdown-header" style="padding:10px 15px;background:#f8f9fa;border-bottom:1px solid #eee;font-weight:600;font-size:14px;">
                                                Đơn hàng gần đây
                                            </div>
                                            <div class="orders-dropdown-content" style="padding:0;">
                                                <div class="orders-loading" style="padding:20px;text-align:center;color:#666;">
                                                    <i class="ti-reload"></i> Đang tải...
                                                </div>
                                                <div class="orders-list" style="display:none;"></div>
                                                <div class="orders-empty" style="display:none;padding:20px;text-align:center;color:#666;">
                                                    Chưa có đơn hàng nào
                                                </div>
                                            </div>
                                            <div class="orders-dropdown-footer" style="padding:10px 15px;border-top:1px solid #eee;text-align:center;">
                                                <a href="{{ route('user.orders') }}" style="color:#D4AF37;text-decoration:none;font-weight:600;font-size:14px;">
                                                    Xem tất cả đơn hàng <i class="ti-arrow-right"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </li>
                                    <li style="border-bottom:1px solid #eee;">
                                        <a href="{{ route('user.profile') }}" style="display:flex;align-items:center;padding:12px 15px;color:#333;text-decoration:none;transition:background 0.3s;">
                                            <i class="ti-user" style="margin-right:10px;color:var(--primary-color, #667eea);"></i>
                                            <span>Chỉnh sửa trang cá nhân</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('auth.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form-frontend').submit();" style="display:flex;align-items:center;padding:12px 15px;color:#e53e3e;text-decoration:none;transition:background 0.3s;">
                                            <i class="ti-power-off" style="margin-right:10px;"></i>
                                            <span>Đăng xuất</span>
                                        </a>
                                        <form id="logout-form-frontend" action="{{ route('auth.logout') }}" method="POST" style="display: none;">
                                            @csrf
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Header Inner -->
    <div class="header-inner">
        <div class="container">
            <div class="cat-nav-head">
                <div class="row">
                    <div class="col-lg-12 col-12">
                        <div class="menu-area">
                            <!-- Main Menu -->
                            <nav class="navbar navbar-expand-lg">
                                <div class="navbar-collapse">	
                                    <div class="nav-inner">	
                                        <ul class="nav main-menu menu navbar-nav">
                                            <li class="{{Request::path()=='' || Request::path()=='home' ? 'active' : ''}}"><a href="{{ route('home') }}">Home</a></li>
                                            <li class="{{Request::path()=='about' ? 'active' : ''}}"><a href="{{ route('about') }}">About Us</a></li>
                                            <li class="@if(Request::path()=='product-grids'||Request::path()=='product-lists'||Request::is('product*'))  active  @endif"><a href="{{ route('home') }}#products">Products</a><span class="new">New</span></li>
                                            <li class="{{Request::path()=='blog' ? 'active' : ''}}"><a href="{{ route('blog.index') }}">Blog</a></li>
                                            <li class="{{Request::path()=='contact' ? 'active' : ''}}"><a href="{{ route('contact') }}">Contact Us</a></li>
                                            @guest
                                            <li><a href="{{ route('auth.login') }}"><i class="ti-user"></i> Login</a></li>
                                            @endguest
                                        </ul>
                                    </div>
                                </div>
                            </nav>
                            <!--/ End Main Menu -->	
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--/ End Header Inner -->
</header>