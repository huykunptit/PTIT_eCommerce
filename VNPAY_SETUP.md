# Hướng dẫn tích hợp VNPay vào Laravel Project

## Đã tích hợp

### 1. Cấu trúc Files đã tạo:

- `config/vnpay.php` - File cấu hình VNPay
- `app/Services/VNPayService.php` - Service xử lý logic thanh toán VNPay
- `app/Http/Controllers/VNPayController.php` - Controller xử lý các request từ VNPay
- `app/Http/Controllers/CheckoutController.php` - Controller xử lý checkout và đặt hàng

### 2. Models đã cập nhật:

- `app/Models/Order.php` - Thêm các trường: shipping_name, shipping_phone, shipping_address, shipping_email, notes, payment_method
- `app/Models/OrderItem.php` - Thêm các trường: variant_id, price, subtotal
- `app/Models/Payment.php` - Thêm các trường: status, transaction_no, transaction_data, bank_code, pay_date

### 3. Routes đã thêm:

```php
// Checkout Routes
Route::middleware('auth')->prefix('checkout')->name('checkout.')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('index');
    Route::post('/', [CheckoutController::class, 'store'])->name('store');
    Route::get('/success', [CheckoutController::class, 'success'])->name('success');
});

// Payment Routes
Route::prefix('payment')->name('payment.')->group(function () {
    Route::prefix('vnpay')->name('vnpay.')->group(function () {
        Route::post('/create', [VNPayController::class, 'createPayment'])->name('create');
        Route::get('/return', [VNPayController::class, 'return'])->name('return');
        Route::post('/ipn', [VNPayController::class, 'ipn'])->name('ipn');
        Route::get('/ipn', [VNPayController::class, 'ipn'])->name('ipn.get');
    });
});
```

## Cấu hình

### 1. Thêm vào file `.env`:

```env
VNPAY_TMN_CODE=your_tmn_code_here
VNPAY_HASH_SECRET=your_hash_secret_here
VNPAY_URL=https://sandbox.vnpayment.vn/paymentv2/vpcpay.html
VNPAY_API_URL=https://sandbox.vnpayment.vn/merchant_webapi/api/transaction
VNPAY_RETURN_URL=/payment/vnpay/return
VNPAY_IPN_URL=/payment/vnpay/ipn
```

**Lưu ý:**
- Sandbox: `https://sandbox.vnpayment.vn/paymentv2/vpcpay.html`
- Production: `https://vnpayment.vn/paymentv2/vpcpay.html`

### 2. Tạo Migration cho các cột mới:

Chạy các lệnh sau để tạo migration:

```bash
php artisan make:migration add_shipping_fields_to_orders_table
php artisan make:migration add_variant_fields_to_order_items_table
php artisan make:migration add_payment_fields_to_payments_table
```

Sau đó chạy migration:
```bash
php artisan migrate
```

### 3. Migration SQL (nếu cần):

```sql
-- Orders table
ALTER TABLE orders ADD COLUMN shipping_name VARCHAR(255) NULL;
ALTER TABLE orders ADD COLUMN shipping_phone VARCHAR(20) NULL;
ALTER TABLE orders ADD COLUMN shipping_address TEXT NULL;
ALTER TABLE orders ADD COLUMN shipping_email VARCHAR(255) NULL;
ALTER TABLE orders ADD COLUMN notes TEXT NULL;
ALTER TABLE orders ADD COLUMN payment_method VARCHAR(50) NULL;

-- Order Items table
ALTER TABLE order_items ADD COLUMN variant_id BIGINT UNSIGNED NULL;
ALTER TABLE order_items ADD COLUMN price DECIMAL(10,2) NULL;
ALTER TABLE order_items ADD COLUMN subtotal DECIMAL(10,2) NULL;
ALTER TABLE order_items ADD FOREIGN KEY (variant_id) REFERENCES product_variants(id);

-- Payments table
ALTER TABLE payments ADD COLUMN status VARCHAR(50) NULL;
ALTER TABLE payments ADD COLUMN transaction_no VARCHAR(255) NULL;
ALTER TABLE payments ADD COLUMN transaction_data TEXT NULL;
ALTER TABLE payments ADD COLUMN bank_code VARCHAR(50) NULL;
ALTER TABLE payments ADD COLUMN pay_date DATETIME NULL;
```

## Cách sử dụng

### 1. Từ giỏ hàng, chuyển đến checkout:

```php
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
```

### 2. Sau khi submit checkout form:

- Nếu chọn VNPay: Tự động redirect đến trang thanh toán VNPay
- Nếu chọn COD: Hiển thị trang thành công

### 3. Sau khi thanh toán VNPay:

- VNPay sẽ redirect về `/payment/vnpay/return`
- VNPay sẽ gọi IPN đến `/payment/vnpay/ipn` để xác nhận thanh toán

## Views cần tạo

Bạn cần tạo các view sau:

1. `resources/views/frontend/checkout/index.blade.php` - Trang checkout
2. `resources/views/frontend/checkout/success.blade.php` - Trang thành công
3. `resources/views/frontend/payment/success.blade.php` - Trang thanh toán thành công
4. `resources/views/frontend/payment/failed.blade.php` - Trang thanh toán thất bại

## Lưu ý quan trọng

1. **IPN URL**: VNPay sẽ gọi IPN URL để xác nhận thanh toán. Đảm bảo URL này có thể truy cập được từ internet (không phải localhost).

2. **Return URL**: URL này sẽ được gọi sau khi khách hàng thanh toán xong trên VNPay.

3. **Security**: Luôn kiểm tra chữ ký (signature) từ VNPay để đảm bảo dữ liệu không bị giả mạo.

4. **Testing**: Sử dụng sandbox environment để test trước khi chuyển sang production.

## Testing

1. Đăng nhập vào tài khoản VNPay sandbox
2. Lấy TMN Code và Hash Secret
3. Cấu hình vào `.env`
4. Test flow: Cart -> Checkout -> VNPay -> Return -> IPN

## Support

Nếu có vấn đề, kiểm tra:
- Logs: `storage/logs/laravel.log`
- VNPay IPN logs trong database (bảng payments)
- Console browser để xem lỗi JavaScript (nếu có)
