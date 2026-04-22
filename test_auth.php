<?php
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

$u = User::where('email', 'test_auth@example.com')->first();
if ($u) {
    $u->delete();
}

$user = User::create([
    'name' => 'Test Auth',
    'email' => 'test_auth@example.com',
    'password' => 'password123',
    'role' => 'customer'
]);

echo "User created with ID: " . $user->id . "\n";
echo "Is password hashed? " . (Hash::needsRehash($user->password) ? "No" : "Yes") . "\n";
echo "Password value: " . $user->password . "\n";

$loginSuccess = Auth::attempt(['email' => 'test_auth@example.com', 'password' => 'password123']);
echo "Login success: " . ($loginSuccess ? "Yes" : "No") . "\n";

$user->delete();
