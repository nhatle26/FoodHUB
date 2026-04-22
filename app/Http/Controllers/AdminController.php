<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;

class AdminController extends Controller
{
    public function dashboard()
    {
        $userCount = User::count();
        $categoryCount = Category::count();
        $shopCount = Schema::hasTable('shops') ? DB::table('shops')->count() : 0;
        $orderCount = Schema::hasTable('orders') ? DB::table('orders')->count() : 0;
        $todayOrders = Schema::hasTable('orders') ? DB::table('orders')->whereDate('created_at', Carbon::today())->count() : 0;
        $pendingShopCount = Schema::hasTable('shops') ? DB::table('shops')->where('status', 'pending')->count() : 0;
        $pendingShops = Schema::hasTable('shops') ? DB::table('shops')->where('status', 'pending')->limit(3)->get() : [];
        $latestOrders = Schema::hasTable('orders') ? DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->leftJoin('customers', 'users.id', '=', 'customers.user_id')
            ->join('shops', 'orders.shop_id', '=', 'shops.id')
            ->select(
                'orders.order_code',
                'orders.total',
                'orders.status',
                'orders.created_at',
                DB::raw('COALESCE(customers.full_name, users.email) as customer_name'),
                'shops.name as shop_name'
            )
            ->orderByDesc('orders.created_at')
            ->limit(5)
            ->get() : [];
        $orderTrend = [130, 145, 118, 150, 175, 190, 210];

        return view('admin.dashboard', compact(
            'userCount',
            'categoryCount',
            'shopCount',
            'orderCount',
            'todayOrders',
            'pendingShopCount',
            'pendingShops',
            'latestOrders',
            'orderTrend'
        ));
    }

    public function pendingShops()
    {
        if (!Schema::hasTable('shops')) {
            return redirect()->route('admin.dashboard')->with('error', 'Bảng shops chưa tồn tại.');
        }

        $pendingShops = DB::table('shops')
            ->where('status', 'pending')
            ->orderByDesc('created_at')
            ->paginate(12);

        return view('admin.shops.pending', compact('pendingShops'));
    }

    public function approveShop($shopId)
    {
        if (!Schema::hasTable('shops')) {
            return redirect()->route('admin.dashboard')->with('error', 'Bảng shops chưa tồn tại.');
        }

        DB::table('shops')
            ->where('id', $shopId)
            ->update(['status' => 'active', 'updated_at' => now()]);

        return redirect()->route('admin.dashboard')->with('success', 'Shop đã được duyệt thành công.');
    }

    public function rejectShop($shopId)
    {
        if (!Schema::hasTable('shops')) {
            return redirect()->route('admin.dashboard')->with('error', 'Bảng shops chưa tồn tại.');
        }

        DB::table('shops')
            ->where('id', $shopId)
            ->update(['status' => 'banned', 'updated_at' => now()]);

        return redirect()->route('admin.dashboard')->with('success', 'Shop đã bị từ chối và sẽ không còn hiển thị ở danh sách chờ duyệt.');
    }

    /**
     * Display a listing of users
     */
    public function index()
    {
        $users = User::paginate(10);
        return view('admin.index', compact('users'));
    }

    /**
     * Show form for creating a new user
     */
    public function create()
    {
        return view('admin.create');
    }

    /**
     * Store a newly created user in database
     */
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
            return redirect()->route('admin.users.index')
                ->with('success', 'Tạo user thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->with('error', 'Lỗi: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show form for editing a user
     */
    public function edit(User $user)
    {
        return view('admin.edit', compact('user'));
    }

    /**
     * Update a user in database
     */
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
                if ($shop) {
                    $shop->name = $validated['name'];
                    $shop->save();
                    
                    $details = $shop->details ?? new \App\Models\ShopDetail(['shop_id' => $shop->id]);
                    $details->phone = $validated['phone'] ?? null;
                    $details->address = $validated['address'] ?? null;
                    $details->save();
                }
            }
            DB::commit();
            return redirect()->route('admin.users.index')
                ->with('success', 'Cập nhật user thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    /**
     * Delete a user from database
     */
    public function destroy(User $user)
    {
        try {
            $user->delete();
            return redirect()->route('admin.users.index')
                ->with('success', 'Xóa user thành công!');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }
}
