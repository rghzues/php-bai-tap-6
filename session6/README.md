# BÀI TẬP SESSION 6: SESSIONS & COOKIES

## Cấu trúc dự án

```
session6/
├── frontend/
│   ├── login.php        ← Trang đăng nhập
│   ├── register.php     ← Trang đăng ký
│   └── product.php      ← Trang sản phẩm (yêu cầu đăng nhập)
└── backend/
    ├── users.json       ← Lưu trữ tài khoản người dùng
    └── products.json    ← Dữ liệu sản phẩm
```

## Cách chạy

### Yêu cầu
- PHP 7.4+ (hoặc XAMPP/WAMP/Laragon)
- Web browser

### Khởi động
1. Copy thư mục `session6` vào thư mục gốc web server (htdocs hoặc www)
2. Truy cập: `http://localhost/session6/frontend/login.php`

## Luồng hoạt động

### 1. Trang Login (`frontend/login.php`)
- Người dùng nhập Username & Password
- Hệ thống kiểm tra thông tin với `backend/users.json`
- Nếu đúng: tạo **Session** + **Cookie** (30 ngày), chuyển đến trang sản phẩm
- Nếu sai: hiện thông báo lỗi
- Nếu chưa có tài khoản: click **Register** để đăng ký

### 2. Trang Register (`frontend/register.php`)
- Điền: Username, Email, Password, Confirm Password
- Sau khi đăng ký thành công → tự động quay về trang Login
- Mật khẩu được mã hóa bằng `password_hash()` (bcrypt)

### 3. Trang Product (`frontend/product.php`)
- **Bắt buộc đăng nhập** mới xem được
- Kiểm tra Session → kiểm tra Cookie → nếu không có cả hai thì redirect về Login
- Hiển thị danh sách sản phẩm từ `backend/products.json`
- Có nút Logout để đăng xuất (xóa session + cookie)

## Tính năng Session & Cookie

| Tính năng | Mô tả |
|-----------|-------|
| `session_start()` | Khởi tạo session trên mỗi trang |
| `$_SESSION['user']` | Lưu thông tin user sau khi đăng nhập |
| `setcookie('remember_user', ...)` | Cookie lưu 30 ngày |
| `session_destroy()` | Xóa session khi logout |

## Thêm sản phẩm

Chỉnh sửa file `backend/products.json`:

```json
[
    {
        "id": 4,
        "name": "Tên sản phẩm",
        "price": 999,
        "category": "Danh mục",
        "image": "https://link-anh.jpg"
    }
]
```
