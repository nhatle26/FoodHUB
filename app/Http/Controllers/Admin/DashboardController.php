<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use App\Models\Shop;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Thống kê 4 số liệu tổng quát
        $totalShops = Shop::count();
        $totalUsers = User::where('role', 'customer')->count();
        $totalOrders = Order::count();
        $totalRevenue = Order::where('status', 'delivered')->sum('total_amount');

        // 2. Lấy dữ liệu doanh thu 7 ngày gần nhất cho Chart.js
        $revenueData = Order::where('status', 'delivered')
            ->where('ordered_at', '>=', now()->subDays(7))
            ->select(
                DB::raw('DATE(ordered_at) as date'),
                DB::raw('SUM(total_amount) as total')
            )
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        // 3. Top 5 đơn hàng mới nhất (Real-time)
        $recentOrders = Order::with('user')->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalShops', 'totalUsers', 'totalOrders', 'totalRevenue',
            'revenueData', 'recentOrders'
        ));
    }
}
