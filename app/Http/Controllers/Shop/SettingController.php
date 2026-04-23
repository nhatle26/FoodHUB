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

    /**
     * Lưu ảnh từ base64 string (Cropper.js)
     */
    private function saveBase64Image(string $base64Data, string $folder): string
    {
        $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $base64Data);
        $imageData = base64_decode($imageData);

        $filename = $folder . '/' . Str::uuid() . '.jpg';
        \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $imageData);

        return $filename;
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
            'cover_image_data' => 'nullable|string',
            'logo_data' => 'nullable|string',
            'banner' => 'nullable|image|max:2048',
            'logo' => 'nullable|image|max:1024',
        ]);

        $shop->name = $validated['name'];
        if ($shop->isDirty('name')) {
            $shop->slug = Str::slug($validated['name']) . '-' . $shop->id;
        }
        $shop->save();

        $details = $shop->details;
        $details->description = $validated['description'] ?? null;
        $details->phone = $validated['phone'] ?? null;
        $details->address = $validated['address'] ?? null;
        $details->open_time = $validated['open_time'] ?? null;
        $details->close_time = $validated['close_time'] ?? null;

        if ($request->filled('cover_image_data')) {
            $details->cover_image = $this->saveBase64Image($request->cover_image_data, 'shops/covers');
        } elseif ($request->hasFile('banner')) {
            $path = $request->file('banner')->store('shops/banners', 'public');
            $details->cover_image = $path;
        }

        if ($request->filled('logo_data')) {
            $details->logo = $this->saveBase64Image($request->logo_data, 'shops/logos');
        } elseif ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('shops/logos', 'public');
            $details->logo = $path;
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
