@extends('frontend.layouts.master')
@section('title','Thanh toán Sepay - PTIT eCommerce')
@section('main-content')

<section class="sepay-section" style="padding:40px 0;background:#f5f7fb;">
    <div class="container">
        <div class="row">
            <!-- Thông tin chuyển khoản -->
            <div class="col-lg-6 mb-4">
                <div class="card" style="border-radius:12px;box-shadow:0 10px 25px rgba(0,0,0,0.05);">
                    <div class="card-body" style="padding:24px 28px;">
                        <h5 style="font-weight:700;margin-bottom:16px;">Thông tin thanh toán ngân hàng</h5>
                        <p style="font-size:13px;color:#666;margin-bottom:16px;">
                            Vui lòng chuyển khoản đúng <strong>NỘI DUNG</strong> bên dưới để hệ thống tự động đối soát đơn hàng #{{ $order->id }}.
                        </p>

                        <div class="mb-3">
                            <label class="text-muted mb-1">Ngân hàng thụ hưởng</label>
                            <div style="font-weight:600;">Vietcombank (VCB)</div>
                        </div>

                        <div class="mb-3">
                            <label class="text-muted mb-1">Số tài khoản</label>
                            <div class="d-flex align-items-center">
                                <strong id="sepay-account">{{ $account }}</strong>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="text-muted mb-1">Nội dung chuyển khoản (bắt buộc)</label>
                            <div class="d-flex align-items-center">
                                <strong id="sepay-des">{{ $des }}</strong>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="text-muted mb-1">Số tiền</label>
                            <div><strong style="color:#2563eb;font-size:18px;">
                                {{ number_format($amount, 0, ',', '.') }}₫
                            </strong></div>
                        </div>

                        <small class="text-muted">
                            Nếu sau 5–10 phút chưa nhận được xác nhận thanh toán, vui lòng liên hệ Admin kèm theo nội dung chuyển khoản trên.
                        </small>
                    </div>
                </div>
            </div>

            <!-- QR Sepay -->
            <div class="col-lg-6 mb-4">
                <div class="card" style="border-radius:12px;box-shadow:0 10px 25px rgba(0,0,0,0.05);text-align:center;">
                    <div class="card-body" style="padding:24px 28px;">
                        <h5 style="font-weight:700;margin-bottom:12px;">Quét mã để thanh toán</h5>
                        <p style="font-size:13px;color:#666;margin-bottom:20px;">
                            Mở App ngân hàng/Ví điện tử, chọn quét QR và xác nhận thanh toán.
                        </p>
                        <div style="background:#f9fafb;border-radius:16px;padding:16px;display:inline-block;">
                            <img src="{{ $qrUrl }}" alt="QR Sepay" style="max-width:260px;width:100%;border-radius:12px;">
                        </div>
                        <p class="mt-3" style="font-size:13px;color:#999;">
                            Hệ thống sử dụng Sepay để tạo QR ngân hàng Vietcombank.
                        </p>

                        <div id="sepay-payment-status" class="alert alert-info mt-3" style="display:none;">
                            <i class="fa fa-spinner fa-spin mr-2"></i>Đang chờ xác nhận thanh toán...
                        </div>

                        <div id="sepay-payment-success" class="alert alert-success mt-3" style="display:none;">
                            <i class="fa fa-check-circle mr-2"></i>Thanh toán thành công! Đơn hàng của bạn đã được xác nhận.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
    (function() {
        var orderId = {{ $order->id }};
        var statusUrl = "{{ route('payment.sepay.status', $order->id) }}";
        var $statusBox = document.getElementById('sepay-payment-status');
        var $successBox = document.getElementById('sepay-payment-success');
        var hasShownSuccess = false;

        function updateStatus() {
            fetch(statusUrl, {
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(function(res){ return res.json(); })
            .then(function(data) {
                if (!data.success) return;

                if (data.status === 'paid') {
                    if (!hasShownSuccess) {
                        hasShownSuccess = true;
                        if ($statusBox) $statusBox.style.display = 'none';
                        if ($successBox) $successBox.style.display = 'block';

                        // Optional: sau vài giây chuyển về trang success
                        setTimeout(function() {
                            window.location.href = "{{ route('checkout.success') }}?order_id=" + orderId;
                        }, 4000);
                    }
                } else if (data.status === 'pending_payment') {
                    if ($statusBox && !hasShownSuccess) {
                        $statusBox.style.display = 'block';
                    }
                }
            })
            .catch(function(err) {
                console.error('Error polling order status:', err);
            });
        }

        // Bắt đầu polling sau khi trang load
        document.addEventListener('DOMContentLoaded', function() {
            // Hiện trạng thái chờ nếu chưa thanh toán
            @if($order->status === 'pending_payment')
                if ($statusBox) $statusBox.style.display = 'block';
            @endif

            setInterval(updateStatus, 5000); // 5s/lần
        });
    })();
</script>
@endpush

@endsection
