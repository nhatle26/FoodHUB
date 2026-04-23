<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ShopManageController extends Controller
{
    /**
     * Danh sách tất cả shop (có filter theo status)
     */
    public function index(Request $request)
    {
        $query = Shop::with('details')
            ->withCount('products')
            ->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $shops = $query->paginate(15)->withQueryString();

        $statusCounts = Shop::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('admin.shops.index', compact('shops', 'statusCounts'));
    }

    /**
     * Xem chi tiết một shop
     */
    public function show($id)
    {
        $shop = Shop::with(['details', 'products', 'user'])
            ->findOrFail($id);

        $recentOrders = DB::table('orders')
            ->where('shop_id', $id)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('admin.shops.show', compact('shop', 'recentOrders'));
    }

    /**
     * Form chỉnh sửa shop
     */
    public function edit($id)
    {
        $shop = Shop::with('details')->findOrFail($id);
        $categories = \App\Models\Category::where('is_active', true)->orderBy('sort_order')->get();

        return view('admin.shops.edit', compact('shop', 'categories'));
    }

    /**
     * Lưu thay đổi shop
     */
    public function update(Request $request, $id)
    {
        $shop = Shop::with('details')->findOrFail($id);

        $request->validate([
            'name'        => 'required|max:100|unique:shops,name,' . $id,
            'status'      => 'required|in:pending,active,banned',
            'category_id' => 'required|exists:categories,id',
            'phone'       => 'nullable|max:15',
            'address'     => 'nullable|max:255',
            'description' => 'nullable|max:500',
            'open_time'   => 'nullable|date_format:H:i',
            'close_time'  => 'nullable|date_format:H:i',
        ]);

        $shop->update([
            'name'        => $request->name,
            'status'      => $request->status,
            'category_id' => $request->category_id,
        ]);

        // Cập nhật shop_details
        $details = $shop->details ?? new \App\Models\ShopDetail(['shop_id' => $shop->id]);
        $details->phone       = $request->phone;
        $details->address     = $request->address;
        $details->description = $request->description;
        $details->open_time   = $request->open_time;
        $details->close_time  = $request->close_time;
        $details->save();

        return redirect()->route('admin.shops.show', $id)
            ->with('success', 'Thông tin shop đã được cập nhật.');
    }

    /**
     * Danh sách shop chờ duyệt
     */
    public function pendingShops()
    {
        $pendingShops = DB::table('shops')
            ->leftJoin('shop_details', 'shops.id', '=', 'shop_details.shop_id')
            ->where('shops.status', 'pending')
            ->select(
                'shops.id',
                'shops.name',
                'shops.status',
                'shops.created_at',
                DB::raw("COALESCE(shop_details.address, '') as address"),
                DB::raw("COALESCE(shop_details.phone, '') as phone")
            )
            ->orderByDesc('shops.created_at')
            ->paginate(12);

        return view('admin.shops.pending', compact('pendingShops'));
    }

    public function approveShop($shopId)
    {
        DB::table('shops')
            ->where('id', $shopId)
            ->update(['status' => 'active', 'updated_at' => now()]);

        return redirect()->route('admin.dashboard')->with('success', 'Shop đã được duyệt thành công.');
    }

    public function rejectShop($shopId)
    {
        DB::table('shops')
            ->where('id', $shopId)
            ->update(['status' => 'banned', 'updated_at' => now()]);

        return redirect()->route('admin.dashboard')->with('success', 'Shop đã bị từ chối.');
    }
}
