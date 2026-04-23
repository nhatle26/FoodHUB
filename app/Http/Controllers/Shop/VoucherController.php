<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VoucherController extends Controller
{
    private function getShop()
    {
        $shop = Shop::where('user_id', Auth::id())->first();
        if (!$shop) abort(403, 'Unauthorized');
        return $shop;
    }

    public function index()
    {
        $shop = $this->getShop();
        $vouchers = Voucher::where('shop_id', $shop->id)->orderByDesc('created_at')->paginate(10);
        return view('shop.vouchers.index', compact('shop', 'vouchers'));
    }

    public function create()
    {
        $shop = $this->getShop();
        return view('shop.vouchers.create', compact('shop'));
    }

    public function store(Request $request)
    {
        $shop = $this->getShop();

        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:vouchers,code',
            'type' => 'required|in:percent,fixed',
            'value' => 'required|numeric|min:0|max:999999999',
            'min_order_amount' => 'nullable|numeric|min:0|max:999999999',
            'max_discount' => 'nullable|numeric|min:0|max:999999999',
            'usage_limit' => 'nullable|integer|min:1|max:9999',
            'expires_at' => 'nullable|date|after:today',
            'is_active' => 'boolean',
        ]);

        $validated['shop_id'] = $shop->id;
        $validated['is_active'] = $request->has('is_active');

        Voucher::create($validated);

        return redirect()->route('shop.vouchers.index')->with('success', 'Tạo voucher thành công!');
    }

    public function edit(Voucher $voucher)
    {
        $shop = $this->getShop();
        if ($voucher->shop_id !== $shop->id) abort(403);
        
        return view('shop.vouchers.edit', compact('shop', 'voucher'));
    }

    public function update(Request $request, Voucher $voucher)
    {
        $shop = $this->getShop();
        if ($voucher->shop_id !== $shop->id) abort(403);

        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:vouchers,code,' . $voucher->id,
            'type' => 'required|in:percent,fixed',
            'value' => 'required|numeric|min:0|max:999999999',
            'min_order_amount' => 'nullable|numeric|min:0|max:999999999',
            'max_discount' => 'nullable|numeric|min:0|max:999999999',
            'usage_limit' => 'nullable|integer|min:1|max:9999',
            'expires_at' => 'nullable|date',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $voucher->update($validated);

        return redirect()->route('shop.vouchers.index')->with('success', 'Cập nhật voucher thành công!');
    }

    public function destroy(Voucher $voucher)
    {
        $shop = $this->getShop();
        if ($voucher->shop_id !== $shop->id) abort(403);

        $voucher->delete();
        return redirect()->route('shop.vouchers.index')->with('success', 'Xóa voucher thành công!');
    }
}
