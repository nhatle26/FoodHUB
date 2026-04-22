@extends('layouts.admin')

@section('title', 'Quản lý Tất cả Đơn hàng')
@section('page-title', 'Đơn hàng Toàn hệ thống')
@section('page-subtitle', 'Quản lý, theo dõi và xuất dữ liệu đơn hàng của tất cả các Shop')

@section('content')
<div class="admin-card mb-4">
    <form action="{{ route('admin.orders.index') }}" method="GET" class="row g-3 align-items-end">
        <div class="col-md-3">
            <label class="form-label text-muted">Trạng thái</label>
            <select name="status" class="form-select">
                <option value="">Tất cả</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                <option value="delivering" {{ request('status') == 'delivering' ? 'selected' : '' }}>Đang giao</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label text-muted">Cửa hàng (Shop)</label>
            <select name="shop_id" class="form-select">
                <option value="">Tất cả Shop</option>
                @foreach($shops as $shop)
                    <option value="{{ $shop->id }}" {{ request('shop_id') == $shop->id ? 'selected' : '' }}>{{ $shop->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-5 d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-filter"></i> Lọc kết quả</button>
            <a href="{{ route('admin.orders.export', request()->all()) }}" class="btn btn-success"><i class="bi bi-file-earmark-excel"></i> Xuất CSV</a>
        </div>
    </form>
</div>

<div class="admin-card">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th>Mã Đơn</th>
                    <th>Ngày Đặt</th>
                    <th>Shop</th>
                    <th>Khách Hàng</th>
                    <th>Tổng Tiền</th>
                    <th>Trạng Thái</th>
                    <th>Hành Động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr>
                    <td class="fw-bold">#{{ $order->order_code }}</td>
                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $order->shop->name ?? 'N/A' }}</td>
                    <td>
                        {{ $order->user->customer->full_name ?? ($order->user->email) }}<br>
                        <small class="text-muted">{{ $order->delivery->customer_phone ?? '' }}</small>
                    </td>
                    <td class="fw-bold text-primary">{{ number_format($order->total) }}₫</td>
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
                    <td>
                        <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary">Chi tiết</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">Không tìm thấy đơn hàng nào.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($orders->hasPages())
    <div class="mt-4 d-flex justify-content-end">
        {{ $orders->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection
