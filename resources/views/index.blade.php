@extends('layouts.app')

@section('title', 'FoodHUB - Giao đồ ăn tận nơi')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endsection

@section('content')
<div class="container py-4">
    
    <div class="home-banner mb-5 text-center">
        <h1 class="fw-bold text-brand mb-2">Hôm nay bạn muốn ăn gì?</h1>
        <p class="text-muted mb-4 fs-5">Khám phá hàng ngàn món ngon giao tận nơi</p>
        
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <form action="{{ route('home') }}" method="GET" class="search-box d-flex align-items-center">
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    <i class="fas fa-search text-muted ms-4"></i>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Tìm kiếm quán ăn, trà sữa, gà rán...">
                    <button class="btn btn-brand" type="submit">Tìm</button>
                </form>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Khám phá danh mục</h4>
    </div>
    
    <div class="category-wrapper mb-5">
        <a href="{{ route('home', ['search' => request('search')]) }}" class="category-item {{ !request('category') ? 'active' : '' }}">
            <div class="category-icon-box">
                <i class="fas fa-th-large"></i>
            </div>
            <span>Tất cả</span>
        </a>

        @if(isset($categories))
            @php
                $iconMap = [
                    'icons/trasua.png' => 'fas fa-mug-hot',
                    'icons/doanvan.png' => 'fas fa-cookie',
                    'icons/fastfood.png' => 'fas fa-hamburger',
                    'icons/com.png' => 'fas fa-box',
                    'icons/banhmi.png' => 'fas fa-hotdog',
                    'icons/trangmieng.png' => 'fas fa-ice-cream',
                    'icons/bunpho.png' => 'fas fa-bowl-rice',
                    'icons/pizza.png' => 'fas fa-pizza-slice',
                    'icons/cafe.png' => 'fas fa-coffee',
                    'icons/haisan.png' => 'fas fa-fish',
                    'icons/comtam.png' => 'fas fa-utensils',
                    'icons/chay.png' => 'fas fa-leaf',
                    'icons/nuocep.png' => 'fas fa-glass-martini-alt',
                    'icons/mi.png' => 'fas fa-bacon',
                    'icons/banhngot.png' => 'fas fa-birthday-cake',
                    'icons/nuong.png' => 'fas fa-fire',
                    'icons/garan.png' => 'fas fa-drumstick-bite',
                    'icons/salad.png' => 'fas fa-carrot',
                ];
            @endphp
            @foreach($categories as $category)
            <a href="{{ route('home', ['category' => $category->id, 'search' => request('search')]) }}" class="category-item {{ request('category') == $category->id ? 'active' : '' }}">
                <div class="category-icon-box">
                    @php
                        $faIcon = $iconMap[$category->icon] ?? 'fas fa-utensils';
                    @endphp
                    <i class="{{ $faIcon }}"></i>
                </div>
                <span>{{ $category->name }}</span>
            </a>
            @endforeach
        @endif
    </div>

    <div class="d-flex justify-content-between align-items-end mb-4">
        <h4 class="fw-bold mb-0">
            @if(request('search'))
                Kết quả cho: <span class="text-brand">"{{ request('search') }}"</span>
            @else
                Quán ngon gần bạn
            @endif
        </h4>
        <a href="#" class="text-brand text-decoration-none fw-semibold">Xem thêm <i class="fas fa-angle-right ms-1"></i></a>
    </div>

    @if(isset($shops) && $shops->count() > 0)
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4 mb-5">
            @foreach($shops as $shop)
            <div class="col">
                <a href="{{ route('shop.show', $shop->id) }}" class="text-decoration-none text-dark">
                    <div class="card shop-card h-100">
                        <div class="position-relative">
                            @php
                                $coverImg = $shop->details->cover_image ?? null;
                                $imgUrl = 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=400&q=80';
                                if ($coverImg) {
                                    $imgUrl = str_starts_with($coverImg, 'http') ? $coverImg : asset('storage/' . preg_replace('/^\/?(storage\/)?/', '', $coverImg));
                                }
                            @endphp
                            <img src="{{ $imgUrl }}" class="card-img-top" alt="{{ $shop->name }}" style="height: 200px; object-fit: cover;">
                            
                            <div class="badge-rating">
                                <i class="fas fa-star me-1"></i> {{ $shop->rating ?? '4.8' }}
                            </div>
                            
                            <div class="badge-promo">
                                Giảm 20%
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <h5 class="card-title text-truncate">{{ $shop->name }}</h5>
                            <p class="card-text text-muted small mb-2 text-truncate">
                                {{ $shop->category ? $shop->category->name : 'Nhiều món ngon' }}
                            </p>
                            
                            <hr class="my-2 opacity-10">
                            
                            <div class="shop-info-meta d-flex justify-content-between">
                                <span><i class="fas fa-motorcycle me-1 text-muted"></i> 15-20 Phút</span>
                                <span><i class="fas fa-map-marker-alt me-1 text-muted"></i> 2.5 km</span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>

        <div class="d-flex justify-content-center">
            {{ $shops->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
    @else
        <div class="text-center py-5 my-5">
            <i class="fas fa-search-minus fa-4x text-muted opacity-25 mb-3"></i>
            <h5 class="text-muted fw-normal">Không tìm thấy quán ăn phù hợp</h5>
            <a href="{{ route('home') }}" class="btn btn-outline-brand rounded-pill mt-3 px-4">Xóa bộ lọc</a>
        </div>
    @endif

</div>
@endsection