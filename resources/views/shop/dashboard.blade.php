@extends('layouts.shop')

@section('title', 'Tổng quan')

@section('content')
    <!-- Stats Grid -->
    <div class="dashboard-stats">
        <div class="stat-card">
            <div class="stat-icon đơn-hàng"><i class="bi bi-receipt"></i></div>
            <div class="stat-content">
                <div class="stat-value">{{ $shop->orders ? $shop->orders->count() : 0 }}</div>
                <div class="stat-label">Đơn hàng này</div>
                <div class="stat-change positive">{{ $shop->orders && $shop->orders->count() > 2 ? '+' . round(($shop->orders->count() / ($shop->orders->count() - 1) - 1) * 100) . '%' : '+0%' }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon doanh-thu"><i class="bi bi-currency-dollar"></i></div>
            <div class="stat-content">
                <div class="stat-value">{{ $shop->orders ? number_format($shop->orders->sum('total') / 1000000, 1) : 0 }}M</div>
                <div class="stat-label">Doanh thu</div>
                <div class="stat-change positive">+12%</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon chờ-xử-lý"><i class="bi bi-clock-history"></i></div>
            <div class="stat-content">
                <div class="stat-value">{{ $shop->orders ? $shop->orders->where('status', 'pending')->count() : 0 }}</div>
                <div class="stat-label">Đơn chờ xử lý</div>
                <div class="stat-change negative">-5%</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon đánh-giá"><i class="bi bi-star-fill"></i></div>
            <div class="stat-content">
                <div class="stat-value">4.8</div>
                <div class="stat-label">Đánh giá TB</div>
                <div class="stat-change positive">+0.2</div>
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
                    labels: ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN'],
                    datasets: [{
                        label: 'Doanh thu (₫)',
                        data: [2400000, 3200000, 2800000, 4100000, 4800000, 5200000, 4200000],
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
