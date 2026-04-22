@extends('layouts.auth')

@section('title', 'Đơn hàng của tôi')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Lịch sử đơn hàng</h2>
        <a href="{{ route('home') }}" class="btn btn-outline-brand rounded-pill">Tiếp tục mua sắm</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if($orders->count() > 0)
        <div class="card border-0 shadow-sm rounded-4">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Mã đơn</th>
                            <th>Shop</th>
                            <th>Ngày đặt</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th class="text-end pe-4">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                        <tr>
                            <td class="ps-4 fw-medium">#{{ $order->order_code }}</td>
                            <td>{{ $order->shop->name ?? 'N/A' }}</td>
                            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            <td class="fw-bold text-brand">{{ number_format($order->total, 0, ',', '.') }}₫</td>
                            <td>
                                <span class="badge bg-{{ $order->status === 'completed' || $order->status === 'delivered' ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'warning') }}">
                                    @switch($order->status)
                                        @case('pending') Chờ xác nhận @break
                                        @case('confirmed') Đã xác nhận @break
                                        @case('preparing') Đang chuẩn bị @break
                                        @case('delivering') Đang giao @break
                                        @case('delivered') Đã giao @break
                                        @case('completed') Hoàn thành @break
                                        @case('cancelled') Đã hủy @break
                                        @default {{ $order->status }}
                                    @endswitch
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('customer.orders.show', $order->id) }}" class="btn btn-sm btn-outline-secondary rounded-pill me-2">Chi tiết</a>
                                @if($order->status === 'pending')
                                    <form action="{{ route('customer.orders.cancel', $order->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill" onclick="return confirm('Bạn chắc chắn muốn hủy đơn hàng này?')">Hủy đơn</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-4 d-flex justify-content-end">
            {{ $orders->links('pagination::bootstrap-5') }}
        </div>
    @else
        <div class="text-center py-5">
            <h4 class="text-muted">Bạn chưa có đơn hàng nào</h4>
            <a href="{{ route('home') }}" class="btn btn-brand rounded-pill mt-3 px-4">Khám phá ngay</a>
        </div>
    @endif
</div>
@endsection
