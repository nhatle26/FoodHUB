@extends('layouts.shop')

@section('title', 'Doanh thu')

@section('content')
    <div class="page-header">
        <div>
            <h2>Doanh thu</h2>
            <p class="text-muted">Phân tích doanh thu cửa hàng</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="dashboard-stats">
        <div class="stat-card">
            <div class="stat-icon doanh-thu"><i class="bi bi-currency-dollar"></i></div>
            <div class="stat-content">
                <div class="stat-value">{{ $shop->orders ? number_format($shop->orders->sum('total'), 0, ',', '.') : '0' }}₫</div>
                <div class="stat-label">Tổng doanh thu</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon đơn-hàng"><i class="bi bi-receipt"></i></div>
            <div class="stat-content">
                <div class="stat-value">{{ $shop->orders ? $shop->orders->count() : 0 }}</div>
                <div class="stat-label">Số đơn hàng</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon doanh-thu"><i class="bi bi-graph-up"></i></div>
            <div class="stat-content">
                <div class="stat-value">{{ $shop->orders && $shop->orders->count() > 0 ? number_format($shop->orders->sum('total') / $shop->orders->count(), 0, ',', '.') : '0' }}₫</div>
                <div class="stat-label">Đơn hàng trung bình</div>
            </div>
        </div>
    </div>

    <!-- Charts Grid -->
    <div class="charts-grid">
        <!-- Revenue Line Chart -->
        <div class="dashboard-section chart-section">
            <div class="section-header">
                <h2>Doanh thu 30 ngày</h2>
            </div>
            <div class="chart-container">
                <canvas id="revenueLineChart"></canvas>
            </div>
        </div>

        <!-- Revenue Bar Chart -->
        <div class="dashboard-section chart-section">
            <div class="section-header">
                <h2>Doanh thu theo ngày</h2>
            </div>
            <div class="chart-container">
                <canvas id="revenueBarChart"></canvas>
            </div>
        </div>

        <!-- Order Status Chart -->
        <div class="dashboard-section chart-section">
            <div class="section-header">
                <h2>Trạng thái đơn hàng</h2>
            </div>
            <div class="chart-container">
                <canvas id="orderStatusChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Best Selling Products -->
    <div class="dashboard-section">
        <div class="section-header">
            <h2><i class="bi bi-fire"></i> Sản phẩm bán chạy</h2>
        </div>
        @if($shop->products && count($shop->products) > 0)
            <div class="products-ranking">
                <table>
                    <thead>
                        <tr>
                            <th class="rank">Xếp hạng</th>
                            <th>Sản phẩm</th>
                            <th class="center">Nhóm</th>
                            <th class="center">Đã bán</th>
                            <th class="right">Doanh thu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($shop->products->sortByDesc('total_sold')->take(10) as $product)
                            <tr>
                                <td class="rank">
                                    @if($loop->iteration == 1)
                                        <span class="badge-top1">🥇</span>
                                    @elseif($loop->iteration == 2)
                                        <span class="badge-top2">🥈</span>
                                    @elseif($loop->iteration == 3)
                                        <span class="badge-top3">🥉</span>
                                    @else
                                        <span class="badge-rank">{{ $loop->iteration }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="product-row">
                                        @if($product->image)
                                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="product-thumb-small">
                                        @else
                                            <div class="product-thumb-small-empty"><i class="bi bi-image"></i></div>
                                        @endif
                                        <div>
                                            <div class="product-name-small">{{ $product->name }}</div>
                                            <small class="text-muted">{{ Str::limit($product->description, 40) }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="center">{{ $product->product_group }}</td>
                                <td class="center">
                                    <strong>{{ $product->total_sold }}</strong>
                                </td>
                                <td class="right price">
                                    {{ number_format($product->price * $product->total_sold, 0, ',', '.') }}₫
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state-small">
                <p><i class="bi bi-box"></i> Chưa có sản phẩm nào</p>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
    <script>
        // Revenue Line Chart
        const ctxLine = document.getElementById('revenueLineChart');
        if (ctxLine) {
            new Chart(ctxLine, {
                type: 'line',
                data: {
                    labels: ['1', '5', '10', '15', '20', '25', '30'],
                    datasets: [{
                        label: 'Doanh thu (₫)',
                        data: [2400000, 3200000, 2800000, 4100000, 4800000, 5200000, 4200000],
                        borderColor: '#f97316',
                        backgroundColor: 'rgba(249, 115, 22, 0.05)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 5,
                        pointBackgroundColor: '#f97316',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
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

        // Revenue Bar Chart
        const ctxBar = document.getElementById('revenueBarChart');
        if (ctxBar) {
            new Chart(ctxBar, {
                type: 'bar',
                data: {
                    labels: ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN'],
                    datasets: [{
                        label: 'Doanh thu (₫)',
                        data: [2400000, 3200000, 2800000, 4100000, 4800000, 5200000, 4200000],
                        backgroundColor: '#f97316',
                        borderRadius: 8,
                        borderSkipped: false,
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

        // Order Status Chart (Pie/Doughnut)
        const ctxStatus = document.getElementById('orderStatusChart');
        if (ctxStatus) {
            new Chart(ctxStatus, {
                type: 'doughnut',
                data: {
                    labels: ['Đã giao', 'Đang giao', 'Đã xác nhận', 'Chờ xử lý'],
                    datasets: [{
                        data: [45, 25, 20, 10],
                        backgroundColor: [
                            '#10b981',
                            '#3b82f6',
                            '#f97316',
                            '#ef4444'
                        ],
                        borderColor: '#fff',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 15
                            }
                        }
                    }
                }
            });
        }
    </script>
@endpush
