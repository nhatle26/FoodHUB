@extends('layouts.admin')

@section('title', 'Chi tiết Đơn hàng')
@section('page-title', 'Chi tiết Đơn hàng: #' . $order->order_code)
@section('page-subtitle', 'Xem chi tiết các món ăn, giao hàng và shop cung cấp')

@section('content')
<div class="mb-3">
    <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Quay lại</a>
</div>

<div class="row g-4">
    <div class="col-md-8">
        <div class="admin-card h-100">
            <h5 class="fw-bold mb-4">Danh sách món ăn</h5>
            <div class="table-responsive">
                <table class="table">
                    <thead class="table-light">
                        <tr>
                            <th>Món</th>
                            <th class="text-center">Số lượng</th>
                            <th class="text-end">Đơn giá</th>
                            <th class="text-end">Tổng</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td>{{ $item->product->name ?? 'Sản phẩm đã bị xóa' }}</td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-end">{{ number_format($item->product_price, 0, ',', '.') }}₫</td>
                            <td class="text-end fw-bold">{{ number_format($item->subtotal, 0, ',', '.') }}₫</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end fw-bold">Tạm tính</td>
                            <td class="text-end fw-bold">{{ number_format($order->subtotal, 0, ',', '.') }}₫</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-end fw-bold text-primary fs-5 border-0">Tổng cộng</td>
                            <td class="text-end fw-bold text-primary fs-5 border-0">{{ number_format($order->total, 0, ',', '.') }}₫</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="admin-card mb-4">
            <h5 class="fw-bold mb-3">Thông tin Đơn hàng</h5>
            <p class="mb-2"><strong>Ngày đặt:</strong> {{ $order->created_at->format('d/m/Y H:i:s') }}</p>
            <p class="mb-2"><strong>Trạng thái:</strong> 
                <span class="badge bg-{{ $order->status === 'completed' || $order->status === 'delivered' ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'warning') }}">
                    {{ strtoupper($order->status) }}
                </span>
            </p>
        </div>

        <div class="admin-card mb-4">
            <h5 class="fw-bold mb-3">Thông tin Khách hàng</h5>
            <p class="mb-2"><strong>Tên:</strong> {{ $order->user->customer->full_name ?? ($order->user->email) }}</p>
            <p class="mb-2"><strong>Số điện thoại:</strong> {{ $order->delivery->customer_phone ?? 'N/A' }}</p>
            <p class="mb-0"><strong>Địa chỉ giao hàng:</strong> {{ $order->delivery->delivery_address ?? 'N/A' }}</p>
        </div>

        <div class="admin-card">
            <h5 class="fw-bold mb-3">Thông tin Shop</h5>
            <p class="mb-2"><strong>Tên Shop:</strong> {{ $order->shop->name ?? 'N/A' }}</p>
            <p class="mb-0"><strong>ID Shop:</strong> {{ $order->shop_id }}</p>
        </div>
    </div>
</div>
@endsection
