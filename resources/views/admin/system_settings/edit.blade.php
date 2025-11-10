@extends('backend.layouts.master')

@section('main-content')
<div class="card">
  <h5 class="card-header">Cấu hình Trang chủ</h5>
  <div class="card-body">
    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <form method="post" action="{{ route('admin.system_settings.update') }}">
      @csrf
      <div class="form-group">
        <label>Tiêu đề</label>
        <input type="text" class="form-control" name="home_title" value="{{ old('home_title', $data['home_title']) }}">
      </div>
      <div class="form-group">
        <label>Phụ đề</label>
        <input type="text" class="form-control" name="home_subtitle" value="{{ old('home_subtitle', $data['home_subtitle']) }}">
      </div>
      <div class="form-group">
        <label>Hero image URL</label>
        <input type="text" class="form-control" name="home_hero_image" value="{{ old('home_hero_image', $data['home_hero_image']) }}">
        <small class="text-muted">URL hoặc đường dẫn nội bộ. Hỗ trợ ảnh ngoài, sẽ gắn referrerpolicy="no-referrer" khi hiển thị.</small>
      </div>
      <div class="form-group">
        <label>Banner image URL</label>
        <input type="text" class="form-control" name="home_banner_image" value="{{ old('home_banner_image', $data['home_banner_image']) }}">
      </div>
      <hr>
      <div class="form-group">
        <label>ID banners hiển thị (danh sách id, dạng: 1,2,3)</label>
        <input type="text" class="form-control" name="home_banner_ids" value="{{ old('home_banner_ids', $data['home_banner_ids']) }}">
      </div>
      <div class="form-group">
        <label>ID categories nổi bật (danh sách id, dạng: 5,6,7,8)</label>
        <input type="text" class="form-control" name="home_category_ids" value="{{ old('home_category_ids', $data['home_category_ids']) }}">
      </div>
      <div class="form-group">
        <label>ID sản phẩm nổi bật (danh sách id, dạng: 10,12,15)</label>
        <input type="text" class="form-control" name="home_product_ids" value="{{ old('home_product_ids', $data['home_product_ids']) }}">
      </div>
      <div class="form-group">
        <button type="submit" class="btn btn-success">Lưu</button>
      </div>
    </form>
  </div>
</div>
@endsection


