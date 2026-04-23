@extends('layouts.shop')

@section('title', 'Tổng quan')

@section('content')
    <!-- Stats Grid -->
    <div class="dashboard-stats">
        <div class="stat-card">
            <div class="stat-icon đơn-hàng"><i class="bi bi-receipt"></i></div>
            <div class="stat-content">
                <div class="stat-value">{{ $ordersThisMonth }}</div>
                <div class="stat-label">Đơn hàng (tháng này)</div>
                <div class="stat-change {{ $growthOrders >= 0 ? 'positive' : 'negative' }}">{{ $growthOrders >= 0 ? '+' : '' }}{{ $growthOrders }}%</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon doanh-thu"><i class="bi bi-currency-dollar"></i></div>
            <div class="stat-content">
                <div class="stat-value">{{ $revThisMonth > 0 ? number_format($revThisMonth / 1000000, 1) . 'M' : '0' }}</div>
                <div class="stat-label">Doanh thu</div>
                <div class="stat-change {{ $growthRev >= 0 ? 'positive' : 'negative' }}">{{ $growthRev >= 0 ? '+' : '' }}{{ $growthRev }}%</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon chờ-xử-lý"><i class="bi bi-clock-history"></i></div>
            <div class="stat-content">
                <div class="stat-value">{{ $pendingNow }}</div>
                <div class="stat-label">Đơn chờ xử lý</div>
                <div class="stat-change {{ $growthPending >= 0 ? 'positive' : 'negative' }}">{{ $growthPending >= 0 ? '+' : '' }}{{ $growthPending }}%</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon đánh-giá"><i class="bi bi-star-fill"></i></div>
            <div class="stat-content">
                <div class="stat-value">{{ number_format($avgRating, 1) }}</div>
                <div class="stat-label">Đánh giá TB</div>
                <div class="stat-change {{ $growthRating >= 0 ? 'positive' : 'negative' }}">{{ $growthRating >= 0 ? '+' : '' }}{{ $growthRating }}</div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="dashboard-actions">
        <a href="{{ route('shop.products.create') }}" class="action-btn action-add">
            <span><i class="bi bi-plus-circle"></i></span> Thêm sản phẩm mới
        </a>
        <a href="{{ route('shop.orders.index') }}" class="action-btn action-view">
            <span><i class="bi bi-receipt"></i></span> Xem đơn hàng
        </a>
    </div>

    <!-- Revenue Chart -->
    <div class="dashboard-section chart-section">
        <div class="section-header">
            <h2>Doanh thu 7 ngày gần nhất</h2>
            <small>Theo dõi xu hướng doanh thu hàng tuần</small>
        </div>
        <div class="chart-container">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="dashboard-section">
        <div class="section-header">
            <h2>Đơn hàng gần đây</h2>
            <a href="{{ route('shop.orders.index') }}" class="view-all-link">Xem tất cả</a>
        </div>
        @if($shop->orders && count($shop->orders) > 0)
            <div class="recent-orders">
                <table>
                    <thead>
                        <tr>
                            <th>Mã đơn</th>
                            <th>Khách hàng</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Thời gian</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($shop->orders->sortByDesc('created_at')->take(6) as $order)
                            <tr>
                                <td><strong>#{{ $order->id }}</strong></td>
                                <td>{{ $order->user->customer->full_name ?? ($order->user->shop->name ?? $order->user->email) }}</td>
                                <td class="price">{{ number_format($order->total ?? 0, 0, ',', '.') }}₫</td>
                                <td>
                                    <span class="status-badge status-{{ $order->status ?? 'pending' }}">
                                        {{ ucfirst($order->status ?? 'Chờ xử lý') }}
                                    </span>
                                </td>
                                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('shop.orders.show', $order->id) }}" class="action-link view"><i class="bi bi-eye"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state-small">
                <p><i class="bi bi-inbox"></i> Chưa có đơn hàng nào</p>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
    <script>
        const ctx = document.getElementById('revenueChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($chartLabels) !!},
                    datasets: [{
                        label: 'Doanh thu (₫)',
                        data: {!! json_encode($chartData) !!},
                        borderColor: '#f97316',
                        backgroundColor: 'rgba(249, 115, 22, 0.05)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 6,
                        pointBackgroundColor: '#f97316',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointHoverRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return (value / 1000000).toFixed(1) + 'M';
                                }
                            }
                        }
                    }
                }
            });
        }
    </script>
@endpush
