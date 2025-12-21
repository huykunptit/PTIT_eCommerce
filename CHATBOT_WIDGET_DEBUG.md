# Chatbot Widget Debug Guide

## ✅ Đã sửa các vấn đề:

1. ✅ Thêm `@stack('scripts')` vào layout để load JavaScript
2. ✅ Thêm `!important` vào CSS để đảm bảo widget hiển thị
3. ✅ Tăng z-index lên 99999
4. ✅ Thêm debug console logs
5. ✅ Đảm bảo widget hiển thị ngay cả khi chưa login

## 🔍 Cách kiểm tra widget có hiển thị:

### 1. Kiểm tra trong Browser:
1. Mở website: `http://localhost:8082`
2. Mở Developer Tools (F12)
3. Vào tab **Console**
4. Tìm dòng: `Chatbot widget script loading...`
5. Nếu thấy: `Chatbot widget initialized successfully` → Widget đã load

### 2. Kiểm tra trong Elements:
1. Mở Developer Tools (F12)
2. Vào tab **Elements** (hoặc **Inspector**)
3. Tìm element: `<div id="chatbot-widget">`
4. Nếu thấy → Widget đã được render

### 3. Kiểm tra CSS:
1. Trong Elements, chọn `#chatbot-widget`
2. Kiểm tra CSS:
   - `position: fixed`
   - `bottom: 20px`
   - `right: 20px`
   - `z-index: 99999`
   - `display: block`

## 🐛 Nếu widget vẫn không hiển thị:

### Bước 1: Clear cache
```bash
# Clear Laravel cache
docker exec shop_app php artisan view:clear
docker exec shop_app php artisan config:clear
docker exec shop_app php artisan cache:clear
```

### Bước 2: Kiểm tra file có tồn tại
```bash
# Kiểm tra component
ls -la resources/views/components/chatbot-widget.blade.php

# Kiểm tra layout
grep -n "chatbot-widget" resources/views/frontend/layouts/master.blade.php
```

### Bước 3: Kiểm tra console errors
1. Mở browser console (F12)
2. Xem có lỗi JavaScript không
3. Các lỗi thường gặp:
   - `Chatbot widget element not found!` → Component chưa được include
   - `Cannot read property...` → JavaScript có lỗi
   - CORS errors → FastAPI URL không đúng

### Bước 4: Kiểm tra network
1. Mở Developer Tools → Network tab
2. Reload trang
3. Kiểm tra có file CSS/JS nào bị 404 không

### Bước 5: Test thủ công
Thêm vào bất kỳ trang nào để test:
```html
<div id="chatbot-widget" style="position:fixed;bottom:20px;right:20px;z-index:99999;width:60px;height:60px;background:#D4AF37;border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-size:24px;cursor:pointer;">
    <i class="fa fa-comments"></i>
</div>
```

## 📋 Checklist:

- [ ] File `chatbot-widget.blade.php` tồn tại
- [ ] File được include trong `master.blade.php`
- [ ] Có `@stack('scripts')` trong layout
- [ ] Có `@stack('styles')` trong head
- [ ] Không có lỗi JavaScript trong console
- [ ] CSS được load (kiểm tra trong Network tab)
- [ ] Z-index đủ cao (99999)
- [ ] Widget không bị ẩn bởi element khác

## 🔧 Quick Fix:

Nếu vẫn không thấy, thử thêm trực tiếp vào layout:

```blade
<!-- Test widget - thêm vào cuối body -->
<div id="chatbot-widget" style="position:fixed!important;bottom:20px!important;right:20px!important;z-index:99999!important;width:60px;height:60px;background:#D4AF37;border-radius:50%;display:flex!important;align-items:center;justify-content:center;color:white;font-size:24px;cursor:pointer;box-shadow:0 4px 12px rgba(0,0,0,0.3);">
    <i class="fa fa-comments"></i>
</div>
```

Nếu button này hiển thị → Vấn đề là ở component hoặc CSS
Nếu button này không hiển thị → Vấn đề là ở layout hoặc cache

## 📞 Nếu vẫn không được:

1. Kiểm tra logs: `docker logs shop_nginx -f`
2. Kiểm tra Laravel logs: `docker exec shop_app tail -f storage/logs/laravel.log`
3. Kiểm tra view cache: `docker exec shop_app php artisan view:clear`

