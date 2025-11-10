<?php

return [
    /*
    |--------------------------------------------------------------------------
    | VNPay Configuration
    |--------------------------------------------------------------------------
    |
    | Cấu hình thông tin kết nối VNPay
    |
    */

    'tmn_code' => env('VNPAY_TMN_CODE', ''),
    'hash_secret' => env('VNPAY_HASH_SECRET', ''),
    
    // URL thanh toán
    'url' => env('VNPAY_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html'),
    
    // URL API
    'api_url' => env('VNPAY_API_URL', 'https://sandbox.vnpayment.vn/merchant_webapi/api/transaction'),
    
    // URL return sau khi thanh toán
    'return_url' => env('VNPAY_RETURN_URL', '/payment/vnpay/return'),
    
    // URL IPN (Instant Payment Notification)
    'ipn_url' => env('VNPAY_IPN_URL', '/payment/vnpay/ipn'),
    
    // Version API (có thể dùng 2.0.0 hoặc 2.1.0)
    'version' => '2.1.0',
    
    // Currency
    'currency' => 'VND',
    
    // Locale
    'locale' => 'vn', // vn hoặc en
    
    // Order type
    'order_type' => 'other',
    
    // Expire time (minutes)
    'expire_time' => 15,
];

