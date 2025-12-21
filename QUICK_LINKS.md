# 🔗 Quick Links - Các Tính Năng Chạy Thực Tế

## 📋 Mục Lục
1. [Swagger API Documentation](#1-swagger-api-documentation)
2. [FastAPI Service](#2-fastapi-service)
3. [Chatbot AI](#3-chatbot-ai)
4. [Test Usecases](#4-test-usecases)

---

## 1. Swagger API Documentation

### 🌐 Link Truy Cập:
```
http://localhost:8082/api/documentation
```

### 📝 Mô Tả:
- **Swagger UI** để xem và test tất cả API endpoints
- Có thể test trực tiếp trên browser
- Hiển thị đầy đủ request/response schemas
- Hỗ trợ authentication với Bearer token

### 🚀 Cách Sử Dụng:
1. Mở link trên browser
2. Click "Authorize" để nhập Bearer token (nếu cần)
3. Chọn endpoint muốn test
4. Click "Try it out" và điền thông tin
5. Click "Execute" để test

### 🔑 Lấy Token để Test:
```bash
# Login và lấy token
curl -X POST http://localhost:8082/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "user@example.com",
    "password": "password"
  }'
```

### 📌 Các Endpoints Chính:
- `POST /api/auth/login` - Đăng nhập
- `POST /api/auth/register` - Đăng ký
- `GET /api/auth/profile` - Lấy profile
- `POST /api/chatbot/message` - Gửi tin nhắn chatbot
- `GET /api/chatbot/system-data` - Lấy dữ liệu hệ thống

---

## 2. FastAPI Service

### 🌐 Link Truy Cập:
```
http://localhost:8001
```

### 📝 API Documentation (Swagger):
```
http://localhost:8001/docs
```

### 📝 Alternative Docs (ReDoc):
```
http://localhost:8001/redoc
```

### 🏥 Health Check:
```
http://localhost:8001/health
```

### 🤖 Chatbot Endpoints:
```
# HTTP Chat
POST http://localhost:8001/chatbot/chat

# WebSocket Chat
WS ws://localhost:8001/chatbot/ws

# Health Check
GET http://localhost:8001/chatbot/health
```

### 🚀 Cách Sử Dụng:
1. **Xem Documentation**: Truy cập `http://localhost:8001/docs`
2. **Test Health**: `curl http://localhost:8001/health`
3. **Test Chatbot**: 
   ```bash
   curl -X POST http://localhost:8001/chatbot/chat \
     -H "Content-Type: application/json" \
     -d '{
       "message": "Có bao nhiêu sản phẩm?",
       "conversation_id": "test_123",
       "system_data": {
         "total_products": 150,
         "available_products": 120
       }
     }'
   ```

### ⚙️ Cấu Hình Cần Thiết:
```env
OPENAI_API_KEY=sk-your-key-here
OPENAI_MODEL=gpt-3.5-turbo
LARAVEL_URL=http://shop_app:9000
```

---

## 3. Chatbot AI

### 🌐 Widget trên Website:
```
http://localhost:8082
```
Widget tự động hiển thị ở **góc dưới bên phải** mọi trang.

### 📱 Cách Sử Dụng Widget:
1. Mở bất kỳ trang nào trên website
2. Click vào **button chatbot** (icon comments màu vàng)
3. Nhập câu hỏi vào ô chat
4. Bot sẽ trả lời dựa trên dữ liệu hệ thống

### 🔌 API Endpoints:

#### Lấy Token:
```bash
GET http://localhost:8082/api/chatbot/token
Headers: Cookie (session-based)
```

#### Gửi Tin Nhắn:
```bash
POST http://localhost:8082/api/chatbot/message
Headers: 
  Authorization: Bearer {token}
  Content-Type: application/json
Body:
{
  "message": "Có bao nhiêu sản phẩm?",
  "conversation_id": "conv_123"
}
```

#### Lấy System Data:
```bash
GET http://localhost:8082/api/chatbot/system-data
Headers: Authorization: Bearer {token}
```

### 🧪 Test Chatbot qua cURL:
```bash
# 1. Lấy token
TOKEN=$(curl -X POST http://localhost:8082/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","password":"password"}' \
  | jq -r '.token')

# 2. Gửi tin nhắn
curl -X POST http://localhost:8082/api/chatbot/message \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "message": "Có bao nhiêu sản phẩm đang có sẵn?",
    "conversation_id": "test_123"
  }'
```

### 🌐 WebSocket Test (JavaScript):
```javascript
const ws = new WebSocket('ws://localhost:8001/chatbot/ws');

ws.onopen = () => {
  ws.send(JSON.stringify({
    message: "Xin chào!",
    conversation_id: "ws_test",
    user_id: 1,
    system_data: { total_products: 150 }
  }));
};

ws.onmessage = (event) => {
  const data = JSON.parse(event.data);
  console.log('Bot:', data.response);
};
```

---

## 4. Test Usecases

### 🧪 Chạy Tests:

#### Tất cả Tests:
```bash
docker exec shop_app php artisan test
```

#### Chỉ Feature Tests:
```bash
docker exec shop_app php artisan test --testsuite=Feature
```

#### Test Cụ Thể (Role & Checkout):
```bash
docker exec shop_app php artisan test --filter=RoleAndCheckoutFlowTest
```

#### Test với Coverage:
```bash
docker exec shop_app php artisan test --coverage
```

### 📊 Test Results:

#### ✅ Test Cases Đã Có:
1. **Guest redirect to login** - Guest bị redirect khi vào checkout
2. **User with cart can view checkout** - User có giỏ hàng xem được checkout
3. **Checkout fails when cart empty** - Checkout lỗi khi giỏ trống
4. **COD creates order** - COD tạo order và clear cart
5. **VNPay redirects** - VNPay redirect đến gateway
6. **Validation errors** - Lỗi validation trả về
7. **Admin access** - Admin truy cập được dashboard
8. **Non-admin blocked** - User thường bị chặn admin area
9. **Employee access** - Employee truy cập được dashboard
10. **User blocked from employee** - User thường bị chặn employee area

### 📝 Xem Test Code:
```bash
# File test chính
cat tests/Feature/RoleAndCheckoutFlowTest.php
```

### 🔍 Debug Test:
```bash
# Chạy test với verbose
docker exec shop_app php artisan test --filter=RoleAndCheckoutFlowTest -v

# Chạy test cụ thể
docker exec shop_app php artisan test --filter=test_checkout_cod_creates_order
```

---

## 🚀 Quick Start Commands

### Khởi động tất cả services:
```bash
docker compose up -d
```

### Kiểm tra services đang chạy:
```bash
docker ps
```

### Xem logs:
```bash
# Laravel logs
docker exec shop_app tail -f storage/logs/laravel.log

# FastAPI logs
docker logs shop_fastapi -f

# Nginx logs
docker logs shop_nginx -f
```

### Restart services:
```bash
# Restart Laravel
docker compose restart app

# Restart FastAPI
docker compose restart fastapi

# Restart tất cả
docker compose restart
```

---

## 📋 Checklist Trước Khi Sử Dụng

### ✅ Swagger:
- [ ] Services đang chạy: `docker ps`
- [ ] Truy cập: `http://localhost:8082/api/documentation`
- [ ] Generate docs: `docker exec shop_app php artisan l5-swagger:generate`

### ✅ FastAPI:
- [ ] Service đang chạy: `docker ps | grep fastapi`
- [ ] Truy cập: `http://localhost:8001/docs`
- [ ] Health check: `curl http://localhost:8001/health`
- [ ] Có `OPENAI_API_KEY` trong `.env`

### ✅ Chatbot:
- [ ] Widget hiển thị trên website
- [ ] FastAPI service đang chạy
- [ ] Có `OPENAI_API_KEY` trong `.env`
- [ ] Test: Click widget và gửi tin nhắn

### ✅ Tests:
- [ ] Chạy: `docker exec shop_app php artisan test`
- [ ] Tất cả tests pass
- [ ] Xem kết quả chi tiết

---

## 🆘 Troubleshooting

### Swagger không hiển thị:
```bash
# Generate lại docs
docker exec shop_app php artisan l5-swagger:generate

# Clear cache
docker exec shop_app php artisan config:clear
docker exec shop_app php artisan cache:clear
```

### FastAPI không chạy:
```bash
# Rebuild
docker compose build fastapi
docker compose up -d fastapi

# Check logs
docker logs shop_fastapi
```

### Chatbot không phản hồi:
```bash
# Check FastAPI
curl http://localhost:8001/chatbot/health

# Check OpenAI key
docker exec shop_fastapi env | grep OPENAI

# Check logs
docker logs shop_fastapi -f
```

### Tests fail:
```bash
# Clear test database
docker exec shop_app php artisan migrate:fresh

# Run tests again
docker exec shop_app php artisan test
```

---

## 📞 Support

Nếu gặp vấn đề, kiểm tra:
1. **Logs**: `docker logs {container_name}`
2. **Services**: `docker ps`
3. **Environment**: `.env` file
4. **Ports**: Đảm bảo ports không bị conflict

---

**Last Updated**: {{ date('Y-m-d H:i:s') }}

