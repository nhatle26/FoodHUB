<?php

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

echo "<h2>FoodHub - Fix Passwords</h2>";
foreach ($accounts as $email) {
    $user = User::where('email', $email)->first();
    if (!$user) {
        echo "<p style='color:gray'>[ NOT FOUND ] $email</p>";
        continue;
    }
    $user->password = Hash::make('password');
    $user->save();
    echo "<p style='color:green'>[ FIXED ] $email - role: {$user->role} - password reset to 'password'</p>";
}
echo "<hr><b>Done. Xóa file này sau khi dùng!</b>";
