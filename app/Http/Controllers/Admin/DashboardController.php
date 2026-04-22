<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
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
}
