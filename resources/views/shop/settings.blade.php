@extends('layouts.shop')

@section('title', 'Cài đặt')

@section('content')
    <div class="page-header">
        <div>
            <h2>Cài đặt cửa hàng</h2>
            <p class="text-muted">Quản lý thông tin cửa hàng của bạn</p>
        </div>
    </div>

    <div class="settings-container">
        <!-- Shop Info Card -->
        <div class="settings-card">
            <div class="card-header">
                <h3><i class="bi bi-shop"></i> Thông tin cửa hàng</h3>
            </div>
            <div class="card-body">
                <div class="info-item">
                    <label>Tên cửa hàng:</label>
                    <p>{{ $shop->name }}</p>
                </div>
                <div class="info-item">
                    <label>Địa chỉ:</label>
                    <p>{{ $shop->address }}</p>
                </div>
                <div class="info-item">
                    <label>Số điện thoại:</label>
                    <p>{{ $shop->phone }}</p>
                </div>
                <div class="info-item">
                    <label>Mô tả:</label>
                    <p>{{ Str::limit($shop->description, 100) }}</p>
                </div>
                <a href="#" class="btn btn-primary"><i class="bi bi-pencil"></i> Chỉnh sửa thông tin</a>
            </div>
        </div>

        <!-- Statistics Card -->
        <div class="settings-card">
            <div class="card-header">
                <h3><i class="bi bi-bar-chart"></i> Thống kê</h3>
            </div>
            <div class="card-body">
                <div class="stat-row">
                    <span>Tổng sản phẩm:</span>
                    <strong>{{ $shop->products ? $shop->products->count() : 0 }}</strong>
                </div>
                <div class="stat-row">
                    <span>Tổng đơn hàng:</span>
                    <strong>{{ $shop->orders ? $shop->orders->count() : 0 }}</strong>
                </div>
                <div class="stat-row">
                    <span>Doanh thu tổng:</span>
                    <strong>{{ $shop->orders ? number_format($shop->orders->sum('total'), 0, ',', '.') : '0' }}₫</strong>
                </div>
            </div>
        </div>

        <!-- Security Card -->
        <div class="settings-card">
            <div class="card-header">
                <h3><i class="bi bi-shield-lock"></i> Bảo mật</h3>
            </div>
            <div class="card-body">
                <p class="text-muted" style="margin: 0 0 15px 0;">Quản lý mật khẩu và bảo mật tài khoản của bạn</p>
                <a href="#" class="btn btn-secondary"><i class="bi bi-key"></i> Đổi mật khẩu</a>
            </div>
        </div>

        <!-- Account Card -->
        <div class="settings-card">
            <div class="card-header">
                <h3><i class="bi bi-person"></i> Tài khoản</h3>
            </div>
            <div class="card-body">
                <div class="info-item">
                    <label>Email:</label>
                    <p>{{ Auth::user()->email }}</p>
                </div>
                <div class="info-item">
                    <label>Tên chủ cửa hàng:</label>
                    <p>{{ Auth::user()->name }}</p>
                </div>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="btn btn-danger"><i class="bi bi-door-left"></i> Đăng xuất</button>
                </form>
            </div>
        </div>
    </div>

    <style>
        .settings-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
            gap: 25px;
            margin-top: 25px;
        }

        .settings-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .card-header {
            background: #f9fafb;
            padding: 20px;
            border-bottom: 1px solid #e5e7eb;
        }

        .card-header h3 {
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 16px;
            font-weight: 700;
            color: var(--text-primary);
        }

        .card-header i {
            font-size: 18px;
            color: var(--primary-color);
        }

        .card-body {
            padding: 20px;
        }

        .info-item {
            margin-bottom: 15px;
        }

        .info-item label {
            display: block;
            font-size: 12px;
            color: var(--text-secondary);
            margin-bottom: 5px;
            font-weight: 600;
        }

        .info-item p {
            margin: 0;
            font-size: 14px;
            color: var(--text-primary);
        }

        .stat-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #e5e7eb;
            font-size: 14px;
        }

        .stat-row:last-child {
            border-bottom: none;
        }

        .stat-row strong {
            color: var(--primary-color);
            font-size: 16px;
        }

        .inline {
            display: inline;
        }

        .text-muted {
            color: var(--text-secondary);
            font-size: 13px;
        }

        @media (max-width: 768px) {
            .settings-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection
