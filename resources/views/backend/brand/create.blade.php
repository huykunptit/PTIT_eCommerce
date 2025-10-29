@extends('backend.layouts.master')
@section('title','PTIT  || Brand Create')
@section('main-content')

<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <h5 class="card-header">Thêm thương hiệu</h5>
            <div class="card-body">
                <form method="post" action="{{route('brand.store')}}">
                    {{csrf_field()}}

                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="title" name="title" placeholder=" " value="{{old('title')}}">
                        <label for="title">Tên thương hiệu <span class="text-danger">*</span></label>
                        @error('title')
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>

                    <div class="form-floating mb-3">
                        <select class="form-select" id="status" name="status" aria-label=" ">
                            <option value="active" {{old('status')=='active'?'selected':''}}>Hoạt động</option>
                            <option value="inactive" {{old('status')=='inactive'?'selected':''}}>Không hoạt động</option>
                        </select>
                        <label for="status">Trạng thái <span class="text-danger">*</span></label>
                        @error('status')
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <button type="reset" class="btn btn-warning">Đặt lại</button>
                        <button class="btn btn-success" type="submit">Thêm thương hiệu</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<link rel="stylesheet" href="{{asset('backend/summernote/summernote.min.css')}}">
@endpush
@push('scripts')
<script src="/vendor/laravel-filemanager/js/stand-alone-button.js"></script>
<script src="{{asset('backend/summernote/summernote.min.js')}}"></script>
<script>
    $('#lfm').filemanager('image');

    $(document).ready(function() {
    $('#description').summernote({
      placeholder: "Write short description.....",
        tabsize: 2,
        height: 150
    });
    });
</script>
@endpush