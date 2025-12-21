@extends('backend.layouts.master')
@section('title','Quản lý vai trò')
@section('main-content')
<div class="card shadow mb-4">
  <div class="card-header py-3">
    <h6 class="m-0 font-weight-bold text-primary float-left">Danh sách vai trò</h6>
    <a href="{{route('admin.roles.create')}}" class="btn btn-primary btn-sm float-right"><i class="fa fa-plus"></i> Thêm vai trò</a>
  </div>
  <div class="card-body">
    @include('backend.layouts.notification')
    <div class="table-responsive">
      <table class="table table-bordered" width="100%" cellspacing="0">
        <thead>
          <tr>
            <th>ID</th>
            <th>Tên vai trò</th>
            <th>Mã vai trò</th>
            <th>Hành động</th>
          </tr>
        </thead>
        <tbody>
        @foreach($roles as $role)
          <tr>
            <td>{{$role->id}}</td>
            <td>{{$role->role_name}}</td>
            <td>{{$role->role_code}}</td>
            <td>
              <a href="{{route('admin.roles.edit',$role->id)}}" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i></a>
              <form action="{{route('admin.roles.destroy',$role->id)}}" method="POST" style="display:inline-block;">
                @csrf
                @method('DELETE')
                <button class="btn btn-danger btn-sm" onclick="return confirm('Xóa vai trò này?')"><i class="fa fa-trash"></i></button>
              </form>
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
      {{$roles->links()}}
    </div>
  </div>
</div>
@endsection


