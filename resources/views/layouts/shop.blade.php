<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Shop Dashboard') - FoodHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/shop.css') }}">
</head>
<body>
    <div class="shop-container">
        <!-- Sidebar -->
        <aside class="shop-sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    <span class="logo-badge"><i class="bi bi-cup-fill"></i></span>
                    <span class="logo-text">FoodHub</span>
                </div>
            </div>

            <nav class="sidebar-menu">
                <a href="{{ route('shop.dashboard') }}" class="menu-item @if(Route::currentRouteName() == 'shop.dashboard') active @endif">
                    <span class="menu-icon"><i class="bi bi-speedometer"></i></span>
                    <span class="menu-label">Tổng quan</span>
                </a>
                <a href="{{ route('shop.orders.index') }}" class="menu-item @if(str_contains(Route::currentRouteName(), 'orders')) active @endif">
                    <span class="menu-icon"><i class="bi bi-receipt"></i></span>
                    <span class="menu-label">Doanh hàng</span>
                </a>
                <a href="{{ route('shop.products.index') }}" class="menu-item @if(str_contains(Route::currentRouteName(), 'products')) active @endif">
                    <span class="menu-icon"><i class="bi bi-handbag"></i></span>
                    <span class="menu-label">Sản phẩm</span>
                </a>
                <a href="{{ route('shop.revenue') }}" class="menu-item @if(Route::currentRouteName() == 'shop.revenue') active @endif">
                    <span class="menu-icon"><i class="bi bi-graph-up"></i></span>
                    <span class="menu-label">Doanh thu</span>
                </a>
                <a href="{{ route('shop.settings') }}" class="menu-item @if(Route::currentRouteName() == 'shop.settings') active @endif">
                    <span class="menu-icon"><i class="bi bi-gear"></i></span>
                    <span class="menu-label">Cài đặt</span>
                </a>
            </nav>

            <div class="sidebar-footer">
                <div class="user-info">
                    <div class="user-avatar"><i class="bi bi-person-circle"></i></div>
                    <div class="user-details">
                        <div class="user-name">{{ Auth::user()->customer->full_name ?? (Auth::user()->shop->name ?? Auth::user()->email) }}</div>
                        <div class="user-role">{{ Auth::user()->role ?? 'shop' }}</div>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST" class="logout-form">
                    @csrf
                    <button type="submit" class="logout-btn">
                        <span><i class="bi bi-door-left"></i></span> Đăng xuất
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Area -->
        <main class="shop-main">
            <header class="shop-header">
                <div class="header-title">
                    <h1>@yield('title', 'Dashboard')</h1>
                </div>
            </header>

            <div class="shop-content">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
