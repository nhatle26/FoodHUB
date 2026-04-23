<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use Illuminate\Support\Facades\Auth;

class ShopController extends Controller
{
    public function dashboard()
    {
        $shop = Shop::with(['orders', 'metrics'])->where('user_id', Auth::id())->first();

        if (!$shop) {
            abort(403, 'Unauthorized');
        }

        // --- DASHBOARD METRICS ---
        $now = \Carbon\Carbon::now();
        
        // Orders (This month)
        $ordersThisMonth = $shop->orders()->whereMonth('created_at', $now->month)->whereYear('created_at', $now->year)->count();
        $ordersLastMonth = $shop->orders()->whereMonth('created_at', $now->copy()->subMonth()->month)->whereYear('created_at', $now->copy()->subMonth()->year)->count();
        $growthOrders = $ordersLastMonth > 0 ? round((($ordersThisMonth - $ordersLastMonth) / $ordersLastMonth) * 100) : ($ordersThisMonth > 0 ? 100 : 0);

        // Revenue (This month - delivered only)
        $revThisMonth = $shop->orders()->whereMonth('created_at', $now->month)->whereYear('created_at', $now->year)->where('status', 'delivered')->sum('total');
        $revLastMonth = $shop->orders()->whereMonth('created_at', $now->copy()->subMonth()->month)->whereYear('created_at', $now->copy()->subMonth()->year)->where('status', 'delivered')->sum('total');
        $growthRev = $revLastMonth > 0 ? round((($revThisMonth - $revLastMonth) / $revLastMonth) * 100) : ($revThisMonth > 0 ? 100 : 0);

        // Pending Orders (Total current)
        $pendingNow = $shop->orders()->where('status', 'pending')->count();
        $pendingLastMonth = $shop->orders()->whereMonth('created_at', '<', $now->month)->where('status', 'pending')->count();
        $growthPending = $pendingLastMonth > 0 ? round((($pendingNow - $pendingLastMonth) / $pendingLastMonth) * 100) : ($pendingNow > 0 ? 100 : 0);

        // Average Rating
        $avgRating = $shop->metrics ? $shop->metrics->rating_avg : 0;
        $growthRating = 0; // Rating growth could be calculated via reviews history if needed, defaulted to 0 for now

        // --- 7-DAY REVENUE CHART ---
        $chartLabels = [];
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = \Carbon\Carbon::today()->subDays($i);
            $chartLabels[] = $date->format('d/m');
            $chartData[] = $shop->orders()->whereDate('created_at', $date)->where('status', 'delivered')->sum('total');
        }

        return view('shop.dashboard', compact(
            'shop', 'ordersThisMonth', 'growthOrders', 
            'revThisMonth', 'growthRev', 
            'pendingNow', 'growthPending', 
            'avgRating', 'growthRating',
            'chartLabels', 'chartData'
        ));
    }

    public function revenue()
    {
        $shop = Shop::where('user_id', Auth::id())->first();

        if (!$shop) {
            abort(403, 'Unauthorized');
        }

        // --- STATS ---
        $totalOrders = $shop->orders()->count();
        $totalRev = $shop->orders()->where('status', 'delivered')->sum('total');
        $avgOrderValue = $totalOrders > 0 ? round($totalRev / $totalOrders) : 0;

        // --- 30-DAY LINE CHART ---
        $lineChartLabels = [];
        $lineChartData = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = \Carbon\Carbon::today()->subDays($i);
            $lineChartLabels[] = $date->format('d/m');
            $lineChartData[] = $shop->orders()->whereDate('created_at', $date)->where('status', 'delivered')->sum('total');
        }

        // --- 7-DAY BAR CHART ---
        $barChartLabels = [];
        $barChartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = \Carbon\Carbon::today()->subDays($i);
            // Example: 'T2', 'T3', etc.
            $daysMap = [0 => 'CN', 1 => 'T2', 2 => 'T3', 3 => 'T4', 4 => 'T5', 5 => 'T6', 6 => 'T7'];
            $barChartLabels[] = $daysMap[$date->dayOfWeek];
            $barChartData[] = $shop->orders()->whereDate('created_at', $date)->where('status', 'delivered')->sum('total');
        }

        // --- PIE CHART (ORDER STATUS) ---
        $statusCounts = $shop->orders()
            ->select('status', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')->toArray();

        // Mapping standard statuses to Pie Chart data: Đã giao, Đang giao, Đã xác nhận, Chờ xử lý
        $pieData = [
            $statusCounts['delivered'] ?? 0,
            $statusCounts['delivering'] ?? 0,
            $statusCounts['confirmed'] ?? 0,
            $statusCounts['pending'] ?? 0,
        ];

        return view('shop.revenue', compact(
            'shop', 'totalRev', 'totalOrders', 'avgOrderValue',
            'lineChartLabels', 'lineChartData',
            'barChartLabels', 'barChartData',
            'pieData'
        ));
    }

    public function settings()
    {
        // Get shop info for current authenticated user
        $shop = Shop::where('user_id', Auth::id())->first();

        if (!$shop) {
            abort(403, 'Unauthorized');
        }

        return view('shop.settings', compact('shop'));
    }
}
