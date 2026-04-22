<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class OrderManageController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('shop', 'user', 'delivery', 'items.product');

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->has('shop_id') && $request->shop_id !== '') {
            $query->where('shop_id', $request->shop_id);
        }

        $orders = $query->orderByDesc('created_at')->paginate(15);
        $shops = \App\Models\Shop::all();

        return view('admin.orders.index', compact('orders', 'shops'));
    }

    public function show(Order $order)
    {
        $order->load('shop', 'user.customer', 'delivery', 'items.product');
        return view('admin.orders.show', compact('order'));
    }

    public function exportCsv(Request $request)
    {
        $query = Order::with('shop', 'user.customer', 'delivery');

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->has('shop_id') && $request->shop_id !== '') {
            $query->where('shop_id', $request->shop_id);
        }

        $orders = $query->orderByDesc('created_at')->get();

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=orders_" . date('Y-m-d_H-i-s') . ".csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($orders) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for Excel UTF-8 reading
            fputs($file, $bom =(chr(0xEF) . chr(0xBB) . chr(0xBF)));

            fputcsv($file, ['ID Đơn hàng', 'Mã đơn', 'Ngày đặt', 'Khách hàng', 'SĐT', 'Địa chỉ', 'Tên Shop', 'Tổng tiền', 'Trạng thái']);

            foreach ($orders as $order) {
                $customerName = $order->user->customer->full_name ?? ($order->user->email);
                $customerPhone = $order->delivery->customer_phone ?? '';
                $deliveryAddress = $order->delivery->delivery_address ?? '';
                $shopName = $order->shop->name ?? '';
                $total = $order->total;
                $status = $order->status;
                $date = $order->created_at->format('d/m/Y H:i');

                fputcsv($file, [$order->id, $order->order_code, $date, $customerName, $customerPhone, $deliveryAddress, $shopName, $total, $status]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}
