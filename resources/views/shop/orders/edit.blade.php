@extends('layouts.shop')

@section('title', 'Chỉnh sửa đơn hàng #' . $order->id)

@section('content')
    <div class="form-header">
        <h2>Chỉnh sửa đơn hàng #{{ $order->id }}</h2>
        <a href="{{ route('shop.orders.show', $order->id) }}" class="btn-back"><i class="bi bi-chevron-left"></i> Quay lại</a>
    </div>

    <form action="{{ route('shop.orders.update', $order->id) }}" method="POST" class="order-form">
        @csrf
        @method('PUT')

        <div class="form-section">
            <h3>Cập nhật trạng thái đơn hàng</h3>

            <div class="form-group">
                <label for="status">Trạng thái <span class="required">*</span></label>
                <select id="status" name="status" class="form-control @error('status') is-invalid @enderror" required>
                    <option value="pending" @if($order->status == 'pending') selected @endif>Chờ xử lý</option>
                    <option value="confirmed" @if($order->status == 'confirmed') selected @endif>Đã xác nhận</option>
                    <option value="delivering" @if($order->status == 'delivering') selected @endif>Đang giao</option>
                    <option value="delivered" @if($order->status == 'delivered') selected @endif>Đã giao</option>
                    <option value="cancelled" @if($order->status == 'cancelled') selected @endif>Đã hủy</option>
                </select>
                @error('status')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="form-section">
            <h3>Thông tin đơn hàng</h3>

            <div class="form-group">
                <label>Khách hàng</label>
                <p class="form-static">{{ $order->user->customer->full_name ?? ($order->user->shop->name ?? $order->user->email) }}</p>
            </div>

            <div class="form-group">
                <label>Số điện thoại</label>
                <p class="form-static">{{ $order->delivery->customer_phone ?? 'N/A' }}</p>
            </div>

            <div class="form-group">
                <label>Địa chỉ giao hàng</label>
                <p class="form-static">{{ $order->delivery->delivery_address ?? 'N/A' }}</p>
            </div>

            <div class="form-group">
                <label>Tổng tiền</label>
                <p class="form-static price">{{ number_format($order->total ?? 0, 0, ',', '.') }}₫</p>
            </div>
        </div>

        <div class="form-section">
            <h3>Sản phẩm trong đơn hàng</h3>
            @if($order->items && is_array($order->items))
                <table class="order-items-table">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th class="center">Số lượng</th>
                            <th class="right">Giá</th>
                            <th class="right">Tổng</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                            <tr>
                                <td>{{ $item['product_name'] ?? 'N/A' }}</td>
                                <td class="center">{{ $item['quantity'] ?? 0 }}</td>
                                <td class="right">{{ number_format($item['price'] ?? 0, 0, ',', '.') }}₫</td>
                                <td class="right price">{{ number_format(($item['quantity'] ?? 0) * ($item['price'] ?? 0), 0, ',', '.') }}₫</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <div class="form-actions">
            <a href="{{ route('shop.orders.show', $order->id) }}" class="btn btn-secondary">Hủy</a>
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Lưu thay đổi</button>
        </div>
    </form>
@endsection
