@extends('frontend.layouts.master')

@section('main-content')
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">
<link href="{{ asset('css/theme.css') }}" rel="stylesheet">
<style>
body {
    margin-top: 20px;
    background: #f8f8f8;
}

.e-profile {
    padding: 20px 0;
}

.e-navlist {
    list-style: none;
    padding: 0;
    margin: 0;
}

.e-navlist .nav-link {
    color: var(--text-dark);
    padding: 10px 15px;
    border-radius: 4px;
    transition: all 0.3s;
}

.e-navlist .nav-link:hover,
.e-navlist .nav-link.active {
    background: var(--gradient-primary);
    color: var(--white);
}

.avatar-container {
    width: 140px;
    height: 140px;
    border-radius: 50%;
    overflow: hidden;
    background: var(--light-gray);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    cursor: pointer;
    transition: all 0.3s;
}

.avatar-container:hover {
    box-shadow: var(--shadow-medium);
}

.avatar-container img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.avatar-placeholder {
    color: var(--text-muted);
    font: bold 8pt Arial;
}

.avatar-upload-btn {
    position: absolute;
    bottom: 0;
    right: 0;
    background: var(--gradient-primary);
    color: var(--white);
    border: 2px solid var(--white);
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s;
}

.avatar-upload-btn:hover {
    transform: scale(1.1);
}

.form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.badge-custom {
    background: var(--gradient-primary);
    color: var(--white);
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}
</style>

<div class="container">
    <div class="row flex-lg-nowrap">
        <div class="col-12 col-lg-auto mb-3" style="width: 200px;">
            <div class="card p-3">
                <div class="e-navlist e-navlist--active-bg">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link px-2 active" href="{{ route('user.profile') }}">
                                <i class="fa fa-fw fa-cog mr-1"></i>
                                <span>Cài đặt</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-2" href="{{ route('user.orders') }}">
                                <i class="fa fa-fw fa-shopping-cart mr-1"></i>
                                <span>Đơn hàng</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-2" href="{{ route('wishlist.index') }}">
                                <i class="fa fa-fw fa-heart mr-1"></i>
                                <span>Yêu thích</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="row">
                <div class="col mb-3">
                    <div class="card">
                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            <div class="e-profile">
                                <div class="row">
                                    <div class="col-12 col-sm-auto mb-3">
                                        <div class="mx-auto" style="width: 140px;">
                                            <div class="avatar-container mx-auto">
                                                @if($user->avatar)
                                                    <img src="{{ asset($user->avatar) }}" alt="{{ $user->name }}" id="current-avatar">
                                                @else
                                                    <div class="avatar-placeholder">
                                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                                    </div>
                                                @endif
                                                <label for="avatar" class="avatar-upload-btn">
                                                    <i class="fa fa-camera"></i>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col d-flex flex-column flex-sm-row justify-content-between mb-3">
                                        <div class="text-center text-sm-left mb-2 mb-sm-0">
                                            <h4 class="pt-sm-2 pb-1 mb-0 text-nowrap">{{ $user->name }}</h4>
                                            <p class="mb-0">{{ $user->email }}</p>
                                            <div class="text-muted">
                                                <small>Thành viên từ {{ $user->created_at->format('d/m/Y') }}</small>
                                            </div>
                                            <div class="mt-2">
                                                <label for="avatar" class="btn btn-primary btn-sm">
                                                    <i class="fa fa-fw fa-camera"></i>
                                                    <span>Đổi ảnh đại diện</span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="text-center text-sm-right">
                                            <span class="badge-custom">Người dùng</span>
                                            <div class="text-muted">
                                                <small>ID: {{ str_pad($user->id, 6, '0', STR_PAD_LEFT) }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <ul class="nav nav-tabs">
                                    <li class="nav-item">
                                        <a href="#" class="active nav-link">Cài đặt</a>
                                    </li>
                                </ul>
                                <div class="tab-content pt-3">
                                    <div class="tab-pane active">
                                        <form method="post" action="{{ route('user.profile.update') }}" enctype="multipart/form-data" novalidate>
                                            @csrf
                                            @method('PUT')
                                            
                                            <input type="file" class="d-none" id="avatar" name="avatar" accept="image/*">
                                            
                                            <div class="row">
                                                <div class="col">
                                                    <div class="row">
                                                        <div class="col">
                                                            <div class="form-group">
                                                                <label>Họ và tên <span class="text-danger">*</span></label>
                                                                <input class="form-control @error('name') is-invalid @enderror" type="text" name="name" placeholder="Nhập họ và tên" value="{{ old('name', $user->name) }}" required>
                                                                @error('name')
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col">
                                                            <div class="form-group">
                                                                <label>Số điện thoại</label>
                                                                <input class="form-control @error('phone_number') is-invalid @enderror" type="text" name="phone_number" placeholder="Nhập số điện thoại" value="{{ old('phone_number', $user->phone_number) }}">
                                                                @error('phone_number')
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col">
                                                            <div class="form-group">
                                                                <label>Email <span class="text-danger">*</span></label>
                                                                <input class="form-control @error('email') is-invalid @enderror" type="email" name="email" placeholder="Nhập email" value="{{ old('email', $user->email) }}" required>
                                                                @error('email')
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col mb-3">
                                                            <div class="form-group">
                                                                <label>Địa chỉ</label>
                                                                <textarea class="form-control @error('address') is-invalid @enderror" rows="5" name="address" placeholder="Nhập địa chỉ">{{ old('address', $user->address) }}</textarea>
                                                                @error('address')
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12 col-sm-6 mb-3">
                                                    <div class="mb-2"><b>Đổi mật khẩu</b></div>
                                                    <div class="row">
                                                        <div class="col">
                                                            <div class="form-group">
                                                                <label>Mật khẩu mới</label>
                                                                <input class="form-control @error('password') is-invalid @enderror" type="password" name="password" placeholder="Để trống nếu không đổi">
                                                                @error('password')
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col">
                                                            <div class="form-group">
                                                                <label>Xác nhận mật khẩu</label>
                                                                <input class="form-control" type="password" name="password_confirmation" placeholder="Xác nhận mật khẩu mới">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-5 offset-sm-1 mb-3">
                                                    <div class="mb-2"><b>Thông tin tài khoản</b></div>
                                                    <div class="row">
                                                        <div class="col">
                                                            <div class="form-group">
                                                                <label>Vai trò</label>
                                                                <input class="form-control" type="text" value="{{ ucfirst($user->role) }}" disabled>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col">
                                                            <div class="form-group">
                                                                <label>Ngày tham gia</label>
                                                                <input class="form-control" type="text" value="{{ $user->created_at->format('d/m/Y') }}" disabled>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col d-flex justify-content-end">
                                                    <button type="reset" class="btn btn-secondary mr-2">Reset</button>
                                                    <button class="btn btn-primary" type="submit">Lưu thay đổi</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-3 mb-3">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="px-xl-3">
                                <a href="{{ route('auth.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form-profile').submit();" class="btn btn-block btn-secondary">
                                    <i class="fa fa-sign-out"></i>
                                    <span>Đăng xuất</span>
                                </a>
                                <form id="logout-form-profile" action="{{ route('auth.logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title font-weight-bold">Hỗ trợ</h6>
                            <p class="card-text">Nhận hỗ trợ nhanh chóng từ đội ngũ của chúng tôi.</p>
                            <a href="{{ route('contact') }}" class="btn btn-primary btn-block">Liên hệ</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
                const avatarContainer = document.querySelector('.avatar-container');
                const currentAvatar = document.getElementById('current-avatar');
                const placeholder = avatarContainer.querySelector('.avatar-placeholder');
                
                if (currentAvatar) {
                    currentAvatar.src = e.target.result;
                } else {
                    if (placeholder) {
                        placeholder.style.display = 'none';
                    }
                    const img = document.createElement('img');
                    img.id = 'current-avatar';
                    img.src = e.target.result;
                    img.alt = '{{ $user->name }}';
                    img.style.width = '100%';
                    img.style.height = '100%';
                    img.style.objectFit = 'cover';
                    avatarContainer.insertBefore(img, avatarContainer.firstChild);
                }
            }
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush
