@extends('backend.layouts.master')
@section('title','PTIT  || Thông báo')
@section('main-content')
<div class="card">
    <div class="row">
        <div class="col-md-12">
           @include('backend.layouts.notification')
        </div>
    </div>
  <h5 class="card-header d-flex justify-content-between align-items-center">
    <span>Thông báo</span>
    <form method="POST" action="{{ route('notifications.read-all') }}" class="m-0" id="markAllReadForm">
      @csrf
      <button type="submit" class="btn btn-sm btn-outline-primary">Đánh dấu đã đọc tất cả</button>
    </form>
  </h5>
  <div class="card-body">
    @if(($notifications ?? collect())->count() > 0)
    <table class="table table-hover admin-table" id="notification-dataTable">
      <thead>
        <tr>
          <th scope="col">#</th>
          <th scope="col">Thời gian</th>
          <th scope="col">Tiêu đề</th>
          <th scope="col">Nội dung</th>
          <th scope="col" class="text-center">Trạng thái</th>
          <th scope="col" class="text-center">Thao tác</th>
        </tr>
      </thead>
      <tbody>
        @foreach (($notifications ?? []) as $notification)

        @php
          $unread = $notification->read_at === null;
        @endphp
        <tr class="{{ $unread ? 'bg-light' : '' }}">
          <td scope="row">{{ $loop->index + 1 }}</td>
          <td>{{ $notification->created_at?->format('d/m/Y H:i') }}</td>
          <td>{{ $notification->data['title'] ?? 'Thông báo' }}</td>
          <td>{{ $notification->data['message'] ?? '' }}</td>
          <td class="text-center">
            @if($unread)
              <span class="badge badge-danger">Chưa đọc</span>
            @else
              <span class="badge badge-secondary">Đã đọc</span>
            @endif
          </td>
          <td class="text-center">
            @if($unread)
              <form method="POST" action="{{ route('notifications.read', $notification->id) }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-success">Đánh dấu đã đọc</button>
              </form>
            @else
              <button type="button" class="btn btn-sm btn-outline-secondary" disabled>Đã đọc</button>
            @endif
          </td>
        </tr>

        @endforeach
      </tbody>
    </table>
    @if(($notifications ?? null) && $notifications->hasPages())
      <div class="mt-3">{{ $notifications->links() }}</div>
    @endif
    @else
      <div class="text-center text-muted py-4">Không có thông báo</div>
    @endif
  </div>
</div>
@endsection
@push('styles')
  <link href="{{asset('backend/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css" />

@endpush
@push('scripts')
  <script src="{{asset('backend/vendor/datatables/jquery.dataTables.min.js')}}"></script>
  <script src="{{asset('backend/vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

  <!-- Page level custom scripts -->
  <script src="{{asset('backend/js/demo/datatables-demo.js')}}"></script>
  <script>

      if ($('#notification-dataTable').length) {
        $('#notification-dataTable').DataTable({
          paging: false,
          searching: false,
          info: false,
          "columnDefs": [
            {
              "orderable": false,
              "targets": [5]
            }
          ]
        });
      }
  </script>
@endpush
