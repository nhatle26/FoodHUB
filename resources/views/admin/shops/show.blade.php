@extends('layouts.admin')

@section('title', 'Chi tiết Shop — ' . $shop->name)
@section('page-title', 'Chi tiết Shop')
@section('page-subtitle', $shop->name)

@section('content')
{{-- Breadcrumb --}}
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('admin.shops.index') }}">Quản lý Shop</a></li>
        <li class="breadcrumb-item active">{{ $shop->name }}</li>
    </ol>
</nav>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row g-4">
    {{-- Cột trái: thông tin --}}
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4 text-center">
                @php 
                    $logo = $shop->details->logo ?? null; 
                    $logoUrl = $logo ? (str_starts_with($logo, 'http') ? $logo : asset('storage/' . $logo)) : null;
                @endphp
                @if($logoUrl)
                    <img src="{{ $logoUrl }}" class="rounded-circle mb-3 shadow"
                         width="90" height="90" style="object-fit:cover">
                @else
                    <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center mx-auto mb-3 shadow"
                         style="width:90px;height:90px">
                        <i class="fas fa-store fs-2 text-primary"></i>
                    </div>
                @endif
                <h5 class="fw-bold mb-1">{{ $shop->name }}</h5>
                @php
                    $sc = ['active'=>'success','pending'=>'warning','banned'=>'danger'][$shop->status] ?? 'secondary';
                    $sl = ['active'=>'Đang hoạt động','pending'=>'Chờ duyệt','banned'=>'Bị cấm'][$shop->status] ?? $shop->status;
                @endphp
                <span class="badge bg-{{ $sc }} fs-6 px-3 mb-3">{{ $sl }}</span>

                <div class="d-grid gap-2">
                    <a href="{{ route('admin.shops.edit', $shop->id) }}"
                       class="btn btn-primary rounded-3">
                        <i class="fas fa-edit me-2"></i>Chỉnh sửa shop
                    </a>
                    @if($shop->status === 'pending')
                    <form action="{{ route('admin.shops.approve', $shop->id) }}" method="POST">
                        @csrf
                        <button class="btn btn-success w-100 rounded-3">
                            <i class="fas fa-check me-2"></i>Duyệt shop
                        </button>
                    </form>
                    <form action="{{ route('admin.shops.reject', $shop->id) }}" method="POST">
                        @csrf
                        <button class="btn btn-outline-danger w-100 rounded-3">
                            <i class="fas fa-times me-2"></i>Từ chối
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <h6 class="fw-bold text-muted mb-3">THÔNG TIN LIÊN HỆ</h6>
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="fas fa-phone text-primary me-2"></i>
                        {{ $shop->details->phone ?? 'Chưa cập nhật' }}
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-map-marker-alt text-danger me-2"></i>
                        {{ $shop->details->address ?? 'Chưa cập nhật' }}
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-clock text-warning me-2"></i>
                        {{ $shop->details->open_time ?? '--:--' }} – {{ $shop->details->close_time ?? '--:--' }}
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-envelope text-info me-2"></i>
                        {{ $shop->user->email ?? 'N/A' }}
                    </li>
                    <li>
                        <i class="fas fa-calendar-alt text-secondary me-2"></i>
                        Tham gia: {{ $shop->created_at->format('d/m/Y') }}
                    </li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Cột phải: sản phẩm + đơn hàng --}}
    <div class="col-md-8">
        {{-- Ảnh bìa --}}
        @if($shop->details->cover_image ?? null)
        @php $coverUrl = str_starts_with($shop->details->cover_image, 'http') ? $shop->details->cover_image : asset('storage/' . $shop->details->cover_image); @endphp
        <div class="rounded-4 overflow-hidden mb-4 shadow-sm" style="height:180px">
            <img src="{{ $coverUrl }}" class="w-100 h-100" style="object-fit:cover">
        </div>
        @endif

        {{-- Mô tả --}}
        @if($shop->details->description ?? null)
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h6 class="fw-bold text-muted mb-2">MÔ TẢ</h6>
                <p class="mb-0 text-secondary">{{ $shop->details->description }}</p>
            </div>
        </div>
        @endif

        {{-- Sản phẩm --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                <h6 class="fw-bold">Sản phẩm ({{ $shop->products->count() }})</h6>
            </div>
            <div class="card-body p-4">
                @forelse($shop->products->take(6) as $product)
                <div class="d-flex align-items-center gap-3 mb-3">
                    @if($product->image)
                        @php $productImgUrl = str_starts_with($product->image, 'http') ? $product->image : asset('storage/' . $product->image); @endphp
                        <img src="{{ $productImgUrl }}" class="rounded-3" width="48" height="48" style="object-fit:cover">
                    @else
                        <div class="rounded-3 bg-light d-flex align-items-center justify-content-center" style="width:48px;height:48px">
                            <i class="fas fa-utensils text-muted"></i>
                        </div>
                    @endif
                    <div class="flex-grow-1">
                        <div class="fw-semibold small">{{ $product->name }}</div>
                        <div class="text-muted small">{{ number_format($product->price, 0, ',', '.') }}₫</div>
                    </div>
                    <span class="badge {{ $product->is_available ? 'bg-success' : 'bg-secondary' }}">
                        {{ $product->is_available ? 'Bán' : 'Ẩn' }}
                    </span>
                </div>
                @empty
                <p class="text-muted mb-0">Chưa có sản phẩm.</p>
                @endforelse
                @if($shop->products->count() > 6)
                    <p class="text-muted small mb-0">... và {{ $shop->products->count() - 6 }} sản phẩm khác.</p>
                @endif
            </div>
        </div>

        {{-- Đơn hàng gần nhất --}}
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                <h6 class="fw-bold">Đơn hàng gần nhất</h6>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Mã đơn</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Thời gian</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentOrders as $order)
                        <tr>
                            <td class="ps-4 fw-medium">#{{ $order->order_code }}</td>
                            <td>{{ number_format($order->total, 0, ',', '.') }}₫</td>
                            <td>
                                @php $oc = ['completed'=>'success','delivered'=>'success','cancelled'=>'danger','pending'=>'warning'][$order->status] ?? 'secondary'; @endphp
                                <span class="badge bg-{{ $oc }}">{{ ucfirst($order->status) }}</span>
                            </td>
                            <td class="text-muted small">{{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted py-3">Chưa có đơn hàng.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
