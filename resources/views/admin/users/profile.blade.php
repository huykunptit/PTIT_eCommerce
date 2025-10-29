@extends('backend.layouts.master')

@section('title','Admin Profile')

@section('main-content')

<div class="row create-user-wrapper">
  <div class="col-lg-4">
    <div class="card mb-4">
      <div class="card-body text-center">
        <img src="{{ $profile->photo ? asset($profile->photo) : asset('backend/img/avatar.png') }}" alt="avatar" class="rounded-circle img-fluid" style="width: 150px;">
        <h5 class="my-3">{{$profile->name}}</h5>
        <p class="text-muted mb-1">{{$profile->role}}</p>
        <p class="text-muted mb-4">{{$profile->email}}</p>
        <div class="d-flex justify-content-center mb-2">
          <a href="{{route('admin.users.edit', $profile->id)}}" class="btn btn-primary">Chỉnh sửa</a>
        </div>
      </div>
    </div>
    <div class="card mb-4 mb-lg-0">
      <div class="card-body p-0">
        <ul class="list-group list-group-flush rounded-3">
          <li class="list-group-item d-flex justify-content-between align-items-center p-3">
            <i class="fas fa-envelope text-primary"></i>
            <p class="mb-0">{{$profile->email}}</p>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center p-3">
            <i class="fas fa-user text-secondary"></i>
            <p class="mb-0">{{$profile->role}}</p>
          </li>
        </ul>
      </div>
    </div>
  </div>
  <div class="col-lg-8">
    <div class="card mb-4">
      <div class="card-body">
        <div class="row">
          <div class="col-sm-3"><p class="mb-0">Họ và tên</p></div>
          <div class="col-sm-9"><p class="text-muted mb-0">{{$profile->name}}</p></div>
        </div>
        <hr>
        <div class="row">
          <div class="col-sm-3"><p class="mb-0">Email</p></div>
          <div class="col-sm-9"><p class="text-muted mb-0">{{$profile->email}}</p></div>
        </div>
        <hr>
        <div class="row">
          <div class="col-sm-3"><p class="mb-0">Vai trò</p></div>
          <div class="col-sm-9"><p class="text-muted mb-0">{{$profile->role}}</p></div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

<style>
  i{font-size:14px;padding-right:8px;}
</style>

@push('scripts')
@endpush