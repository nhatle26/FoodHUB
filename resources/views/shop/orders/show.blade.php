@extends('layouts.shop')

@section('title', 'Chi tiết đơn hàng #' . $order->id)

@section('content')
    <div class="order-detail-header">
        <div>
            <a href="{{ route('shop.orders.index') }}" class="btn-back"><i class="bi bi-chevron-left"></i> Quay lại</a>
            <h2>Chi tiết đơn hàng #{{ $order->id }}</h2>
        </div>
    </div>

    <div class="order-detail-container">
        <!-- Left Column: Order Info -->
        <div class="order-detail-main">
            <!-- Order Status -->
            <div class="order-section">
                <h3>Trạng thái đơn hàng</h3>
                <div class="status-timeline">
                    <div class="timeline-item @if(in_array($order->status, ['confirmed', 'preparing', 'delivering', 'delivered'])) completed @endif">
                        <div class="timeline-icon"><i class="bi bi-check-circle"></i></div>
                        <div class="timeline-text">
                            <div class="timeline-label">Chờ xử lý</div>
                            <small>{{ $order->created_at->format('d/m/Y H:i') }}</small>
                        </div>
                    </div>
                    <div class="timeline-item @if(in_array($order->status, ['delivering', 'delivered'])) completed @endif">
                        <div class="timeline-icon"><i class="bi bi-check-circle"></i></div>
                        <div class="timeline-text">
                            <div class="timeline-label">Đã xác nhận</div>
                            <small>{{ $order->created_at->format('d/m/Y H:i') }}</small>
                        </div>
                    </div>
                    <div class="timeline-item @if(in_array($order->status, ['delivered'])) completed @endif">
                        <div class="timeline-icon"><i class="bi bi-check-circle"></i></div>
                        <div class="timeline-text">
                            <div class="timeline-label">Đang giao</div>
                            <small>{{ $order->created_at->format('d/m/Y H:i') }}</small>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-icon"><i class="bi bi-check-circle"></i></div>
                        <div class="timeline-text">
                            <div class="timeline-label">Đã giao</div>
                            <small>-</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer Info -->
            <div class="order-section">
                <h3>Thông tin khách hàng</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <label>Tên khách hàng</label>
                        <p>{{ $order->user->customer->full_name ?? ($order->user->shop->name ?? $order->user->email) }}</p>
                    </div>
                    <div class="info-item">
                        <label>Số điện thoại</label>
                        <p>{{ $order->delivery->customer_phone ?? 'N/A' }}</p>
                    </div>
                    <div class="info-item full-width">
                        <label>Địa chỉ giao hàng</label>
                        <p>{{ $order->delivery->delivery_address ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="order-section">
                <h3>Chi tiết sản phẩm</h3>
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
                        @if($order->items && count($order->items) > 0)
                            @foreach($order->items as $item)
                                <tr>
                                    <td>{{ $item->product_name ?? 'N/A' }}</td>
                                    <td class="center">{{ $item->quantity ?? 0 }}</td>
                                    <td class="right">{{ number_format($item->product_price ?? 0, 0, ',', '.') }}₫</td>
                                    <td class="right price">{{ number_format($item->subtotal ?? 0, 0, ',', '.') }}₫</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4" class="center">Không có sản phẩm</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right Column: Summary -->
        <div class="order-detail-sidebar">
            <!-- Order Summary -->
            <div class="summary-card">
                <h4>Tóm tắt đơn hàng</h4>

                <div class="summary-item">
                    <span>Tổng giá trị:</span>
                    <strong>{{ number_format($order->total ?? 0, 0, ',', '.') }}₫</strong>
                </div>

                <div class="status-section">
                    <label>Trạng thái hiện tại:</label>
                    <span class="status-badge status-{{ $order->status }} large">
                        @switch($order->status)
                            @case('pending')
                                Chờ xử lý
                                @break
                            @case('confirmed')
                                Đã xác nhận
                                @break
                            @case('delivering')
                                Đang giao
                                @break
                            @case('delivered')
                                Đã giao
                                @break
                            @case('cancelled')
                                Đã hủy
                                @break
                        @endswitch
                    </span>
                </div>

                <div class="order-dates">
                    <div>
                        <small>Ngày đặt hàng</small>
                        <p>{{ $order->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <small>Cập nhật lần cuối</small>
                        <p>{{ $order->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>

                <div class="action-buttons">
                    <div class="status-update-section">
                        <label style="font-size: 12px; color: var(--text-secondary); margin-bottom: 8px; display: block;">Cập nhật trạng thái:</label>
                        <div style="display: flex; gap: 8px;">
                            <select id="statusSelect" class="status-select-show" style="flex: 1;">
                                <option value="pending" @if($order->status == 'pending') selected @endif>Chờ xử lý</option>
                                <option value="confirmed" @if($order->status == 'confirmed') selected @endif>Đã xác nhận</option>
                                <option value="delivering" @if($order->status == 'delivering') selected @endif>Đang giao</option>
                                <option value="delivered" @if($order->status == 'delivered') selected @endif>Đã giao</option>
                                <option value="cancelled" @if($order->status == 'cancelled') selected @endif>Đã hủy</option>
                            </select>
                            <button type="button" class="btn btn-primary" onclick="updateOrderStatusShow({{ $order->id }}, document.getElementById('statusSelect').value)" style="white-space: nowrap;">
                                <i class="bi bi-check-circle"></i> Cập nhật
                            </button>
                        </div>
                    </div>
                    <form action="{{ route('shop.orders.destroy', $order->id) }}" method="POST" class="full-width">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger full-width" onclick="return confirm('Xác nhận xóa đơn hàng này?')">
                            <i class="bi bi-trash3"></i> Xóa đơn hàng
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function updateOrderStatusShow(orderId, status) {
            fetch(`/shop/orders/${orderId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ status: status })
            })
            .then(response => {
                if (!response.ok) throw new Error('Failed to update');
                return response.json();
            })
            .then(data => {
                // Reload page to see updated status
                location.reload();
            })
            .catch(error => {
                alert('Có lỗi khi cập nhật trạng thái');
                console.error('Error:', error);
            });
        }
    </script>
@endpush
