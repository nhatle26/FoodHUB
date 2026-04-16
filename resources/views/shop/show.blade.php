@extends('layouts.app')

@section('title', $shopData['name'] . ' - FoodHUB')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/shop-detail.css') }}">
@endsection

@section('content')
<!-- Mũi tên quay lại hẹp theo yều cầu -->
<div class="shop-header-bar">
    <a href="{{ route('home') }}" class="back-btn"><i class="fas fa-arrow-left"></i></a>
    <h1 class="shop-title-nav">{{ $shopData['name'] }}</h1>
</div>

<!-- Hero Banner Mở Rộng -->
<div class="shop-hero" style="background-image: url('{{ $shopData['banner'] }}');">
    <!-- Ảnh inset tô Phở góc trái dưới -->
    <div class="shop-hero-inset">
        <img src="https://images.unsplash.com/photo-1582878826629-29b7ad1cdc43?w=200&q=80" alt="Tô Phở Việt Nam">
    </div>
</div>

<!-- Khối thông tin chi tiết -->
<div class="shop-info-block">
    <h1 class="shop-info-title">{{ $shopData['name'] }}</h1>
    <div class="status-badge">{{ $shopData['status'] }}</div>
    
    <div class="info-row">
        <div class="info-item">
            <i class="fas fa-star icon-rating"></i>
            <span><strong>{{ $shopData['rating'] }}</strong> ({{ number_format($shopData['reviews_count']) }} đánh giá)</span>
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

<!-- Bố cục Nội dung Chính -->
<div class="shop-layout">
    
    <!-- Cột trái: Navigation -->
    <aside class="category-sidebar">
        <div class="category-menu-card">
            <h4 class="category-menu-title">Danh mục</h4>
            <div class="d-flex flex-column">
                <a href="#cat-khai-vi" class="menu-link active">Khai vị</a>
                <a href="#cat-mon-chinh" class="menu-link">Món chính</a>
                <a href="#cat-do-uong" class="menu-link">Đồ uống</a>
                <a href="#cat-trang-mieng" class="menu-link">Tráng miệng</a>
            </div>
        </div>
    </aside>

    <!-- Cột phải: Content Layout -->
    <main class="content-area">
        
        <!-- Cột danh sách món (Bên trái của phần body) -->
        <div class="food-list-container">
            @foreach($menuCategories as $catName => $items)
                <h3 class="section-title" id="cat-{{ Str::slug($catName) }}">{{ $catName }}</h3>
                
                @if(count($items) > 0)
                    @foreach($items as $item)
                        <div class="food-card">
                            <img src="{{ $item['image'] }}" class="food-img" alt="{{ $item['name'] }}">
                            <div class="food-details">
                                <h5 class="food-name">{{ $item['name'] }}</h5>
                                <p class="food-desc">{{ $item['description'] }}</p>
                                <div class="food-bottom">
                                    <span class="food-price">{{ number_format($item['price']) }}đ</span>
                                    <button class="btn-add-cart" onclick="addToCart({{ $item['id'] }}, '{{ addslashes($item['name']) }}', {{ $item['price'] }})">+ Thêm vào giỏ</button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="text-muted fst-italic">Đang cập nhật món mới...</p>
                @endif
            @endforeach
        </div>

        <!-- Floating Cart (Bên phải của phần body) -->
        <div class="cart-container-wrap">
            <div class="floating-cart">
                <h4 class="cart-title">
                    <i class="fas fa-shopping-basket text-brand"></i> Giỏ hàng
                </h4>
                
                <div class="cart-items">
                    <!-- Javascript sẽ render giỏ hàng tại đây -->
                </div>

                <hr class="cart-divider">

                <div class="cart-total">
                    <span class="text-muted fw-semibold">Tạm tính:</span>
                    <span class="cart-total-value">0đ</span>
                </div>

                <button class="btn btn-checkout" onclick="alert('Tính năng đặt hàng sẽ được phát triển sau!')">Đặt hàng</button>

            </div>
        </div>

    </main>
</div>
@endsection

@section('scripts')
<script>
    let cart = {};

    function addToCart(id, name, price) {
        if (cart[id]) {
            cart[id].qty += 1;
        } else {
            cart[id] = { name: name, price: parseFloat(price), qty: 1 };
        }
        renderCart();
    }

    function updateQty(id, delta) {
        if (cart[id]) {
            cart[id].qty += delta;
            if (cart[id].qty <= 0) {
                delete cart[id];
            }
        }
        renderCart();
    }

    function renderCart() {
        const cartItemsContainer = document.querySelector('.cart-items');
        const cartTotalEl = document.querySelector('.cart-total-value');
        
        cartItemsContainer.innerHTML = '';
        let total = 0;
        let hasItems = false;

        for (let id in cart) {
            hasItems = true;
            let item = cart[id];
            let itemTotal = item.price * item.qty;
            total += itemTotal;

            cartItemsContainer.innerHTML += `
                <div class="cart-item">
                    <div class="cart-item-info">
                        <div class="cart-item-name">${item.name}</div>
                        <div class="cart-qty-ctrl">
                            <button class="qty-btn" onclick="updateQty('${id}', -1)">–</button>
                            <span>${item.qty}</span>
                            <button class="qty-btn" onclick="updateQty('${id}', 1)">+</button>
                        </div>
                    </div>
                    <div class="cart-item-price">${new Intl.NumberFormat('vi-VN').format(itemTotal)}đ</div>
                </div>
            `;
        }

        if (!hasItems) {
            cartItemsContainer.innerHTML = '<p class="text-muted fst-italic">Giỏ hàng trống</p>';
            cartTotalEl.innerText = '0đ';
        } else {
            cartTotalEl.innerText = new Intl.NumberFormat('vi-VN').format(total) + 'đ';
        }
    }

    // Khởi tạo giỏ hàng rỗng khi load trang
    document.addEventListener('DOMContentLoaded', () => {
        renderCart();
    });
</script>
@endsection
