<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'FoodHub Admin')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --sidebar-bg: #0f172a;
            --sidebar-text: #cbd5e1;
            --sidebar-active: #f97316;
            --card-bg: #ffffff;
            --page-bg: #f3f4f8;
            --border: rgba(148,163,184,.18);
            --text-strong: #111827;
        }
        body {
            min-height: 100vh;
            background: var(--page-bg);
            color: var(--text-strong);
            font-family: Inter, system-ui, sans-serif;
        }
        .admin-shell {
            display: grid;
            grid-template-columns: 260px 1fr;
            min-height: 100vh;
        }
        .admin-sidebar {
            background: var(--sidebar-bg);
            color: var(--sidebar-text);
            display: flex;
            flex-direction: column;
            padding: 28px 18px;
            gap: 28px;
        }
        .admin-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.3rem;
            font-weight: 700;
            color: white;
        }
        .admin-logo span:last-child {
            color: #fb923c;
        }
        .nav-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .nav-link-admin {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 16px;
            border-radius: 16px;
            color: var(--sidebar-text);
            text-decoration: none;
            transition: background .2s ease, color .2s ease;
            font-size: 0.95rem;
        }
        .nav-link-admin:hover,
        .nav-link-admin.active {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        .admin-content {
            padding: 28px;
        }
        .admin-header {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            align-items: center;
            margin-bottom: 24px;
        }
        .admin-breadcrumb {
            font-size: 0.95rem;
            color: #6b7280;
            margin-top: 6px;
        }
        .admin-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .admin-card {
            background: var(--card-bg);
            border-radius: 28px;
            border: 1px solid var(--border);
            box-shadow: 0 24px 60px rgba(15,23,42,.08);
            padding: 24px;
        }
        .card-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 20px;
        }
        .status-chip {
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 0.82rem;
            font-weight: 600;
        }
        .status-active { background: #d1fae5; color: #047857; }
        .status-inactive { background: #fce7f3; color: #be123c; }
        .text-small { font-size: 0.95rem; color: #6b7280; }
        .table-admin {
            background: #fff;
            border-radius: 24px;
            overflow: hidden;
        }
        .table-admin th {
            background: #f8fafc;
            border-bottom: 1px solid #e5e7eb;
            font-weight: 700;
        }
        .table-admin td,
        .table-admin th { vertical-align: middle; }
        .btn-primary-soft {
            background: rgba(220,38,38,.1);
            border: 1px solid rgba(220,38,38,.2);
            color: #dc2626;
        }
        .stat-card {
            background: #fff;
            border-radius: 28px;
            box-shadow: 0 26px 70px rgba(15,23,42,.08);
            border: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            flex-direction: column;
            min-height: 180px;
        }
        .stat-card.simple::after { display: none; }
        .stat-card-body {
            position: relative;
            z-index: 1;
            padding: 24px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
        }
        .stat-card h6 {
            margin-bottom: 12px;
            font-size: 0.95rem;
            font-weight: 700;
            color: #111827;
        }
        .stat-card strong {
            display: block;
            font-size: 2.6rem;
            color: #111827;
            margin-bottom: 10px;
        }
        .stat-card p {
            margin-bottom: 0;
            color: #6b7280;
            line-height: 1.6;
        }
        .stat-label {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 14px;
            border-radius: 999px;
            font-size: 0.85rem;
            font-weight: 700;
            white-space: nowrap;
        }
        .stat-label.green { background: #dcfce7; color: #166534; }
        .stat-label.blue { background: #dbeafe; color: #1d4ed8; }
        .stat-label.red { background: #fee2e2; color: #991b1b; }
        .stat-label.orange { background: #fff7ed; color: #c2410c; }
        .dashboard-grid {
            display: grid;
            grid-template-columns: 1.7fr 1fr;
            gap: 22px;
        }
        .chart-card,
        .pending-card,
        .orders-card {
            background: #fff;
            border-radius: 28px;
            box-shadow: 0 24px 60px rgba(15,23,42,.08);
            border: 1px solid var(--border);
        }
        .card-header-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 14px;
            margin-bottom: 22px;
        }
        .card-header-row h5 {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 700;
        }
        .card-header-row p {
            margin: 6px 0 0;
            color: #6b7280;
        }
        .card-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .btn-outline-secondary {
            border-color: #e5e7eb;
            color: #374151;
            background: #fff;
            transition: background .2s ease, color .2s ease;
        }
        .btn-outline-secondary:hover {
            background: #f8fafc;
        }
        .btn-danger {
            background: #ef4444;
            border-color: #ef4444;
            color: #fff;
        }
        .small-button {
            border-radius: 999px;
            padding: 0.75rem 1rem;
            font-size: 0.88rem;
            font-weight: 700;
            border: 1px solid #e5e7eb;
            background: #fff;
            color: #1f2937;
            transition: all .2s ease;
        }
        .small-button:hover {
            background: #f8fafc;
            color: #111827;
        }
        .pending-shop {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 18px;
            border: 1px solid #e5e7eb;
            border-radius: 22px;
            margin-bottom: 14px;
            background: #f8fafc;
        }
        .pending-shop:last-child { margin-bottom: 0; }
        .pending-shop-img {
            width: 62px;
            height: 62px;
            border-radius: 18px;
            object-fit: cover;
            background: #e2e8f0;
        }
        .pending-shop-info strong {
            display: block;
            margin-bottom: 4px;
            font-size: 0.98rem;
        }
        .pending-shop-info small {
            color: #6b7280;
        }
        .pending-shop-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .table-admin thead th {
            border-bottom: 0;
            color: #6b7280;
            font-size: 0.82rem;
            text-transform: uppercase;
            letter-spacing: .08em;
        }
        .table-admin tbody tr {
            border-bottom: 1px solid #e5e7eb;
        }
        .table-admin tbody tr:hover {
            background: #f8fafc;
        }
        .table-admin td {
            padding: 18px 14px;
        }
        .status-badge {
            display: inline-block;
            padding: 8px 12px;
            border-radius: 999px;
            font-size: 0.82rem;
            font-weight: 700;
        }
        .status-badge.complete { background: #dcfce7; color: #166534; }
        .status-badge.warning { background: #fef3c7; color: #92400e; }
        .status-badge.info { background: #dbeafe; color: #1d4ed8; }
        .status-badge.danger { background: #fee2e2; color: #991b1b; }
        .mini-chart {
            position: relative;
            min-height: 240px;
            background: #f8fafc;
            border-radius: 24px;
            padding: 28px 24px 22px;
            margin-top: 10px;
        }
        .trend-line {
            position: relative;
            width: 100%;
            height: 180px;
        }
        .trend-dot {
            position: absolute;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #ef4444;
            border: 3px solid #fff;
            box-shadow: 0 14px 28px rgba(239,68,68,.18);
        }
        .trend-bar {
            position: absolute;
            height: 4px;
            background: linear-gradient(90deg, rgba(239,68,68,0.95), rgba(251,191,36,0.95));
            transform-origin: left center;
            border-radius: 999px;
        }
        .table-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 18px;
            margin-top: 16px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 0.95rem;
        }
        .pagination-wrapper {
            display: flex;
            align-items: center;
        }
        .pagination {
            display: flex;
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .pagination .page-item {
            margin-left: 8px;
        }
        .pagination .page-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            height: 36px;
            padding: 0 12px;
            border-radius: 999px;
            border: 1px solid #e5e7eb;
            color: #374151;
            text-decoration: none;
            background: #fff;
            transition: background .2s ease, border-color .2s ease;
        }
        .pagination .page-item.active .page-link {
            background: #ef4444;
            border-color: #ef4444;
            color: #fff;
        }
        .pagination .page-item.disabled .page-link {
            color: #9ca3af;
            border-color: #f3f4f6;
            cursor: not-allowed;
            background: #f8fafc;
        }
        @media (max-width: 1400px) {
            .card-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }
        @media (max-width: 992px) {
            .admin-shell {
                grid-template-columns: 1fr;
            }
            .admin-sidebar {
                flex-direction: row;
                overflow-x: auto;
                gap: 14px;
                padding: 16px;
            }
            .admin-logo { display: none; }
            .nav-group { flex-direction: row; }
            .admin-content { padding: 16px; }
            .card-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="admin-shell">
        <aside class="admin-sidebar">
            <div class="admin-logo">FoodHub<span>Admin</span></div>
            <nav class="nav-group">
                <a href="{{ route('admin.dashboard') }}" class="nav-link-admin {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">🏠 Dashboard</a>
                <a href="{{ route('admin.users.index') }}" class="nav-link-admin {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">👥 Quản lý Người dùng</a>
                <a href="{{ route('admin.categories.index') }}" class="nav-link-admin {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">🗂 Quản lý Danh mục</a>
                <a href="{{ route('admin.shops.pending') }}" class="nav-link-admin {{ request()->routeIs('admin.shops.*') ? 'active' : '' }}">🏪 Quản lý Shop</a>
                <a href="{{ route('admin.orders.index') }}" class="nav-link-admin {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">🧾 Quản lý Đơn hàng</a>
                <a href="#" class="nav-link-admin">📊 Báo cáo & Thống kê</a>
                <a href="#" class="nav-link-admin">⚙️ Cài đặt</a>
            </nav>
            <div style="margin-top:auto; padding-top: 16px; border-top: 1px solid rgba(255,255,255,0.08);">
                <div style="display:flex;align-items:center;gap:12px;">
                    <div style="width:42px;height:42px;border-radius:50%;background:#2563eb;
                        display:flex;align-items:center;justify-content:center;color:white;font-weight:700;">A</div>
                    <div>
                        <div style="font-weight:700;color:white;">Admin Quản trị</div>
                        <div style="font-size:.85rem;color:#9ca3af;">admin@foodhub.vn</div>
                    </div>
                </div>
            </div>
        </aside>

        <main class="admin-content">
            <header class="admin-header">
                <div>
                    <h2>@yield('page-title', 'Bảng điều khiển')</h2>
                    <div class="admin-breadcrumb">@yield('page-subtitle', 'Trang quản trị hệ thống')</div>
                </div>
                <div class="admin-actions">
                    <div class="input-group" style="max-width:320px; min-width:220px;">
                        <span class="input-group-text" style="background:#f3f4f6;border-radius:999px 0 0 999px;">🔎</span>
                        <input type="search" class="form-control" placeholder="Tìm kiếm shop, đơn hàng, người dùng...">
                    </div>
                </div>
            </header>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
