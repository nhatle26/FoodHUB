<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SettingController extends Controller
{
    private function getShop()
    {
        $shop = Shop::where('user_id', Auth::id())->with('details')->first();
        if (!$shop) abort(403, 'Unauthorized');
        return $shop;
    }

    public function index()
    {
        $shop = $this->getShop();
        return view('shop.settings.index', compact('shop'));
    }

    public function update(Request $request)
    {
        $shop = $this->getShop();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'open_time' => 'nullable',
            'close_time' => 'nullable',
            'banner' => 'nullable|image|max:2048',
            'logo' => 'nullable|image|max:1024',
        ]);

        $shop->name = $validated['name'];
        if ($shop->isDirty('name')) {
            $shop->slug = Str::slug($validated['name']) . '-' . $shop->id;
        }
        $shop->description = $validated['description'] ?? null;
        $shop->save();

        $details = $shop->details;
        $details->phone = $validated['phone'] ?? null;
        $details->address = $validated['address'] ?? null;
        $details->open_time = $validated['open_time'] ?? null;
        $details->close_time = $validated['close_time'] ?? null;

        if ($request->hasFile('banner')) {
            $path = $request->file('banner')->store('shops/banners', 'public');
            $details->banner = '/storage/' . $path;
        }

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('shops/logos', 'public');
            $details->logo = '/storage/' . $path;
        }

        $details->save();

        return redirect()->route('shop.settings')->with('success', 'Cập nhật thông tin quán thành công!');
    }

    public function toggleStatus()
    {
        $shop = $this->getShop();
        
        // Toggle between active and inactive, but not pending/banned
        if ($shop->status === 'active') {
            $shop->status = 'inactive';
        } elseif ($shop->status === 'inactive') {
            $shop->status = 'active';
        } else {
            return back()->with('error', 'Không thể đổi trạng thái khi shop chưa được duyệt hoặc bị khóa.');
        }
        
        $shop->save();
        
        return back()->with('success', 'Đã thay đổi trạng thái hoạt động!');
    }
}
