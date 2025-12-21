@extends('backend.layouts.master')
@section('title','Chỉnh sửa Tag')
@section('main-content')
<div class="container-fluid">
    @include('backend.layouts.notification')
    
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Chỉnh sửa Tag</h1>
        <a href="{{ route('admin.tags.index') }}" class="btn btn-secondary">
            <i class="fa fa-arrow-left"></i> Quay lại
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('admin.tags.update', $tag->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="form-group">
                    <label for="name">Tên Tag <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                           id="name" name="name" value="{{ old('name', $tag->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="description">Mô tả</label>
                    <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $tag->description) }}</textarea>
                </div>

                <div class="form-group">
                    <label for="color">Màu sắc</label>
                    <div class="input-group">
                        <input type="color" class="form-control" id="color" name="color" 
                               value="{{ old('color', $tag->color) }}" style="width: 80px;">
                        <input type="text" class="form-control" id="color-text" 
                               value="{{ old('color', $tag->color) }}" readonly>
                    </div>
                    <small class="form-text text-muted">Chọn màu sắc cho tag</small>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> Cập nhật
                    </button>
                    <a href="{{ route('admin.tags.index') }}" class="btn btn-secondary">Hủy</a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('color').addEventListener('input', function(e) {
    document.getElementById('color-text').value = e.target.value;
});
</script>
@endpush
@endsection

