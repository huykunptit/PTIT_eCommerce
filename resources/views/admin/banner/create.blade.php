@extends('backend.layouts.master')

@section('title','PTIT  || Banner Create')

@section('main-content')

<div class="card">
    <h5 class="card-header">Add Banner</h5>
    <div class="card-body">
      <form method="post" action="{{route('admin.banner.store')}}" enctype="multipart/form-data">
        {{csrf_field()}}
        <div class="form-group">
          <label for="inputTitle" class="col-form-label">Title <span class="text-danger">*</span></label>
        <input id="inputTitle" type="text" name="title" placeholder="Enter title"  value="{{old('title')}}" class="form-control">
        @error('title')
        <span class="text-danger">{{$message}}</span>
        @enderror
        </div>

        <div class="form-group">
          <label for="inputDesc" class="col-form-label">Description</label>
          <textarea class="form-control" id="description" name="description">{{old('description')}}</textarea>
          @error('description')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>

        <div class="form-group">
        <label for="image" class="col-form-label">Photo <span class="text-danger">*</span></label>
        <input id="image" class="form-control" type="file" name="image" accept="image/*" data-preview-target="#bannerImagePreview">
          <div class="mt-2" data-preview-wrapper style="display:none;">
            <img id="bannerImagePreview" alt="Xem trước ảnh" style="max-height:140px;border:1px solid #ddd;border-radius:6px;padding:4px;display:none;">
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
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
          </select>
          @error('status')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>
        <div class="form-group mb-3">
          <button type="reset" class="btn btn-warning">Reset</button>
           <button class="btn btn-success" type="submit">Submit</button>
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