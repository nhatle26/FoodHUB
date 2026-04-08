<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>@yield('title', 'Đăng ký mở shop') - FoodHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html, body {
            margin: 0;
            min-height: 100%;
            max-width: 100%;
            overflow-x: hidden;
            overscroll-behavior-y: none;
        }
        body {
            min-height: 100dvh;
            background: #f7f4ef;
            color: #1f2937;
            font-family: Arial, sans-serif;
        }
        .auth-shell {
            min-height: 100vh;
            min-height: 100dvh;
            width: 100%;
            overflow-x: hidden;
        }
        .auth-side {
            min-height: 100vh;
            min-height: 100dvh;
            color: #fff;
            background:
                linear-gradient(rgba(12, 12, 12, 0.35), rgba(12, 12, 12, 0.72)),
                url('https://images.unsplash.com/photo-1552566626-52f8b828add9?auto=format&fit=crop&w=900&q=80') center/cover no-repeat;
        }
        .auth-logo {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
        }
        .auth-logo-badge {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #ff5a36;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 20px rgba(255, 90, 54, 0.28);
        }
        .icon-svg {
            width: 20px;
            height: 20px;
            fill: currentColor;
            flex: 0 0 auto;
        }
        .auth-logo-badge .icon-svg {
            width: 21px;
            height: 21px;
        }
        .auth-side h1 {
            font-size: 46px;
            font-weight: 700;
            line-height: 1.05;
            margin-bottom: 18px;
        }
        .auth-side p, .auth-side li {
            color: rgba(255, 255, 255, 0.84);
        }
        .auth-main {
            min-height: 100vh;
            min-height: 100dvh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 18px;
        }
        .auth-form-wrap {
            width: 100%;
            max-width: 700px;
        }
        .auth-card {
            width: 100%;
            border: 0;
            border-radius: 22px;
            box-shadow: 0 20px 55px rgba(15, 23, 42, 0.08);
        }
        .step-progress {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 24px;
        }
        .step-node {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
        }
        .step-node.active { background: #ff5a36; color: #fff; }
        .step-node.done { background: #22c55e; color: #fff; }
        .step-node.idle { background: #e5e7eb; color: #6b7280; }
        .step-line { flex: 1; height: 2px; background: #e5e7eb; }
        .step-line.done { background: #86efac; }
        .soft-note {
            border: 1px solid #bbf7d0;
            background: #f0fdf4;
            color: #15803d;
            border-radius: 14px;
            padding: 10px 14px;
            font-size: 14px;
        }
        .upload-box {
            border: 1px dashed #d7dbe3;
            border-radius: 18px;
            padding: 16px;
            background: linear-gradient(180deg, #ffffff 0%, #fafafa 100%);
        }
        .upload-box.has-image .upload-trigger {
            display: none;
        }
        .upload-trigger {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            min-height: 96px;
            border-radius: 14px;
            background: #fff7f3;
            color: #ea580c;
            text-align: center;
            cursor: pointer;
            font-weight: 600;
        }
        .upload-icon-badge {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #fff;
            color: #ea580c;
            box-shadow: 0 8px 18px rgba(234, 88, 12, 0.12);
        }
        .upload-trigger .icon-svg {
            width: 22px;
            height: 22px;
        }
        .upload-actions {
            display: none;
            gap: 10px;
            margin-top: 12px;
        }
        .upload-box.has-image .upload-actions {
            display: flex;
        }
        .upload-action-btn {
            border: 1px solid #e5e7eb;
            background: #fff;
            color: #4b5563;
            border-radius: 12px;
            padding: 8px 12px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
        }
        .upload-action-btn.delete {
            color: #dc2626;
            border-color: #fecaca;
            background: #fff5f5;
        }
        .upload-preview {
            display: none;
            width: 100%;
            max-height: 150px;
            object-fit: cover;
            border-radius: 14px;
            margin-top: 12px;
            border: 1px solid #e5e7eb;
        }
        .logo-preview {
            display: none;
            width: 86px;
            height: 86px;
            object-fit: cover;
            border-radius: 20px;
            margin-top: 12px;
            border: 1px solid #e5e7eb;
            background: #fff;
        }
        .inline-fields > div {
            flex: 1;
        }
        .input-icon-field {
            position: relative;
        }
        .input-icon-field .input-icon {
            position: absolute;
            top: 50%;
            left: 16px;
            transform: translateY(-50%);
            color: #9ca3af;
            pointer-events: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .input-icon-field .input-icon .icon-svg {
            width: 17px;
            height: 17px;
        }
        .input-with-icon {
            padding-left: 44px;
        }
        .category-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 8px;
        }
        .category-option input { display: none; }
        .category-chip {
            display: block;
            border: 1px solid #e5e7eb;
            border-radius: 999px;
            padding: 8px 12px;
            background: #fff;
            font-size: 13px;
            font-weight: 600;
            color: #4b5563;
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .category-option input:checked + .category-chip {
            border-color: #ff5a36;
            background: #fff1eb;
            color: #ea580c;
        }
        .btn-foodhub {
            background: #ff5a36;
            border-color: #ff5a36;
            color: #fff;
        }
        .btn-foodhub:hover {
            background: #ee4d2d;
            border-color: #ee4d2d;
            color: #fff;
        }
        .auth-feature-icon {
            width: 28px;
            height: 28px;
            min-width: 28px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.16);
            color: #fff;
            margin-top: 2px;
        }
        .auth-feature-icon .icon-svg {
            width: 16px;
            height: 16px;
        }
        @media (max-width: 991.98px) {
            .auth-side {
                min-height: 320px;
                padding-top: calc(1.5rem + env(safe-area-inset-top));
            }
            .auth-side h1 { font-size: 34px; }
            .category-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .auth-main {
                min-height: auto;
                align-items: flex-start;
                padding-top: 0;
            }
        }
        @media (max-width: 575.98px) {
            .category-grid { grid-template-columns: repeat(1, minmax(0, 1fr)); }
        }
    </style>
</head>
<body>
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
