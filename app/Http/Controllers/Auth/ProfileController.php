<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\PublicPath;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show()
    {
        return view('profile', ['user' => Auth::user()]);
    }

    public function updateInfo(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:15',
            'address' => 'nullable|string|max:500',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarFile = $request->file('avatar');
            $avatarName = time() . '_' . Str::slug($request->name ?? 'avatar') . '.' . $avatarFile->getClientOriginalExtension();
            $avatarPath = $avatarFile->storeAs('avatars', $avatarName, 'public');
        }

        if ($user->role === 'customer') {
            $customer = $user->customer ?? new \App\Models\Customer(['user_id' => $user->id]);
            if ($avatarPath) {
                if ($customer->avatar && \Storage::disk('public')->exists($customer->avatar)) {
                    \Storage::disk('public')->delete($customer->avatar);
                }
                $customer->avatar = $avatarPath;
            }
            $customer->full_name = $request->name;
            $customer->phone = $request->phone;
            $customer->save();

            if ($request->address) {
                \App\Models\CustomerAddress::updateOrCreate(
                    ['user_id' => $user->id, 'is_default' => 1],
                    ['phone_number' => $request->phone ?? '', 'address_line' => $request->address]
                );
            }
        } elseif ($user->role === 'shop') {
            $shop = $user->shop;
            if ($shop) {
                $shop->name = $request->name;
                $shop->save();

                $details = $shop->details ?? new \App\Models\ShopDetail(['shop_id' => $shop->id]);
                if ($avatarPath) {
                    if ($details->logo && \Storage::disk('public')->exists($details->logo)) {
                        \Storage::disk('public')->delete($details->logo);
                    }
                    $details->logo = $avatarPath;
                }
                $details->phone = $request->phone;
                $details->address = $request->address;
                $details->save();
            }
        } elseif ($user->role === 'admin') {
            // Admin might not have a specific profile table in this schema
            // We just ignore the profile update or add an admins table if needed.
        }

        return back()->with('success_info', 'Thông tin cá nhân đã được cập nhật.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng.']);
        }

        $user->password = $request->password;
        $user->save();

        return back()->with('success_password', 'Mật khẩu đã được cập nhật.');
    }
}
