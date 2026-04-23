@extends('layouts.shop')

@section('title', 'Thiết lập Shop')

@section('content')
<div class="page-header">
    <div>
        <h2>Thiết lập Shop</h2>
        <p class="text-muted">Cập nhật thông tin và trạng thái cửa hàng của bạn</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="row">
    <div class="col-md-8">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <form action="{{ route('shop.settings.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tên Shop</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $shop->name) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Mô tả ngắn</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $shop->description) }}</textarea>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Số điện thoại</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $shop->details->phone ?? '') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Giờ mở cửa</label>
                            <div class="d-flex align-items-center">
                                <input type="time" name="open_time" class="form-control" value="{{ old('open_time', $shop->details->open_time ?? '') }}">
                                <span class="mx-2">-</span>
                                <input type="time" name="close_time" class="form-control" value="{{ old('close_time', $shop->details->close_time ?? '') }}">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Địa chỉ</label>
                        <input type="text" name="address" class="form-control" value="{{ old('address', $shop->details->address ?? '') }}">
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Ảnh Logo</label>
                            <input type="file" name="logo" class="form-control" accept="image/*">
                            @if($shop->details->logo ?? false)
                                @php $logoUrl = str_starts_with($shop->details->logo, 'http') ? $shop->details->logo : asset('storage/' . $shop->details->logo); @endphp
                                <img src="{{ $logoUrl }}" class="mt-2 rounded" style="height: 60px;">
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Ảnh Banner (Bìa)</label>
                            <input type="file" name="banner" class="form-control" accept="image/*">
                            @if($shop->details->banner ?? false)
                                @php $bannerUrl = str_starts_with($shop->details->banner, 'http') ? $shop->details->banner : asset('storage/' . $shop->details->banner); @endphp
                                <img src="{{ $bannerUrl }}" class="mt-2 rounded w-100" style="height: 60px; object-fit: cover;">
                            @endif
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm border-0 text-center p-4">
            <h5 class="fw-bold mb-3">Trạng thái hoạt động</h5>
            
            <div class="mb-4">
                @if($shop->status === 'active')
                    <span class="badge bg-success fs-6 px-4 py-2">Đang Mở Cửa</span>
                    <p class="text-muted mt-2 small">Khách hàng có thể nhìn thấy và đặt món từ shop của bạn.</p>
                @elseif($shop->status === 'inactive')
                    <span class="badge bg-secondary fs-6 px-4 py-2">Tạm Đóng Cửa</span>
                    <p class="text-muted mt-2 small">Shop của bạn hiện đang ẩn đối với khách hàng.</p>
                @elseif($shop->status === 'pending')
                    <span class="badge bg-warning fs-6 px-4 py-2">Chờ Duyệt</span>
                    <p class="text-muted mt-2 small">Shop đang chờ Quản trị viên duyệt.</p>
                @else
                    <span class="badge bg-danger fs-6 px-4 py-2">Đã Khóa</span>
                @endif
            </div>

            @if($shop->status === 'active' || $shop->status === 'inactive')
            <form action="{{ route('shop.settings.toggle_status') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-{{ $shop->status === 'active' ? 'outline-danger' : 'success' }} w-100">
                    {{ $shop->status === 'active' ? 'Tạm đóng cửa' : 'Mở cửa trở lại' }}
                </button>
            </form>
            @endif
        </div>
    </div>
</div>
@endsection
