<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Display a listing of the customer's orders.
     */
    public function index(Request $request)
    {
        $query = Order::where('user_id', Auth::id())->with('shop', 'items.product');

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $orders = $query->orderByDesc('created_at')->paginate(10);

        return view('customer.orders.index', compact('orders'));
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $order->load('shop', 'items.product', 'delivery');

        return view('customer.orders.show', compact('order'));
    }

    /**
     * Cancel the specified order (only if pending).
     */
    public function cancel(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        if ($order->status !== 'pending') {
            return back()->with('error', 'Không thể hủy đơn hàng đã được xác nhận hoặc đang xử lý.');
        }

        $order->update(['status' => 'cancelled']);

        return back()->with('success', 'Hủy đơn hàng thành công.');
    }
}
