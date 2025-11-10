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
      <h6 class="m-0 font-weight-bold text-primary float-left">Coupon List</h6>
      <a href="{{route('admin.coupon.create')}}" class="btn btn-primary btn-sm float-right" data-toggle="tooltip" data-placement="bottom" title="Add User"><i class="fas fa-plus"></i> Add Coupon</a>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        @if(count($coupons)>0)
        <table class="table table-bordered" id="banner-dataTable" width="100%" cellspacing="0">
          <thead>
            <tr>
              <th>S.N.</th>
              <th>Coupon Code</th>
              <th>Type</th>
              <th>Value</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tfoot>
            <tr>
                <th>S.N.</th>
                <th>Coupon Code</th>
                <th>Type</th>
                <th>Value</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
          </tfoot>
          <tbody>
            @foreach($coupons as $coupon)   
                <tr>
                    <td>{{$coupon->id}}</td>
                    <td>{{$coupon->code}}</td>
                    <td>
                        @if($coupon->type=='fixed')
                            <span class="badge badge-primary">{{$coupon->type}}</span>
                        @else
                            <span class="badge badge-warning">{{$coupon->type}}</span>
                        @endif
                    </td>
                    <td>
                        @if($coupon->type=='fixed')
                            ${{number_format($coupon->value,2)}}
                        @else
                            {{$coupon->value}}%
                        @endif</td>
                    <td>
                        @if($coupon->status=='active')
                            <span class="badge badge-success">{{$coupon->status}}</span>
                        @else
                            <span class="badge badge-warning">{{$coupon->status}}</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{route('admin.coupon.edit',$coupon->id)}}" class="btn btn-primary btn-sm float-left mr-1" style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip" title="edit" data-placement="bottom"><i class="fas fa-edit"></i></a>
                        <form method="POST" action="{{route('admin.coupon.destroy',[$coupon->id])}}">
                          @csrf 
                          @method('delete')
                              <button class="btn btn-danger btn-sm dltBtn" data-id={{$coupon->id}} style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip" data-placement="bottom" title="Delete"><i class="fas fa-trash-alt"></i></button>
                        </form>
                    </td>
                    {{-- Delete Modal --}}
                    {{-- <div class="modal fade" id="delModal{{$user->id}}" tabindex="-1" role="dialog" aria-labelledby="#delModal{{$user->id}}Label" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title" id="#delModal{{$user->id}}Label">Delete user</h5>
                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                              </button>
                            </div>
                            <div class="modal-body">
                              <form method="post" action="{{ route('banners.destroy',$user->id) }}">
                                @csrf 
                                @method('delete')
                                <button type="submit" class="btn btn-danger" style="margin:auto; text-align:center">Parmanent delete user</button>
                              </form>
                            </div>
                          </div>
                        </div>
                    </div> --}}
                </tr>  
            @endforeach
          </tbody>
        </table>
        <span style="float:right">{{$coupons->links()}}</span>
        @else
          <h6 class="text-center">No Coupon found!!! Please create coupon</h6>
        @endif
      </div>
    </div>
</div>
@endsection

@push('styles')
  @include('admin.partials.datatable_assets', ['section'=>'styles'])
  <style>
      .zoom { transition: transform .2s; }
      .zoom:hover { transform: scale(3.2); }
  </style>
@endpush

@push('scripts')

  @include('admin.partials.datatable_assets', ['section'=>'scripts', 'tableId'=>'banner-dataTable', 'nonOrderable'=>[4,5]])
@endpush