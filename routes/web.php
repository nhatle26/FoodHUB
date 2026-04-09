<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\CategoryController;

// Group các route liên quan đến Auth
Route::prefix('auth')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/user-register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/user-register', [RegisterController::class, 'register'])->name('register.post');
});
// // Địa chỉ "cứu viện"
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
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('shops/pending', [AdminController::class, 'pendingShops'])->name('shops.pending');
    Route::post('shops/{shop}/approve', [AdminController::class, 'approveShop'])->name('shops.approve');
    Route::post('shops/{shop}/reject', [AdminController::class, 'rejectShop'])->name('shops.reject');
    Route::resource('users', AdminController::class);
    Route::resource('categories', CategoryController::class)->except(['show']);
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [ProfileController::class, 'show'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::post('/profile', [ProfileController::class, 'updateInfo'])->name('profile.update');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
});

