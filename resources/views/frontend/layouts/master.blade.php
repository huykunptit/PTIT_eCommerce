<!DOCTYPE html>
<html lang="zxx">
<head>
	@include('frontend.layouts.head')	
</head>
<body class="js">

	<!-- Global Loading Overlay -->
	<div id="global-loader" style="position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;background:rgba(255,255,255,0.85);">
		<img src="{{ asset('images/loading.gif') }}" alt="Loading" style="width:100%;height:auto;image-rendering:-webkit-optimize-contrast;"/>
	</div>
	<script>
		window.addEventListener('load', function () {
			var loader = document.getElementById('global-loader');
			if (loader) loader.style.display = 'none';
		});
	</script>
	
	@include('frontend.layouts.notification')
	<!-- Header -->
	@include('frontend.layouts.header')
	<!--/ End Header -->
	@yield('main-content')
	
	@include('frontend.layouts.footer')

</body>
</html>