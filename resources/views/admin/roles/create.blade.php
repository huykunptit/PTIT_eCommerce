@extends('backend.layouts.master')
@section('title','Thêm vai trò')
@section('main-content')
<div class="row">
  <div class="col-lg-6">
    <div class="card">
      <h5 class="card-header">Thêm vai trò</h5>
      <div class="card-body">
        <form method="post" action="{{route('admin.roles.store')}}">
          @csrf
          <div class="form-group">
            <label for="role_name" class="col-form-label">Tên vai trò <span class="text-danger">*</span></label>
            <input id="role_name" type="text" name="role_name" value="{{old('role_name')}}" class="form-control">
            @error('role_name')<span class="text-danger">{{$message}}</span>@enderror
          </div>
          <div class="form-group">
            <label for="role_code" class="col-form-label">Mã vai trò <span class="text-danger">*</span></label>
            <input id="role_code" type="text" name="role_code" value="{{old('role_code')}}" class="form-control">
            @error('role_code')<span class="text-danger">{{$message}}</span>@enderror
          </div>
          <div class="form-group mb-3">
            <button type="reset" class="btn btn-warning">Đặt lại</button>
            <button class="btn btn-success" type="submit">Thêm</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection


