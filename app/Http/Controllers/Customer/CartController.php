<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CartController extends Controller
{
    /**
     * Hiển thị danh sách món ăn trong giỏ hàng
     */
    public function index()
    {
        $cartItems = Cart::with('product.shop')
            ->where('user_id', Auth::id())
            ->get();

        $subtotal = $cartItems->sum(function($item) {
            return $item->product->price * $item->quantity;
        });

        return view('customer.cart.index', compact('cartItems', 'subtotal'));
    }

    /**
     * Thêm món ăn vào giỏ hàng
     */
    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);
        $userId = Auth::id();

        $existingCartItem = Cart::where('user_id', $userId)->first();

        if ($existingCartItem && $existingCartItem->shop_id != $product->shop_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Bạn chỉ có thể đặt món tại 1 shop trong một đơn hàng!'
            ], 400);
        }

        $cart = Cart::where('user_id', $userId)
                    ->where('product_id', $product->id)
                    ->first();

        if ($cart) {
            $cart->quantity += $request->quantity;
            $cart->save();
        } else {
            Cart::create([
                'user_id' => $userId,
                'product_id' => $product->id,
                'shop_id' => $product->shop_id,
                'quantity' => $request->quantity,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Đã thêm vào giỏ!',
            'cart_count' => Cart::where('user_id', $userId)->count()
        ]);
    }

    public function updateQuantity(Request $request, $id)
    {
        $cart = Cart::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $request->validate(['quantity' => 'required|integer|min:1']);
        $cart->update(['quantity' => $request->quantity]);
        return back()->with('success', 'Đã cập nhật!');
    }

    public function remove($id)
    {
        $cart = Cart::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $cart->delete();
        return back()->with('success', 'Đã xóa món!');
    }

    public function processCheckout(Request $request)
    {
        $cartItems = Cart::where('user_id', auth()->id())->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng trống!');
        }

        $subtotal = $cartItems->sum(function($item) {
            return $item->product->price * $item->quantity;
        });

        try {
            DB::beginTransaction();

            $order = Order::create([
                'user_id'          => auth()->id(),
                'shop_id'          => $cartItems->first()->shop_id,
                'order_code'       => 'FH-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5)),
                'customer_name'    => auth()->user()->name,
                'customer_phone'   => auth()->user()->phone ?? '0123456789',
                'delivery_address' => $request->delivery_address ?? auth()->user()->address ?? 'Đà Nẵng',
                'note'             => $request->note,
                'subtotal'         => $subtotal,
                'delivery_fee'     => 0,
                'discount_amount'  => 0,
                'total_amount'     => $subtotal,
                'payment_method'   => $request->payment_method ?? 'cod',
                'payment_status'   => 'pending',
                'status'           => 'pending',
                'ordered_at'       => now(),
            ]);

            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id'     => $order->id,
                    'product_id'   => $item->product_id,
                    'product_name' => $item->product->name,
                    'unit_price'   => $item->product->price,
                    'quantity'     => $item->quantity,
                    'line_total'   => $item->product->price * $item->quantity,
                    'note'         => null,
                ]);
            }

            Cart::where('user_id', auth()->id())->delete();

            DB::commit();
            return redirect()->route('cart.index')->with('success', 'Bèng đặt đơn thành công rồi!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }
}
