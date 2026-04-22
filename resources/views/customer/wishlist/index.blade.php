@extends('layouts.auth')

@section('title', 'Shop Yêu Thích')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Shop Yêu Thích</h2>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($wishlists->count() > 0)
        <div class="row g-4">
            @foreach($wishlists as $wishlist)
                @if($wishlist->shop)
                <div class="col-md-4 col-lg-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                        <div class="position-relative">
                            <img src="{{ asset($wishlist->shop->details->banner ?? 'images/default-shop.jpg') }}" class="card-img-top" alt="{{ $wishlist->shop->name }}" style="height: 160px; object-fit: cover;">
                            <form action="{{ route('customer.wishlist.toggle', $wishlist->shop->id) }}" method="POST" class="position-absolute top-0 end-0 m-2">
                                @csrf
                                <button type="submit" class="btn btn-light rounded-circle shadow-sm text-danger" style="width: 36px; height: 36px; padding: 0;">
                                    <i class="fas fa-heart"></i>
                                </button>
                            </form>
                        </div>
                        <div class="card-body position-relative pt-4 px-3">
                            <img src="{{ asset($wishlist->shop->details->logo ?? 'images/default-logo.png') }}" class="position-absolute rounded-circle shadow-sm bg-white" style="width: 60px; height: 60px; top: -30px; left: 16px; object-fit: cover;">
                            <h5 class="card-title fw-bold text-truncate mb-1">{{ $wishlist->shop->name }}</h5>
                            <p class="text-muted small mb-3 text-truncate"><i class="fas fa-map-marker-alt me-1 text-danger"></i> {{ $wishlist->shop->details->address ?? 'Đang cập nhật địa chỉ' }}</p>
                            <div class="d-grid mt-auto">
                                <a href="{{ route('shop.show', $wishlist->shop->id) }}" class="btn btn-outline-brand rounded-pill">Đến Shop</a>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            @endforeach
        </div>
    @else
        <div class="text-center py-5">
            <h4 class="text-muted">Bạn chưa yêu thích shop nào</h4>
            <a href="{{ route('home') }}" class="btn btn-brand rounded-pill mt-3 px-4">Khám phá ngay</a>
        </div>
    @endif
</div>
@endsection
