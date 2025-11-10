# Sửa lỗi Payment Method ENUM

## Vấn đề
Lỗi: `SQLSTATE[01000]: Warning: 1265 Data truncated for column 'payment_method'`

Nguyên nhân: Cột `payment_method` trong bảng `payments` là ENUM và chưa có giá trị `vnpay` và `cod`.

## Giải pháp

### Cách 1: Chạy Migration (Khuyến nghị)
```bash
php artisan migrate
```

### Cách 2: Chạy SQL trực tiếp
Nếu không thể chạy migration, chạy SQL sau trong database:

```sql
ALTER TABLE payments MODIFY COLUMN payment_method ENUM('credit_card', 'paypal', 'cash_on_delivery', 'vnpay', 'cod') NOT NULL;
```

### Cách 3: Kiểm tra migration đã chạy chưa
```bash
php artisan migrate:status
```

Tìm migration: `2025_11_07_022611_update_payments_payment_method_enum_to_include_vnpay`

Nếu chưa chạy, sẽ hiển thị `Pending`. Chạy:
```bash
php artisan migrate
```

## Sau khi chạy migration
- Thanh toán VNPay sẽ hoạt động bình thường
- Có thể lưu `payment_method = 'vnpay'` hoặc `'cod'`
- Lỗi sẽ không còn xuất hiện

## Lưu ý
- Đảm bảo database connection đúng trong `.env`
- Backup database trước khi chạy migration (nếu cần)
- Code đã có try-catch để tránh crash, nhưng vẫn cần chạy migration để lưu được payment

