<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

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

        // Tính tổng tiền tạm tính
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

        // 1. Kiểm tra xem giỏ hàng hiện tại có món của shop khác không
        $existingCartItem = Cart::where('user_id', $userId)->first();

        if ($existingCartItem && $existingCartItem->shop_id != $product->shop_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Bạn chỉ có thể đặt món tại 1 shop trong một đơn hàng. Vui lòng xóa giỏ hàng cũ nếu muốn đặt tại shop này!'
            ], 400);
        }

        // 2. Kiểm tra nếu món này đã có trong giỏ thì tăng số lượng
        $cart = Cart::where('user_id', $userId)
                    ->where('product_id', $product->id)
                    ->first();

        if ($cart) {
            $cart->quantity += $request->quantity;
            $cart->save();
        } else {
            // 3. Nếu chưa có thì tạo mới
            Cart::create([
                'user_id' => $userId,
                'product_id' => $product->id,
                'shop_id' => $product->shop_id,
                'quantity' => $request->quantity,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Đã thêm món vào giỏ hàng!',
            'cart_count' => Cart::where('user_id', $userId)->count()
        ]);
    }

    /**
     * Cập nhật số lượng món ăn trong giỏ
     */
    public function updateQuantity(Request $request, $id)
    {
        $cart = Cart::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        $request->validate(['quantity' => 'required|integer|min:1']);

        $cart->update(['quantity' => $request->quantity]);

        return back()->with('success', 'Đã cập nhật số lượng!');
    }

    /**
     * Xóa 1 món khỏi giỏ hàng
     */
    public function remove($id)
    {
        $cart = Cart::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $cart->delete();

        return back()->with('success', 'Đã xóa món khỏi giỏ hàng!');
    }

    /**
     * Xóa sạch giỏ hàng (Dùng khi user muốn đổi shop)
     */
    public function clear()
    {
        Cart::where('user_id', Auth::id())->delete();
        return back()->with('success', 'Đã làm trống giỏ hàng!');
    }
}
