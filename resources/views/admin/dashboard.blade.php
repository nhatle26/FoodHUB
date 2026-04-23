@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('page-title', 'Dashboard')
@section('page-subtitle', 'Tổng quan quản trị FoodHub')

@section('content')
    <div class="card-grid mb-4">
        <div class="stat-card">
            <div class="stat-card-body">
                <div>
                    <h6>Tổng shop</h6>
                    <strong>{{ $shopCount }}</strong>
                    <p>Shop đang hoạt động trên FoodHub.</p>
                </div>
                <span class="stat-label blue">+3 tuần này</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-body">
                <div>
                    <h6>Người dùng</h6>
                    <strong>{{ $userCount }}</strong>
                    <p>Tài khoản đã đăng ký sử dụng dịch vụ.</p>
                </div>
                <span class="stat-label green">+24 hôm nay</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-body">
                <div>
                    <h6>Đơn hôm nay</h6>
                    <strong>{{ $todayOrders }}</strong>
                    <p>Đơn hàng được tạo trong ngày.</p>
                </div>
                <span class="stat-label orange">+12% so với hôm qua</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-body">
                <div>
                    <h6>Shop chờ duyệt</h6>
                    <strong>{{ $pendingShopCount }}</strong>
                    <p>Shop mới cần xét duyệt để lên trang chủ.</p>
                </div>
                <span class="stat-label red">Cần xử lý</span>
            </div>
        </div>
    </div>

    <div class="dashboard-grid mb-4">
        <div class="chart-card admin-card">
            <div class="card-header-row">
                <div>
                    <h5>Đơn hàng 7 ngày gần nhất</h5>
                    <p>Xu hướng đơn hàng mới nhất của hệ thống.</p>
                </div>
                <button class="btn btn-outline-secondary small-button">Xem biểu đồ</button>
            </div>

            <div class="mini-chart" id="orderTrendChart">
                <div class="trend-line" id="orderTrendLine"></div>
                <div class="d-flex justify-content-between mt-3 text-small text-muted">
                    @foreach($trendLabels as $label)
                        <span>{{ $label }}</span>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="pending-card admin-card">
            <div class="card-header-row">
                <div>
                    <h5>Shop chờ duyệt</h5>
                    <p>Danh sách shop mới cần xét duyệt.</p>
                </div>
                <a href="{{ route('admin.shops.pending') }}" class="btn btn-primary small-button">Xem tất cả</a>
            </div>

            @forelse($pendingShops as $shop)
                <div class="pending-shop">
                    @if($shop->logo)
                        <img src="{{ asset('storage/' . $shop->logo) }}" alt="shop" class="pending-shop-img" style="object-fit: cover;">
                    @else
                        <img src="https://via.placeholder.com/62" alt="shop" class="pending-shop-img">
                    @endif
                    <div class="pending-shop-info">
                        <strong>{{ $shop->name }}</strong>
                        <small>{{ Str::limit($shop->address, 40) }}</small>
                    </div>
                    <div class="pending-shop-actions">
                        <form action="{{ route('admin.shops.approve', $shop->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success">Duyệt</button>
                        </form>
                        <form action="{{ route('admin.shops.reject', $shop->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-danger">Từ chối</button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="text-muted">Không có shop chờ duyệt.</div>
            @endforelse
        </div>
    </div>

    <div class="orders-card admin-card">
        <div class="card-header-row">
            <div>
                <h5>Đơn hàng gần nhất</h5>
                <p>Thông tin đơn hàng mới nhất và trạng thái xử lý.</p>
            </div>
            <div class="card-actions">
                <button class="btn btn-outline-secondary small-button">Lọc</button>
                <button class="btn btn-danger small-button">Xuất Excel</button>
            </div>
        </div>

        <div class="table-responsive table-admin">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>MÃ ĐƠN</th>
                        <th>KHÁCH HÀNG</th>
                        <th>SHOP</th>
                        <th>TỔNG TIỀN</th>
                        <th>TRẠNG THÁI</th>
                        <th>THỜI GIAN</th>
                        <th>THAO TÁC</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($latestOrders as $order)
                        <tr>
                            <td>{{ $order->order_code }}</td>
                            <td>{{ $order->customer_name }}</td>
                            <td>{{ $order->shop_name }}</td>
                            <td>{{ number_format($order->total, 0, ',', '.') }}đ</td>
                            <td>
                                @php
                                    $statusClass = 'badge bg-secondary';
                                    if (in_array($order->status, ['delivered'])) $statusClass = 'badge bg-success';
                                    elseif (in_array($order->status, ['delivering', 'confirmed', 'preparing'])) $statusClass = 'badge bg-warning text-dark';
                                    elseif ($order->status === 'cancelled') $statusClass = 'badge bg-danger';
                                    elseif ($order->status === 'pending') $statusClass = 'badge bg-info text-dark';
                                @endphp
                                <span class="{{ $statusClass }}">{{ ucfirst($order->status) }}</span>
                            </td>
                            <td>{{ \Illuminate\Support\Carbon::parse($order->created_at)->format('H:i') }}</td>
                            <td><a href="#" class="text-danger">Chi tiết</a></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">Chưa có đơn hàng.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="table-footer">
            <span>Hiển thị 1 đến {{ $latestOrders->count() }} trong {{ $orderCount }} đơn hàng</span>
            <div class="d-flex gap-1">
                <button class="btn btn-outline-secondary small-button">Trước</button>
                <button class="btn btn-primary small-button">1</button>
                <button class="btn btn-outline-secondary small-button">2</button>
                <button class="btn btn-outline-secondary small-button">3</button>
                <button class="btn btn-outline-secondary small-button">Sau</button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const trend = @json($orderTrend);
            const chart = document.getElementById('orderTrendLine');
            const width = chart.clientWidth;
            const height = chart.clientHeight;
            const max = Math.max(...trend) || 1;
            const stepX = width / (trend.length - 1);

            trend.forEach((value, index) => {
                const x = stepX * index;
                const y = height - (value / max) * (height - 40) - 20;

                const dot = document.createElement('span');
                dot.className = 'trend-dot';
                dot.style.left = `${x - 6}px`;
                dot.style.top = `${y - 6}px`;
                chart.appendChild(dot);

                if (index > 0) {
                    const prev = trend[index - 1];
                    const prevX = stepX * (index - 1);
                    const prevY = height - (prev / max) * (height - 40) - 20;
                    const dx = x - prevX;
                    const dy = y - prevY;
                    const line = document.createElement('div');
                    line.className = 'trend-bar';
                    line.style.width = `${Math.sqrt(dx * dx + dy * dy)}px`;
                    line.style.transform = `translate(${prevX}px, ${prevY}px) rotate(${Math.atan2(dy, dx)}rad)`;
                    chart.appendChild(line);
                }
            });
        });
    </script>
@endsection
