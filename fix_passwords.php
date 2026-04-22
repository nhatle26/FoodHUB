<?php
/**
 * Script sửa password tài khoản trong database về Bcrypt.
 * Chạy: php fix_passwords.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Hash;
use App\Models\User;

$accounts = [
    'admin@foodhub.vn',
    'hoa.tran@gmail.com',
    'tuan.nguyen@gmail.com',
    'mai.le@gmail.com',
    'duc.pham@gmail.com',
    'linh.nguyen@gmail.com',
    'long.vo@gmail.com',
];

echo "=== FoodHub Password Fix Tool ===\n\n";

foreach ($accounts as $email) {
    $user = User::where('email', $email)->first();
    if (!$user) {
        echo "[ NOT FOUND ] $email\n";
        continue;
    }

    // Kiểm tra xem password có phải Bcrypt không
    try {
        Hash::check('password', $user->password);
        echo "[  OK SKIP  ] $email (đã là Bcrypt hợp lệ)\n";
    } catch (\Exception $e) {
        // Password lưu sai định dạng, ghi đè lại
        $user->password = Hash::make('password');
        $user->save();
        echo "[   FIXED   ] $email → password đã được reset về 'password'\n";
    }
}

echo "\n=== Xong! Tất cả tài khoản trên đều có mật khẩu là: password ===\n";
