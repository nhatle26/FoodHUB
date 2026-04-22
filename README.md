# FoodHub - Nền Tảng Đặt Đồ Ăn Đa Shop Trực Tuyến

FoodHub là một ứng dụng đặt đồ ăn trực tuyến theo mô hình đa shop (multi-vendor) được xây dựng bằng framework Laravel. Dự án cho phép nhiều cửa hàng thực phẩm cùng đăng ký kinh doanh trên một nền tảng duy nhất, đồng thời cung cấp trải nghiệm đặt món trơn tru cho khách hàng.

## 🚀 Tính năng nổi bật

### 🍔 Dành cho Khách hàng (Customer)
*   **Tìm kiếm & Khám phá:** Duyệt danh sách quán ăn, phân loại theo danh mục (Trà sữa, Đồ ăn vặt, v.v.), tìm kiếm tên quán/món ăn.
*   **Giỏ hàng thông minh:** Đặt món, thay đổi số lượng, kiểm tra chỉ đặt cùng 1 shop trong 1 lần checkout.
*   **Quản lý Đơn hàng:** Xem lịch sử đặt món, theo dõi trạng thái, hủy đơn khi chưa xác nhận.
*   **Tương tác:** Thêm shop vào danh sách "Yêu thích", viết đánh giá (Review 1-5 sao) sau khi đơn hoàn thành.
*   **Hồ sơ cá nhân:** Cập nhật thông tin liên hệ, địa chỉ giao hàng mặc định, đổi avatar.

### 🏪 Dành cho Chủ cửa hàng (Vendor/Shop)
*   **Cửa hàng trực tuyến:** Quản lý Logo, Banner, Giờ mở cửa, mô tả quán, trạng thái Đóng/Mở.
*   **Quản lý Menu:** Thêm, sửa, xóa, phân loại món ăn. Đánh dấu hết hàng tạm thời.
*   **Xử lý Đơn hàng:** Chuyển trạng thái đơn hàng (Chờ xác nhận -> Đang chuẩn bị -> Đang giao -> Đã giao), Từ chối đơn hàng.
*   **Mã giảm giá (Voucher):** Tự tạo mã giảm giá theo %, theo số tiền cố định, thiết lập điều kiện sử dụng.
*   **Thống kê:** Dashboard theo dõi doanh thu và đơn hàng trực quan.

### 👑 Dành cho Quản trị viên (Admin)
*   **Quản lý Hệ thống:** Dashboard thống kê tổng quan (User, Shop, Doanh số toàn sàn).
*   **Kiểm duyệt:** Xét duyệt tài khoản đăng ký Shop mới, khóa Shop vi phạm.
*   **Quản lý Users & Danh mục:** Thêm, sửa, xóa người dùng và danh mục thực phẩm.
*   **Báo cáo Đơn hàng:** Theo dõi toàn bộ luồng đơn hàng, chức năng **Xuất file CSV (Export)** đơn hàng toàn sàn.

## 🛠 Tech Stack

*   **Backend:** PHP 8.2, Laravel 10 (Hoặc 11)
*   **Frontend:** Blade Template, Bootstrap 5 (Custom CSS), JavaScript
*   **Database:** MySQL
*   **Tính năng bổ sung:** Laravel DB Transactions, Eloquent ORM (Relationships), File Storage (Upload ảnh)

## ⚙️ Hướng dẫn cài đặt (Local)

1. **Clone repository:**
   ```bash
   git clone <repo-url>
   cd FoodHUB
   ```

2. **Cài đặt thư viện:**
   ```bash
   composer install
   npm install
   ```

3. **Cấu hình môi trường:**
   Tạo file `.env` từ file mẫu và thiết lập kết nối MySQL của bạn:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Chạy Migration & Seeder (Tạo dữ liệu mẫu):**
   ```bash
   php artisan migrate:fresh --seed
   ```

5. **Liên kết thư mục Storage (Để hiển thị ảnh upload):**
   ```bash
   php artisan storage:link
   ```

6. **Chạy ứng dụng:**
   ```bash
   php artisan serve
   ```
   Mở trình duyệt tại: `http://localhost:8000`

## 👥 Phân công công việc (Team 3 người)

*   **Thành viên 1 (Frontend & UX):** Cấu hình Layout Blade, Authentication UI, Trang chủ, Trang chi tiết Shop. Giỏ hàng và Checkout UI.
*   **Thành viên 2 (Vendor & Product):** Database Schema, CRUD Sản phẩm, Quản lý đơn hàng (phía Shop), Tính năng Voucher, Cài đặt thông tin Shop.
*   **Thành viên 3 (Customer & Admin):** Quản lý Giỏ hàng (Backend), Lịch sử đơn hàng, Tính năng Đánh giá, Yêu thích. Dashboard Admin, Xuất CSV, Kiểm duyệt Shop.

---
*Dự án kết thúc môn học - Nhóm phát triển FoodHUB*
