<!DOCTYPE html>
<html lang="en">

@include('backend.layouts.head')

<body id="page-top">

  <!-- Global Loading Overlay -->
  <div id="global-loader" style="position:fixed;inset:0;z-index:1055;display:flex;align-items:center;justify-content:center;background:rgba(255,255,255,0.85);">
    <img src="{{ asset('images/loading.gif') }}" alt="Loading" style="width:100%;height:auto;image-rendering:-webkit-optimize-contrast;"/>
  </div>
  <script>
    window.addEventListener('load', function () {
      var loader = document.getElementById('global-loader');
      if (loader) loader.style.display = 'none';
    });
  </script>

  <!-- Page Wrapper -->
  <div id="wrapper">

    <!-- Sidebar -->
    @include('backend.layouts.sidebar')
    <!-- End of Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

      <!-- Main Content -->
      <div id="content">

        <!-- Topbar -->
        @include('backend.layouts.header')
        <!-- End of Topbar -->

        <!-- Begin Page Content -->
        @yield('main-content')
        <!-- /.container-fluid -->

      </div>
      <!-- End of Main Content -->
      @include('backend.layouts.footer')

</body>

</html>
