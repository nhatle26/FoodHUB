@extends('layouts.app')

@section('title', 'Đơn hàng của tôi — FoodHub')

@section('styles')
<style>
    .order-tabs .nav-link {
        border-radius: 999px;
        padding: 0.5rem 1.2rem;
        font-weight: 600;
        color: #6b7280;
        border: 1px solid #e5e7eb;
        background: #fff;
        transition: all .2s;
    }
    .order-tabs .nav-link.active {
        background: #ff5a36;
        border-color: #ff5a36;
        color: #fff;
    }
    .order-card {
        border-radius: 16px;
        border: 1px solid #e5e7eb;
        background: #fff;
        transition: box-shadow .2s;
        overflow: hidden;
    }
    .order-card:hover { box-shadow: 0 8px 30px rgba(0,0,0,.08); }
    .order-status-badge {
        font-size: 0.8rem;
        font-weight: 700;
        padding: 0.35em 0.8em;
        border-radius: 999px;
    }
    .status-timeline {
        display: flex;
        align-items: center;
        gap: 0;
        margin: 12px 0 4px;
    }
    .status-step {
        display: flex;
        flex-direction: column;
        align-items: center;
        flex: 1;
        position: relative;
    }
    .status-step:not(:last-child)::after {
        content: '';
        position: absolute;
        left: 50%;
        top: 12px;
        width: 100%;
        height: 2px;
        background: #e5e7eb;
        z-index: 0;
    }
    .status-step.done:not(:last-child)::after { background: #22c55e; }
    .step-dot {
        width: 24px; height: 24px;
        border-radius: 50%;
        background: #e5e7eb;
        border: 3px solid #e5e7eb;
        z-index: 1;
        display: flex; align-items: center; justify-content: center;
    }
    .status-step.done .step-dot {
        background: #22c55e;
        border-color: #22c55e;
        color: #fff;
        font-size: 0.65rem;
    }
    .status-step.current .step-dot {
        background: #ff5a36;
        border-color: #ff5a36;
        color: #fff;
        font-size: 0.65rem;
    }
    .step-label { font-size: 0.68rem; color: #9ca3af; margin-top: 4px; text-align: center; }
    .status-step.done .step-label,
    .status-step.current .step-label { color: #374151; font-weight: 600; }
</style>
@endsection

@section('content')
<div class="container py-5" style="max-width: 900px">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Đơn hàng của tôi</h2>
            <p class="text-muted mb-0">Theo dõi tất cả đơn hàng của bạn</p>
        </div>
        <a href="{{ route('home') }}" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="fas fa-plus me-2"></i>Đặt thêm
        </a>
    </div>

    {{-- Filter tabs --}}
    @php
        $tabs = [
            'all'       => ['Tất cả', ''],
            'pending'   => ['Chờ xác nhận', 'warning'],
            'confirmed' => ['Đã xác nhận', 'primary'],
            'preparing' => ['Đang chuẩn bị', 'info'],
            'delivering'=> ['Đang giao', 'brand'],
            'delivered' => ['Đã giao', 'success'],
            'completed' => ['Hoàn thành', 'success'],
            'cancelled' => ['Đã hủy', 'danger'],
        ];
        $currentTab = request('status', 'all');
    @endphp
    <div class="d-flex flex-wrap gap-2 mb-4 order-tabs">
        @foreach($tabs as $key => [$label, $color])
        <a href="{{ route('customer.orders.index', $key !== 'all' ? ['status' => $key] : []) }}"
           class="nav-link {{ $currentTab === $key ? 'active' : '' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>

    @if($orders->count() > 0)
        <div class="d-flex flex-column gap-3">
            @foreach($orders as $order)
            @php
                $steps = ['pending','confirmed','preparing','delivering','delivered'];
                $currentIdx = array_search($order->status, $steps);
            @endphp
            <div class="order-card">
                <div class="p-4 pb-0">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                        <div>
                            <span class="fw-bold text-muted small">#{{ $order->order_code }}</span>
                            <h6 class="fw-bold mb-0 mt-1">{{ $order->shop->name ?? 'N/A' }}</h6>
                            <small class="text-muted">{{ $order->created_at->format('d/m/Y H:i') }}</small>
                        </div>
                        <div class="text-end">
                            @php
                                $sc = match($order->status) {
                                    'completed','delivered' => 'success',
                                    'cancelled' => 'danger',
                                    'pending' => 'warning',
                                    'delivering' => 'primary',
                                    default => 'secondary'
                                };
                                $sl = match($order->status) {
                                    'pending' => 'Chờ xác nhận',
                                    'confirmed' => 'Đã xác nhận',
                                    'preparing' => 'Đang chuẩn bị',
                                    'delivering' => 'Đang giao',
                                    'delivered' => 'Đã giao',
                                    'completed' => 'Hoàn thành',
                                    'cancelled' => 'Đã hủy',
                                    default => $order->status
                                };
                            @endphp
                            <span class="order-status-badge bg-{{ $sc }} text-white">{{ $sl }}</span>
                            <div class="fw-bold text-danger mt-1">{{ number_format($order->total, 0, ',', '.') }}₫</div>
                        </div>
                    </div>

                    {{-- Timeline (chỉ show khi không hủy) --}}
                    @if($order->status !== 'cancelled')
                    <div class="status-timeline mt-3">
                        @foreach($steps as $idx => $step)
                        @php
                            $isDone    = $currentIdx !== false && $idx < $currentIdx;
                            $isCurrent = $currentIdx !== false && $idx === $currentIdx;
                            $stepClass = $isDone ? 'done' : ($isCurrent ? 'current' : '');
                            $icons     = ['📋','✅','🍳','🛵','📦'];
                            $labels    = ['Chờ xác nhận','Đã xác nhận','Chuẩn bị','Đang giao','Đã giao'];
                        @endphp
                        <div class="status-step {{ $stepClass }}">
                            <div class="step-dot">
                                @if($isDone || $isCurrent) <i class="fas fa-check" style="font-size:9px"></i> @endif
                            </div>
                            <span class="step-label">{{ $labels[$idx] }}</span>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>

                <div class="p-4 pt-3 d-flex justify-content-between align-items-center border-top mt-3">
                    <small class="text-muted">{{ $order->items->count() }} món</small>
                    <div class="d-flex gap-2">
                        @if($order->status === 'pending')
                        <form action="{{ route('customer.orders.cancel', $order->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill"
                                    onclick="return confirm('Bạn chắc chắn muốn hủy đơn hàng này?')">Hủy đơn</button>
                        </form>
                        @endif
                        <a href="{{ route('customer.orders.show', $order->id) }}"
                           class="btn btn-sm btn-primary rounded-pill px-3">
                            <i class="fas fa-eye me-1"></i> Chi tiết
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-4 d-flex justify-content-center">
            {{ $orders->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
    @else
        <div class="text-center py-5">
            <div class="mb-3" style="font-size:4rem">🛵</div>
            <h5 class="fw-bold text-muted">Chưa có đơn hàng nào</h5>
            <p class="text-muted">Đặt món ngay để trải nghiệm dịch vụ của FoodHub!</p>
            <a href="{{ route('home') }}" class="btn btn-danger rounded-pill mt-2 px-5">Khám phá ngay</a>
        </div>
    @endif
</div>
@endsection
