# API Documentation & Chatbot Setup

## 🚀 Quick Start

### 1. Swagger/OpenAPI Documentation

#### Đã setup:
- ✅ L5 Swagger package installed
- ✅ Base OpenAPI configuration
- ✅ Annotations cho Authentication & Chatbot APIs
- ✅ Swagger UI available tại: `http://localhost:8082/api/documentation`

#### Generate docs:
```bash
docker exec shop_app php artisan l5-swagger:generate
```

#### Auto-generate (thêm vào `.env`):
```env
L5_SWAGGER_GENERATE_ALWAYS=true
```

### 2. FastAPI Chatbot Service

#### Setup:
```bash
# 1. Thêm OpenAI API key vào .env
echo "OPENAI_API_KEY=sk-your-key-here" >> .env
echo "OPENAI_MODEL=gpt-3.5-turbo" >> .env

# 2. Build và start FastAPI service
docker compose build fastapi
docker compose up -d fastapi

# 3. Check service
curl http://localhost:8001/health
curl http://localhost:8001/chatbot/health
```

#### Test Chatbot:
```bash
# Lấy token
TOKEN=$(curl -X POST http://localhost:8082/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","password":"password"}' \
  | jq -r '.token')

# Gửi tin nhắn
curl -X POST http://localhost:8082/api/chatbot/message \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"message": "Có bao nhiêu sản phẩm?"}'
```

## 📋 Kiến trúc Chatbot

```
┌─────────────┐
│   Client    │
│  (Browser)  │
└──────┬──────┘
       │
       │ HTTP/WebSocket
       ▼
┌─────────────────┐
│  Laravel API    │
│ /api/chatbot/*  │
└──────┬──────────┘
       │
       │ HTTP
       ▼
┌─────────────────┐
│  FastAPI        │
│  Chatbot Service│
│  (OpenAI)       │
└──────┬──────────┘
       │
       │ Query
       ▼
┌─────────────────┐
│  Laravel DB     │
│  (Products,     │
│   Orders, etc)  │
└─────────────────┘
```

## 🔧 Tính năng Chatbot

### 1. Đọc dữ liệu hệ thống
- Tổng số sản phẩm
- Số sản phẩm có sẵn
- Tổng số đơn hàng
- Đơn hàng của user

### 2. Trả lời thông minh
- Hỏi về số lượng, giá cả
- Hỗ trợ đặt hàng
- Tra cứu đơn hàng
- Tư vấn sản phẩm

### 3. Real-time Chat
- WebSocket endpoint: `ws://localhost:8001/chatbot/ws`
- Không cần polling
- Conversation context được giữ

## 📝 API Endpoints

### Swagger UI
- URL: `http://localhost:8082/api/documentation`
- Có thể test trực tiếp trên UI

### Chatbot APIs
- `POST /api/chatbot/message` - HTTP chat
- `GET /api/chatbot/system-data` - System data
- `WS /chatbot/ws` - WebSocket chat (port 8001)

## 🎯 Next Steps

1. **Thêm Swagger annotations** cho các controllers còn lại:
   - CartController
   - ProductController  
   - OrderController
   - AdminController

2. **Mở rộng Chatbot**:
   - Thêm LangChain cho RAG
   - Kết nối trực tiếp database
   - Thêm memory/conversation history

3. **Frontend Integration**:
   - Tạo chat widget
   - Kết nối WebSocket
   - UI/UX cho chatbot

## 📚 Documentation Files

- `SWAGGER_ANNOTATIONS.md` - Hướng dẫn thêm annotations
- `CHATBOT_SETUP.md` - Chi tiết setup chatbot
- `API_DOCUMENTATION.md` - API documentation (nếu có)

## ⚠️ Lưu ý

1. **OpenAI API Key**: Cần có key hợp lệ để chatbot hoạt động
2. **Chi phí**: GPT-3.5-turbo ~$0.002/1K tokens
3. **Rate Limits**: Có thể cần implement rate limiting
4. **Security**: Đảm bảo validate input từ user

