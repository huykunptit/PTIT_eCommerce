<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class VNPayService
{
    /**
     * Tạo URL thanh toán VNPay
     *
     * @param array $data
     * @return string
     */
    public function createPaymentUrl($data)
    {
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        
        $vnp_TmnCode = config('vnpay.tmn_code');
        $vnp_HashSecret = config('vnpay.hash_secret');
        $vnp_Url = config('vnpay.url');
        $vnp_ReturnUrl = url(config('vnpay.return_url'));
        
        $vnp_TxnRef = $data['order_id'] ?? time(); // Mã đơn hàng
        $vnp_Amount = $data['amount']; // Số tiền
        $vnp_Locale = $data['locale'] ?? config('vnpay.locale');
        $vnp_BankCode = $data['bank_code'] ?? '';
        $vnp_IpAddr = request()->ip();
        $vnp_OrderInfo = $data['order_info'] ?? 'Thanh toan don hang: ' . $vnp_TxnRef;
        $vnp_OrderType = config('vnpay.order_type');
        
        $vnp_CreateDate = date('YmdHis');
        $vnp_ExpireDate = date('YmdHis', strtotime('+' . config('vnpay.expire_time') . ' minutes', strtotime($vnp_CreateDate)));
        
        // Tạo inputData theo đúng thứ tự như hướng dẫn VNPay
        $inputData = array(
            "vnp_Version" => config('vnpay.version'),
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount * 100, // VNPay yêu cầu số tiền nhân 100
            "vnp_Command" => "pay",
            "vnp_CreateDate" => $vnp_CreateDate,
            "vnp_CurrCode" => config('vnpay.currency'),
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_ReturnUrl,
            "vnp_TxnRef" => $vnp_TxnRef,
            "vnp_ExpireDate" => $vnp_ExpireDate
        );
        
        
        if (!empty($vnp_BankCode)) {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }
        
   
        ksort($inputData);
        
        $query = "";
        $hashdata = "";
        $i = 0;
        
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }
        
        $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
        $vnp_Url = $vnp_Url . "?" . $query . "vnp_SecureHash=" . $vnpSecureHash;
        
        return $vnp_Url;
    }
    
    /**
     * Xác thực chữ ký từ VNPay
     *
     * @param array $data
     * @return bool
     */
    public function validateSignature($data)
    {
        $vnp_HashSecret = config('vnpay.hash_secret');
        $vnp_SecureHash = $data['vnp_SecureHash'] ?? '';
        
        unset($data['vnp_SecureHash']);
        ksort($data);
        
        $i = 0;
        $hashData = "";
        foreach ($data as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }
        
        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
        
        return $secureHash === $vnp_SecureHash;
    }
    
    /**
     * Xử lý dữ liệu từ VNPay return
     *
     * @param array $data
     * @return array
     */
    public function processReturnData($data)
    {
        $result = [
            'success' => false,
            'message' => '',
            'order_id' => $data['vnp_TxnRef'] ?? null,
            'amount' => isset($data['vnp_Amount']) ? $data['vnp_Amount'] / 100 : 0,
            'transaction_no' => $data['vnp_TransactionNo'] ?? null,
            'response_code' => $data['vnp_ResponseCode'] ?? null,
            'bank_code' => $data['vnp_BankCode'] ?? null,
            'pay_date' => $data['vnp_PayDate'] ?? null,
            'order_info' => $data['vnp_OrderInfo'] ?? null,
        ];
        
        // Kiểm tra chữ ký
        if (!$this->validateSignature($data)) {
            $result['message'] = 'Chữ ký không hợp lệ';
            return $result;
        }
        
        // Kiểm tra mã phản hồi
        if ($result['response_code'] == '00') {
            $result['success'] = true;
            $result['message'] = 'Thanh toán thành công';
        } else {
            $result['message'] = $this->getResponseMessage($result['response_code']);
        }
        
        return $result;
    }
    
    /**
     * Xử lý dữ liệu từ VNPay IPN
     *
     * @param array $data
     * @return array
     */
    public function processIpnData($data)
    {
        $result = [
            'RspCode' => '99',
            'Message' => 'Unknown error',
        ];
        
        try {
            // Kiểm tra chữ ký
            if (!$this->validateSignature($data)) {
                $result['RspCode'] = '97';
                $result['Message'] = 'Invalid signature';
                return $result;
            }
            
            $vnp_Amount = isset($data['vnp_Amount']) ? $data['vnp_Amount'] / 100 : 0;
            $orderId = $data['vnp_TxnRef'] ?? null;
            
            if (!$orderId) {
                $result['RspCode'] = '01';
                $result['Message'] = 'Order not found';
                return $result;
            }
            
            // Tìm đơn hàng trong database
            $order = \App\Models\Order::find($orderId);
            
            if (!$order) {
                $result['RspCode'] = '01';
                $result['Message'] = 'Order not found';
                return $result;
            }
            
            // Kiểm tra số tiền
            if ($order->total_amount != $vnp_Amount) {
                $result['RspCode'] = '04';
                $result['Message'] = 'Invalid amount';
                return $result;
            }
            
            // Kiểm tra trạng thái đơn hàng (tránh xử lý trùng lặp)
            $payment = \App\Models\Payment::where('order_id', $orderId)
                ->where('transaction_no', $data['vnp_TransactionNo'] ?? null)
                ->first();
            
            if ($payment && $payment->status == 'success') {
                $result['RspCode'] = '02';
                $result['Message'] = 'Order already confirmed';
                return $result;
            }
            
            // Cập nhật trạng thái thanh toán
            if ($data['vnp_ResponseCode'] == '00' && ($data['vnp_TransactionStatus'] ?? '') == '00') {
                // Thanh toán thành công
                $result['RspCode'] = '00';
                $result['Message'] = 'Confirm Success';
            } else {
                // Thanh toán thất bại
                $result['RspCode'] = '00';
                $result['Message'] = 'Confirm Success';
            }
            
        } catch (\Exception $e) {
            Log::error('VNPay IPN Error: ' . $e->getMessage());
            $result['RspCode'] = '99';
            $result['Message'] = 'Unknown error';
        }
        
        return $result;
    }
    
    /**
     * Lấy thông báo lỗi từ mã phản hồi
     *
     * @param string $responseCode
     * @return string
     */
    private function getResponseMessage($responseCode)
    {
        $messages = [
            '00' => 'Giao dịch thành công',
            '07' => 'Trừ tiền thành công. Giao dịch bị nghi ngờ (liên quan tới lừa đảo, giao dịch bất thường).',
            '09' => 'Thẻ/Tài khoản chưa đăng ký dịch vụ InternetBanking',
            '10' => 'Xác thực thông tin thẻ/tài khoản không đúng. Vui lòng thử lại',
            '11' => 'Đã hết hạn chờ thanh toán. Vui lòng thử lại',
            '12' => 'Thẻ/Tài khoản bị khóa',
            '13' => 'Nhập sai mật khẩu xác thực giao dịch (OTP). Vui lòng thử lại',
            '51' => 'Tài khoản không đủ số dư để thực hiện giao dịch',
            '65' => 'Tài khoản đã vượt quá hạn mức giao dịch trong ngày',
            '75' => 'Ngân hàng thanh toán đang bảo trì',
            '79' => 'Nhập sai mật khẩu thanh toán quá số lần quy định',
            '99' => 'Lỗi không xác định',
        ];
        
        return $messages[$responseCode] ?? 'Lỗi không xác định';
    }
}

