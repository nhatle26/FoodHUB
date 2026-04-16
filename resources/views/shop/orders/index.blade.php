@extends('layouts.shop')

@section('title', 'Quản lý đơn hàng')

@section('content')
    <div class="page-header">
        <div>
            <h2>Quản lý đơn hàng</h2>
            <p class="text-muted">Theo dõi và quản lý tất cả đơn hàng</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-section">
        <div class="filter-group">
            <div class="search-box">
                <i class="bi bi-search"></i>
                <input type="text" id="searchInput" placeholder="Tìm theo mã đơn, khách hàng..." class="search-input">
            </div>

            <select id="statusFilter" class="filter-input">
                <option value="">Tất cả trạng thái</option>
                <option value="pending">Chờ xử lý</option>
                <option value="confirmed">Đã xác nhận</option>
                <option value="shipped">Đang giao</option>
                <option value="delivered">Đã giao</option>
                <option value="cancelled">Đã hủy</option>
            </select>

            <select id="sortFilter" class="filter-input">
                <option value="latest">Mới nhất</option>
                <option value="oldest">Cũ nhất</option>
                <option value="highest">Giá cao nhất</option>
                <option value="lowest">Giá thấp nhất</option>
            </select>

            <button class="btn-export">
                <i class="bi bi-download"></i> Xuất Excel
            </button>
        </div>
        <div class="filter-summary">
            Tìm thấy <strong>{{ $orders->total() }}</strong> đơn hàng
        </div>
    </div>

    <!-- Orders Table -->
    @if($orders->count() > 0)
        <div class="orders-table-wrapper">
            <table class="orders-table">
                <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>Liên hệ</th>
                        <th>Địa chỉ</th>
                        <th>Số món</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Thời gian</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        <tr>
                            <td><strong>#{{ $order->id }}</strong></td>
                            <td>{{ $order->user ? $order->user->name : ($order->customer_name ?? 'N/A') }}</td>
                            <td>
                                <small>{{ $order->customer_phone }}</small>
                            </td>
                            <td>
                                <small>{{ Str::limit($order->delivery_address, 30) }}</small>
                            </td>
                            <td class="center">{{ $order->items ? count($order->items) : 0 }}</td>
                            <td class="price">{{ number_format($order->total ?? 0, 0, ',', '.') }}₫</td>
                            <td>
                                <span class="status-badge status-{{ $order->status }}">
                                    @switch($order->status)
                                        @case('pending')
                                            Chờ xử lý
                                            @break
                                        @case('confirmed')
                                            Đã xác nhận
                                            @break
                                        @case('shipped')
                                            Đang giao
                                            @break
                                        @case('delivered')
                                            Đã giao
                                            @break
                                        @case('cancelled')
                                            Đã hủy
                                            @break
                                        @default
                                            {{ ucfirst($order->status) }}
                                    @endswitch
                                </span>
                            </td>
                            <td>
                                <small>{{ $order->created_at->format('d/m/Y') }}</small><br>
                                <small class="text-muted">{{ $order->created_at->format('H:i') }}</small>
                            </td>
                            <td class="actions">
                                <div class="action-dropdown">
                                    <select class="status-select" onchange="updateOrderStatus({{ $order->id }}, this.value)">
                                        <option value="pending" @if($order->status == 'pending') selected @endif>Chờ xử lý</option>
                                        <option value="confirmed" @if($order->status == 'confirmed') selected @endif>Đã xác nhận</option>
                                        <option value="shipped" @if($order->status == 'shipped') selected @endif>Đang giao</option>
                                        <option value="delivered" @if($order->status == 'delivered') selected @endif>Đã giao</option>
                                        <option value="cancelled" @if($order->status == 'cancelled') selected @endif>Đã hủy</option>
                                    </select>
                                </div>
                                <a href="{{ route('shop.orders.show', $order->id) }}" class="action-icon view" title="Xem chi tiết">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <form action="{{ route('shop.orders.destroy', $order->id) }}" method="POST" class="action-delete" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-icon delete" title="Xóa" onclick="return confirm('Xác nhận xóa?')">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="pagination-wrapper">
            {{ $orders->links() }}
        </div>
    @else
        <div class="empty-state">
            <div class="empty-icon"><i class="bi bi-inbox"></i></div>
            <h3>Chưa có đơn hàng nào</h3>
            <p>Hãy tạo đơn hàng đầu tiên của bạn ngay</p>
            <a href="{{ route('shop.orders.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Tạo đơn hàng mới
            </a>
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        // Update order status
        function updateOrderStatus(orderId, status) {
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
                // Reload page or update UI
                location.reload();
            })
            .catch(error => {
                alert('Có lỗi khi cập nhật trạng thái');
                console.error('Error:', error);
            });
        }

        // Search functionality
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const query = this.value.toLowerCase();
            document.querySelectorAll('.orders-table tbody tr').forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });
        });

        // Status filter
        document.getElementById('statusFilter').addEventListener('change', function() {
            const status = this.value.toLowerCase();
            document.querySelectorAll('.orders-table tbody tr').forEach(row => {
                if (!status) {
                    row.style.display = '';
                } else {
                    const badge = row.querySelector('.status-badge');
                    row.style.display = badge.className.includes(status) ? '' : 'none';
                }
            });
        });
    </script>
@endpush
