@extends('layouts.auth')

@section('title', 'Don hang & Thanh toan')

@section('content')
@php
    $displayName = optional(Auth::user()->customer)->full_name
        ?? optional(Auth::user()->shop)->name
        ?? Auth::user()->email;
    $phone = optional(Auth::user()->customer)->phone
        ?? Auth::user()->phone
        ?? '0123456789';
    $address = optional(optional(optional(Auth::user()->customer)->addresses)->where('is_default', 1)->first())->address_line
        ?? Auth::user()->address
        ?? 'Da Nang';
@endphp

<div class="container py-5">
    @if (session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-4">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger border-0 shadow-sm rounded-4">{{ session('error') }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <a href="{{ route('home') }}" class="text-dark me-3 text-decoration-none">
                <i class="bi bi-arrow-left fs-4"></i>
            </a>
            <h4 class="mb-0 fw-bold">Don hang & Thanh toan</h4>
        </div>
        <span class="badge bg-success rounded-pill px-3 py-2 shadow-sm">Mien phi ship</span>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                        <h6 class="fw-bold mb-0">{{ $shop->name ?? 'Shop' }}</h6>
                        <a href="{{ route('home') }}" class="text-muted small text-decoration-none">Them mon</a>
                    </div>

                    <div class="order-items">
                        @forelse ($orderItems as $item)
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <div class="d-flex align-items-center">
                                    @php
                                        $imgUrl = !empty($item['product_image']) ? (str_starts_with($item['product_image'], 'http') ? $item['product_image'] : asset('storage/' . preg_replace('/^\/?(storage\/)?/', '', $item['product_image']))) : 'https://images.unsplash.com/photo-1544681280-d2dc2c07a3c3?w=300&q=80';
                                    @endphp
                                    <img
                                        src="{{ $imgUrl }}"
                                        class="rounded-3 border"
                                        width="80"
                                        height="80"
                                        style="object-fit: cover;"
                                        alt="{{ $item['product_name'] }}"
                                    >

                                    <div class="ms-3">
                                        <h6 class="mb-1 fw-bold">{{ $item['product_name'] }}</h6>
                                        <div class="text-muted small mb-2">{{ $item['product_group'] ?? 'Khac' }}</div>

                                        <div class="d-flex align-items-center bg-light rounded-pill px-2 border" style="width: fit-content;">
                                            <form action="{{ route('checkout.items.update', $item['product_id']) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="quantity" value="{{ $item['quantity'] - 1 }}">
                                                <button type="submit" class="btn btn-sm p-0 px-2 text-muted" {{ $item['quantity'] <= 1 ? 'disabled' : '' }}>-</button>
                                            </form>

                                            <span class="px-2 fw-bold small">{{ $item['quantity'] }}</span>

                                            <form action="{{ route('checkout.items.update', $item['product_id']) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="quantity" value="{{ $item['quantity'] + 1 }}">
                                                <button type="submit" class="btn btn-sm p-0 px-2 text-muted">+</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-end">
                                    <span class="fw-bold text-danger fs-5">{{ number_format($item['subtotal'], 0, ',', '.') }}₫</span>

                                    <form action="{{ route('checkout.items.remove', $item['product_id']) }}" method="POST" class="d-inline ms-2">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-0">Xoa</button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-muted">Don tam dang trong.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3 text-danger">
                        <span class="me-2">Voucher</span>
                        <h6 class="fw-bold mb-0 text-dark">Ma giam gia</h6>
                    </div>

                    <form action="{{ route('checkout.index') }}" method="GET">
                        <div class="input-group mb-2">
                            <input
                                type="text"
                                name="voucher_code"
                                value="{{ $voucherCode }}"
                                class="form-control bg-light border-0 py-2 px-3"
                                style="border-radius: 12px 0 0 12px;"
                                placeholder="Thu FOODHUB20 hoac NEWUSER"
                            >
                            <button class="btn btn-danger px-4 fw-bold" style="border-radius: 0 12px 12px 0;" type="submit">
                                Ap dung
                            </button>
                        </div>
                    </form>

                    @if ($errors->has('voucher_code') || $voucherFeedbackError)
                        <div class="mt-3 p-3 rounded-3 bg-danger-subtle text-danger small border border-danger-subtle">
                            {{ $errors->first('voucher_code') ?: $voucherFeedbackError }}
                        </div>
                    @elseif ($appliedVoucher)
                        <div class="mt-3 p-3 rounded-3 bg-success-subtle text-success small border border-success-subtle">
                            Da ap dung ma <strong>{{ $appliedVoucher['code'] }}</strong>.
                            Ban duoc giam <strong>{{ number_format($discount, 0, ',', '.') }}₫</strong>.
                        </div>

                        @if (! empty($appliedVoucher['condition_labels']))
                            <div class="mt-2 text-muted small">
                                {{ implode(' | ', $appliedVoucher['condition_labels']) }}
                            </div>
                        @endif
                    @else
                        <div class="mt-3 p-2 rounded-3 bg-success-subtle text-success small border border-success-subtle text-center">
                            Don tu 100k - Mien phi giao hang!
                        </div>
                    @endif
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-4">Chi tiet thanh toan</h6>

                    <div class="d-flex justify-content-between mb-2 text-muted small">
                        <span>Tam tinh ({{ $orderItems->count() }} mon)</span>
                        <span>{{ number_format($subtotal, 0, ',', '.') }}₫</span>
                    </div>

                    <div class="d-flex justify-content-between mb-2 text-muted small">
                        <span>Phi giao hang</span>
                        <span class="text-success fw-bold">{{ number_format($shippingFee, 0, ',', '.') }}₫</span>
                    </div>

                    <div class="d-flex justify-content-between mb-3 text-muted small">
                        <span>Giam gia voucher</span>
                        <span class="fw-bold {{ $discount > 0 ? 'text-danger' : '' }}">-{{ number_format($discount, 0, ',', '.') }}₫</span>
                    </div>

                    <hr class="text-muted opacity-25">

                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">Tong cong</h5>
                        <h3 class="fw-bold text-danger mb-0">{{ number_format($total, 0, ',', '.') }}₫</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <form action="{{ route('checkout.process') }}" method="POST">
                @csrf
                <input type="hidden" name="voucher_code" value="{{ $appliedVoucher['code'] ?? $voucherCode }}">

                <div class="card border-danger shadow-sm rounded-4 mb-4 border-2">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3 text-danger">
                            <span class="me-2">Dia chi</span>
                            <h6 class="fw-bold mb-0 text-dark">Dia chi giao hang</h6>
                        </div>

                        <div class="p-3 border rounded-3 bg-white border-danger shadow-sm">
                            <p class="mb-1 fw-bold text-dark">{{ $displayName }}</p>
                            <p class="mb-1 small text-muted">{{ $phone }}</p>
                            <p class="small text-muted mb-2">{{ $address }}</p>

                            <textarea
                                name="delivery_address"
                                class="form-control bg-light border-0 rounded-3 shadow-sm"
                                rows="2"
                                placeholder="Nhap dia chi giao hang"
                            >{{ old('delivery_address', $address) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3 small text-muted text-uppercase">Ghi chu cho nguoi ban</h6>
                        <textarea
                            name="note"
                            class="form-control bg-light border-0 rounded-3 shadow-sm"
                            rows="3"
                            placeholder="Vi du: Khong cay, nhieu rau mui, nuoc dung rieng..."
                        >{{ old('note') }}</textarea>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-4">Phuong thuc thanh toan</h6>

                        <div class="list-group gap-3">
                            <label class="list-group-item d-flex align-items-center justify-content-between p-3 border-danger rounded-3 bg-danger-subtle cursor-pointer shadow-sm" style="border-width: 2px;">
                                <div class="d-flex align-items-center">
                                    <input class="form-check-input me-3 border-danger" type="radio" name="payment_method" value="cod" checked>
                                    <span class="fs-4 me-3">COD</span>
                                    <div>
                                        <p class="mb-0 fw-bold small text-dark">Tien mat</p>
                                        <p class="mb-0 text-muted extra-small" style="font-size: 11px;">Thanh toan khi nhan hang</p>
                                    </div>
                                </div>
                                <i class="bi bi-check-circle-fill text-danger"></i>
                            </label>

                            <div class="list-group-item d-flex align-items-center p-3 border rounded-3 bg-light opacity-50">
                                <span class="fs-4 me-3">MoMo</span>
                                <div>
                                    <p class="mb-0 fw-bold small text-muted">Vi MoMo</p>
                                    <p class="mb-0 text-muted extra-small" style="font-size: 11px;">Chua ho tro</p>
                                </div>
                            </div>

                            <div class="list-group-item d-flex align-items-center p-3 border rounded-3 bg-light opacity-50">
                                <span class="fs-4 me-3">ATM</span>
                                <div>
                                    <p class="mb-0 fw-bold small text-muted">The ATM/Visa</p>
                                    <p class="mb-0 text-muted extra-small" style="font-size: 11px;">Chua ho tro</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="sticky-bottom bg-white p-2">
                    <button
                        type="submit"
                        class="btn btn-danger w-100 py-3 rounded-4 fw-bold fs-5 shadow-lg active-scale transition"
                        @disabled($orderItems->isEmpty())
                    >
                        Xac nhan dat hang • {{ number_format($total) }}d
                    </button>

                    <p class="text-center text-muted small mt-3 px-4" style="font-size: 10px; line-height: 1.2;">
                        Bang viec dat hang, ban dong y voi dieu khoan su dung cua FoodHub.
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
