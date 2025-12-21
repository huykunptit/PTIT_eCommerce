<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\VNPayService;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderConfirmationMail;
use OpenApi\Annotations as OA;

class VNPayController extends Controller
{
    protected $vnpayService;
    
    public function __construct(VNPayService $vnpayService)
    {
        $this->vnpayService = $vnpayService;
    }
    
    /**
     * Tạo URL thanh toán VNPay
     *
     * @OA\Post(
     *     path="/payment/vnpay/create",
     *     tags={"User - Payments"},
     *     summary="Tạo giao dịch thanh toán VNPay cho đơn hàng",
     *     description="Nhận order_id và amount, kiểm tra hợp lệ và redirect sang cổng VNPay.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"order_id","amount"},
     *             @OA\Property(property="order_id", type="integer", example=123),
     *             @OA\Property(property="amount", type="number", example=1500000)
     *         )
     *     ),
     *     @OA\Response(response=302, description="Redirect sang VNPay hoặc quay lại trang checkout khi lỗi"),
     *     @OA\Response(response=400, description="Thông tin không hợp lệ")
     * )
     */
    public function createPayment(Request $request)
    {
        // Kiểm tra cấu hình VNPay
        $tmnCode = config('vnpay.tmn_code');
        $hashSecret = config('vnpay.hash_secret');
        if (empty($tmnCode) || empty($hashSecret)) {
            return redirect()->route('checkout.index')
                ->with('error', 'Hệ thống thanh toán VNPay chưa được cấu hình. Vui lòng liên hệ quản trị viên.');
        }
        
        $orderId = $request->input('order_id') ?? session('vnpay_order_id');
        $amount = $request->input('amount') ?? session('vnpay_amount');
        // dd($orderId, $amount);
        if (!$orderId || !$amount) {
            return redirect()->route('checkout.index')->with('error', 'Thông tin thanh toán không hợp lệ');
        }
        
        $order = Order::findOrFail($orderId);
        
        // Kiểm tra đơn hàng đã được thanh toán chưa
        $existingPayment = Payment::where('order_id', $order->id)
            ->where('status', 'success')
            ->first();
            
        if ($existingPayment) {
            return redirect()->route('checkout.index')->with('error', 'Đơn hàng này đã được thanh toán');
        }
        
        // Kiểm tra số tiền
        if ($order->total_amount != $amount) {
            return redirect()->route('checkout.index')->with('error', 'Số tiền không khớp với đơn hàng');
        }
        
        $data = [
            'order_id' => $order->id,
            'amount' => $amount,
            'bank_code' => $request->input('bank_code', ''),
            'locale' => $request->input('locale', 'vn'),
            'order_info' => 'Thanh toan don hang #' . $order->id,
        ];
        
        try {
            $paymentUrl = $this->vnpayService->createPaymentUrl($data);
            
            // KHÔNG xóa session ở đây - cần giữ lại để VNPay return có thể lấy
            // session()->forget(['vnpay_order_id', 'vnpay_amount']);
            
            return redirect($paymentUrl);
        } catch (\Exception $e) {
            Log::error('VNPay create payment error: ' . $e->getMessage());
            return redirect()->route('checkout.index')
                ->with('error', 'Có lỗi xảy ra khi tạo giao dịch thanh toán. Vui lòng thử lại.');
        }
    }
    
    /**
     * Xử lý return từ VNPay
     */
    public function return(Request $request)
    {
        $inputData = [];
        foreach ($request->all() as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }
        
        $result = $this->vnpayService->processReturnData($inputData);
        
        $order = null;
        if ($result['order_id']) {
            $order = Order::find($result['order_id']);
        }
        
        if ($result['success']) {
            // Tạo hoặc cập nhật payment record
            if ($order) {
                try {
                    Payment::updateOrCreate(
                        [
                            'order_id' => $order->id,
                            'transaction_no' => $result['transaction_no'],
                        ],
                        [
                            'payment_method' => 'vnpay',
                            'amount' => $result['amount'],
                            'status' => 'success',
                            'transaction_data' => json_encode($inputData),
                            'bank_code' => $result['bank_code'],
                            'pay_date' => $result['pay_date'] ? date('Y-m-d H:i:s', strtotime($result['pay_date'])) : now(),
                        ]
                    );
                } catch (\Illuminate\Database\QueryException $e) {
                    Log::error('Payment save error (success): ' . $e->getMessage());
                    if (strpos($e->getMessage(), 'payment_method') !== false) {
                        Log::error('Payment method ENUM error. Please run migration: php artisan migrate');
                    }
                }
                
                // Cập nhật trạng thái đơn hàng
                $order->update(['status' => 'paid']);
                
                // Gửi email xác nhận đơn hàng
                try {
                    $email = $order->shipping_email ?? $order->user->email ?? null;
                    if ($email) {
                        Mail::to($email)->send(new OrderConfirmationMail($order));
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to send order confirmation email: ' . $e->getMessage());
                }
                
                // Xóa giỏ hàng sau khi thanh toán thành công
                session()->forget('cart');
                session()->forget(['vnpay_order_id', 'vnpay_amount']);
            }
            
            return view('frontend.payment.success', [
                'order' => $order,
                'result' => $result,
            ]);
        } else {
            // Thanh toán thất bại hoặc hủy
            if ($order) {
                try {
                    Payment::updateOrCreate(
                        [
                            'order_id' => $order->id,
                            'transaction_no' => $result['transaction_no'] ?? 'failed_' . time(),
                        ],
                        [
                            'payment_method' => 'vnpay',
                            'amount' => $result['amount'],
                            'status' => 'failed',
                            'transaction_data' => json_encode($inputData),
                            'bank_code' => $result['bank_code'],
                        ]
                    );
                } catch (\Exception $e) {
                    // Nếu lỗi do ENUM, log và tiếp tục
                    Log::error('Payment save error: ' . $e->getMessage());
                }
            }
            
            return view('frontend.payment.failed', [
                'order' => $order,
                'result' => $result,
            ]);
        }
    }
    
    /**
     * Xử lý IPN từ VNPay
     */
    public function ipn(Request $request)
    {
        $inputData = [];
        foreach ($request->all() as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }
        
        $result = $this->vnpayService->processIpnData($inputData);
        
        // Cập nhật payment trong database nếu thành công
        if ($result['RspCode'] == '00') {
            $orderId = $inputData['vnp_TxnRef'] ?? null;
            $transactionNo = $inputData['vnp_TransactionNo'] ?? null;
            
            if ($orderId && $transactionNo) {
                $order = Order::find($orderId);
                
                if ($order) {
                    $paymentStatus = ($inputData['vnp_ResponseCode'] ?? '') == '00' && 
                                    ($inputData['vnp_TransactionStatus'] ?? '') == '00' 
                                    ? 'success' : 'failed';
                    
                    try {
                        Payment::updateOrCreate(
                            [
                                'order_id' => $order->id,
                                'transaction_no' => $transactionNo,
                            ],
                            [
                                'payment_method' => 'vnpay',
                                'amount' => isset($inputData['vnp_Amount']) ? $inputData['vnp_Amount'] / 100 : 0,
                                'status' => $paymentStatus,
                                'transaction_data' => json_encode($inputData),
                                'bank_code' => $inputData['vnp_BankCode'] ?? null,
                                'pay_date' => isset($inputData['vnp_PayDate']) ? 
                                    date('Y-m-d H:i:s', strtotime($inputData['vnp_PayDate'])) : now(),
                            ]
                        );
                    } catch (\Illuminate\Database\QueryException $e) {
                        Log::error('Payment save error (IPN): ' . $e->getMessage());
                        if (strpos($e->getMessage(), 'payment_method') !== false) {
                            Log::error('Payment method ENUM error. Please run migration: php artisan migrate');
                        }
                    }
                    
                    if ($paymentStatus == 'success') {
                        $order->update(['status' => 'paid']);
                    }
                }
            }
        }
        
        return response()->json($result);
    }
}

