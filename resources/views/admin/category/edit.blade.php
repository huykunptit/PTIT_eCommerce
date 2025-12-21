@extends('backend.layouts.master')

@section('main-content')

<div class="card">
    <h5 class="card-header">Cập nhật danh mục</h5>
    <div class="card-body">
      <form method="post" action="{{route('admin.categories.update',$category->id)}}" enctype="multipart/form-data">
        @csrf 
        @method('PUT')

        <div class="form-group floating-group">
          <input id="name" type="text" name="name" placeholder=" "  value="{{$category->name}}" class="form-control modern-input">
          <label for="name" class="col-form-label">Tên danh mục <span class="text-danger">*</span></label>
          @error('name')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>

        <div class="form-group floating-group">
          <input id="image" type="file" name="image" class="form-control modern-input" data-preview-target="#categoryImagePreview">
          <label for="image" class="col-form-label">Ảnh danh mục</label>
          <div class="mt-2" data-preview-wrapper style="{{ $category->image ? '' : 'display:none;' }}">
            <img id="categoryImagePreview" src="{{$category->image}}" alt="{{$category->name}}" style="max-height:120px;border:1px solid #ddd;border-radius:6px;padding:4px;{{ $category->image ? '' : 'display:none;' }}">
          </div>
          @error('image')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>

        <div class="form-group floating-group">
          <textarea class="form-control modern-input" id="description" name="description" placeholder=" ">{{$category->description}}</textarea>
          <label for="description" class="col-form-label">Mô tả</label>
          @error('description')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>

        <div class="form-group floating-group">
          @php $parents = \App\Models\Category::select('id','name')->get(); @endphp
          <select id="parent_category_id" name="parent_category_id" class="form-control modern-input">
              <option value="">-- Chọn danh mục cha --</option>
              @foreach($parents as $p)
                <option value="{{$p->id}}" {{ $category->parent_category_id==$p->id ? 'selected' : '' }}>{{$p->name}}</option>
              @endforeach
          </select>
          <label for="parent_category_id" class="col-form-label">Danh mục cha</label>
          @error('parent_category_id')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>

        <div class="form-group mb-3">
           <button class="btn btn-success" type="submit">Lưu</button>
        </div>
      </form>
    </div>
</div>

@endsection

@push('styles')
@endpush
@push('scripts')
@endpush