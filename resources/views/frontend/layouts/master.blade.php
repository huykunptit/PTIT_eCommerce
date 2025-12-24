<!DOCTYPE html>
<html lang="zxx">
<head>
    @include('frontend.layouts.head')

    {{-- CSS cho component / widget --}}
    @stack('styles')
</head>

<body class="js">

	<!-- Global Loading Overlay - Disabled -->
	{{-- <div id="global-loader" style="position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;background:rgba(255,255,255,0.85);">
		<img src="{{ asset('images/loading.gif') }}" alt="Loading" style="width:100%;height:auto;image-rendering:-webkit-optimize-contrast;"/>
	</div>
	<script>
		window.addEventListener('load', function () {
			var loader = document.getElementById('global-loader');
			if (loader) loader.style.display = 'none';
		});
	</script> --}}
	
	@include('frontend.layouts.notification')
	<!-- Header -->
	@include('frontend.layouts.header')
	<!--/ End Header -->
	@yield('main-content')
	
	@include('frontend.layouts.footer')
	
	<!-- Luxury Modals -->
	@include('frontend.components.modals')
	
	<!-- Mini Cart Sidebar -->
	<div id="mini-cart-overlay" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:10000;backdrop-filter:blur(2px);"></div>
	<div id="mini-cart-sidebar" style="position:fixed;top:0;width:450px;max-width:90vw;height:100vh;background:#fff;z-index:10001;transition:right 0.3s ease;overflow-y:auto;box-shadow:-2px 0 10px rgba(0,0,0,0.1);">
		<div class="mini-cart-header" style="padding:20px;border-bottom:1px solid #eee;display:flex;justify-content:space-between;align-items:center;position:sticky;top:0;background:#fff;z-index:10;">
			<h3 style="margin:0;font-size:20px;font-weight:600;">Giỏ hàng <span class="cart-count-badge" style="font-size:14px;color:#666;">(0)</span></h3>
			<button type="button" class="mini-cart-close" style="background:none;border:none;font-size:24px;cursor:pointer;color:#666;">&times;</button>
		</div>
		<div class="mini-cart-body" style="padding:20px;">
			<div style="text-align:center;padding:20px;color:#28a745;font-weight:600;margin-bottom:15px;border-bottom:1px solid #eee;padding-bottom:15px;">
				Miễn phí vận chuyển cho tất cả đơn hàng
			</div>
			<ul class="mini-cart-items" style="list-style:none;padding:0;margin:0;"></ul>
		</div>
		<div class="mini-cart-footer" style="padding:20px;border-top:1px solid #eee;position:sticky;bottom:0;background:#fff;">
			<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:15px;font-size:18px;font-weight:600;">
				<span>Tổng cộng:</span>
				<span class="mini-cart-total" style="color:#D4AF37;font-size:20px;">0₫</span>
			</div>
			<a href="{{ route('cart.index') }}" class="btn btn-primary" style="display:block;width:100%;padding:15px;text-align:center;background:#D4AF37;color:#1a1a1a;text-decoration:none;border-radius:4px;font-weight:600;margin-bottom:10px;">Xem giỏ hàng</a>
			<a href="{{ route('checkout.index') }}" class="btn btn-secondary" style="display:block;width:100%;padding:15px;text-align:center;background:#1a1a1a;color:#fff;text-decoration:none;border-radius:4px;font-weight:600;">Thanh toán</a>
		</div>
	</div>
	
	<style>
	#mini-cart-sidebar { right: -450px; }
	#mini-cart-sidebar.active { right: 0 !important; }
	.mini-cart-item { display:flex;gap:15px;padding:15px 0;border-bottom:1px solid #f0f0f0;position:relative; }
	.mini-cart-item:last-child { border-bottom:none; }
	.mini-cart-img { width:80px;height:80px;flex-shrink:0;border-radius:8px;overflow:hidden;display:block; }
	.mini-cart-img img { width:100%;height:100%;object-fit:cover; }
	.mini-cart-details { flex:1; }
	.mini-cart-details h4 { margin:0 0 5px 0;font-size:14px;font-weight:600; }
	.mini-cart-details h4 a { color:#1a1a1a;text-decoration:none; }
	.mini-cart-details h4 a:hover { color:#D4AF37; }
	.mini-cart-variant { font-size:12px;color:#666;margin-bottom:8px; }
	.mini-cart-qty-price { display:flex;justify-content:space-between;align-items:center;margin-top:8px; }
	.mini-cart-qty { display:flex;align-items:center;gap:8px; }
	.qty-btn-mini { width:28px;height:28px;border:1px solid #ddd;background:#fff;cursor:pointer;border-radius:4px;display:flex;align-items:center;justify-content:center;font-size:16px; }
	.qty-btn-mini:hover { background:#D4AF37;color:#fff;border-color:#D4AF37; }
	.qty-value { min-width:30px;text-align:center;font-weight:600; }
	.mini-cart-price { font-size:16px;font-weight:600;color:#D4AF37; }
	.mini-cart-remove { position:absolute;top:15px;right:0;background:none;border:none;color:#dc3545;cursor:pointer;font-size:18px;padding:5px; }
	.mini-cart-remove:hover { color:#c82333; }
	.empty-message { text-align:center;padding:40px 20px;color:#666; }
	@media (max-width: 576px) {
		#mini-cart-sidebar { width:100vw;right:-100vw; }
	}
	</style>

	<!-- CSRF token cho các widget JS (ví dụ: Chatbot AI) -->
	<meta name="csrf-token" content="{{ csrf_token() }}">
	@if(Auth::check())
		<meta name="user-id" content="{{ Auth::id() }}">
	@endif

	@stack('scripts')
</body>
</html>