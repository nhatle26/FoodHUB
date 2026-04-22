<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ShopManageController extends Controller
{
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
}
