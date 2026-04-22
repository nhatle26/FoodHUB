<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Review;
use App\Models\ShopMetric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    public function store(Request $request, Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        if ($order->status !== 'completed' && $order->status !== 'delivered') {
            return back()->with('error', 'Bạn chỉ có thể đánh giá đơn hàng đã hoàn thành.');
        }

        if (Review::where('order_id', $order->id)->exists()) {
            return back()->with('error', 'Bạn đã đánh giá đơn hàng này rồi.');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();
            
            Review::create([
                'user_id' => Auth::id(),
                'shop_id' => $order->shop_id,
                'order_id' => $order->id,
                'rating' => $validated['rating'],
                'comment' => $validated['comment'],
            ]);

            // Update shop metrics
            $metric = ShopMetric::firstOrCreate(['shop_id' => $order->shop_id]);
            $metric->total_reviews += 1;
            $metric->rating = Review::where('shop_id', $order->shop_id)->avg('rating');
            $metric->save();

            DB::commit();
            return back()->with('success', 'Cảm ơn bạn đã đánh giá!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}
