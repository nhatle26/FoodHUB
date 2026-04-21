<?php
use Illuminate\Support\Facades\Auth;
use App\Models\User;

$user = User::where('email', 'admin@foodhub.vn')->first();
echo "User exists: " . ($user ? "Yes" : "No") . "\n";
if ($user) {
    echo "Password in DB: " . $user->password . "\n";
}

$loginSuccess = Auth::attempt(['email' => 'admin@foodhub.vn', 'password' => 'password']);
echo "Login success with 'password': " . ($loginSuccess ? "Yes" : "No") . "\n";
