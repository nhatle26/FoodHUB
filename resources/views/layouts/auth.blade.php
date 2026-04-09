<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - FoodHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link rel="stylesheet" href="{{ asset('css/user_register.css') }}">
    <link rel="stylesheet" href="{{ asset('css/shop_register.css') }}">
</head>
<body>

    @yield('content')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
{{-- ------------------------------------ --}}
    <div class="container-fluid auth-shell px-0">
        <div class="row g-0 auth-shell">
            <div class="col-lg-4 auth-side d-flex flex-column justify-content-between p-4 p-lg-4">
                <div class="auth-logo">
                    <span class="auth-logo-badge">
                        <svg viewBox="0 0 16 16" class="icon-svg" aria-hidden="true">
                            <path d="M0 1v2a2 2 0 0 0 1.5 1.937V15h1V4.937A2 2 0 0 0 4 3V1h-.5v2a1 1 0 0 1-1 1h-.25V1h-.5v3H1.5a1 1 0 0 1-1-1V1zM5 1v5h.5a2.5 2.5 0 0 0 2.45-2H8V1zm3 0v3.5A3.5 3.5 0 0 1 4.5 8H4v7h1V8h.5A4.5 4.5 0 0 0 10 3.5V1z"/>
                        </svg>
                    </span>
                    <span>FoodHub</span>
                </div>
                <div>
                    <h1>Mở cửa hàng trên FoodHub</h1>
                    <p class="mb-4">Tiếp cận hàng nghìn khách hàng, tăng doanh thu và tạo trang shop chuyên nghiệp ngay trên nền tảng đặt món.</p>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-3 d-flex align-items-start gap-2">
                            <span class="auth-feature-icon">
                                <svg viewBox="0 0 16 16" class="icon-svg" aria-hidden="true"><path d="M8 0c.314 0 .602.181.742.466l1.63 3.3 3.643.53a.82.82 0 0 1 .455 1.398l-2.636 2.57.622 3.629A.82.82 0 0 1 11.267 13L8 11.281 4.733 13a.82.82 0 0 1-1.19-.867l.622-3.63L1.53 5.934a.82.82 0 0 1 .455-1.397l3.643-.53 1.63-3.3A.82.82 0 0 1 8 0"/></svg>
                            </span>
                            <span>Đăng ký miễn phí, duyệt nhanh trong 1-2 ngày</span>
                        </li>
                        <li class="mb-3 d-flex align-items-start gap-2">
                            <span class="auth-feature-icon">
                                <svg viewBox="0 0 16 16" class="icon-svg" aria-hidden="true"><path d="M0 0h1v15h15v1H0zm10 10.5a.5.5 0 0 1 .5-.5h1v3h-1a.5.5 0 0 1-.5-.5zm-4-3a.5.5 0 0 1 .5-.5h1v6h-1a.5.5 0 0 1-.5-.5zm-4 5a.5.5 0 0 1 .5-.5h1v1h-1a.5.5 0 0 1-.5-.5zm12-8a.5.5 0 0 1 .5-.5h1v9h-1a.5.5 0 0 1-.5-.5z"/></svg>
                            </span>
                            <span>Quản lý đơn hàng và doanh thu dễ dàng</span>
                        </li>
                        <li class="d-flex align-items-start gap-2">
                            <span class="auth-feature-icon">
                                <svg viewBox="0 0 16 16" class="icon-svg" aria-hidden="true"><path d="M8 16s6-5.686 6-10A6 6 0 1 0 2 6c0 4.314 6 10 6 10m0-7.5A2.5 2.5 0 1 1 8 3.5a2.5 2.5 0 0 1 0 5"/></svg>
                            </span>
                            <span>Tiếp cận khách hàng trong khu vực của bạn</span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-8 auth-main">
                <div class="auth-form-wrap">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
