@extends('backend.layouts.master')

@section('main-content')

<div class="row create-user-wrapper">
  <div class="col-lg-6">
    <div class="card form-half">
        <h5 class="card-header">Thêm người dùng</h5>
        <div class="card-body">
          <form method="post" action="{{route('admin.users.store')}}" enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="form-group floating-group">
              <input id="inputTitle" type="text" name="name" placeholder=" "  value="{{old('name')}}" class="form-control modern-input preview-name">
              <label for="inputTitle" class="col-form-label">Họ và tên</label>
            @error('name')
            <span class="text-danger">{{$message}}</span>
            @enderror
            </div>

            <div class="form-group floating-group">
              <input id="inputEmail" type="email" name="email" placeholder=" "  value="{{old('email')}}" class="form-control modern-input preview-email">
              <label for="inputEmail" class="col-form-label">Email</label>
              @error('email')
              <span class="text-danger">{{$message}}</span>
              @enderror
            </div>

            <div class="form-group floating-group">
              <input id="inputPassword" type="password" name="password" placeholder=" "  value="{{old('password')}}" class="form-control modern-input">
              <label for="inputPassword" class="col-form-label">Mật khẩu</label>
              @error('password')
              <span class="text-danger">{{$message}}</span>
              @enderror
            </div>

            <div class="form-group floating-group">
              <input id="inputPasswordConfirm" type="password" name="password_confirmation" placeholder=" " class="form-control modern-input">
              <label for="inputPasswordConfirm" class="col-form-label">Xác nhận lại mật khẩu</label>
              @error('password_confirmation')
              <span class="text-danger">{{$message}}</span>
              @enderror
            </div>

            <div class="form-group">
            <label for="inputPhoto" class="col-form-label">Avatar</label>
            <input id="inputPhoto" type="file" name="photo" class="filepond" accept="image/*" />
              @error('photo')
              <span class="text-danger">{{$message}}</span>
              @enderror
            </div>
            @php 
            $roles=DB::table('users')->select('role')->get();
            @endphp
            <div class="form-group">
                <label for="role" class="col-form-label">Vai trò</label>
                <select name="role" class="form-control preview-role">
                    <option value="">-----Select Role-----</option>
                    @foreach($roles as $role)
                        <option value="{{$role->role}}">{{$role->role}}</option>
                    @endforeach
                </select>
              @error('role')
              <span class="text-danger">{{$message}}</span>
              @enderror
              </div>
              <div class="form-group">
                <label for="status" class="col-form-label">Trạng thái</label>
                <select name="status" class="form-control preview-status">
                    <option value="active">Hoạt động</option>
                    <option value="inactive">Dừng hoạt động</option>
                </select>
              @error('status')
              <span class="text-danger">{{$message}}</span>
              @enderror
              </div>
            <div class="form-group mb-3">
              <button type="reset" class="btn btn-warning">Reset thông tin</button>
               <button class="btn btn-success" type="submit">Lưu</button>
            </div>
          </form>
        </div>
    </div>
  </div>
  <div class="col-lg-6">
    <div class="card mb-4 profile-preview-card">
      <div class="card-body text-center">
        <img src="{{ old('photo') }}" alt="avatar" class="rounded-circle img-fluid preview-avatar" style="width: 150px;">
        <h5 class="my-3 preview-name-text">{{ old('name') ?: 'Tên của bạn' }}</h5>
        <p class="text-muted mb-1 preview-role-text">{{ old('role') ?: 'Role' }}</p>
        <p class="text-muted mb-4 preview-email-text">{{ old('email') ?: 'email@example.com' }}</p>
        <div class="d-flex justify-content-center mb-2">
          <button type="button" class="btn btn-primary">Follow</button>
          <button type="button" class="btn btn-outline-primary ms-1">Message</button>
        </div>
      </div>
    </div>
    <div class="card mb-4">
      <div class="card-body">
        <div class="row">
          <div class="col-sm-3"><p class="mb-0">Họ và tên</p></div>
          <div class="col-sm-9"><p class="text-muted mb-0 preview-name-text">{{ old('name') ?: 'Tên của bạn' }}</p></div>
        </div>
        <hr>
        <div class="row">
          <div class="col-sm-3"><p class="mb-0">Email</p></div>
          <div class="col-sm-9"><p class="text-muted mb-0 preview-email-text">{{ old('email') ?: 'email@example.com' }}</p></div>
        </div>
        <hr>
        <div class="row">
          <div class="col-sm-3"><p class="mb-0">Vai trò</p></div>
          <div class="col-sm-9"><p class="text-muted mb-0 preview-role-text">{{ old('role') ?: 'Vai trò' }}</p></div>
        </div>
        <hr>
        <div class="row">
          <div class="col-sm-3"><p class="mb-0">Trạng thái</p></div>
          <div class="col-sm-9"><p class="text-muted mb-0 preview-status-text">{{ old('status') ?: 'Hoạt động' }}</p></div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
    // Live preview bindings
    $(function(){
      const update = function(){
        $('.preview-name-text').text($('.preview-name').val()||'Your Name');
        $('.preview-email-text').text($('.preview-email').val()||'email@example.com');
        $('.preview-role-text').text($('.preview-role').val()||'Role');
        $('.preview-status-text').text($('.preview-status').val()||'Active');
      };
      $('.preview-name, .preview-email, .preview-role, .preview-status, .preview-photo').on('input change', update);
      update();
    });
</script>
<script src="https://unpkg.com/filepond-plugin-image-preview@4.6.11/dist/filepond-plugin-image-preview.min.js"></script>
<script src="https://unpkg.com/filepond@4.30.6/dist/filepond.min.js"></script>
<script>
  FilePond.registerPlugin(FilePondPluginImagePreview);
  const inputElement = document.querySelector('input.filepond');
  if(inputElement){
    const pond = FilePond.create(inputElement, {
      allowMultiple: false,
      stylePanelAspectRatio: 1,
      imagePreviewHeight: 120,
      instantUpload: false,
      storeAsFile: true,
      labelIdle: 'Kéo & thả ảnh hoặc <span class="filepond--label-action">Chọn ảnh</span>',
    });
    pond.on('addfile', (error, fileItem) => {
      if (!error && fileItem && fileItem.file) {
        const url = URL.createObjectURL(fileItem.file);
        document.querySelector('.preview-avatar').src = url;
      }
    });
  }
</script>
@endpush