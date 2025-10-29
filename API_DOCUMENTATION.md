# Admin API Documentation

## Base URL
```
http://localhost:8080/api
```

## Authentication
Tất cả API endpoints yêu cầu authentication với Bearer token.

### Headers
```
Authorization: Bearer {your_token}
Content-Type: application/json
Accept: application/json
```

## Authentication Endpoints

### Login
```http
POST /api/auth/login
```

**Request Body:**
```json
{
    "email": "admin@shop.com",
    "password": "admin123"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Login successful",
    "user": {
        "id": 1,
        "name": "Admin",
        "email": "admin@shop.com",
        "role": "admin"
    },
    "token": "1|abc123..."
}
```

### Register
```http
POST /api/auth/register
```

**Request Body:**
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "phone_number": "0123456789",
    "address": "123 Street, City"
}
```

### Logout
```http
POST /api/auth/logout
```

### Get Profile
```http
GET /api/auth/profile
```

## Admin Dashboard Endpoints

### Get Dashboard Stats
```http
GET /api/admin/dashboard
```

**Response:**
```json
{
    "success": true,
    "data": {
        "total_users": 10,
        "total_products": 25,
        "total_categories": 5,
        "total_orders": 15,
        "recent_orders": [...],
        "recent_users": [...]
    }
}
```

### Get Statistics
```http
GET /api/admin/statistics
```

## Users Management

### Get All Users
```http
GET /api/admin/users?search=john&per_page=10
```

### Get User by ID
```http
GET /api/admin/users/{id}
```

### Create User
```http
POST /api/admin/users
```

**Request Body:**
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "phone_number": "0123456789",
    "address": "123 Street, City"
}
```

### Update User
```http
PUT /api/admin/users/{id}
```

**Request Body:**
```json
{
    "name": "John Doe Updated",
    "email": "john.updated@example.com",
    "phone_number": "0123456789",
    "address": "456 New Street, City"
}
```

### Delete User
```http
DELETE /api/admin/users/{id}
```

## Categories Management

### Get All Categories
```http
GET /api/admin/categories?search=electronics&per_page=10
```

### Get Category by ID
```http
GET /api/admin/categories/{id}
```

### Create Category
```http
POST /api/admin/categories
```

**Request Body:**
```json
{
    "name": "Electronics",
    "description": "Electronic devices and gadgets"
}
```

### Update Category
```http
PUT /api/admin/categories/{id}
```

**Request Body:**
```json
{
    "name": "Electronics Updated",
    "description": "Updated description"
}
```

### Delete Category
```http
DELETE /api/admin/categories/{id}
```

## Products Management

### Get All Products
```http
GET /api/admin/products?search=phone&category_id=1&per_page=10
```

### Get Product by ID
```http
GET /api/admin/products/{id}
```

### Create Product
```http
POST /api/admin/products
```

**Request Body:**
```json
{
    "name": "iPhone 15",
    "description": "Latest iPhone model",
    "price": 25000000,
    "stock": 50,
    "category_id": 1,
    "image": "iphone15.jpg"
}
```

### Update Product
```http
PUT /api/admin/products/{id}
```

**Request Body:**
```json
{
    "name": "iPhone 15 Pro",
    "description": "Updated description",
    "price": 30000000,
    "stock": 30,
    "category_id": 1,
    "image": "iphone15pro.jpg"
}
```

### Delete Product
```http
DELETE /api/admin/products/{id}
```

## Orders Management

### Get All Orders
```http
GET /api/admin/orders?status=pending&search=john&per_page=10
```

### Get Order by ID
```http
GET /api/admin/orders/{id}
```

### Update Order Status
```http
PUT /api/admin/orders/{id}/status
```

**Request Body:**
```json
{
    "status": "processing"
}
```

**Available Status Values:**
- `pending`
- `processing`
- `shipped`
- `delivered`
- `cancelled`

## Response Format

### Success Response
```json
{
    "success": true,
    "message": "Operation completed successfully",
    "data": {...}
}
```

### Error Response
```json
{
    "success": false,
    "message": "Error description",
    "errors": {
        "field": ["Error message"]
    }
}
```

## HTTP Status Codes

- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Validation Error
- `500` - Server Error

## Query Parameters

### Pagination
- `per_page` - Number of items per page (default: 10)

### Search
- `search` - Search term for filtering results

### Filtering
- `category_id` - Filter products by category
- `status` - Filter orders by status

## Example Usage with cURL

### Login
```bash
curl -X POST http://localhost:8080/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@shop.com",
    "password": "admin123"
  }'
```

### Get Users with Token
```bash
curl -X GET http://localhost:8080/api/admin/users \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"
```

### Create Category
```bash
curl -X POST http://localhost:8080/api/admin/categories \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Electronics",
    "description": "Electronic devices"
  }'
``` 