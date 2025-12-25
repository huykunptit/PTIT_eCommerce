# TÀI LIỆU PHÂN TÍCH VÀ THIẾT KẾ HỆ THỐNG (SYSTEM ANALYSIS & DESIGN)
## PTIT eCommerce – Hệ thống Thương mại Điện tử Trang sức Cao cấp

---

## MỤC LỤC

- [CHƯƠNG 1: TỔNG QUAN](#chương-1-tổng-quan)
- [CHƯƠNG 2: CƠ SỞ LÝ THUYẾT VÀ CÔNG NGHỆ](#chương-2-cơ-sở-lý-thuyết-và-công-nghệ)
- [CHƯƠNG 3: YÊU CẦU HỆ THỐNG](#chương-3-yêu-cầu-hệ-thống)
- [CHƯƠNG 4: THIẾT KẾ HỆ THỐNG](#chương-4-thiết-kế-hệ-thống)
- [CHƯƠNG 5: KẾT QUẢ](#chương-5-kết-quả)
- [CHƯƠNG 6: TỔNG KẾT](#chương-6-tổng-kết)
- [PHỤ LỤC: HƯỚNG DẪN VẼ BIỂU ĐỒ](#phụ-lục-hướng-dẫn-vẽ-biểu-đồ)

---

## CHƯƠNG 1: TỔNG QUAN

### 1.1. Giới thiệu bài toán

PTIT eCommerce là hệ thống thương mại điện tử (mô hình B2C) cho cửa hàng trang sức: khách hàng xem sản phẩm, thêm giỏ hàng, đặt hàng và thanh toán; phía quản trị có dashboard và quản lý dữ liệu vận hành.

### 1.2. Mục tiêu

- Số hóa quy trình bán hàng: sản phẩm → giỏ hàng → checkout → thanh toán.
- Quản trị tập trung: sản phẩm/danh mục/đơn hàng/người dùng.
- Tích hợp thanh toán: VNPay và SePay.
- Tích hợp Chatbot AI tư vấn sản phẩm (microservice).

### 1.3. Đối tượng sử dụng

- Khách hàng
- Admin
- Nhân viên (xử lý đơn theo phân công)

### 1.4. Phạm vi

- Trong phạm vi: website B2C + admin panel; thanh toán VNPay/SePay/COD; chatbot AI.
- Ngoài phạm vi: app mobile native; BI/ERP nâng cao; quản lý kho theo lô/serial.

---

## CHƯƠNG 2: CƠ SỞ LÝ THUYẾT VÀ CÔNG NGHỆ

### 2.1. Cơ sở lý thuyết

#### 2.1.1. MVC trong Laravel

- Model: biểu diễn dữ liệu và quan hệ (Eloquent ORM)
- View: Blade templates
- Controller: xử lý request/response và điều phối nghiệp vụ

#### 2.1.2. REST API & Token Authentication

Hệ thống cung cấp API cho một số nghiệp vụ (auth/cart/orders/admin/chatbot). Xác thực API dùng token theo Laravel Sanctum.

#### 2.1.3. Microservice cho AI Chatbot

Chatbot AI được tách thành FastAPI service để:
- Dễ triển khai/scale độc lập với Laravel
- Dễ thay đổi nhà cung cấp model (Gemini/OpenAI fallback)
- Giảm rủi ro ảnh hưởng đến nghiệp vụ lõi khi AI lỗi

### 2.2. Công nghệ sử dụng

| Thành phần | Công nghệ |
|---|---|
| Backend | Laravel 10.x (PHP 8.1+) |
| Frontend | Blade + Bootstrap + jQuery |
| Database | MySQL |
| Cache/Session | Redis (cấu hình sẵn) |
| Auth API | Laravel Sanctum |
| AI Chatbot | FastAPI (Python) + Gemini API (fallback OpenAI) |
| Payment | VNPay + SePay |
| API Docs | L5-Swagger |
| DevOps | Docker / docker-compose |

### 2.3. Sơ đồ công nghệ (PlantUML)

Bạn có thể copy khối PlantUML dưới đây (từ @startuml đến @enduml) sang planttext.com để render.

@startuml
skinparam packageStyle rectangle

package "Client" {
    [Web Browser] as Browser
}

package "Laravel Monolith" {
    [Blade UI] as Blade
    [REST API] as API
    [Controllers/Services] as App
    [Sanctum] as Sanctum
}

package "Data" {
    database "MySQL" as MySQL
    [Redis] as Redis
}

package "AI Microservice" {
    [FastAPI] as FastAPI
}

cloud "External" {
    [VNPay] as VNPay
    [SePay] as SePay
    [Gemini API] as Gemini
}

Browser --> Blade
Browser --> API
Blade --> App
API --> Sanctum
App --> MySQL
App --> Redis
App --> VNPay
App --> SePay
App --> FastAPI
FastAPI --> Gemini
@enduml

---

## CHƯƠNG 3: YÊU CẦU HỆ THỐNG

### 3.1. Tác nhân (Actors)

- Khách hàng
- Admin
- Nhân viên

### 3.2. Yêu cầu chức năng (Functional Requirements)

| Nhóm | Chức năng chính |
|---|---|
| Xác thực | đăng ký, đăng nhập, đăng xuất, profile |
| Sản phẩm | xem danh sách/chi tiết, tìm kiếm, quản lý (admin) |
| Giỏ hàng | thêm/sửa/xóa item |
| Đơn hàng | tạo đơn, theo dõi trạng thái, phân công xử lý |
| Thanh toán | COD, VNPay (redirect/return), SePay (QR/webhook) |
| Quản trị | dashboard thống kê, quản lý sản phẩm/danh mục/đơn hàng |
| Chatbot AI | hỏi đáp/tư vấn dựa trên dữ liệu hệ thống |

### 3.3. Yêu cầu phi chức năng (Non-functional Requirements)

- Bảo mật: hash mật khẩu; token auth cho API; phân quyền theo role/permissions.
- Hiệu năng: tối ưu truy vấn; có thể tận dụng cache.
- Tin cậy: logging; thanh toán có kiểm tra chữ ký/hash.
- Khả năng mở rộng: tách AI thành microservice.

---

## CHƯƠNG 4: THIẾT KẾ HỆ THỐNG

### 4.1. Kiến trúc tổng thể

Hệ thống áp dụng kiến trúc phân lớp (presentation/business/data) trong Laravel, đồng thời tách Chatbot AI thành microservice FastAPI.

@startuml
skinparam componentStyle uml2

[Web Browser] as Browser

package "Laravel" {
    [Blade UI] as Blade
    [Controllers] as Controllers
    [Services] as Services
    [Eloquent ORM] as ORM
    [REST API] as API
}

database "MySQL" as MySQL
[Redis] as Redis

package "FastAPI" {
    [Chatbot Router] as ChatRouter
    [Chatbot Service] as ChatService
}

cloud "External" {
    [VNPay] as VNPay
    [SePay] as SePay
    [Gemini API] as Gemini
}

Browser --> Blade
Browser --> API
Blade --> Controllers
API --> Controllers
Controllers --> Services
Services --> ORM
ORM --> MySQL
Services --> Redis
Services --> VNPay
Services --> SePay
Controllers --> ChatRouter : HTTP
ChatRouter --> ChatService
ChatService --> Gemini
@enduml

#### 4.1.1. Use Case Diagram (PlantUML)

@startuml
left to right direction
skinparam packageStyle rectangle

actor "Khách hàng" as Customer
actor "Admin" as Admin
actor "Nhân viên" as Employee

rectangle "PTIT eCommerce" {
    usecase "Đăng ký/Đăng nhập" as UCAuth
    usecase "Xem & Tìm kiếm sản phẩm" as UCProduct
    usecase "Giỏ hàng" as UCCart
    usecase "Checkout/Tạo đơn" as UCCheckout
    usecase "Thanh toán VNPay" as UCVNPay
    usecase "Thanh toán SePay" as UCSePay
    usecase "Theo dõi đơn hàng" as UCTrack
    usecase "Chatbot AI" as UCChat

    usecase "Quản lý sản phẩm" as UCAProd
    usecase "Quản lý danh mục" as UCACat
    usecase "Quản lý đơn hàng" as UCAOrd
    usecase "Dashboard thống kê" as UCADash
    usecase "Cập nhật trạng thái/Phân công" as UCEmp
}

Customer --> UCAuth
Customer --> UCProduct
Customer --> UCCart
Customer --> UCCheckout
UCCheckout --> UCVNPay : <<extend>>
UCCheckout --> UCSePay : <<extend>>
Customer --> UCTrack
Customer --> UCChat

Admin --> UCAuth
Admin --> UCAProd
Admin --> UCACat
Admin --> UCAOrd
Admin --> UCADash

Employee --> UCAuth
Employee --> UCAOrd
Employee --> UCEmp
@enduml

### 4.2. Thiết kế cơ sở dữ liệu (Data Dictionary)

Nguồn chuẩn: các file migration trong thư mục database/migrations.

Ký hiệu:
- PK: Primary Key
- FK: Foreign Key
- AI: Auto Increment

#### 4.2.1. roles

| Cột | Kiểu | Ràng buộc | Ghi chú |
|---|---|---|---|
| id | BIGINT | PK, AI | |
| role_name | TEXT | NOT NULL | |
| role_code | TEXT | NOT NULL | |
| created_at | TIMESTAMP | NULL | |
| updated_at | TIMESTAMP | NULL | |

#### 4.2.2. users

| Cột | Kiểu | Ràng buộc | Ghi chú |
|---|---|---|---|
| id | BIGINT | PK, AI | |
| name | VARCHAR(255) | NOT NULL | |
| email | VARCHAR(255) | UNIQUE, NOT NULL | |
| password | VARCHAR(255) | NOT NULL | |
| photo | VARCHAR(255) | NULL | |
| remember_token | VARCHAR(100) | NULL | |
| phone_number | VARCHAR(15) | NULL | |
| address | TEXT | NULL | |
| avatar | VARCHAR(255) | NULL | |
| status | VARCHAR(255) | NOT NULL, DEFAULT 'active' | |
| permissions | JSON | NULL | Quyền chi tiết |
| role_id | BIGINT | FK → roles(id), NULL | onDelete(cascade) |
| created_at | TIMESTAMP | NULL | |
| updated_at | TIMESTAMP | NULL | |

#### 4.2.3. categories

| Cột | Kiểu | Ràng buộc | Ghi chú |
|---|---|---|---|
| id | BIGINT | PK, AI | |
| name | VARCHAR(255) | NOT NULL | |
| image | VARCHAR(255) | NULL | |
| description | TEXT | NULL | |
| parent_category_id | BIGINT | FK → categories(id), NULL | |
| created_at | TIMESTAMP | NULL | |
| updated_at | TIMESTAMP | NULL | |

#### 4.2.4. brands

| Cột | Kiểu | Ràng buộc | Ghi chú |
|---|---|---|---|
| id | BIGINT | PK, AI | |
| title | VARCHAR(255) | NOT NULL | |
| slug | TEXT | NULL | |
| status | VARCHAR(255) | NOT NULL | |
| created_at | TIMESTAMP | NULL | |
| updated_at | TIMESTAMP | NULL | |

#### 4.2.5. products

| Cột | Kiểu | Ràng buộc | Ghi chú |
|---|---|---|---|
| id | BIGINT | PK, AI | |
| name | VARCHAR(255) | NOT NULL | |
| description | TEXT | NOT NULL | |
| price | DECIMAL(15,2) | NOT NULL | |
| quantity | INT | NOT NULL | |
| seller_id | BIGINT | FK → users(id) | |
| category_id | BIGINT | FK → categories(id) | |
| image_url | VARCHAR(255) | NULL | |
| status | ENUM('active','inactive') | NOT NULL, DEFAULT 'active' | |
| created_at | TIMESTAMP | NULL | |
| updated_at | TIMESTAMP | NULL | |

#### 4.2.6. product_variants

| Cột | Kiểu | Ràng buộc | Ghi chú |
|---|---|---|---|
| id | BIGINT | PK, AI | |
| product_id | BIGINT | FK → products(id) | onDelete(cascade), indexed |
| sku | VARCHAR(255) | NULL | |
| attributes | JSON | NOT NULL | ví dụ: {"size":"M","color":"Red"} |
| price | DECIMAL(12,2) | NOT NULL | |
| stock | INT | NOT NULL, DEFAULT 0 | |
| image | VARCHAR(255) | NULL | |
| status | ENUM('active','inactive') | NOT NULL, DEFAULT 'active' | |
| created_at | TIMESTAMP | NULL | |
| updated_at | TIMESTAMP | NULL | |

#### 4.2.7. tags

| Cột | Kiểu | Ràng buộc | Ghi chú |
|---|---|---|---|
| id | BIGINT | PK, AI | |
| name | VARCHAR(255) | UNIQUE, NOT NULL | |
| slug | VARCHAR(255) | UNIQUE, NOT NULL | |
| description | TEXT | NULL | |
| color | VARCHAR(7) | NOT NULL, DEFAULT '#D4AF37' | |
| created_at | TIMESTAMP | NULL | |
| updated_at | TIMESTAMP | NULL | |

#### 4.2.8. product_tags

| Cột | Kiểu | Ràng buộc | Ghi chú |
|---|---|---|---|
| id | BIGINT | PK, AI | |
| product_id | BIGINT | FK → products(id) | onDelete(cascade) |
| tag_id | BIGINT | FK → tags(id) | onDelete(cascade) |
| created_at | TIMESTAMP | NULL | |
| updated_at | TIMESTAMP | NULL | |

Ràng buộc bổ sung: UNIQUE(product_id, tag_id)

#### 4.2.9. shopping_cart

| Cột | Kiểu | Ràng buộc | Ghi chú |
|---|---|---|---|
| id | BIGINT | PK, AI | |
| user_id | BIGINT | FK → users(id) | |
| product_id | BIGINT | FK → products(id) | |
| quantity | INT | NOT NULL | |
| created_at | TIMESTAMP | NULL | |
| updated_at | TIMESTAMP | NULL | |

#### 4.2.10. orders

| Cột | Kiểu | Ràng buộc | Ghi chú |
|---|---|---|---|
| id | BIGINT | PK, AI | |
| user_id | BIGINT | FK → users(id) | |
| assigned_to | BIGINT | FK → users(id), NULL | onDelete(set null) |
| assigned_shipper | BIGINT | FK → users(id), NULL | onDelete(set null) |
| assigned_packer | BIGINT | FK → users(id), NULL | onDelete(set null) |
| total_amount | DECIMAL(10,2) | NOT NULL | |
| status | ENUM('pending','pending_payment','paid','shipped','completed','canceled') | NOT NULL, DEFAULT 'pending' | |
| shipping_status | ENUM('pending_pickup','in_transit','delivered','cancelled','returned') | NOT NULL, DEFAULT 'pending_pickup' | |
| shipping_name | VARCHAR(255) | NULL | |
| shipping_phone | VARCHAR(20) | NULL | |
| shipping_address | TEXT | NULL | |
| shipping_email | VARCHAR(255) | NULL | |
| notes | TEXT | NULL | |
| payment_method | VARCHAR(50) | NULL | |
| created_at | TIMESTAMP | NULL | |
| updated_at | TIMESTAMP | NULL | |

#### 4.2.11. order_items

| Cột | Kiểu | Ràng buộc | Ghi chú |
|---|---|---|---|
| id | BIGINT | PK, AI | |
| order_id | BIGINT | FK → orders(id) | |
| product_id | BIGINT | FK → products(id) | |
| variant_id | BIGINT | FK → product_variants(id), NULL | onDelete(set null) |
| quantity | INT | NOT NULL | |
| price | DECIMAL(10,2) | NULL | field bổ sung |
| subtotal | DECIMAL(10,2) | NULL | field bổ sung |
| price_at_purchase | DECIMAL(10,2) | NOT NULL | |
| created_at | TIMESTAMP | NULL | |
| updated_at | TIMESTAMP | NULL | |

#### 4.2.12. payments

| Cột | Kiểu | Ràng buộc | Ghi chú |
|---|---|---|---|
| id | BIGINT | PK, AI | |
| order_id | BIGINT | FK → orders(id) | |
| payment_method | ENUM('credit_card','paypal','cash_on_delivery','vnpay','cod') | NOT NULL | |
| payment_status | ENUM('paid','pending') | NOT NULL, DEFAULT 'pending' | |
| payment_date | TIMESTAMP | NOT NULL, DEFAULT CURRENT_TIMESTAMP | |
| amount | DECIMAL(10,2) | NOT NULL | |
| status | VARCHAR(50) | NULL | gateway status |
| transaction_no | VARCHAR(255) | NULL | |
| transaction_data | TEXT | NULL | |
| bank_code | VARCHAR(50) | NULL | |
| pay_date | DATETIME | NULL | |
| created_at | TIMESTAMP | NULL | |
| updated_at | TIMESTAMP | NULL | |

#### 4.2.13. reviews

| Cột | Kiểu | Ràng buộc | Ghi chú |
|---|---|---|---|
| id | BIGINT | PK, AI | |
| product_id | BIGINT | FK → products(id) | |
| user_id | BIGINT | FK → users(id) | |
| rating | INT | CHECK rating BETWEEN 1 AND 5 | |
| comment | TEXT | NULL | |
| photo | VARCHAR(255) | NULL | |
| created_at | TIMESTAMP | NULL | |
| updated_at | TIMESTAMP | NULL | |

#### 4.2.14. product_reviews

| Cột | Kiểu | Ràng buộc | Ghi chú |
|---|---|---|---|
| id | BIGINT | PK, AI | |
| product_id | BIGINT | FK → products(id) | onDelete(cascade), indexed |
| user_id | BIGINT | FK → users(id), NULL | onDelete(set null) |
| name | VARCHAR(255) | NULL | |
| email | VARCHAR(255) | NULL | |
| rating | INT | NOT NULL, DEFAULT 5 | |
| comment | TEXT | NULL | |
| status | ENUM('pending','approved','rejected') | NOT NULL, DEFAULT 'pending' | indexed |
| created_at | TIMESTAMP | NULL | |
| updated_at | TIMESTAMP | NULL | |

#### 4.2.15. banners

| Cột | Kiểu | Ràng buộc | Ghi chú |
|---|---|---|---|
| id | BIGINT | PK, AI | |
| title | VARCHAR(255) | NOT NULL | |
| slug | VARCHAR(255) | UNIQUE, NOT NULL | |
| description | TEXT | NULL | |
| photo | VARCHAR(255) | NULL | |
| status | ENUM('active','inactive') | NOT NULL, DEFAULT 'active' | |
| created_at | TIMESTAMP | NULL | |
| updated_at | TIMESTAMP | NULL | |

#### 4.2.16. coupons

| Cột | Kiểu | Ràng buộc | Ghi chú |
|---|---|---|---|
| id | BIGINT | PK, AI | |
| code | VARCHAR(255) | UNIQUE, NOT NULL | |
| type | ENUM('fixed','percent') | NOT NULL | |
| value | DECIMAL(10,2) | NOT NULL | |
| photo | VARCHAR(255) | NULL | |
| status | ENUM('active','inactive') | NOT NULL, DEFAULT 'active' | |
| created_at | TIMESTAMP | NULL | |
| updated_at | TIMESTAMP | NULL | |

#### 4.2.17. order_cancellations

| Cột | Kiểu | Ràng buộc | Ghi chú |
|---|---|---|---|
| id | BIGINT | PK, AI | |
| order_id | BIGINT | FK → orders(id) | onDelete(cascade) |
| user_id | BIGINT | FK → users(id) | onDelete(cascade) |
| reason | ENUM('changed_mind','found_cheaper','wrong_item','delivery_too_long','payment_issue','other') | NOT NULL | |
| reason_detail | TEXT | NULL | |
| status | ENUM('pending','approved','rejected') | NOT NULL, DEFAULT 'pending' | |
| admin_note | TEXT | NULL | |
| created_at | TIMESTAMP | NULL | |
| updated_at | TIMESTAMP | NULL | |

#### 4.2.18. order_returns

| Cột | Kiểu | Ràng buộc | Ghi chú |
|---|---|---|---|
| id | BIGINT | PK, AI | |
| order_id | BIGINT | FK → orders(id) | onDelete(cascade) |
| user_id | BIGINT | FK → users(id) | onDelete(cascade) |
| reason | ENUM('defective','wrong_item','not_as_described','damaged_during_shipping','size_issue','color_issue','other') | NOT NULL | |
| reason_detail | TEXT | NULL | |
| status | ENUM('pending','approved','rejected','processing','completed') | NOT NULL, DEFAULT 'pending' | |
| admin_note | TEXT | NULL | |
| created_at | TIMESTAMP | NULL | |
| updated_at | TIMESTAMP | NULL | |

#### 4.2.19. system_settings

| Cột | Kiểu | Ràng buộc | Ghi chú |
|---|---|---|---|
| id | BIGINT | PK, AI | |
| key | VARCHAR(255) | UNIQUE, NOT NULL | |
| value | TEXT | NULL | |
| created_at | TIMESTAMP | NULL | |
| updated_at | TIMESTAMP | NULL | |

#### 4.2.20. sepay_transactions

| Cột | Kiểu | Ràng buộc | Ghi chú |
|---|---|---|---|
| id | BIGINT | PK, AI | |
| gateway | VARCHAR(255) | NOT NULL | |
| transactionDate | VARCHAR(255) | NOT NULL | |
| accountNumber | VARCHAR(255) | NOT NULL | |
| subAccount | VARCHAR(255) | NULL | |
| code | VARCHAR(255) | NULL | |
| content | VARCHAR(255) | NOT NULL | |
| transferType | VARCHAR(255) | NOT NULL | |
| description | VARCHAR(1000) | NULL | |
| transferAmount | BIGINT | NOT NULL | |
| referenceCode | VARCHAR(255) | NULL | |
| created_at | TIMESTAMP | NULL | |
| updated_at | TIMESTAMP | NULL | |

#### 4.2.21. notifications

| Cột | Kiểu | Ràng buộc | Ghi chú |
|---|---|---|---|
| id | UUID | PK | |
| type | VARCHAR(255) | NOT NULL | |
| notifiable_type | VARCHAR(255) | NOT NULL | morphs |
| notifiable_id | BIGINT | NOT NULL | morphs |
| data | TEXT | NOT NULL | |
| read_at | TIMESTAMP | NULL | |
| created_at | TIMESTAMP | NULL | |
| updated_at | TIMESTAMP | NULL | |

#### 4.2.22. posts

| Cột | Kiểu | Ràng buộc | Ghi chú |
|---|---|---|---|
| id | BIGINT | PK, AI | |
| title | VARCHAR(255) | NOT NULL | |
| quote | VARCHAR(255) | NULL | |
| summary | TEXT | NOT NULL | |
| description | LONGTEXT | NULL | |
| post_cat_id | BIGINT | NULL | (không ràng buộc FK trong migration) |
| tags | VARCHAR(255) | NULL | |
| added_by | BIGINT | FK → users(id) | |
| photo | VARCHAR(255) | NULL | |
| status | ENUM('active','inactive') | NOT NULL, DEFAULT 'active' | |
| created_at | TIMESTAMP | NULL | |
| updated_at | TIMESTAMP | NULL | |

#### 4.2.23. comments

| Cột | Kiểu | Ràng buộc | Ghi chú |
|---|---|---|---|
| id | BIGINT | PK, AI | |
| user_id | BIGINT | FK → users(id) | |
| post_id | BIGINT | FK → posts(id) | |
| comment | TEXT | NOT NULL | |
| photo | VARCHAR(255) | NULL | |
| status | ENUM('active','inactive') | NOT NULL, DEFAULT 'active' | |
| created_at | TIMESTAMP | NULL | |
| updated_at | TIMESTAMP | NULL | |

#### 4.2.24. product_posts

| Cột | Kiểu | Ràng buộc | Ghi chú |
|---|---|---|---|
| id | BIGINT | PK, AI | |
| product_id | BIGINT | FK → products(id) | onDelete(cascade), indexed |
| content | TEXT | NOT NULL | |
| description | TEXT | NULL | |
| status | ENUM('draft','published') | NOT NULL, DEFAULT 'draft' | indexed |
| created_at | TIMESTAMP | NULL | |
| updated_at | TIMESTAMP | NULL | |

---

### 4.3. Thiết kế luồng xử lý

#### 4.3.1. Sequence Diagram – Thanh toán VNPay (mức khái quát)

@startuml
actor Customer
participant "Frontend" as FE
participant "Checkout" as Checkout
participant "VNPayController" as VNC
participant "VNPay Gateway" as VNG
database "MySQL" as DB

Customer -> FE: Chọn VNPay
FE -> Checkout: Tạo đơn hàng
Checkout -> DB: INSERT orders
Checkout --> FE: order_id

FE -> VNC: POST /payment/vnpay/create(order_id)
VNC --> FE: Redirect VNPay URL
FE -> VNG: Redirect
Customer -> VNG: Thanh toán
VNG --> FE: Redirect return URL

FE -> VNC: GET /payment/vnpay/return
VNC -> DB: INSERT/UPDATE payments
VNC -> DB: UPDATE orders.status
VNC --> FE: Trang kết quả
@enduml

#### 4.3.2. Activity Diagram – Luồng trạng thái đơn hàng

@startuml
start
:Khách thêm sản phẩm vào giỏ;
:Checkout;
if (Chọn phương thức?) then (VNPay)
    :Redirect VNPay;
    if (Thành công?) then (Có)
        :orders.status = paid;
    else (Không)
        :orders.status = pending_payment;
    endif
else (COD/SePay)
    :Tạo đơn pending;
endif
:Admin/Nhân viên xử lý;
:Cập nhật shipping_status;
stop
@enduml

### 4.4. Thiết kế lớp (Class Diagram – rút gọn)

@startuml
skinparam classAttributeIconSize 0

class User
class Role
class Category
class Product
class ProductVariant
class Order
class OrderItem
class Payment

User "*" --> "1" Role
Category "1" --> "*" Product
Product "1" --> "*" ProductVariant
User "1" --> "*" Order
Order "1" --> "*" OrderItem
Order "1" --> "*" Payment
@enduml

---

## CHƯƠNG 5: KẾT QUẢ

### 5.1. Các chức năng đã triển khai

- Xác thực & phân quyền: đăng ký/đăng nhập, phân quyền theo role/permissions.
- Sản phẩm: danh sách/chi tiết/tìm kiếm; quản trị CRUD sản phẩm/danh mục.
- Giỏ hàng: thêm/sửa/xóa sản phẩm.
- Đơn hàng: tạo đơn, theo dõi trạng thái, phân công xử lý theo vai trò.
- Thanh toán: COD, VNPay (redirect/return), SePay (QR + webhook).
- Chatbot AI: tư vấn/hỏi đáp (FastAPI microservice).

### 5.2. Hình ảnh giao diện (placeholder)

> Khi nộp báo cáo, thay thế các placeholder bên dưới bằng ảnh chụp màn hình thực tế từ hệ thống.

- (Ảnh) Trang chủ
- (Ảnh) Danh sách sản phẩm
- (Ảnh) Chi tiết sản phẩm
- (Ảnh) Giỏ hàng
- (Ảnh) Checkout / Thanh toán
- (Ảnh) Trang quản trị: Dashboard
- (Ảnh) Trang quản trị: Quản lý sản phẩm
- (Ảnh) Trang quản trị: Quản lý đơn hàng

### 5.3. Kết quả kiểm thử (tóm tắt)

- Manual test: Auth, Cart, Checkout, VNPay/SePay, phân công xử lý đơn.
- API test: kiểm tra endpoint bằng Swagger (L5-Swagger) và Postman collection.

---

## CHƯƠNG 6: TỔNG KẾT

### 6.1. Kết luận

PTIT eCommerce đáp ứng các chức năng cốt lõi của một hệ thống thương mại điện tử: quản lý sản phẩm – giỏ hàng – đơn hàng – thanh toán và quản trị. Điểm nhấn gồm tích hợp VNPay/SePay và chatbot AI triển khai theo kiến trúc microservice.

### 6.2. Định hướng phát triển

- Tối ưu hiệu năng: caching, tối ưu query, queue cho email.
- Tăng bảo mật: rate limit, audit input, logging nghiệp vụ.
- Mở rộng: API-first cho mobile; bổ sung các luồng marketing/CRM.
- AI: knowledge base + logging hội thoại + đánh giá chất lượng trả lời.

---

## PHỤ LỤC: HƯỚNG DẪN VẼ BIỂU ĐỒ

- Toàn bộ biểu đồ trong tài liệu được viết bằng PlantUML.
- Copy khối từ @startuml đến @enduml và dán vào https://www.planttext.com/ để xuất ảnh.
