import os
from typing import Any, Dict, Optional

import httpx
from fastapi import FastAPI, HTTPException
from pydantic import BaseModel

from .chatbot import router as chatbot_router

BASE_URL = os.getenv("LARAVEL_BASE_URL", "http://shop_nginx")

app = FastAPI(title="PTIT eCommerce API Proxy & Chatbot", version="0.1.0")

# Include chatbot router
app.include_router(chatbot_router)


class LoginRequest(BaseModel):
    login: str
    password: str


class CheckoutRequest(BaseModel):
    token: Optional[str] = None
    payment_method: str
    shipping_name: str
    shipping_phone: str
    shipping_address: str
    shipping_email: Optional[str] = None
    notes: Optional[str] = None


@app.get("/health")
async def health() -> Dict[str, str]:
    return {"status": "ok"}


@app.post("/proxy/login")
async def proxy_login(payload: LoginRequest) -> Any:
    url = f"{BASE_URL}/auth/login"
    async with httpx.AsyncClient(follow_redirects=True) as client:
        resp = await client.post(url, data=payload.model_dump())
    if resp.status_code >= 400:
        raise HTTPException(status_code=resp.status_code, detail=resp.text)
    return {"status": resp.status_code, "cookies": resp.cookies.jar.get_dict()}


@app.get("/proxy/products")
async def proxy_products() -> Any:
    url = f"{BASE_URL}/api/products" if "/api" in BASE_URL else f"{BASE_URL}/api/products"
    async with httpx.AsyncClient() as client:
        resp = await client.get(url)
    if resp.status_code >= 400:
        raise HTTPException(status_code=resp.status_code, detail=resp.text)
    return resp.json()


@app.post("/proxy/checkout")
async def proxy_checkout(payload: CheckoutRequest) -> Any:
    url = f"{BASE_URL}/checkout"
    headers = {}
    if payload.token:
        headers["Authorization"] = f"Bearer {payload.token}"

    async with httpx.AsyncClient(follow_redirects=False) as client:
        resp = await client.post(url, data=payload.model_dump(exclude_none=True), headers=headers)

    return {
        "status": resp.status_code,
        "headers": dict(resp.headers),
        "body": resp.text,
    }

