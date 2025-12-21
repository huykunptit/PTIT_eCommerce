# Chatbot Widget Guide

## Tổng quan

Chatbot widget đã được tích hợp vào frontend, hiển thị ở góc dưới bên phải mọi trang web.

## Tính năng

### ✅ Đã có:
- **UI đẹp, responsive**: Thiết kế hiện đại, tương thích mobile
- **WebSocket support**: Chat real-time không cần polling
- **HTTP fallback**: Tự động chuyển sang HTTP nếu WebSocket lỗi
- **Auto token management**: Tự động lấy và lưu token
- **Typing indicator**: Hiển thị khi bot đang trả lời
- **Message history**: Lưu lịch sử trong session
- **Minimize/Close**: Có thể thu nhỏ hoặc đóng widget

### 🎨 UI Features:
- Gradient button với animation
- Badge thông báo tin nhắn mới
- Avatar cho bot và user
- Timestamp cho mỗi tin nhắn
- Smooth animations

## Cách sử dụng

### 1. Widget tự động hiển thị
Widget đã được include trong `frontend/layouts/master.blade.php`, tự động hiển thị trên mọi trang.

### 2. Khi user chưa đăng nhập
- Widget vẫn hiển thị
- Có thể chat nhưng không có context về đơn hàng của user
- System data vẫn được lấy (tổng số sản phẩm, etc.)

### 3. Khi user đã đăng nhập
- Tự động lấy token từ API
- Có context về đơn hàng của user
- WebSocket tự động kết nối khi mở widget

## Cấu hình

### Environment Variables
```env
FASTAPI_URL=http://fastapi:8001  # URL của FastAPI service
```

### Token Management
- Token được lưu trong `localStorage` với key `chatbot_token`
- Tự động refresh khi hết hạn
- Token có scope `chatbot:use` để bảo mật

## Customization

### Thay đổi màu sắc
Trong `chatbot-widget.blade.php`, tìm và thay đổi:
```css
background: linear-gradient(135deg, #D4AF37 0%, #B8941F 100%);
```
Thay `#D4AF37` và `#B8941F` bằng màu bạn muốn.

### Thay đổi vị trí
```css
.chatbot-widget {
    bottom: 20px;  /* Khoảng cách từ dưới */
    right: 20px;  /* Khoảng cách từ phải */
}
```

### Thay đổi kích thước
```css
.chatbot-window {
    width: 380px;   /* Chiều rộng */
    height: 600px;  /* Chiều cao */
}
```

## API Endpoints sử dụng

### 1. Lấy token
```
GET /api/chatbot/token
Headers: Cookie (session-based auth)
Response: { "token": "1|xxxxx" }
```

### 2. Gửi tin nhắn (HTTP)
```
POST /api/chatbot/message
Headers: Authorization: Bearer {token}
Body: {
    "message": "Câu hỏi",
    "conversation_id": "conv_123",
    "system_data": {...}
}
```

### 3. Lấy system data
```
GET /api/chatbot/system-data
Headers: Authorization: Bearer {token}
Response: {
    "total_products": 150,
    "available_products": 120,
    ...
}
```

### 4. WebSocket
```
WS ws://localhost:8001/chatbot/ws
Message: {
    "message": "Câu hỏi",
    "conversation_id": "conv_123",
    "user_id": 1,
    "system_data": {...}
}
```

## Troubleshooting

### Widget không hiển thị
1. Kiểm tra đã include component: `@include('components.chatbot-widget')`
2. Kiểm tra console có lỗi JavaScript
3. Kiểm tra CSS có bị conflict

### WebSocket không kết nối
1. Kiểm tra FastAPI service đang chạy: `docker ps | grep fastapi`
2. Kiểm tra `FASTAPI_URL` trong `.env`
3. Widget sẽ tự động fallback sang HTTP API

### Token không lấy được
1. Kiểm tra user đã đăng nhập
2. Kiểm tra route `/api/chatbot/token` có hoạt động
3. Kiểm tra console có lỗi CORS hoặc network

### Bot không trả lời
1. Kiểm tra `OPENAI_API_KEY` đã được set
2. Kiểm tra FastAPI logs: `docker logs shop_fastapi`
3. Kiểm tra Laravel logs: `storage/logs/laravel.log`

## Mobile Optimization

Widget tự động responsive:
- Trên mobile: chiếm toàn bộ chiều rộng màn hình
- Chiều cao: tối đa 90vh
- Touch-friendly buttons

## Security

- Token được lưu trong localStorage (có thể chuyển sang httpOnly cookie)
- CSRF protection cho HTTP requests
- Token có scope riêng `chatbot:use`
- Input validation trên cả client và server

## Next Steps

1. **Thêm conversation history**: Lưu lịch sử chat vào database
2. **File upload**: Cho phép gửi ảnh sản phẩm
3. **Voice input**: Hỗ trợ nhập bằng giọng nói
4. **Multi-language**: Hỗ trợ nhiều ngôn ngữ
5. **Analytics**: Track số lượng tin nhắn, thời gian phản hồi

