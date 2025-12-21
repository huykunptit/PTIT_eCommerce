@extends('backend.layouts.master')
@section('title','Quản lý Tags')
@section('main-content')
<div class="container-fluid">
    @include('backend.layouts.notification')
    
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Quản lý Tags</h1>
        <a href="{{ route('admin.tags.create') }}" class="btn btn-primary">
            <i class="fa fa-plus"></i> Thêm Tag mới
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên</th>
                            <th>Màu sắc</th>
                            <th>Số sản phẩm</th>
                            <th>Mô tả</th>
                            <th>Ngày tạo</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tags as $tag)
                        <tr>
                            <td>{{ $tag->id }}</td>
                            <td>
                                <span class="badge" style="background-color: {{ $tag->color }}; color: white; padding: 5px 10px;">
                                    {{ $tag->name }}
                                </span>
                            </td>
                            <td>
                                <div style="width: 30px; height: 30px; background-color: {{ $tag->color }}; border-radius: 4px; border: 1px solid #ddd;"></div>
                            </td>
                            <td class="text-center">{{ $tag->products_count ?? 0 }}</td>
                            <td>{{ Str::limit($tag->description ?? 'N/A', 50) }}</td>
                            <td>{{ $tag->created_at->format('d/m/Y') }}</td>
                            <td>
                                <a href="{{ route('admin.tags.edit', $tag->id) }}" class="btn btn-sm btn-primary">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.tags.destroy', $tag->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa tag này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Chưa có tag nào</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($tags->hasPages())
            <div class="mt-3">
                {{ $tags->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

