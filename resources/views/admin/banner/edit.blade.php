@extends('backend.layouts.master')
@section('title','PTIT  || Banner Edit')
@section('main-content')

<div class="card">
    <h5 class="card-header">Edit Banner</h5>
    <div class="card-body">
      <form method="post" action="{{route('admin.banner.update',$banner->id)}}" enctype="multipart/form-data">
        @csrf 
        @method('PATCH')
        <div class="form-group">
          <label for="inputTitle" class="col-form-label">Title <span class="text-danger">*</span></label>
        <input id="inputTitle" type="text" name="title" placeholder="Enter title"  value="{{$banner->title}}" class="form-control">
        @error('title')
        <span class="text-danger">{{$message}}</span>
        @enderror
        </div>

        <div class="form-group">
          <label for="inputDesc" class="col-form-label">Description</label>
          <textarea class="form-control" id="description" name="description">{{$banner->description}}</textarea>
          @error('description')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>

        <div class="form-group">
        <label for="image" class="col-form-label">Photo</label>
        <input id="image" class="form-control" type="file" name="image" accept="image/*" data-preview-target="#bannerImagePreview">
        <div class="mt-2" data-preview-wrapper style="{{ $banner->photo ? '' : 'display:none;' }}">
          <img id="bannerImagePreview" src="{{$banner->photo}}" alt="current" style="max-height:140px;border:1px solid #ddd;border-radius:6px;padding:4px;{{ $banner->photo ? '' : 'display:none;' }}" />
        </div>
          @error('image')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>

        <div class="form-group">
          <label for="image_url" class="col-form-label">Hoặc dán URL ảnh</label>
          <input id="image_url" class="form-control" type="url" name="image_url" value="{{old('image_url')}}" placeholder="https://...">
          @error('image_url')
          <span class="text-danger">{{$message}}</span>
          @enderror
          <small class="text-muted">Chọn một trong hai: tải file hoặc nhập URL.</small>
        </div>
        
        <div class="form-group">
          <label for="status" class="col-form-label">Status <span class="text-danger">*</span></label>
          <select name="status" class="form-control">
            <option value="active" {{(($banner->status=='active') ? 'selected' : '')}}>Active</option>
            <option value="inactive" {{(($banner->status=='inactive') ? 'selected' : '')}}>Inactive</option>
          </select>
          @error('status')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>
        <div class="form-group mb-3">
           <button class="btn btn-success" type="submit">Update</button>
        </div>
      </form>
    </div>
</div>

@endsection

@push('styles')
<link rel="stylesheet" href="{{asset('backend/summernote/summernote.min.css')}}">
@endpush
@push('scripts')
<script src="{{asset('backend/summernote/summernote.min.js')}}"></script>
<script>
    $(document).ready(function() {
      $('#description').summernote({
        placeholder: "Write short description.....",
        tabsize: 2,
        height: 150
      });
    });

</script>
@endpush