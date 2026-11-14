# SoleVibe - Website Bán Giày Sneaker Theo Mô Hình MVC

Hệ thống website bán giày trực tuyến được xây dựng hoàn chỉnh theo kiến trúc chuẩn **PHP MVC (Model - View - Controller)** thuần, hướng đối tượng (OOP), sử dụng PDO và cơ sở dữ liệu MySQL.

---

## 1. Sơ Đồ Kiến Trúc MVC Bán Giày

```
                                      ┌───────────────┐
                                      │   Trình duyệt │
                                      │  (Khách hàng) │
                                      └───────┬───────┘
                                              │ HTTP Request (?act=... / POST)
                                              ▼
                                      ┌───────────────┐
                                      │   index.php   │ Front Controller
                                      │  (Router.php) │
                                      └───────┬───────┘
                                              │ Điều phối (Dispatch)
                                              ▼
                                    ┌───────────────────┐
                      ┌─────────────┤    Controllers    ├─────────────┐
                      │             └─────────┬─────────┘             │
                      ▼                       │                       ▼
              ┌───────────────┐               │               ┌───────────────┐
              │    Models     │               │               │     Views     │
              │  (PDO / SQL)  │               │               │ (HTML/UI/CSS) │
              └───────┬───────┘               │               └───────▲───────┘
                      │                       │                       │
                      ▼                       │                       │
              ┌───────────────┐               │ Trả dữ liệu Data      │ Render
              │ MySQL Database│───────────────┘───────────────────────┘
              │ (`shoe_store`)│
              └───────────────┘
```

### Nguyên Tắc Hoạt Động 3 Tầng (MVC):
1. **Controller (Bộ điều khiển)**: Tiếp nhận Request từ người dùng thông qua Router, kiểm tra tính hợp lệ dữ liệu, gọi Model tương ứng để lấy hoặc ghi dữ liệu, sau đó truyền data sang View và hiển thị ra màn hình.
2. **Model (Mô hình dữ liệu)**: Chịu trách nhiệm toàn bộ về truy vấn cơ sở dữ liệu MySQL bằng PDO (`Prepared Statements`), bảo vệ chống SQL Injection, xử lý transaction khi tạo đơn hàng & trừ kho biến thể size giày.
3. **View (Giao diện hiển thị)**: Chỉ chứa mã HTML, CSS (Bootstrap 5, FontAwesome) và các thẻ PHP hiển thị (`echo`, `foreach`, `if`), tuyệt đối **không** viết câu lệnh SQL trong View.

---

## 2. Cấu Trúc Thư Mục Dự Án

```
ban giay2/
├── app/
│   ├── Controllers/             # Tầng Điều khiển
│   │   ├── BaseController.php   # Controller cha (render view, flash, auth check)
│   │   ├── HomeController.php   # Trang chủ, banner, giày hot
│   │   ├── ProductController.php# Danh sách giày, lọc danh mục/hãng/giá, chi tiết giày
│   │   ├── CartController.php   # Quản lý giỏ hàng (thêm size, sửa số lượng, xóa)
│   │   ├── CheckoutController.php # Đặt hàng, tính ship, tạo đơn & chi tiết đơn
│   │   ├── AuthController.php   # Đăng nhập, đăng ký tài khoản, đăng xuất
│   │   └── Admin/               # Quản trị viên
│   │       ├── DashboardController.php # Thống kê doanh thu, đơn hàng, biểu đồ
│   │       ├── ProductController.php   # Quản lý giày (Thêm, Sửa, Xóa)
│   │       ├── CategoryController.php  # Quản lý danh mục
│   │       └── OrderController.php     # Quản lý & duyệt trạng thái đơn hàng
│   ├── Core/                    # Khung sườn hệ thống
│   │   ├── Database.php         # Kết nối PDO Singleton
│   │   ├── BaseModel.php        # Lớp Model cha với CRUD & Transaction
│   │   ├── Router.php           # Điều hướng route tập trung
│   │   └── Helper.php           # Các hàm tiện ích: format_price(), url(), flash()
│   ├── Models/                  # Tầng Mô hình dữ liệu
│   │   ├── Product.php          # Sản phẩm giày, tìm kiếm, lọc, liên quan
│   │   ├── ProductVariant.php   # Biến thể: Size (38-44), màu sắc, tồn kho
│   │   ├── Category.php         # Danh mục giày (Sneaker, Running, Basketball...)
│   │   ├── Brand.php            # Thương hiệu (Nike, Adidas, Puma, Converse, Vans)
│   │   ├── Cart.php             # Xử lý giỏ hàng lưu trong Session
│   │   ├── Order.php            # Đơn hàng, thống kê doanh thu
│   │   └── User.php             # Người dùng, phân quyền Admin / Customer
│   └── Views/                   # Tầng Giao diện người dùng
│       ├── layouts/             # Layout dùng chung (header, footer, admin sidebar)
│       ├── client/              # Giao diện khách mua hàng (home, shop, detail, cart...)
│       └── admin/               # Giao diện quản trị (dashboard, products, orders...)
├── config/
│   ├── app.php                  # Cấu hình website
│   └── database.php             # Cấu hình kết nối MySQL
├── database.sql                 # CSDL mẫu đầy đủ bảng & dữ liệu test
├── index.php                    # Front Controller - Điểm đón request duy nhất
├── .htaccess                    # Rewrite URL thân thiện trên Apache
└── README.md
```

---

## 3. Đặc Thù Mô Hình MVC Cho Website Bán Giày

Khác với các mặt hàng thông thường (như sách hay điện thoại), một website bán giày có nghiệp vụ cốt lõi:
- **Biến thể sản phẩm (Product Variants)**: Một đôi giày cùng tên có nhiều **Size** (38, 39, 40, 41, 42, 43, 44), **Màu sắc** và **Tồn kho riêng từng size**.
- **Quản lý tồn kho theo Size**: Khi khách đặt Size 41, hệ thống phải trừ tồn kho của đúng Size 41 đó mà không ảnh hưởng tới Size 40 hay 42.
- **Transaction an toàn**: Khi đặt hàng, toàn bộ quá trình: Tạo đơn hàng -> Thêm chi tiết đơn -> Trừ kho biến thể đều được đóng gói trong một `PDO Transaction` (`beginTransaction`, `commit`, `rollBack`).

---

## 4. Hướng Dẫn Cài Đặt & Chạy Website

### Cách 1: Chạy trực tiếp qua Laragon (Khuyên dùng)
1. Bật **Laragon** (Start All: Apache & MySQL).
2. Đảm bảo database `shoe_store` đã được tạo và import file `database.sql` (bằng HeidiSQL hoặc phpMyAdmin).
3. Mở trình duyệt và truy cập:
   - Trang người dùng: `http://localhost/www/ban%20giay2/`
   - Hoặc tên miền ảo (nếu có): `http://ban-giay2.test/`

### Cách 2: Chạy bằng PHP Built-in Server
Mở Terminal / PowerShell tại thư mục `c:\laragon\www\www\ban giay2`:
```powershell
php -S localhost:8000
```
Truy cập: `http://localhost:8000/`

---

## 5. Tài Khoản Kiểm Thử

| Vai trò | Email | Mật khẩu | Chức năng |
|---|---|---|---|
| **Quản trị viên (Admin)** | `admin@solemate.vn` | `123456` | Toàn quyền quản trị Dashboard, Giày, Đơn hàng, Danh mục |
| **Khách hàng (Customer)** | `phong@gmail.com` | `123456` | Mua hàng, chọn size, xem giỏ, thanh toán đơn |
