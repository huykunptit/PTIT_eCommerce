"""
Chatbot AI Service sử dụng Gemini (ưu tiên) hoặc OpenAI làm fallback.
Đọc dữ liệu từ Laravel (số lượng, giá, đơn hàng) và trả lời khách hàng.
"""
import os
import json
from typing import Optional, Dict, Any
from fastapi import APIRouter, WebSocket, WebSocketDisconnect, HTTPException
from pydantic import BaseModel
import httpx

router = APIRouter(prefix="/chatbot", tags=["Chatbot"])

# Prefer nginx (HTTP) over php-fpm port (not HTTP)
LARAVEL_URL = os.getenv("LARAVEL_BASE_URL") or os.getenv("LARAVEL_URL", "http://shop_nginx")

# Gemini configuration
gemini_model = None
gemini_configured = False
try:
    gemini_api_key = os.getenv("GEMINI_API_KEY")
    if gemini_api_key:
        import google.generativeai as genai

        genai.configure(api_key=gemini_api_key)
        gemini_model_name = os.getenv("GEMINI_MODEL", "gemini-2.5-flash")
        gemini_model = genai.GenerativeModel(gemini_model_name)
        gemini_configured = True
except Exception as e:  # pragma: no cover - chỉ để debug cấu hình
    print(f"[Chatbot] Gemini init error: {e}")
    gemini_model = None
    gemini_configured = False

# OpenAI fallback (nếu vẫn muốn dùng)
openai_client = None
openai_configured = False
try:
    from openai import OpenAI  # type: ignore

    if os.getenv("OPENAI_API_KEY"):
        openai_client = OpenAI(api_key=os.getenv("OPENAI_API_KEY"))
        openai_configured = True
except Exception as e:  # pragma: no cover
    print(f"[Chatbot] OpenAI init error: {e}")
    openai_client = None
    openai_configured = False


class ChatMessage(BaseModel):
    message: str
    conversation_id: Optional[str] = None
    user_id: Optional[int] = None
    system_data: Optional[Dict[str, Any]] = None


class ChatResponse(BaseModel):
    response: str
    conversation_id: str


def build_system_prompt(system_data: Optional[Dict[str, Any]] = None) -> str:
    """Xây dựng system prompt với dữ liệu hệ thống"""
    base_prompt = """Bạn là chatbot hỗ trợ khách hàng của một cửa hàng thương mại điện tử.
Nhiệm vụ của bạn là:
1. Trả lời câu hỏi về sản phẩm, giá cả, số lượng tồn kho
2. Hỗ trợ đặt hàng và thanh toán
3. Cung cấp thông tin về đơn hàng
4. Trả lời bằng tiếng Việt, thân thiện và chuyên nghiệp

"""
    
    if system_data:
        prompt = base_prompt + f"""
Dữ liệu hệ thống hiện tại:
- Tổng số sản phẩm: {system_data.get('total_products', 0)}
- Sản phẩm có sẵn: {system_data.get('available_products', 0)}
- Tổng số đơn hàng: {system_data.get('total_orders', 0)}
"""
        if system_data.get('user_orders'):
            prompt += f"- Số đơn hàng của bạn: {system_data.get('user_orders', 0)}\n"
        
        prompt += "\nHãy sử dụng thông tin này để trả lời chính xác các câu hỏi của khách hàng."
    else:
        prompt = base_prompt
    
    return prompt


async def get_product_info_from_laravel(query: str) -> Optional[Dict]:
    """Lấy thông tin sản phẩm từ Laravel API"""
    try:
        async with httpx.AsyncClient(timeout=10.0) as client:
            # Tìm kiếm sản phẩm
            response = await client.get(
                f"{LARAVEL_URL}/api/products",
                params={"search": query, "limit": 5}
            )
            if response.status_code == 200:
                return response.json()
    except Exception as e:
        print(f"Error fetching product info: {e}")
    return None


@router.post("/chat", response_model=ChatResponse)
async def chat(message: ChatMessage):
    """
    Xử lý tin nhắn từ khách hàng và trả lời bằng AI
    """
    if not (gemini_configured or openai_configured):
        return ChatResponse(
            response="Xin lỗi, dịch vụ AI chưa được cấu hình (Gemini/OpenAI). Vui lòng liên hệ admin.",
            conversation_id=message.conversation_id or "default",
        )

    # Xây dựng system prompt với dữ liệu hệ thống
    system_prompt = build_system_prompt(message.system_data)
    
    # Lấy thông tin sản phẩm nếu cần
    product_info = None
    if any(keyword in message.message.lower() for keyword in ['sản phẩm', 'giá', 'mua', 'có']):
        product_info = await get_product_info_from_laravel(message.message)
    
    # Xây dựng user message với context
    user_message = message.message
    if product_info:
        user_message += f"\n\nThông tin sản phẩm từ hệ thống: {json.dumps(product_info, ensure_ascii=False)}"
    
    try:
        # Ưu tiên Gemini nếu đã cấu hình
        if gemini_configured and gemini_model is not None:
            full_prompt = (
                system_prompt
                + "\n\n---\n"
                + "Khách hàng hỏi:\n"
                + user_message
                + "\n\nHãy trả lời ngắn gọn, dễ hiểu, bằng tiếng Việt, tập trung vào thông tin trong hệ thống."
            )
            result = gemini_model.generate_content(full_prompt)
            response_text = getattr(result, "text", None) or (
                result.candidates[0].content.parts[0].text
                if getattr(result, "candidates", None)
                else "Xin lỗi, tôi chưa thể trả lời câu hỏi này."
            )
        elif openai_configured and openai_client is not None:
            completion = openai_client.chat.completions.create(
                model=os.getenv("OPENAI_MODEL", "gpt-3.5-turbo"),
                messages=[
                    {"role": "system", "content": system_prompt},
                    {"role": "user", "content": user_message},
                ],
                temperature=0.7,
                max_tokens=500,
            )
            response_text = completion.choices[0].message.content
        else:
            return ChatResponse(
                response="Xin lỗi, dịch vụ AI chưa được cấu hình chính xác.",
                conversation_id=message.conversation_id or "default",
            )

        return ChatResponse(
            response=response_text,
            conversation_id=message.conversation_id or f"conv_{message.user_id or 'guest'}",
        )
    except Exception as e:
        # Không trả lỗi chi tiết ra cho end-user (tránh lộ thông tin cấu hình/API provider).
        print(f"[Chatbot] AI provider error: {e}")
        return ChatResponse(
            response="Xin lỗi, dịch vụ AI đang gặp sự cố. Vui lòng thử lại sau hoặc liên hệ admin.",
            conversation_id=message.conversation_id or "default",
        )


@router.websocket("/ws")
async def websocket_chat(websocket: WebSocket):
    """
    WebSocket endpoint cho chat real-time
    """
    await websocket.accept()
    conversation_id = None
    
    try:
        while True:
            data = await websocket.receive_json()
            message = data.get("message", "")
            conversation_id = data.get("conversation_id", conversation_id or f"ws_{id(websocket)}")
            user_id = data.get("user_id")
            system_data = data.get("system_data")
            
            if not message:
                await websocket.send_json({"error": "Message is required"})
                continue
            
            # Xử lý message tương tự như HTTP endpoint
            chat_message = ChatMessage(
                message=message,
                conversation_id=conversation_id,
                user_id=user_id,
                system_data=system_data
            )
            
            response = await chat(chat_message)
            await websocket.send_json({
                "response": response.response,
                "conversation_id": response.conversation_id
            })
            
    except WebSocketDisconnect:
        print(f"WebSocket disconnected: {conversation_id}")
    except Exception as e:
        print(f"WebSocket error: {e}")
        await websocket.close()


@router.get("/health")
async def health_check():
    """Health check cho chatbot service"""
    return {
        "status": "healthy",
        "gemini_configured": gemini_configured,
        "openai_configured": openai_configured,
        "laravel_url": LARAVEL_URL,
    }

