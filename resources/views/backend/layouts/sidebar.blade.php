<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="#">
      
      <div class="sidebar-brand-text mx-3">
        <img src="{{ asset('images/logoden.png') }}" alt="Logo" style="display:block; max-height:200px; width:auto;">
      </div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item active">
      <a class="nav-link" href="{{route('admin.dashboard')}}">
        <i class="fa fa-fw fa-tachometer-alt"></i>
        <span>Bảng điều khiển</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Banner & Media
    </div>

    <!-- Nav Item - Pages Collapse Menu -->
    <!-- Nav Item - Charts -->
    <li class="nav-item">
        <a class="nav-link" href="#">
            <i class="fa fa-fw fa-chart-area"></i>
            <span>Quản lý Media</span></a>
    </li>

    <li class="nav-item">
      <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
        <i class="fa fa-image"></i>
        <span>Banner</span>
      </a>
      <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
          <h6 class="collapse-header">Tùy chọn Banner:</h6>
          <a class="collapse-item" href="{{route('admin.banner.index')}}">Danh sách Banner</a>
          <a class="collapse-item" href="{{route('admin.banner.create')}}">Thêm Banner</a>
        </div>
      </div>
    </li>
    <!-- Divider -->
    <hr class="sidebar-divider">
        <!-- Heading -->
        <div class="sidebar-heading">
            Cửa hàng
        </div>

    <!-- Categories -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#categoryCollapse" aria-expanded="true" aria-controls="categoryCollapse">
          <i class="fa fa-sitemap"></i>
          <span>Danh mục sản phẩm</span>
        </a>
        <div id="categoryCollapse" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Quản lý danh mục:</h6>
            <a class="collapse-item" href="{{route('admin.categories.index')}}">Danh sách danh mục</a>
            <a class="collapse-item" href="{{route('admin.categories.create')}}">Thêm danh mục</a>
          </div>
        </div>
    </li>

     {{-- Brands --}}
     <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#brandCollapse" aria-expanded="true" aria-controls="brandCollapse">
          <i class="fa fa-table"></i>
          <span>Thương hiệu</span>
        </a>
        <div id="brandCollapse" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Tùy chọn thương hiệu:</h6>
            <a class="collapse-item" href="{{route('admin.brands.index')}}">Danh sách thương hiệu</a>
            <a class="collapse-item" href="{{route('admin.brands.create')}}">Thêm thương hiệu</a>
          </div>
        </div>
    </li>



    {{-- Products --}}
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#productCollapse" aria-expanded="true" aria-controls="productCollapse">
          <i class="fa fa-cubes"></i>
          <span>Sản phẩm</span>
        </a>
        <div id="productCollapse" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Tùy chọn sản phẩm:</h6>
            <a class="collapse-item" href="{{route('admin.products.index')}}">Danh sách sản phẩm</a>
            <a class="collapse-item" href="{{route('admin.products.create')}}">Thêm sản phẩm</a>
          </div>
        </div>
    </li>

    <!-- Users -->
    <li class="nav-item">
       <a class="nav-link" href="{{route('admin.users.index')}}">
           <i class="fa fa-users"></i>
           <span>Người dùng</span></a>
    </li>

  
    {{-- Shipping --}}
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#shippingCollapse" aria-expanded="true" aria-controls="shippingCollapse">
          <i class="fa fa-truck"></i>
          <span>Vận chuyển</span>
        </a>
        <div id="shippingCollapse" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Tùy chọn vận chuyển:</h6>
            <a class="collapse-item" href="{{ route('admin.orders') }}">Danh sách đơn hàng</a>
            <a class="collapse-item" href="{{ route('admin.orders') }}">Quản lý vận chuyển</a>
          </div>
        </div>
    </li>

    <!--Orders -->
    <li class="nav-item">
        <a class="nav-link" href="{{route('admin.orders')}}">
            <i class="fa fa-hammer fa-chart-area"></i>
            <span>Đơn hàng</span>
        </a>
    </li>

    <!-- Reviews -->
    <li class="nav-item">
        <a class="nav-link" href="{{route('admin.comment.index')}}">
            <i class="fa fa-comments"></i>
            <span>Đánh giá</span></a>
    </li>
    

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
      Bài viết
    </div>

    <!-- Posts -->
    <li class="nav-item">
      <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#postCollapse" aria-expanded="true" aria-controls="postCollapse">
        <i class="fa fa-fw fa-folder"></i>
        <span>Bài viết</span>
      </a>
      <div id="postCollapse" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
          <h6 class="collapse-header">Tùy chọn bài viết:</h6>
          <a class="collapse-item" href="{{route('admin.post.index')}}">Danh sách bài viết</a>
          <a class="collapse-item" href="{{route('admin.post.create')}}">Thêm bài viết</a>
        </div>
      </div>
    </li>

     <!-- Category -->
     <li class="nav-item">
        <a class="nav-link collapsed" href="{{route('admin.categories.index')}}" data-toggle="collapse" data-target="#postCategoryCollapse" aria-expanded="true" aria-controls="postCategoryCollapse">
          <i class="fa fa-sitemap fa-folder"></i>
          <span>Danh mục bài viết</span>
        </a>
        <div id="postCategoryCollapse" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Tùy chọn danh mục bài viết:</h6>
            <a class="collapse-item" href="{{route('admin.categories.index')}}">Danh sách danh mục</a>
            <a class="collapse-item" href="{{route('admin.categories.create')}}">Thêm danh mục</a>
          </div>
        </div>
      </li>

      <!-- Tags -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#tagCollapse" aria-expanded="true" aria-controls="tagCollapse">
            <i class="fa fa-tags fa-folder"></i>
            <span>Thẻ (tags)</span>
        </a>
        <div id="tagCollapse" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Tùy chọn thẻ:</h6>
            <a class="collapse-item" href="#">Danh sách thẻ</a>
            <a class="collapse-item" href="#">Thêm thẻ</a>
            </div>
        </div>
    </li>

      <!-- Comments -->
      <li class="nav-item">
        <a class="nav-link" href="{{route('admin.comment.index')}}">
            <i class="fa fa-comments fa-chart-area"></i>
            <span>Bình luận</span>
        </a>
      </li>


    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">
     <!-- Heading -->
    <div class="sidebar-heading">
        Cài đặt chung
    </div>
    <li class="nav-item">
      <a class="nav-link" href="{{route('admin.coupon.index')}}">
          <i class="fa fa-table"></i>
          <span>Mã giảm giá</span></a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="{{ route('admin.system_settings.edit') }}">
          <i class="fa fa-sliders-h"></i>
          <span>Cấu hình hệ thống</span></a>
    </li>
    
     <!-- General settings -->
     <li class="nav-item">
        <a class="nav-link" href="#">
            <i class="fa fa-cog"></i>
            <span>Cài đặt</span></a>
    </li>

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
      <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>