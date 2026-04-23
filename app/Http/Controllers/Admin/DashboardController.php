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
        $pendingShops = Schema::hasTable('shops') ? DB::table('shops')
            ->leftJoin('shop_details', 'shops.id', '=', 'shop_details.shop_id')
            ->where('shops.status', 'pending')
            ->select('shops.id', 'shops.name', DB::raw("COALESCE(shop_details.address, '') as address"), 'shop_details.logo')
            ->limit(3)->get() : [];
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
        $orderTrend = [];
        $trendLabels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $orderCountForDay = Schema::hasTable('orders') ? DB::table('orders')->whereDate('created_at', $date)->count() : 0;
            $orderTrend[] = $orderCountForDay;
            
            $daysMap = [0 => 'CN', 1 => 'T2', 2 => 'T3', 3 => 'T4', 4 => 'T5', 5 => 'T6', 6 => 'T7'];
            $trendLabels[] = $daysMap[$date->dayOfWeek];
        }

        return view('admin.dashboard', compact(
            'userCount',
            'categoryCount',
            'shopCount',
            'orderCount',
            'todayOrders',
            'pendingShopCount',
            'pendingShops',
            'latestOrders',
            'orderTrend',
            'trendLabels'
        ));
    }
}
