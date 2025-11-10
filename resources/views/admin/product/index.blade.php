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
      <h6 class="m-0 font-weight-bold text-primary float-left">Danh sách sản phẩm</h6>
      <a href="{{route('admin.products.create')}}" class="btn btn-primary btn-sm float-right" data-toggle="tooltip" data-placement="bottom" title="Thêm người dùng"><i class="fas fa-plus"></i> Thêm người dùng</a>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover" id="user-dataTable" width="100%" cellspacing="0">
          <thead>
            <tr>
              <th>STT</th>
              <th>Tên sản phẩm</th>
              <th>Mô tả sản phẩm</th>
              <th>Ảnh</th>
              <th>Giá</th>
              <th>Số lượng còn lại</th>
              <th>Người bán</th>
              <th>Danh mục</th>
              <th>Trạng thái</th>
            </tr>
          </thead>

          <tbody>
            @foreach($products as $key=>$product)   
                <tr>
                    <td>{{$key++}}</td>
                    <td>{{$product->name}}</td>
                    <td>{{$product->description}}</td>
                    <td>
                        @if($product->image_url)
                            @php
                                $src = \Illuminate\Support\Str::startsWith($product->image_url, ['http://','https://'])
                                    ? $product->image_url
                                    : asset($product->image_url);
                            @endphp
                            <img src="{{ $src }}" referrerpolicy="no-referrer" class="img-fluid rounded-circle" style="max-width:50px" alt="{{$product->name}}">
                        @else
                            <img src="{{asset('backend/img/avatar.png')}}" class="img-fluid rounded-circle" style="max-width:50px" alt="avatar.png">
                        @endif
                    </td>
                    <td>{{$product->price}}</td>
                    <td>{{$product->quantity}}</td>
                    <td>{{$product->seller()}}</td>
                    <td>{{$product->category()}}</td>
                    <td>
                        @if($product->status=='active')
                            <span class="badge badge-success">{{$product->status}}</span>
                        @else
                            <span class="badge badge-warning">{{$product->status}}</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{route('admin.products.edit',$product->id)}}" class="btn btn-primary btn-sm float-left mr-1" style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip" title="edit" data-placement="bottom"><i class="fas fa-edit"></i></a>
                        <button type="button" class="btn btn-danger btn-sm" style="height:30px; width:30px;border-radius:50%" data-toggle="modal" data-target="#confirmDelete{{$product->id}}" title="Delete"><i class="fas fa-trash-alt"></i></button>
                        <!-- Delete Confirm Modal -->
                        <div class="modal fade" id="confirmDelete{{$product->id}}" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteLabel{{$product->id}}" aria-hidden="true">
                          <div class="modal-dialog" role="document">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="confirmDeleteLabel{{$product->id}}">Xác nhận xoá</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                              </div>
                              <div class="modal-body">
                                Bạn có chắc chắn muốn xoá người dùng "{{$product->name}}" không? Hành động này không thể hoàn tác.
                              </div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Huỷ</button>
                                <form method="POST" action="{{route('admin.products.delete',[$product->id])}}" class="d-inline">
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
        <span style="float:right">{{$products->links()}}</span>
      </div>
    </div>
</div>
@endsection

@push('styles')
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css" />
  <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css" />
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap4.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css" />
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
  <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

  <script>
      $('#user-dataTable').DataTable({
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
        language: {
          url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/vi.json'
        },
        columnDefs: [
          { orderable: false, targets: [6,7] }
        ]
      });
  </script>
  <script>
      $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
      });
  </script>
@endpush