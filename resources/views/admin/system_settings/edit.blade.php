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
        <label>Banners hiển thị trên slider trang chủ</label>
        <input type="text"
               class="form-control mb-2"
               id="home_banner_ids"
               name="home_banner_ids"
               value="{{ old('home_banner_ids', $data['home_banner_ids']) }}"
               placeholder="Ví dụ: 1,2,3">
        <small class="text-muted d-block mb-2">Chọn bên dưới, ô này sẽ tự cập nhật theo ID đã chọn.</small>
        <div class="border rounded p-2" style="max-height: 220px; overflow:auto;">
            @foreach($lists['banners'] as $banner)
                @php
                    $selectedIds = collect(preg_split('/[\s,]+/', (string)old('home_banner_ids', $data['home_banner_ids'])))->filter()->map('intval')->all();
                @endphp
                <div class="custom-control custom-checkbox">
                    <input type="checkbox"
                           class="custom-control-input js-sync-ids-banner"
                           id="banner_{{ $banner->id }}"
                           value="{{ $banner->id }}"
                           {{ in_array($banner->id, $selectedIds, true) ? 'checked' : '' }}>
                    <label class="custom-control-label" for="banner_{{ $banner->id }}">
                        #{{ $banner->id }} - {{ $banner->title ?? 'Không tiêu đề' }}
                        <span class="badge badge-{{ $banner->status === 'active' ? 'success' : 'secondary' }}">
                            {{ $banner->status }}
                        </span>
                    </label>
                </div>
            @endforeach
        </div>
      </div>

      <div class="form-group">
        <label>Danh mục nổi bật trên trang chủ</label>
        <input type="text"
               class="form-control mb-2"
               id="home_category_ids"
               name="home_category_ids"
               value="{{ old('home_category_ids', $data['home_category_ids']) }}"
               placeholder="Ví dụ: 5,6,7,8">
        <small class="text-muted d-block mb-2">Chọn danh mục bên dưới, ô sẽ tự cập nhật theo ID.</small>
        <div class="border rounded p-2" style="max-height: 200px; overflow:auto;">
            @php
                $selectedCatIds = collect(preg_split('/[\s,]+/', (string)old('home_category_ids', $data['home_category_ids'])))->filter()->map('intval')->all();
            @endphp
            @foreach($lists['categories'] as $cat)
                <div class="custom-control custom-checkbox">
                    <input type="checkbox"
                           class="custom-control-input js-sync-ids-category"
                           id="cat_{{ $cat->id }}"
                           value="{{ $cat->id }}"
                           {{ in_array($cat->id, $selectedCatIds, true) ? 'checked' : '' }}>
                    <label class="custom-control-label" for="cat_{{ $cat->id }}">
                        #{{ $cat->id }} - {{ $cat->name }}
                    </label>
                </div>
            @endforeach
        </div>
      </div>

      <div class="form-group">
        <label>Sản phẩm nổi bật trên trang chủ</label>
        <input type="text"
               class="form-control mb-2"
               id="home_product_ids"
               name="home_product_ids"
               value="{{ old('home_product_ids', $data['home_product_ids']) }}"
               placeholder="Ví dụ: 10,12,15">
        <small class="text-muted d-block mb-2">Chọn sản phẩm bên dưới (tối đa 50 sản phẩm mới nhất), ô sẽ tự cập nhật theo ID.</small>
        <div class="border rounded p-2" style="max-height: 260px; overflow:auto;">
            @php
                $selectedProdIds = collect(preg_split('/[\s,]+/', (string)old('home_product_ids', $data['home_product_ids'])))->filter()->map('intval')->all();
            @endphp
            @foreach($lists['products'] as $product)
                <div class="custom-control custom-checkbox">
                    <input type="checkbox"
                           class="custom-control-input js-sync-ids-product"
                           id="prod_{{ $product->id }}"
                           value="{{ $product->id }}"
                           {{ in_array($product->id, $selectedProdIds, true) ? 'checked' : '' }}>
                    <label class="custom-control-label" for="prod_{{ $product->id }}">
                        #{{ $product->id }} - {{ $product->name }}
                        @if(!is_null($product->price))
                            <span class="text-muted">({{ number_format($product->price, 0, ',', '.') }}₫)</span>
                        @endif
                    </label>
                </div>
            @endforeach
        </div>
      </div>
          <div class="form-group">
          <label>Bài viết nổi bật trên trang chủ</label>
          <input type="text"
               class="form-control mb-2"
               id="home_post_ids"
               name="home_post_ids"
               value="{{ old('home_post_ids', $data['home_post_ids']) }}"
               placeholder="Ví dụ: 2,5,7">
          <small class="text-muted d-block mb-2">Chọn bài viết bên dưới (tối đa 50 bài mới nhất), ô sẽ tự cập nhật theo ID.</small>
          <div class="border rounded p-2" style="max-height: 260px; overflow:auto;">
            @php
              $selectedPostIds = collect(preg_split('/[\s,]+/', (string)old('home_post_ids', $data['home_post_ids'])))->filter()->map('intval')->all();
            @endphp
            @foreach($lists['posts'] as $post)
              <div class="custom-control custom-checkbox">
                <input type="checkbox"
                     class="custom-control-input js-sync-ids-post"
                     id="post_{{ $post->id }}"
                     value="{{ $post->id }}"
                     {{ in_array($post->id, $selectedPostIds, true) ? 'checked' : '' }}>
                <label class="custom-control-label" for="post_{{ $post->id }}">
                  #{{ $post->id }} - {{ $post->title ?? 'Không tiêu đề' }}
                  <span class="badge badge-{{ $post->status === 'active' ? 'success' : 'secondary' }}">
                    {{ $post->status }}
                  </span>
                </label>
              </div>
            @endforeach
          </div>
          </div>
      <div class="form-group">
        <button type="submit" class="btn btn-success">Lưu</button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    function sync(containerSelector, inputSelector) {
        var checkboxes = document.querySelectorAll(containerSelector);
        var input = document.getElementById(inputSelector);
        if (!checkboxes.length || !input) return;

        function update() {
            var ids = [];
            checkboxes.forEach(function (cb) {
                if (cb.checked) {
                    ids.push(cb.value);
                }
            });
            input.value = ids.join(',');
        }

        checkboxes.forEach(function (cb) {
            cb.addEventListener('change', update);
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        sync('.js-sync-ids-banner', 'home_banner_ids');
        sync('.js-sync-ids-category', 'home_category_ids');
        sync('.js-sync-ids-product', 'home_product_ids');
      sync('.js-sync-ids-post', 'home_post_ids');
    });
})();
</script>
@endpush


