# Chatbot AI Setup Guide

## Tổng quan

Hệ thống chatbot AI được xây dựng với kiến trúc:
- **Laravel Backend**: Controller xử lý request, lấy dữ liệu hệ thống
- **FastAPI Service**: Xử lý AI với OpenAI/LangChain, WebSocket cho real-time chat
- **WebSocket**: Hỗ trợ chat real-time

## Cài đặt

### 1. Cấu hình Environment Variables

Thêm vào `.env`:

```env
# FastAPI Service URL
FASTAPI_URL=http://fastapi:8001

# OpenAI Configuration (bắt buộc cho chatbot)
OPENAI_API_KEY=sk-your-openai-api-key-here
OPENAI_MODEL=gpt-3.5-turbo  # hoặc gpt-4, gpt-4-turbo
```

### 2. Build và chạy FastAPI service

```bash
# Build FastAPI container
docker compose build fastapi

# Start service
docker compose up -d fastapi

# Check logs
docker logs shop_fastapi -f
```

### 3. Test Chatbot

#### HTTP API:
```bash
# Lấy token từ login
TOKEN=$(curl -X POST http://localhost:8082/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","password":"password"}' \
  | jq -r '.token')

# Gửi tin nhắn đến chatbot
curl -X POST http://localhost:8082/api/chatbot/message \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "message": "Có bao nhiêu sản phẩm đang có sẵn?",
    "conversation_id": "conv_123"
  }'
```

#### WebSocket (JavaScript):
```javascript
const ws = new WebSocket('ws://localhost:8001/chatbot/ws');

ws.onopen = () => {
  ws.send(JSON.stringify({
    message: "Xin chào, bạn có thể giúp gì?",
    conversation_id: "conv_123",
    user_id: 1,
    system_data: {
      total_products: 150,
      available_products: 120
    }
  }));
};

ws.onmessage = (event) => {
  const data = JSON.parse(event.data);
  console.log('Bot:', data.response);
};
```

## Tính năng

### 1. Đọc dữ liệu hệ thống
Chatbot tự động lấy:
- Tổng số sản phẩm
- Số sản phẩm có sẵn
- Tổng số đơn hàng
- Số đơn hàng của user (nếu đã login)

### 2. Trả lời thông minh
- Hỏi về số lượng sản phẩm
- Hỏi về giá cả
- Hỗ trợ đặt hàng
- Tra cứu đơn hàng

### 3. Real-time Chat
WebSocket hỗ trợ chat real-time không cần polling

## API Endpoints

### Laravel API

- `POST /api/chatbot/message` - Gửi tin nhắn
- `GET /api/chatbot/system-data` - Lấy dữ liệu hệ thống

### FastAPI Service

- `POST /chatbot/chat` - HTTP chat endpoint
- `WS /chatbot/ws` - WebSocket chat endpoint
- `GET /chatbot/health` - Health check

## Tùy chỉnh

### Thay đổi AI Model

Trong `.env`:
```env
OPENAI_MODEL=gpt-4-turbo  # hoặc model khác
```

### Thêm LangChain

Có thể thay thế OpenAI client bằng LangChain để:
- Kết nối nhiều LLM providers
- RAG (Retrieval Augmented Generation)
- Memory management tốt hơn

### Tích hợp với Database

FastAPI có thể query trực tiếp database nếu cần:
```python
# Thêm database connection
from sqlalchemy import create_engine
engine = create_engine("mysql://user:pass@mysql:3306/shop")
```

## Troubleshooting

### Chatbot không phản hồi
1. Kiểm tra `OPENAI_API_KEY` đã được set
2. Kiểm tra FastAPI service đang chạy: `docker ps | grep fastapi`
3. Xem logs: `docker logs shop_fastapi`

### WebSocket không kết nối
1. Kiểm tra port 8001 đã được expose
2. Kiểm tra firewall/network settings
3. Test với wscat: `wscat -c ws://localhost:8001/chatbot/ws`

## Chi phí

OpenAI API pricing (tính đến 2024):
- GPT-3.5-turbo: ~$0.002 per 1K tokens
- GPT-4: ~$0.03 per 1K tokens

Ước tính: 1000 tin nhắn/ngày với GPT-3.5-turbo ≈ $5-10/tháng

