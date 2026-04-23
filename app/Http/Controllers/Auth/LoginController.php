<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // Hiển thị form đăng nhập
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Xử lý đăng nhập
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            
            $role = Auth::user()->role ?? 'customer';
            $name = Auth::user()->customer->full_name ?? (Auth::user()->shop->name ?? Auth::user()->email);
            
            if ($role === 'admin') {
                return redirect()->intended('/admin')
                    ->with('success', 'Chào mừng Admin!');
            } elseif ($role === 'shop') {
                return redirect()->intended('/shop/dashboard')
                    ->with('success', 'Đăng nhập thành công! Chào mừng ' . $name . '.');
            }
            
            return redirect()->intended('/')
                ->with('success', 'Đăng nhập thành công! Chào mừng ' . $name . '.');
        }

        return back()->withErrors([
            'email' => 'Thông tin đăng nhập không chính xác.',
        ])->onlyInput('email');
    }

    // Xử lý đăng xuất
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
