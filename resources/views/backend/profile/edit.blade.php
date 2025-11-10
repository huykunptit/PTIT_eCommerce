@extends('backend.layouts.master')

@section('main-content')
<div class="card">
    <h5 class="card-header gradient-header" style="color: white;">Chỉnh sửa thông tin cá nhân</h5>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        
        <form method="post" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label for="name" class="col-form-label">Họ và tên <span class="text-danger">*</span></label>
                <input id="name" type="text" name="name" placeholder="Nhập họ và tên" value="{{ old('name', $user->name) }}" class="form-control" required>
                @error('name')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="email" class="col-form-label">Email <span class="text-danger">*</span></label>
                <input id="email" type="email" name="email" placeholder="Nhập email" value="{{ old('email', $user->email) }}" class="form-control" required>
                @error('email')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="phone_number" class="col-form-label">Số điện thoại</label>
                <input id="phone_number" type="text" name="phone_number" placeholder="Nhập số điện thoại" value="{{ old('phone_number', $user->phone_number) }}" class="form-control">
                @error('phone_number')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="address" class="col-form-label">Địa chỉ</label>
                <textarea id="address" name="address" class="form-control" rows="3" placeholder="Nhập địa chỉ">{{ old('address', $user->address) }}</textarea>
                @error('address')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="avatar" class="col-form-label">Ảnh đại diện</label>
                @if($user->avatar)
                <div style="margin-bottom:15px;">
                    <p>Ảnh hiện tại:</p>
                    <img src="{{ asset($user->avatar) }}" alt="Current avatar" style="max-width:150px;max-height:150px;border:1px solid #ddd;border-radius:4px;padding:5px;">
                </div>
                @endif
                <input type="file" class="form-control" id="avatar" name="avatar" accept="image/*">
                @error('avatar')
                <span class="text-danger">{{ $message }}</span>
                @enderror
                <small class="form-text text-muted">Chọn ảnh đại diện mới (JPG, PNG, GIF - tối đa 2MB)</small>
                <div id="avatarPreview" style="margin-top:15px;display:none;">
                    <p>Ảnh mới:</p>
                    <img id="previewImg" src="" alt="Preview" style="max-width:150px;max-height:150px;border:1px solid #ddd;border-radius:4px;padding:5px;">
                </div>
            </div>

            <div class="form-group">
                <label for="password" class="col-form-label">Mật khẩu mới</label>
                <input id="password" type="password" name="password" placeholder="Để trống nếu không đổi mật khẩu" class="form-control">
                @error('password')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation" class="col-form-label">Xác nhận mật khẩu</label>
                <input id="password_confirmation" type="password" name="password_confirmation" placeholder="Xác nhận mật khẩu mới" class="form-control">
            </div>

            <div class="form-group mb-3">
                <button type="reset" class="btn btn-warning">Reset</button>
                <button class="btn btn-success" type="submit">Cập nhật</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Avatar preview
    document.getElementById('avatar').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('previewImg').src = e.target.result;
                document.getElementById('avatarPreview').style.display = 'block';
            }
            reader.readAsDataURL(file);
        } else {
            document.getElementById('avatarPreview').style.display = 'none';
        }
    });
</script>
@endpush

