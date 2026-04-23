<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Get shop for authenticated user
     */
    private function getShop()
    {
        $shop = Shop::where('user_id', Auth::id())->first();
        if (!$shop) {
            abort(403, 'Unauthorized');
        }
        return $shop;
    }

    /**
     * Display a listing of orders
     */
    public function index()
    {
        $shop = $this->getShop();
        $orders = $shop->orders()->with('items.product')->paginate(15);

        return view('shop.orders.index', compact('shop', 'orders'));
    }

    /**
     * Show the form for creating a new order
     */
    public function create()
    {
        $shop = $this->getShop();
        return view('shop.orders.create', compact('shop'));
    }

    /**
     * Store a newly created order
     */
    public function store(Request $request)
    {
        $shop = $this->getShop();

        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_address' => 'required|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'status' => 'nullable|in:pending,confirmed,preparing,delivering,delivered,cancelled',
        ]);

        $order = Order::create([
            'user_id' => Auth::id(),
            'shop_id' => $shop->id,
            'order_code' => 'FH-' . now()->format('Ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(5)),
            'subtotal' => $validated['total'],
            'total' => $validated['total'],
            'status' => $validated['status'] ?? 'pending',
        ]);

        \App\Models\OrderDelivery::create([
            'order_id' => $order->id,
            'customer_phone' => $validated['customer_phone'],
            'delivery_address' => $validated['customer_address'],
        ]);

        return redirect()->route('shop.orders.show', $order)->with('success', 'Đơn hàng đã được tạo thành công!');
    }

    /**
     * Display the specified order
     */
    public function show(Order $order)
    {
        $shop = $this->getShop();

        if ($order->shop_id !== $shop->id) {
            abort(403, 'Unauthorized');
        }

        $order->load('items.product');

        return view('shop.orders.show', compact('shop', 'order'));
    }

    /**
     * Show the form for editing the order
     */
    public function edit(Order $order)
    {
        $shop = $this->getShop();

        if ($order->shop_id !== $shop->id) {
            abort(403, 'Unauthorized');
        }

        $order->load('items.product');

        return view('shop.orders.edit', compact('shop', 'order'));
    }

    /**
     * Update the specified order
     */
    public function update(Request $request, Order $order)
    {
        $shop = $this->getShop();

        if ($order->shop_id !== $shop->id) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,preparing,delivering,delivered,cancelled',
        ]);

        if ($validated['status'] === 'delivered' && $order->status !== 'delivered') {
            foreach ($order->items as $item) {
                if ($item->product) {
                    $item->product->increment('total_sold', $item->quantity);
                }
            }
        }

        $order->update($validated);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('shop.orders.show', $order)->with('success', 'Đơn hàng đã được cập nhật!');
    }

    /**
     * Delete the specified order
     */
    public function destroy(Order $order)
    {
        $shop = $this->getShop();

        if ($order->shop_id !== $shop->id) {
            abort(403, 'Unauthorized');
        }

        $order->delete();

        return redirect()->route('shop.orders.index')->with('success', 'Đơn hàng đã được xóa!');
    }
}
