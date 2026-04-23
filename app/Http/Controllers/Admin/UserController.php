<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index()
    {
        $users = User::paginate(10);
        return view('admin.index', compact('users'));
    }

    public function create()
    {
        return view('admin.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,shop,customer',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $userData = [
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role' => $validated['role'],
            'is_active' => $request->has('is_active'),
        ];

        try {
            DB::beginTransaction();
            $user = User::create($userData);
            
            if ($validated['role'] === 'customer') {
                \App\Models\Customer::create([
                    'user_id' => $user->id,
                    'full_name' => $validated['name'],
                    'phone' => $validated['phone'] ?? null,
                ]);
                if (!empty($validated['address'])) {
                    \App\Models\CustomerAddress::create([
                        'user_id' => $user->id,
                        'address_line' => $validated['address'],
                        'is_default' => 1,
                    ]);
                }
            } elseif ($validated['role'] === 'shop') {
                $shop = \App\Models\Shop::create([
                    'user_id' => $user->id,
                    'category_id' => \App\Models\Category::first()->id ?? 1,
                    'name' => $validated['name'],
                    'slug' => \Illuminate\Support\Str::slug($validated['name']),
                    'status' => 'active',
                ]);
                \App\Models\ShopDetail::create([
                    'shop_id' => $shop->id,
                    'phone' => $validated['phone'] ?? null,
                    'address' => $validated['address'] ?? null,
                ]);
            }
            DB::commit();
            return redirect()->route('admin.users.index')->with('success', 'Tạo user thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(User $user)
    {
        return view('admin.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,shop,customer',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'is_active' => $request->has('is_active'),
        ];

        if ($request->filled('password')) {
            $request->validate(['password' => 'required|string|min:8|confirmed']);
            $userData['password'] = bcrypt($request->password);
        }

        try {
            DB::beginTransaction();
            $user->update($userData);
            
            if ($validated['role'] === 'customer') {
                $customer = $user->customer ?? new \App\Models\Customer(['user_id' => $user->id]);
                $customer->full_name = $validated['name'];
                $customer->phone = $validated['phone'] ?? null;
                $customer->save();
                
                if (!empty($validated['address'])) {
                    \App\Models\CustomerAddress::updateOrCreate(
                        ['user_id' => $user->id, 'is_default' => 1],
                        ['address_line' => $validated['address']]
                    );
                }
            } elseif ($validated['role'] === 'shop') {
                $shop = $user->shop;
                if (!$shop) {
                    $shop = \App\Models\Shop::create([
                        'user_id' => $user->id,
                        'category_id' => \App\Models\Category::first()->id ?? 1,
                        'name' => $validated['name'],
                        'slug' => \Illuminate\Support\Str::slug($validated['name']),
                        'status' => 'active',
                    ]);
                } else {
                    $shop->name = $validated['name'];
                    $shop->save();
                }
                
                $details = $shop->details ?? new \App\Models\ShopDetail(['shop_id' => $shop->id]);
                $details->phone = $validated['phone'] ?? null;
                $details->address = $validated['address'] ?? null;
                $details->save();
            }
            DB::commit();
            return redirect()->route('admin.users.index')->with('success', 'Cập nhật user thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function destroy(User $user)
    {
        try {
            $user->delete();
            return redirect()->route('admin.users.index')->with('success', 'Xóa user thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }
}
