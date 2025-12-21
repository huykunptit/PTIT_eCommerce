# Swagger Annotations Guide

## Đã thêm annotations cho:

### ✅ Authentication Controller
- `POST /api/auth/login` - Đăng nhập
- `POST /api/auth/register` - Đăng ký
- `POST /api/auth/logout` - Đăng xuất
- `GET /api/auth/profile` - Lấy profile

### ✅ Chatbot Controller
- `POST /api/chatbot/message` - Gửi tin nhắn
- `GET /api/chatbot/system-data` - Lấy dữ liệu hệ thống

## Cần thêm annotations cho:

### Cart Controller (API endpoints)
Thêm vào `app/Http/Controllers/CartController.php`:

```php
/**
 * @OA\Post(
 *     path="/api/cart/add",
 *     summary="Thêm sản phẩm vào giỏ hàng",
 *     tags={"Cart"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"product_id"},
 *             @OA\Property(property="product_id", type="integer", example=1),
 *             @OA\Property(property="quantity", type="integer", example=1),
 *             @OA\Property(property="variant_id", type="integer", nullable=true, example=1)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Thêm thành công",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Đã thêm vào giỏ hàng"),
 *             @OA\Property(property="cart_count", type="integer", example=3),
 *             @OA\Property(property="cart_total", type="string", example="1.500.000₫")
 *         )
 *     )
 * )
 */
```

### Product Controller (nếu có API)
```php
/**
 * @OA\Get(
 *     path="/api/products",
 *     summary="Lấy danh sách sản phẩm",
 *     tags={"Products"},
 *     @OA\Parameter(
 *         name="page",
 *         in="query",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Parameter(
 *         name="per_page",
 *         in="query",
 *         @OA\Schema(type="integer", example=20)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Danh sách sản phẩm",
 *         @OA\JsonContent(
 *             @OA\Property(property="data", type="array", @OA\Items(type="object")),
 *             @OA\Property(property="current_page", type="integer"),
 *             @OA\Property(property="total", type="integer")
 *         )
 *     )
 * )
 */
```

### Order Controller
```php
/**
 * @OA\Get(
 *     path="/api/orders",
 *     summary="Lấy danh sách đơn hàng",
 *     tags={"Orders"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Danh sách đơn hàng",
 *         @OA\JsonContent(
 *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
 *         )
 *     )
 * )
 */
```

## Security Scheme

Đảm bảo đã thêm vào `app/Swagger/OpenApi.php`:

```php
/**
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Nhập token từ đăng nhập"
 * )
 */
```

## Generate Documentation

Sau khi thêm annotations:

```bash
docker exec shop_app php artisan l5-swagger:generate
```

Truy cập: `http://localhost:8082/api/documentation`

## Auto-generate

Để tự động generate mỗi khi có thay đổi, thêm vào `.env`:

```env
L5_SWAGGER_GENERATE_ALWAYS=true
```

