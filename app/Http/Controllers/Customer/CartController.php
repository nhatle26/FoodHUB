<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CartController extends Controller
{
    public function index()
    {
        $cartSession = session('cart', ['shop_id' => null, 'items' => []]);
        $items = $cartSession['items'];
        $productIds = array_keys($items);
        $products = \App\Models\Product::with('shop')->whereIn('id', $productIds)->get()->keyBy('id');

        $cartItems = collect();
        $subtotal = 0;

        foreach ($items as $pid => $qty) {
            if ($products->has($pid)) {
                $product = $products[$pid];
                $subtotal += $product->price * $qty;
                $cartItems->push((object)[
                    'id' => $pid, // Sử dụng product_id làm ID item trong views
                    'product_id' => $pid,
                    'quantity' => $qty,
                    'product' => $product,
                    'shop' => $product->shop
                ]);
            }
        }

        return view('customer.cart.index', compact('cartItems', 'subtotal'));
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = \App\Models\Product::findOrFail($request->product_id);
        $cart = session('cart', ['shop_id' => null, 'items' => []]);

        if ($cart['shop_id'] && $cart['shop_id'] != $product->shop_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Bạn chỉ có thể đặt món tại 1 shop trong một đơn hàng!'
            ], 400);
        }

        $cart['shop_id'] = $product->shop_id;
        if (isset($cart['items'][$product->id])) {
            $cart['items'][$product->id] += $request->quantity;
        } else {
            $cart['items'][$product->id] = $request->quantity;
        }

        session(['cart' => $cart]);

        return response()->json([
            'status' => 'success',
            'message' => 'Đã thêm vào giỏ!',
            'cart_count' => array_sum($cart['items'])
        ]);
    }

    public function updateQuantity(Request $request, $id)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);
        $cart = session('cart', ['shop_id' => null, 'items' => []]);
        
        if (isset($cart['items'][$id])) {
            $cart['items'][$id] = $request->quantity;
            session(['cart' => $cart]);
        }
        
        return back()->with('success', 'Đã cập nhật!');
    }

    public function remove($id)
    {
        $cart = session('cart', ['shop_id' => null, 'items' => []]);
        
        if (isset($cart['items'][$id])) {
            unset($cart['items'][$id]);
            if (empty($cart['items'])) {
                $cart['shop_id'] = null;
            }
            session(['cart' => $cart]);
        }
        
        return back()->with('success', 'Đã xóa món!');
    }

    public function processCheckout(Request $request)
    {
        $cartSession = session('cart', ['shop_id' => null, 'items' => []]);
        $items = $cartSession['items'];

        if (empty($items)) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng trống!');
        }

        $productIds = array_keys($items);
        $products = \App\Models\Product::whereIn('id', $productIds)->get()->keyBy('id');

        $subtotal = 0;
        foreach ($items as $pid => $qty) {
            if ($products->has($pid)) {
                $subtotal += $products[$pid]->price * $qty;
            }
        }

        try {
            DB::beginTransaction();

            $order = Order::create([
                'user_id'          => auth()->id(),
                'shop_id'          => $cartSession['shop_id'],
                'order_code'       => 'FH-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5)),
                'subtotal'         => $subtotal,
                'shipping_fee'     => 0,
                'discount'         => 0,
                'total'            => $subtotal,
                'payment_method'   => $request->payment_method ?? 'cod',
                'status'           => 'pending',
            ]);

            \App\Models\OrderDelivery::create([
                'order_id'         => $order->id,
                'customer_phone'   => auth()->user()->customer->phone ?? '0123456789',
                'delivery_address' => $request->delivery_address ?? (auth()->user()->customer->addresses->where('is_default', 1)->first()->address_line ?? 'Đà Nẵng'),
                'note'             => $request->note,
            ]);

            foreach ($items as $pid => $qty) {
                if ($products->has($pid)) {
                    $product = $products[$pid];
                    OrderItem::create([
                        'order_id'     => $order->id,
                        'product_id'   => $product->id,
                        'product_name' => $product->name,
                        'product_price' => $product->price,
                        'quantity'     => $qty,
                        'subtotal'     => $product->price * $qty,
                    ]);
                }
            }

            session()->forget('cart');

            DB::commit();
            return redirect()->route('customer.orders.index')->with('success', 'Bạn đặt đơn thành công rồi!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    /**
     * Đồng bộ giỏ hàng tạm (frontend) vào DB trước khi thanh toán
     */
    public function prepareCheckout(Request $request)
    {
        $request->validate([
            'shop_id' => 'required|exists:shops,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1'
        ]);

        $cart = [
            'shop_id' => $request->shop_id,
            'items' => []
        ];

        foreach ($request->items as $item) {
            $cart['items'][$item['product_id']] = $item['quantity'];
        }
        
        session(['cart' => $cart]);

        return response()->json([
            'status' => 'success',
            'redirect' => route('cart.index')
        ]);
    }
}
