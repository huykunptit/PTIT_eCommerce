
	<!-- Start Footer Area -->
	<footer class="footer">
		<!-- Footer Top -->
		<div class="footer-top section">
			<div class="container">
				<div class="row">
					<div class="col-lg-5 col-md-6 col-12">
						<!-- Single Widget -->
						<div class="single-footer about">
							<div class="logo">
                                <a href="#"><img src="{{asset('backend/img/logo2.png')}}" alt="#"></a>
							</div>
						
					
						</div>
						<!-- End Single Widget -->
					</div>
					<div class="col-lg-2 col-md-6 col-12">
						<!-- Single Widget -->
						<div class="single-footer links">
							<h4>Information</h4>
							<ul>
                                <li><a href="#">About Us</a></li>
								<li><a href="#">Faq</a></li>
								<li><a href="#">Terms & Conditions</a></li>
                                <li><a href="#">Contact Us</a></li>
								<li><a href="#">Help</a></li>
							</ul>
						</div>
						<!-- End Single Widget -->
					</div>
					<div class="col-lg-2 col-md-6 col-12">
						<!-- Single Widget -->
						<div class="single-footer links">
							<h4>Customer Service</h4>
							<ul>
								<li><a href="#">Payment Methods</a></li>
								<li><a href="#">Money-back</a></li>
								<li><a href="#">Returns</a></li>
								<li><a href="#">Shipping</a></li>
								<li><a href="#">Privacy Policy</a></li>
							</ul>
						</div>
						<!-- End Single Widget -->
					</div>
					<div class="col-lg-3 col-md-6 col-12">
						<!-- Single Widget -->
						<div class="single-footer social">
							<h4>Get In Tuch</h4>
							<!-- Single Widget -->
							<div class="contact">
								<ul>
								
								</ul>
							</div>
							<!-- End Single Widget -->
							<div class="sharethis-inline-follow-buttons"></div>
						</div>
						<!-- End Single Widget -->
					</div>
				</div>
			</div>
		</div>
		<!-- End Footer Top -->
		<div class="copyright">
			<div class="container">
				<div class="inner">
					<div class="row">
						<div class="col-lg-6 col-12">
							<div class="left">
								<p>Copyright © {{date('Y')}} <a href="https://github.com/Prajwal100" target="_blank">Prajwal Rai</a>  -  All Rights Reserved.</p>
							</div>
						</div>
						<div class="col-lg-6 col-12">
							<div class="right">
								<img src="{{asset('backend/img/payments.png')}}" alt="#">
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</footer>
	<!-- /End Footer Area -->
 
	<!-- Jquery -->
    <script src="{{asset('frontend/js/jquery.min.js')}}"></script>
    <script src="{{asset('frontend/js/jquery-migrate-3.0.0.js')}}"></script>
	<script src="{{asset('frontend/js/jquery-ui.min.js')}}"></script>
	<!-- Popper JS -->
	<script src="{{asset('frontend/js/popper.min.js')}}"></script>
	<!-- Bootstrap JS -->
	<script src="{{asset('frontend/js/bootstrap.min.js')}}"></script>
	<!-- Color JS -->
	<script src="{{asset('frontend/js/colors.js')}}"></script>
	<!-- Slicknav JS -->
	<script src="{{asset('frontend/js/slicknav.min.js')}}"></script>
	<!-- Owl Carousel JS -->
	<script src="{{asset('frontend/js/owl-carousel.js')}}"></script>
	<!-- Magnific Popup JS -->
	<script src="{{asset('frontend/js/magnific-popup.js')}}"></script>
	<!-- Waypoints JS -->
	<script src="{{asset('frontend/js/waypoints.min.js')}}"></script>
	<!-- Countdown JS -->
	<script src="{{asset('frontend/js/finalcountdown.min.js')}}"></script>
	<!-- Nice Select JS -->
	<script src="{{asset('frontend/js/nicesellect.js')}}"></script>
	<!-- Flex Slider JS -->
	<script src="{{asset('frontend/js/flex-slider.js')}}"></script>
	<!-- ScrollUp JS -->
	<script src="{{asset('frontend/js/scrollup.js')}}"></script>
	<!-- Onepage Nav JS -->
	<script src="{{asset('frontend/js/onepage-nav.min.js')}}"></script>
	{{-- Isotope --}}
	<script src="{{asset('frontend/js/isotope/isotope.pkgd.min.js')}}"></script>
	<!-- Easing JS -->
	<script src="{{asset('frontend/js/easing.js')}}"></script>

	<!-- Active JS -->
	<script src="{{asset('frontend/js/active.js')}}"></script>

	
	@stack('scripts')
	<script>
		setTimeout(function(){
		  $('.alert').slideUp();
		},5000);
		$(function() {
		// ------------------------------------------------------- //
		// Multi Level dropdowns
		// ------------------------------------------------------ //
			$("ul.dropdown-menu [data-toggle='dropdown']").on("click", function(event) {
				event.preventDefault();
				event.stopPropagation();

				$(this).siblings().toggleClass("show");


				if (!$(this).next().hasClass('show')) {
				$(this).parents('.dropdown-menu').first().find('.show').removeClass("show");
				}
				$(this).parents('li.nav-item.dropdown.show').on('hidden.bs.dropdown', function(e) {
				$('.dropdown-submenu .show').removeClass("show");
				});

			});
		});
		
		// Sticky Header on Scroll
		$(window).on('scroll', function() {
			if ($(window).scrollTop() > 100) {
				$('.header').addClass('sticky');
			} else {
				$('.header').removeClass('sticky');
			}
		});
		
		// Mini Cart Sidebar
		var miniCartOpen = false;
		function openMiniCart() {
			if (!miniCartOpen) {
				$('#mini-cart-sidebar').addClass('active');
				$('#mini-cart-overlay').show();
				miniCartOpen = true;
				loadMiniCartData();
			}
		}
		
		function closeMiniCart() {
			$('#mini-cart-sidebar').removeClass('active');
			$('#mini-cart-overlay').hide();
			miniCartOpen = false;
		}
		
		// Bind events (only once)
		$(document).ready(function() {
			$('#mini-cart-overlay').on('click', closeMiniCart);
			$(document).on('click', '.mini-cart-close', closeMiniCart);
		});
		
		function loadMiniCartData() {
			$.ajax({
				url: '{{ route("cart.data") }}',
				method: 'GET',
				timeout: 5000,
				cache: false
			}).done(function(data) {
				const sidebar = $('#mini-cart-sidebar');
				const itemsList = sidebar.find('.mini-cart-items');
				const totalEl = sidebar.find('.mini-cart-total');
				
				itemsList.empty();
				
				// Update cart count badge
				$('.cart-count-badge').text('(' + data.count + ')');
				
				if (data.items.length > 0) {
					data.items.forEach(function(item) {
						const variantText = item.variant ? 
							`<div class="mini-cart-variant">${JSON.parse(item.variant).size || ''} ${JSON.parse(item.variant).option || ''}</div>` : '';
						const li = $(`
							<li class="mini-cart-item">
								<a class="mini-cart-img" href="/product/${item.product_id}">
									<img src="${item.image}" referrerpolicy="no-referrer" alt="${item.product_name}">
								</a>
								<div class="mini-cart-details">
									<h4><a href="/product/${item.product_id}">${item.product_name}</a></h4>
									${variantText}
									<div class="mini-cart-qty-price">
										<div class="mini-cart-qty">
											<button type="button" class="qty-btn-mini qty-minus-mini" data-key="${item.key}">-</button>
											<span class="qty-value">${item.quantity}</span>
											<button type="button" class="qty-btn-mini qty-plus-mini" data-key="${item.key}">+</button>
										</div>
										<span class="mini-cart-price">${new Intl.NumberFormat('vi-VN').format(item.subtotal)}₫</span>
									</div>
								</div>
								<button type="button" class="mini-cart-remove" data-key="${item.key}">
									<i class="ti-close"></i>
								</button>
							</li>
						`);
						itemsList.append(li);
					});
					totalEl.text(data.total_formatted);
				} else {
					itemsList.append('<li class="empty-message">Giỏ hàng trống</li>');
					totalEl.text('0₫');
				}
				
				// Bind events for quantity controls
				$('.qty-minus-mini, .qty-plus-mini').off('click').on('click', function() {
					const key = $(this).data('key');
					const item = $(this).closest('.mini-cart-item');
					const qtyEl = item.find('.qty-value');
					let qty = parseInt(qtyEl.text());
					
					if ($(this).hasClass('qty-plus-mini')) {
						qty++;
					} else if (qty > 1) {
						qty--;
					}
					
					updateCartItemMini(key, qty, item);
				});
				
				$('.mini-cart-remove').off('click').on('click', function() {
					const key = $(this).data('key');
					removeCartItemMini(key, $(this).closest('.mini-cart-item'));
				});
			}).fail(function(xhr, status, error) {
				console.error('Error loading cart data:', error);
				const itemsList = $('#mini-cart-sidebar .mini-cart-items');
				itemsList.html('<li class="empty-message">Lỗi tải dữ liệu giỏ hàng</li>');
			});
		}
		
		function updateCartItemMini(key, quantity, itemEl) {
			$.ajax({
				url: `{{ url('cart/update') }}/${key}`,
				method: 'PUT',
				headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
				data: { quantity: quantity },
				success: function(response) {
					if (response.success) {
						itemEl.find('.qty-value').text(quantity);
						loadMiniCartData();
						loadCartData();
					}
				}
			});
		}
		
		function removeCartItemMini(key, itemEl) {
			$.ajax({
				url: `{{ url('cart/remove') }}/${key}`,
				method: 'DELETE',
				headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
				success: function(response) {
					if (response.success) {
						itemEl.fadeOut(300, function() {
							$(this).remove();
							loadMiniCartData();
							loadCartData();
						});
					}
				}
			});
		}
		
		// Load Cart and Wishlist Data
		function loadCartData() {
			$.ajax({
				url: '{{ route("cart.data") }}',
				method: 'GET',
				timeout: 3000,
				cache: false
			}).done(function(data) {
				$('.cart-count').text(data.count);
				$('.cart-items-count').text(data.count + ' Sản phẩm');
				$('.cart-total').text(data.total_formatted);
				
				const list = $('.cart-items-list');
				list.empty();
				
				if (data.items.length > 0) {
					data.items.slice(0, 3).forEach(function(item) {
						const li = $('<li>');
						li.html(`
							<a class="cart-img" href="/product/${item.product_id}">
								<img src="${item.image}" referrerpolicy="no-referrer" alt="${item.product_name}">
							</a>
							<h4><a href="/product/${item.product_id}">${item.product_name}</a></h4>
							<p class="quantity">${item.quantity} x - <span class="amount">${new Intl.NumberFormat('vi-VN').format(item.price)}₫</span></p>
						`);
						list.append(li);
					});
					if (data.items.length > 3) {
						list.append('<li class="text-center"><small>... và ' + (data.items.length - 3) + ' sản phẩm khác</small></li>');
					}
				} else {
					list.append('<li class="empty-message">Giỏ hàng trống</li>');
				}
			}).fail(function() {
				// Silent fail for header cart
			});
		}
		
		function loadWishlistData() {
			$.ajax({
				url: '{{ route("wishlist.data") }}',
				method: 'GET',
				timeout: 3000,
				cache: false
			}).done(function(data) {
				$('.wishlist-count').text(data.count);
				$('.wishlist-items-count').text(data.count + ' Sản phẩm');
				
				const list = $('.wishlist-items-list');
				list.empty();
				
				if (data.items.length > 0) {
					data.items.slice(0, 3).forEach(function(item) {
						const li = $('<li>');
						li.html(`
							<a class="cart-img" href="/product/${item.product_id}">
								<img src="${item.image}" referrerpolicy="no-referrer" alt="${item.product_name}">
							</a>
							<h4><a href="/product/${item.product_id}">${item.product_name}</a></h4>
							<p class="quantity"><span class="amount">${new Intl.NumberFormat('vi-VN').format(item.price)}₫</span></p>
						`);
						list.append(li);
					});
					if (data.items.length > 3) {
						list.append('<li class="text-center"><small>... và ' + (data.items.length - 3) + ' sản phẩm khác</small></li>');
					}
				} else {
					list.append('<li class="empty-message">Danh sách yêu thích trống</li>');
				}
			}).fail(function() {
				// Silent fail for header wishlist
			});
		}
		
		// Load on page load
		loadCartData();
		loadWishlistData();
		
		// Global function to open mini cart (called from add to cart)
		window.openMiniCart = openMiniCart;
		
		// Search Preview
		var searchTimeout;
		$('#search-input').on('input', function() {
			const query = $(this).val().trim();
			const resultsDiv = $('#search-results');
			const contentDiv = $('.search-results-content');
			
			clearTimeout(searchTimeout);
			
			if (query.length < 2) {
				resultsDiv.hide();
				return;
			}
			
			searchTimeout = setTimeout(function() {
				$.get('{{ route("search") }}', { q: query }, function(products) {
					if (products.length > 0) {
						let html = '<div style="padding:10px;"><div style="font-weight:600;margin-bottom:10px;color:#1a1a1a;">Kết quả tìm kiếm:</div>';
						products.slice(0, 5).forEach(function(product) {
							const photos = (product.image_url || '').split(',');
							const img = photos[0]?.trim() || '';
							const imgSrc = img && (img.startsWith('http://') || img.startsWith('https://')) 
								? img 
								: (img ? '{{ asset("") }}' + img : '{{ asset("backend/img/thumbnail-default.jpg") }}');
							
							html += `
								<a href="/product/${product.id}" style="display:flex;gap:10px;padding:10px;border-bottom:1px solid #f0f0f0;text-decoration:none;color:#1a1a1a;transition:background 0.2s;" onmouseover="this.style.background='#f8f9fa'" onmouseout="this.style.background='#fff'">
									<img src="${imgSrc}" referrerpolicy="no-referrer" style="width:60px;height:60px;object-fit:cover;border-radius:4px;" alt="${product.name}">
									<div style="flex:1;">
										<div style="font-weight:600;margin-bottom:5px;">${product.name}</div>
										<div style="color:#D4AF37;font-weight:600;">${new Intl.NumberFormat('vi-VN').format(product.price || 0)}₫</div>
									</div>
								</a>
							`;
						});
						html += '</div>';
						contentDiv.html(html);
						
						if (products.length > 5) {
							$('#search-view-all').attr('href', '{{ route("home") }}?search=' + encodeURIComponent(query)).show();
						} else {
							$('#search-view-all').hide();
						}
						
						resultsDiv.show();
					} else {
						contentDiv.html('<div style="padding:20px;text-align:center;color:#666;">Không tìm thấy sản phẩm nào</div>');
						$('#search-view-all').hide();
						resultsDiv.show();
					}
				}).fail(function() {
					resultsDiv.hide();
				});
			}, 300);
		});
		
		// Hide search results when clicking outside
		$(document).on('click', function(e) {
			if (!$(e.target).closest('.search-bar').length) {
				$('#search-results').hide();
			}
		});
		
		$('#search-form').on('submit', function(e) {
			const query = $('#search-input').val().trim();
			if (!query) {
				e.preventDefault();
			}
		});
		
		// User dropdown toggle
		$('.user-avatar-toggle').on('click', function(e) {
			e.preventDefault();
			e.stopPropagation();
			$('.user-menu').toggle();
		});
		
		// Orders dropdown toggle
		$('.orders-dropdown-toggle').on('click', function(e) {
			e.preventDefault();
			e.stopPropagation();
			var dropdown = $(this).next('.orders-dropdown');
			dropdown.toggle();
			
			// Load orders if not loaded
			if (dropdown.is(':visible') && !dropdown.data('loaded')) {
				loadRecentOrders();
				dropdown.data('loaded', true);
			}
		});
		
		// Load recent orders
		function loadRecentOrders() {
			$('.orders-loading').show();
			$('.orders-list').hide();
			$('.orders-empty').hide();
			
			$.ajax({
				url: '{{ route("orders.recent") }}',
				method: 'GET',
				headers: {
					'X-CSRF-TOKEN': '{{ csrf_token() }}'
				},
				success: function(response) {
					$('.orders-loading').hide();
					
					if (response.success && response.orders && response.orders.length > 0) {
						var html = '';
						var maxItems = 4;
						var ordersToShow = response.orders.slice(0, maxItems);
						
						ordersToShow.forEach(function(order) {
							var paymentStatusClass = order.payment_status.includes('Đã') ? 'text-success' : 'text-warning';
							var shippingStatusText = getShippingStatusText(order.shipping_status);
							var shippingStatusClass = getShippingStatusClass(order.shipping_status);
							
							html += '<div class="order-item" style="padding:12px 15px;border-bottom:1px solid #f0f0f0;">';
							html += '<div style="display:flex;justify-content:space-between;align-items:start;margin-bottom:8px;">';
							html += '<div>';
							html += '<a href="/orders/' + order.id + '" style="font-weight:600;color:#333;text-decoration:none;font-size:14px;">Đơn #' + order.id + '</a>';
							html += '<div style="font-size:12px;color:#666;margin-top:4px;">' + order.created_at + '</div>';
							html += '</div>';
							html += '<div style="text-align:right;">';
							html += '<div style="font-weight:600;color:#D4AF37;font-size:14px;">' + formatCurrency(order.total_amount) + '₫</div>';
							html += '</div>';
							html += '</div>';
							
							// Items preview
							if (order.items && order.items.length > 0) {
								html += '<div style="margin-top:8px;">';
								order.items.forEach(function(item) {
									html += '<div style="display:flex;gap:8px;margin-bottom:6px;font-size:12px;">';
									html += '<img src="' + item.image + '" style="width:40px;height:40px;object-fit:cover;border-radius:4px;" referrerpolicy="no-referrer">';
									html += '<div style="flex:1;">';
									html += '<div style="color:#333;font-weight:500;">' + item.product_name + '</div>';
									html += '<div style="color:#666;">SL: ' + item.quantity + '</div>';
									html += '</div>';
									html += '</div>';
								});
								html += '</div>';
							}
							
							// Status badges
							html += '<div style="display:flex;gap:8px;margin-top:8px;flex-wrap:wrap;">';
							html += '<span class="badge ' + paymentStatusClass + '" style="font-size:11px;padding:4px 8px;border-radius:4px;background:#f0f0f0;">' + order.payment_status + '</span>';
							html += '<span class="badge ' + shippingStatusClass + '" style="font-size:11px;padding:4px 8px;border-radius:4px;background:#f0f0f0;">' + shippingStatusText + '</span>';
							html += '</div>';
							html += '</div>';
						});
						
						$('.orders-list').html(html).show();
						
						// Show "Xem thêm" if more than maxItems
						if (response.orders.length > maxItems) {
							$('.orders-list').append('<div style="padding:10px 15px;text-align:center;border-top:1px solid #f0f0f0;"><a href="{{ route("user.orders") }}" style="color:#D4AF37;text-decoration:none;font-size:12px;">Xem thêm ' + (response.orders.length - maxItems) + ' đơn hàng <i class="ti-arrow-right"></i></a></div>');
						}
					} else {
						$('.orders-empty').show();
					}
				},
				error: function() {
					$('.orders-loading').hide();
					$('.orders-empty').show();
				}
			});
		}
		
		function getShippingStatusText(status) {
			var statusMap = {
				'pending_pickup': 'Chờ lấy hàng',
				'in_transit': 'Đang vận chuyển',
				'delivered': 'Đã nhận hàng',
				'cancelled': 'Đã hủy',
				'returned': 'Đã hoàn trả'
			};
			return statusMap[status] || status;
		}
		
		function getShippingStatusClass(status) {
			var classMap = {
				'pending_pickup': 'text-warning',
				'in_transit': 'text-info',
				'delivered': 'text-success',
				'cancelled': 'text-danger',
				'returned': 'text-secondary'
			};
			return classMap[status] || '';
		}
		
		function formatCurrency(amount) {
			return new Intl.NumberFormat('vi-VN').format(amount);
		}
		
		// Close user dropdown when clicking outside
		$(document).on('click', function(e) {
			if (!$(e.target).closest('.user-dropdown').length) {
				$('.user-menu').hide();
				$('.orders-dropdown').hide();
			}
		});
	  </script>