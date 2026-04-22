@extends('layouts.app')

@section('title', $shopData['name'] . ' - FoodHUB')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/shop-detail.css') }}">
@endsection

@section('content')
<div class="shop-header-bar">
    <a href="{{ route('home') }}" class="back-btn"><i class="fas fa-arrow-left"></i></a>
    <h1 class="shop-title-nav">{{ $shopData['name'] }}</h1>
</div>

<div class="shop-hero" style="background-image: url('{{ $shopData['banner'] }}');">
    <div class="shop-hero-inset">
        <img src="{{ $shopData['logo'] }}" alt="Logo {{ $shopData['name'] }}">
    </div>
</div>

<div class="shop-info-block">
    <h1 class="shop-info-title">{{ $shopData['name'] }}</h1>
    <div class="status-badge">{{ $shopData['status'] }}</div>

    <div class="info-row">
        <div class="info-item">
            <i class="fas fa-star icon-rating"></i>
            <span><strong>{{ $shopData['rating'] }}</strong> ({{ number_format($shopData['reviews_count']) }} danh gia)</span>
        </div>
        <div class="info-item">
            <i class="fas fa-utensils text-muted"></i>
            <span>{{ $shopData['category'] }}</span>
        </div>
        <div class="info-item">
            <i class="fas fa-map-marker-alt icon-location"></i>
            <span>{{ $shopData['address'] }}</span>
        </div>
        <div class="info-item">
            <i class="far fa-clock icon-clock"></i>
            <span>{{ $shopData['hours'] }}</span>
        </div>
    </div>
</div>

<div class="shop-layout">
    <aside class="category-sidebar">
        <div class="category-menu-card">
            <h4 class="category-menu-title">Danh muc</h4>
            <div class="d-flex flex-column">
                @php $first = true; @endphp
                @foreach (array_keys($menuCategories) as $catName)
                    <a href="#cat-{{ Str::slug($catName) }}" class="menu-link {{ $first ? 'active' : '' }}">{{ $catName }}</a>
                    @php $first = false; @endphp
                @endforeach
            </div>
        </div>
    </aside>

    <main class="content-area">
        <div class="food-list-container">
            @foreach ($menuCategories as $catName => $items)
                <h3 class="section-title" id="cat-{{ Str::slug($catName) }}">{{ $catName }}</h3>

                @if (count($items) > 0)
                    @foreach ($items as $item)
                        <div class="food-card">
                            <img src="{{ $item['image'] }}" class="food-img" alt="{{ $item['name'] }}">
                            <div class="food-details">
                                <h5 class="food-name">{{ $item['name'] }}</h5>
                                <p class="food-desc">{{ $item['description'] }}</p>
                                <div class="food-bottom">
                                    <span class="food-price">{{ number_format($item['price']) }}d</span>
                                    <button class="btn-add-cart" onclick="addToDraft({{ $item['id'] }}, '{{ addslashes($item['name']) }}', {{ $item['price'] }})">
                                        + Them vao don
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="text-muted fst-italic">Dang cap nhat mon moi...</p>
                @endif
            @endforeach
        </div>

        <div class="cart-container-wrap">
            <div class="floating-cart">
                <h4 class="cart-title">
                    <i class="fas fa-receipt text-brand"></i> Don tam
                </h4>

                <div class="cart-items"></div>

                <hr class="cart-divider">

                <div class="cart-total">
                    <span class="text-muted fw-semibold">Tam tinh:</span>
                    <span class="cart-total-value">0d</span>
                </div>

                <button class="btn btn-checkout" onclick="goToCheckout()">Thanh toan</button>
            </div>
        </div>
    </main>
</div>
@endsection

@section('scripts')
<script>
    let orderDraft = {};

    function addToDraft(id, name, price) {
        if (orderDraft[id]) {
            orderDraft[id].qty += 1;
        } else {
            orderDraft[id] = { name, price: parseFloat(price), qty: 1 };
        }

        renderDraft();
    }

    function updateDraftQty(id, delta) {
        if (!orderDraft[id]) {
            return;
        }

        orderDraft[id].qty += delta;

        if (orderDraft[id].qty <= 0) {
            delete orderDraft[id];
        }

        renderDraft();
    }

    function renderDraft() {
        const itemsContainer = document.querySelector('.cart-items');
        const totalEl = document.querySelector('.cart-total-value');

        itemsContainer.innerHTML = '';
        let total = 0;
        let hasItems = false;

        Object.entries(orderDraft).forEach(([id, item]) => {
            hasItems = true;
            const lineTotal = item.price * item.qty;
            total += lineTotal;

            itemsContainer.innerHTML += `
                <div class="cart-item">
                    <div class="cart-item-info">
                        <div class="cart-item-name">${item.name}</div>
                        <div class="cart-qty-ctrl">
                            <button class="qty-btn" onclick="updateDraftQty('${id}', -1)">-</button>
                            <span>${item.qty}</span>
                            <button class="qty-btn" onclick="updateDraftQty('${id}', 1)">+</button>
                        </div>
                    </div>
                    <div class="cart-item-price">${new Intl.NumberFormat('vi-VN').format(lineTotal)}d</div>
                </div>
            `;
        });

        if (!hasItems) {
            itemsContainer.innerHTML = '<p class="text-muted fst-italic">Don tam dang trong</p>';
            totalEl.innerText = '0d';
            return;
        }

        totalEl.innerText = `${new Intl.NumberFormat('vi-VN').format(total)}d`;
    }

    async function goToCheckout() {
        const isAuth = {{ Auth::check() ? 'true' : 'false' }};

        if (!isAuth) {
            alert('Ban can dang nhap de dat hang!');
            window.location.href = "{{ route('login') }}";
            return;
        }

        if (Object.keys(orderDraft).length === 0) {
            alert('Don tam cua ban dang trong!');
            return;
        }

        const button = document.querySelector('.btn-checkout');
        const originalText = button.innerText;
        button.innerText = 'Dang xu ly...';
        button.disabled = true;

        const items = Object.entries(orderDraft).map(([id, item]) => ({
            product_id: parseInt(id, 10),
            quantity: item.qty,
        }));

        try {
            const response = await fetch("{{ route('checkout.prepare') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    shop_id: {{ $shop->id }},
                    items,
                }),
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Khong the chuyen sang trang thanh toan.');
            }

            window.location.href = data.redirect;
        } catch (error) {
            alert(error.message);
            button.innerText = originalText;
            button.disabled = false;
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        renderDraft();
    });
</script>
@endsection
