<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\User;
use App\Models\Order;
use App\Models\Shop;
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
            ->join('shops', 'orders.shop_id', '=', 'shops.id')
            ->select(
                'orders.order_code',
                'orders.total',
                'orders.status',
                'orders.created_at',
                'users.name as customer_name',
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

    public function orders(Request $request)
    {
        $query = Order::with(['user']);

        // Filter by shop
        if ($request->filled('shop_id')) {
            $query->where('shop_id', $request->shop_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->orderByDesc('created_at')->paginate(20);

        $shops = Shop::all();

        return view('admin.orders.index', compact('orders', 'shops'));
    }

    public function exportOrders(Request $request)
    {
        $query = Order::with(['user', 'items']);

        // Same filters as above
        if ($request->filled('shop_id')) {
            $query->where('shop_id', $request->shop_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->orderByDesc('created_at')->get();

        $filename = 'orders_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($orders) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'Order Code',
                'Customer Name',
                'Customer Phone',
                'Shop Name',
                'Total Amount',
                'Status',
                'Payment Method',
                'Ordered At',
                'Delivery Address',
                'Note'
            ]);

            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->order_code,
                    $order->customer_name,
                    $order->customer_phone,
                    $order->shop->name,
                    $order->total,
                    $order->status,
                    $order->payment_method,
                    $order->created_at->format('Y-m-d H:i:s'),
                    $order->delivery_address,
                    $order->note
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
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

        $validated['password'] = bcrypt($validated['password']);
        $validated['is_active'] = $request->has('is_active');

        try {
            User::create($validated);
            return redirect()->route('admin.users.index')
                ->with('success', 'Tạo user thành công!');
        } catch (\Exception $e) {
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

        // Nếu có password mới, validate và update
        if ($request->filled('password')) {
            $request->validate([
                'password' => 'required|string|min:8|confirmed',
            ]);
            $validated['password'] = bcrypt($request->password);
        }

        $validated['is_active'] = $request->has('is_active');

        try {
            $user->update($validated);
            return redirect()->route('admin.users.index')
                ->with('success', 'Cập nhật user thành công!');
        } catch (\Exception $e) {
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
