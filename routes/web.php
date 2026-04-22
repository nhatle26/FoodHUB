<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ShopManageController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\OrderManageController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\OrderController as CustomerOrderController;
use App\Http\Controllers\Customer\ReviewController;
use App\Http\Controllers\Customer\WishlistController;
use App\Http\Controllers\FrontShopController;
use App\Http\Controllers\Shop\ShopController;
use App\Http\Controllers\Shop\ProductController;
use App\Http\Controllers\Shop\OrderController;
use App\Http\Controllers\Shop\VoucherController;
use App\Http\Controllers\Shop\SettingController;

// Group các route liên quan đến Auth
Route::prefix('auth')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/user-register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/user-register', [RegisterController::class, 'register'])->name('register.post');
    Route::get('/shop-register', [RegisterController::class, 'create'])->name('shop.create');
Route::post('/shop-register', [RegisterController::class, 'store'])->name('shop.store');
});

// Shop Routes
Route::prefix('shop')->name('shop.')->middleware('auth')->group(function () {
    Route::get('/dashboard', [ShopController::class, 'dashboard'])->name('dashboard');
    Route::resource('products', ProductController::class);
    Route::resource('orders', OrderController::class);
    Route::resource('vouchers', VoucherController::class);
    Route::get('/revenue', [ShopController::class, 'revenue'])->name('revenue');
    Route::get('/settings', [SettingController::class, 'index'])->name('settings');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
    Route::post('/settings/toggle-status', [SettingController::class, 'toggleStatus'])->name('settings.toggle_status');
});

// Địa chỉ "cứu viện"
// Route::get('/cuu-beng', function () {
//     try {
//         // 1. Dọn sạch data cũ (Nếu có)
//         DB::statement('SET FOREIGN_KEY_CHECKS=0;');
//         DB::table('users')->truncate();
//         DB::statement('SET FOREIGN_KEY_CHECKS=1;');

//         // 2. Ép chèn 1 User tên là "Bèng FoodHub" vào database
//         DB::table('users')->insert([
//             'name' => 'Bèng FoodHub',
//             'email' => 'beng.super@example.com',
//             'password' => bcrypt('beng123'), // Mật khẩu là beng123
//             'created_at' => now(),
//             'updated_at' => now(),
//         ]);

//         return "TUYỆT VỜI! Đã tạo xong User 'Bèng FoodHub'. Bèng mở Workbench lên và NHẤN REFRESH NGAY!";
//     } catch (\Exception $e) {
//         return "Vẫn còn lỗi: " . $e->getMessage();
//     }
// });

// Admin CRUD Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('shops/pending', [ShopManageController::class, 'pendingShops'])->name('shops.pending');
    Route::post('shops/{shop}/approve', [ShopManageController::class, 'approveShop'])->name('shops.approve');
    Route::post('shops/{shop}/reject', [ShopManageController::class, 'rejectShop'])->name('shops.reject');
    Route::resource('users', UserController::class);
    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::get('orders', [OrderManageController::class, 'index'])->name('orders.index');
    Route::get('orders/export', [OrderManageController::class, 'exportCsv'])->name('orders.export');
    Route::get('orders/{order}', [OrderManageController::class, 'show'])->name('orders.show');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [ProfileController::class, 'show'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::post('/profile', [ProfileController::class, 'updateInfo'])->name('profile.update');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
});

// Route chính cho trang chủ
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Route chi tiết quán ăn
Route::get('/shop/{id}', [App\Http\Controllers\FrontShopController::class, 'show'])->name('shop.show');

Route::middleware(['auth'])->group(function () {
    // Trang danh sách giỏ hàng
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');

    // Thêm món (dùng POST)
    Route::post('/cart/add', [CartController::class, 'addToCart'])->name('cart.add');

    // Cập nhật số lượng
    Route::patch('/cart/update/{id}', [CartController::class, 'updateQuantity'])->name('cart.update');

    // Xóa từng món
    Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');

    // Xóa sạch giỏ
    Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

    // Customer Orders
    Route::get('/orders', [CustomerOrderController::class, 'index'])->name('customer.orders.index');
    Route::get('/orders/{order}', [CustomerOrderController::class, 'show'])->name('customer.orders.show');
    Route::post('/orders/{order}/cancel', [CustomerOrderController::class, 'cancel'])->name('customer.orders.cancel');

    // Reviews
    Route::post('/orders/{order}/review', [ReviewController::class, 'store'])->name('customer.reviews.store');

    // Wishlist
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('customer.wishlist.index');
    Route::post('/wishlist/toggle/{shop}', [WishlistController::class, 'toggle'])->name('customer.wishlist.toggle');
});

Route::get('/checkout', [CartController::class, 'index'])->name('cart.index');

Route::post('/checkout/process', [CartController::class, 'processCheckout'])->name('checkout.process');
