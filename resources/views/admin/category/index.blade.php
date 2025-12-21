@extends('backend.layouts.master')

@section('main-content')
 <!-- DataTales Example -->
 <div class="card shadow mb-4">
     <div class="row">
         <div class="col-md-12">
            @include('backend.layouts.notification')
         </div>
     </div>
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary float-left">Danh sách danh mục</h6>
      <a href="{{route('admin.categories.create')}}" class="btn btn-primary btn-sm float-right" data-toggle="tooltip" data-placement="bottom" title="Thêm danh mục"><i class="fa fa-plus"></i> Thêm danh mục</a>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        @if(count($categories)>0)
        <table class="table table-bordered table-striped table-hover" id="category-dataTable" width="100%" cellspacing="0">
          <thead>
            <tr>
              <th>ID</th>
              <th>Tên danh mục</th>
              <th>Ảnh</th>
              <th>Mô tả</th>
              <th>Danh mục cha</th>
              <th>Ngày tạo</th>
              <th>Cập nhật</th>
              <th>Hành động</th>
            </tr>
          </thead>
          <tbody>

            @foreach($categories as $category)
              @php
              @endphp
                <tr>
                    <td>{{$category->id}}</td>
                    <td>{{$category->name}}</td>
                    <td>
                        @if($category->image)
                            @php
                                $src = \Illuminate\Support\Str::startsWith($category->image, ['http://','https://'])
                                    ? $category->image
                                    : asset($category->image);
                            @endphp
                            <img src="{{$src}}" referrerpolicy="no-referrer" class="img-fluid zoom" style="max-width:80px" alt="{{$category->name}}">
                        @else
                            <img src="{{asset('backend/img/thumbnail-default.jpg')}}" class="img-fluid zoom" style="max-width:80px" alt="default">
                        @endif
                    </td>
                    <td>{{$category->description}}</td>
                    <td>{{$category->parent_category_id ? optional($categories->firstWhere('id',$category->parent_category_id))->name : ''}}</td>
                    <td>{{$category->created_at}}</td>
                    <td>{{$category->updated_at}}</td>
                    <td>
                        <a href="{{route('admin.categories.edit',$category->id)}}" class="btn btn-primary btn-sm float-left mr-1" style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip" title="Sửa" data-placement="bottom"><i class="fa fa-edit"></i></a>
                        <button type="button" class="btn btn-danger btn-sm" style="height:30px; width:30px;border-radius:50%" data-toggle="modal" data-target="#confirmDeleteCat{{$category->id}}" title="Xoá"><i class="fa fa-trash-alt"></i></button>
                        <div class="modal fade" id="confirmDeleteCat{{$category->id}}" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteCatLabel{{$category->id}}" aria-hidden="true">
                          <div class="modal-dialog" role="document">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="confirmDeleteCatLabel{{$category->id}}">Xác nhận xoá</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                              </div>
                              <div class="modal-body">Bạn có chắc chắn muốn xoá danh mục "{{$category->name}}"?</div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Huỷ</button>
                                <form method="POST" action="{{route('admin.categories.delete',[$category->id])}}" class="d-inline">
                                  @csrf
                                  @method('delete')
                                  <button type="submit" class="btn btn-danger">Xoá</button>
                                </form>
                              </div>
                            </div>
                          </div>
                        </div>
                    </td>
                </tr>
            @endforeach
          </tbody>
        </table>
        <span style="float:right">{{$categories->links()}}</span>
        @else
          <h6 class="text-center">No Categories found!!! Please create Category</h6>
        @endif
      </div>
    </div>
</div>
@endsection

@push('styles')
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css" />
  <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css" />
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap4.min.css" />
@endpush

@push('scripts')

  <!-- DataTables & extensions (CDN) -->
  <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap4.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap4.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.colVis.min.js"></script>
  <script>
      $('#category-dataTable').DataTable({
        responsive: true,
        lengthChange: true,
        autoWidth: false,
        dom: 'Bfrtip',
        buttons: [
          { extend: 'copy', className: 'btn btn-sm btn-warning' },
          { extend: 'csv', className: 'btn btn-sm btn-warning' },
          { extend: 'excel', className: 'btn btn-sm btn-warning' },
          { extend: 'pdf', className: 'btn btn-sm btn-warning' },
          { extend: 'print', className: 'btn btn-sm btn-warning' },
          { extend: 'colvis', className: 'btn btn-sm btn-warning' }
        ],
        language: { url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/vi.json' },
            columnDefs: [ { orderable:false, targets:[7] } ]
      });
  </script>
@endpush
