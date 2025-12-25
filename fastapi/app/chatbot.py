"""
Chatbot AI Service sử dụng Gemini (ưu tiên) hoặc OpenAI làm fallback.
Đọc dữ liệu từ Laravel (số lượng, giá, đơn hàng) và trả lời khách hàng.
"""
import os
import json
import logging
from typing import Optional, Dict, Any
from fastapi import APIRouter, WebSocket, WebSocketDisconnect
from pydantic import BaseModel
import httpx

router = APIRouter(prefix="/chatbot", tags=["Chatbot"])

logger = logging.getLogger("ptit.chatbot")

# Prefer nginx (HTTP) over php-fpm port (not HTTP)
LARAVEL_URL = os.getenv("LARAVEL_BASE_URL") or os.getenv("LARAVEL_URL", "http://shop_nginx")

# Gemini configuration
gemini_model = None
gemini_configured = False
gemini_model_name = os.getenv("GEMINI_MODEL", "gemini-2.5-flash")
try:
    gemini_api_key = os.getenv("GEMINI_API_KEY")
    if gemini_api_key:
        import google.generativeai as genai

        genai.configure(api_key=gemini_api_key)
        # Model object is created lazily at request time so we can retry with fallbacks.
        gemini_model = None
        gemini_configured = True
except Exception as e:  # pragma: no cover - chỉ để debug cấu hình
    logger.exception("[Chatbot] Gemini init error: %s", e)
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
    logger.exception("[Chatbot] OpenAI init error: %s", e)
    openai_client = None
    openai_configured = False


class ChatMessage(BaseModel):
    message: str
    conversation_id: Optional[str] = None
    user_id: Optional[int] = None
    system_data: Optional[Dict[str, Any]] = None
    debug: Optional[bool] = None


class ChatResponse(BaseModel):
    response: str
    conversation_id: str
    debug: Optional[Dict[str, Any]] = None


def _is_debug_enabled(debug_flag: Optional[bool]) -> bool:
    if debug_flag is True:
        return True
    env_flag = (os.getenv("CHATBOT_DEBUG") or "").strip().lower()
    return env_flag in {"1", "true", "yes", "on"}


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
        logger.warning("Error fetching product info: %s", e)
    return None


def _extract_gemini_text(result: Any) -> str:
    text = getattr(result, "text", None)
    if isinstance(text, str) and text.strip():
        return text.strip()
    try:
        return (
            result.candidates[0].content.parts[0].text
            if getattr(result, "candidates", None)
            else ""
        ).strip()
    except Exception:
        return ""


def _fallback_response(user_message: str, system_data: Optional[Dict[str, Any]]) -> str:
    msg = (user_message or "").lower()
    system_data = system_data or {}
    total_products = int(system_data.get("total_products") or 0)
    available_products = int(system_data.get("available_products") or 0)
    total_orders = int(system_data.get("total_orders") or 0)
    user_orders = int(system_data.get("user_orders") or 0)

    if any(k in msg for k in ["bao nhiêu", "tổng", "số lượng", "có bao nhiêu", "còn"]):
        if "sản phẩm" in msg:
            if total_products or available_products:
                return (
                    f"Hiện tại shop có {total_products} sản phẩm, trong đó {available_products} sản phẩm đang có sẵn. "
                    "Bạn muốn mình gợi ý theo loại/giá không?"
                )
        if "đơn" in msg or "đơn hàng" in msg:
            if total_orders or user_orders:
                return (
                    f"Tổng số đơn hàng trên hệ thống là {total_orders}. "
                    f"Bạn hiện có {user_orders} đơn hàng."
                )

    return (
        "Mình đang gặp lỗi khi gọi dịch vụ AI nên chưa trả lời chính xác được. "
        "Bạn thử hỏi lại sau ít phút hoặc hỏi theo dạng: 'Có bao nhiêu sản phẩm đang có sẵn?'"
    )


@router.post("/chat", response_model=ChatResponse)
async def chat(message: ChatMessage):
    """
    Xử lý tin nhắn từ khách hàng và trả lời bằng AI
    """
    debug_enabled = _is_debug_enabled(message.debug)

    if not (gemini_configured or openai_configured):
        return ChatResponse(
            response="Xin lỗi, dịch vụ AI chưa được cấu hình (Gemini). Vui lòng liên hệ admin.",
            conversation_id=message.conversation_id or "default",
            debug={
                "reason": "no_provider_configured",
                "gemini_configured": gemini_configured,
                "openai_configured": openai_configured,
            }
            if debug_enabled
            else None,
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
        if gemini_configured:
            full_prompt = (
                system_prompt
                + "\n\n---\n"
                + "Khách hàng hỏi:\n"
                + user_message
                + "\n\nHãy trả lời ngắn gọn, dễ hiểu, bằng tiếng Việt, tập trung vào thông tin trong hệ thống."
            )
            try:
                import google.generativeai as genai
                from google.api_core.exceptions import NotFound, PermissionDenied  # type: ignore

                # Try configured model first, then common fallbacks.
                candidates = []
                for name in [gemini_model_name, "gemini-1.5-flash", "gemini-1.5-pro"]:
                    name = (name or "").strip()
                    if name and name not in candidates:
                        candidates.append(name)

                last_error: Optional[Exception] = None
                response_text = ""
                for model_name in candidates:
                    try:
                        model = genai.GenerativeModel(model_name)
                        result = model.generate_content(full_prompt)
                        response_text = _extract_gemini_text(result)
                        if response_text:
                            break
                    except PermissionDenied as e:
                        logger.warning("[Chatbot] Gemini PermissionDenied (model=%s): %s", model_name, e)
                        return ChatResponse(
                            response=(
                                "Xin lỗi, dịch vụ AI chưa hoạt động do API key Gemini bị từ chối. "
                                "Vui lòng tạo API key mới và cập nhật cấu hình."
                            ),
                            conversation_id=message.conversation_id or "default",
                            debug={
                                "provider": "gemini",
                                "model": model_name,
                                "error_type": type(e).__name__,
                                "error": str(e),
                            }
                            if debug_enabled
                            else None,
                        )
                    except NotFound as e:
                        # Model name may be invalid or not available for this API key.
                        logger.warning("[Chatbot] Gemini model not found (model=%s): %s", model_name, e)
                        last_error = e
                        continue
                    except Exception as e:
                        logger.exception("[Chatbot] Gemini error (model=%s): %s", model_name, e)
                        last_error = e
                        continue

                if not response_text:
                    # Keep chatbot usable even if AI provider fails.
                    logger.warning("[Chatbot] Gemini failed; returning fallback response. last_error=%s", last_error)
                    response_text = _fallback_response(message.message, message.system_data)
            except Exception as e:
                # Keep chatbot usable even if provider blows up unexpectedly.
                logger.exception("[Chatbot] Gemini unexpected error: %s", e)
                response_text = _fallback_response(message.message, message.system_data)
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
        logger.exception("[Chatbot] AI provider error: %s", e)
        return ChatResponse(
            response="Xin lỗi, dịch vụ AI đang gặp sự cố. Vui lòng thử lại sau hoặc liên hệ admin.",
            conversation_id=message.conversation_id or "default",
            debug={
                "error_type": type(e).__name__,
                "error": str(e),
            }
            if debug_enabled
            else None,
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
        logger.info("WebSocket disconnected: %s", conversation_id)
    except Exception as e:
        logger.exception("WebSocket error: %s", e)
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

