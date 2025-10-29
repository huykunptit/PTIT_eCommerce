@extends('backend.layouts.master')
@section('title','Sửa vai trò')
@section('main-content')
<div class="row">
  <div class="col-lg-6">
    <div class="card">
      <h5 class="card-header">Sửa vai trò</h5>
      <div class="card-body">
        <form method="post" action="{{route('admin.roles.update',$role->id)}}">
          @csrf
          @method('PATCH')
          <div class="form-group">
            <label for="role_name" class="col-form-label">Tên vai trò <span class="text-danger">*</span></label>
            <input id="role_name" type="text" name="role_name" value="{{$role->role_name}}" class="form-control">
            @error('role_name')<span class="text-danger">{{$message}}</span>@enderror
          </div>
          <div class="form-group">
            <label for="role_code" class="col-form-label">Mã vai trò <span class="text-danger">*</span></label>
            <input id="role_code" type="text" name="role_code" value="{{$role->role_code}}" class="form-control">
            @error('role_code')<span class="text-danger">{{$message}}</span>@enderror
          </div>
          <div class="form-group mb-3">
            <button class="btn btn-success" type="submit">Cập nhật</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection


