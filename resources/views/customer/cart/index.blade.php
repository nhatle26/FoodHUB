@extends('layouts.auth')

@section('title', 'Giỏ hàng & Thanh toán')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <a href="/" class="text-dark me-3 text-decoration-none">
                <i class="bi bi-arrow-left fs-4"></i>
            </a>
            <h4 class="mb-0 fw-bold">Giỏ hàng & Thanh toán</h4>
        </div>
        <span class="badge bg-success rounded-pill px-3 py-2 shadow-sm">Miễn phí ship</span>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">

            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                        <h6 class="fw-bold mb-0">{{ $cartItems->first()->shop->name ?? 'Shop' }}</h6>
                        <a href="/" class="text-muted small text-decoration-none hover-primary">Thêm món</a>
                    </div>

                    <div class="cart-items">
                        @forelse($cartItems as $item)
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <div class="d-flex align-items-center">
                                <img src="{{ asset('storage/' . $item->product->image) }}" class="rounded-3 border" width="80" height="80" style="object-fit: cover;">
                                <div class="ms-3">
                                    <h6 class="mb-1 fw-bold">{{ $item->product->name }}</h6>
                                    <div class="d-flex align-items-center bg-light rounded-pill px-2 border" style="width: fit-content;">
                                        <form action="{{ route('cart.update', $item->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="quantity" value="{{ $item->quantity - 1 }}">
                                            <button type="submit" class="btn btn-sm p-0 px-2 text-muted" {{ $item->quantity <= 1 ? 'disabled' : '' }}>-</button>
                                        </form>
                                        <span class="px-2 fw-bold small">{{ $item->quantity }}</span>
                                        <form action="{{ route('cart.update', $item->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="quantity" value="{{ $item->quantity + 1 }}">
                                            <button type="submit" class="btn btn-sm p-0 px-2 text-muted">+</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="fw-bold text-danger fs-5">{{ number_format($item->product->price * $item->quantity) }}đ</span>
                                <form action="{{ route('cart.remove', $item->id) }}" method="POST" class="d-inline ms-2">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-0">Xóa</button>
                                </form>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-4 text-muted">Giỏ hàng đang chờ món từ Bèng đó!</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3 text-danger">
                        <span class="me-2">🎫</span>
                        <h6 class="fw-bold mb-0 text-dark">Mã giảm giá</h6>
                    </div>
                    <div class="input-group mb-2">
                        <input type="text" class="form-control bg-light border-0 py-2 px-3" style="border-radius: 12px 0 0 12px;" placeholder="Thử FOODHUB20 hoặc NEWUSER">
                        <button class="btn btn-danger px-4 fw-bold" style="border-radius: 0 12px 12px 0;" type="button">Áp dụng</button>
                    </div>
                    <div class="mt-3 p-2 rounded-3 bg-success-subtle text-success small border border-success-subtle text-center">
                        <i class="bi bi-check-circle-fill me-1"></i> Đơn từ 100k - Miễn phí giao hàng!
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-4">Chi tiết thanh toán</h6>
                    <div class="d-flex justify-content-between mb-2 text-muted small">
                        <span>Tạm tính ({{ $cartItems->count() }} món)</span>
                        <span>{{ number_format($subtotal) }}đ</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 text-muted small">
                        <span>Phí giao hàng</span>
                        <span class="text-success fw-bold">Miễn phí</span>
                    </div>
                    <hr class="text-muted opacity-25">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">Tổng cộng</h5>
                        <h3 class="fw-bold text-danger mb-0">{{ number_format($subtotal) }}đ</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <form action="{{ route('checkout.process') }}" method="POST">
                @csrf

                <div class="card border-danger shadow-sm rounded-4 mb-4 border-2">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3 text-danger">
                            <span class="me-2">📍</span>
                            <h6 class="fw-bold mb-0 text-dark">Địa chỉ giao hàng</h6>
                        </div>
                        <div class="p-3 border rounded-3 bg-white border-danger shadow-sm">
                            <p class="mb-1 fw-bold text-dark">{{ Auth::user()->name }}</p>
                            <p class="mb-1 small text-muted">{{ Auth::user()->phone ?? '0123456789' }}</p>
                            <p class="small text-muted mb-2">{{ Auth::user()->address ?? 'Vui lòng cập nhật địa chỉ' }}</p>
                            <textarea name="delivery_address" class="form-control bg-light border-0 rounded-3 shadow-sm" rows="2" placeholder="Nhập địa chỉ giao hàng">{{ Auth::user()->address ?? 'Đà Nẵng' }}</textarea>
                            <span class="badge bg-danger text-white rounded-pill px-2" style="font-size: 10px;">Mặc định</span>
                        </div>
                        <button type="button" class="btn btn-outline-secondary w-100 mt-3 btn-sm rounded-3">Thêm địa chỉ mới</button>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3 small text-muted text-uppercase">Ghi chú cho người bán</h6>
                        <textarea name="note" class="form-control bg-light border-0 rounded-3 shadow-sm" rows="3" placeholder="Ví dụ: Không cay, nhiều rau mùi, nước dùng riêng..."></textarea>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-4">Phương thức thanh toán</h6>
                        <div class="list-group gap-3">
                            <label class="list-group-item d-flex align-items-center justify-content-between p-3 border-danger rounded-3 bg-danger-subtle cursor-pointer shadow-sm" style="border-width: 2px;">
                                <div class="d-flex align-items-center">
                                    <input class="form-check-input me-3 border-danger" type="radio" name="payment_method" value="cod" checked>
                                    <span class="fs-4 me-3">💵</span>
                                    <div>
                                        <p class="mb-0 fw-bold small text-dark">Tiền mặt (COD)</p>
                                        <p class="mb-0 text-muted extra-small" style="font-size: 11px;">Thanh toán khi nhận hàng</p>
                                    </div>
                                </div>
                                <i class="bi bi-check-circle-fill text-danger"></i>
                            </label>

                            <div class="list-group-item d-flex align-items-center p-3 border rounded-3 bg-light opacity-50">
                                <span class="fs-4 me-3">📱</span>
                                <div>
                                    <p class="mb-0 fw-bold small text-muted">Ví MoMo</p>
                                    <p class="mb-0 text-muted extra-small" style="font-size: 11px;">Chưa hỗ trợ</p>
                                </div>
                            </div>

                            <div class="list-group-item d-flex align-items-center p-3 border rounded-3 bg-light opacity-50">
                                <span class="fs-4 me-3">💳</span>
                                <div>
                                    <p class="mb-0 fw-bold small text-muted">Thẻ ATM/Visa</p>
                                    <p class="mb-0 text-muted extra-small" style="font-size: 11px;">Chưa hỗ trợ</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="sticky-bottom bg-white p-2">
                    <button type="submit" class="btn btn-danger w-100 py-3 rounded-4 fw-bold fs-5 shadow-lg active-scale transition">
                        Xác nhận đặt hàng • {{ number_format($subtotal) }}đ
                    </button>
                    <p class="text-center text-muted small mt-3 px-4" style="font-size: 10px; line-height: 1.2;">
                        Bằng việc đặt hàng, bạn đồng ý với <a href="#" class="text-danger text-decoration-none border-bottom border-danger">Điều khoản sử dụng</a> của FoodHub
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    
    .active-scale:active { transform: scale(0.98); }
    .extra-small { font-size: 11px; }
    .cursor-pointer { cursor: pointer; }
</style>
@endsection
