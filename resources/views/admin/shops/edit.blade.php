@extends('layouts.admin')

@section('title', 'Chỉnh sửa Shop — ' . $shop->name)
@section('page-title', 'Chỉnh sửa Shop')
@section('page-subtitle', $shop->name)

@section('content')
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('admin.shops.index') }}">Quản lý Shop</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.shops.show', $shop->id) }}">{{ $shop->name }}</a></li>
        <li class="breadcrumb-item active">Chỉnh sửa</li>
    </ol>
</nav>

@if($errors->any())
    <div class="alert alert-danger rounded-3 mb-4">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Vui lòng kiểm tra lại:</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('admin.shops.update', $shop->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="row g-4">
        {{-- Thông tin cơ bản --}}
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                    <h6 class="fw-bold">Thông tin cơ bản</h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tên shop <span class="text-danger">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $shop->name) }}"
                                   class="form-control rounded-3 @error('name') is-invalid @enderror"
                                   required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Trạng thái <span class="text-danger">*</span></label>
                            <select name="status" class="form-select rounded-3 @error('status') is-invalid @enderror" required>
                                <option value="active"  {{ old('status', $shop->status) === 'active'  ? 'selected' : '' }}>✅ Đang hoạt động</option>
                                <option value="pending" {{ old('status', $shop->status) === 'pending' ? 'selected' : '' }}>⏳ Chờ duyệt</option>
                                <option value="banned"  {{ old('status', $shop->status) === 'banned'  ? 'selected' : '' }}>🚫 Bị cấm</option>
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Danh mục <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select rounded-3 @error('category_id') is-invalid @enderror" required>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}"
                                        {{ old('category_id', $shop->category_id) == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                    <h6 class="fw-bold">Thông tin liên hệ & Giờ mở cửa</h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Số điện thoại</label>
                            <input type="text" name="phone"
                                   value="{{ old('phone', $shop->details->phone ?? '') }}"
                                   class="form-control rounded-3 @error('phone') is-invalid @enderror"
                                   placeholder="0901234567">
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Địa chỉ</label>
                            <input type="text" name="address"
                                   value="{{ old('address', $shop->details->address ?? '') }}"
                                   class="form-control rounded-3 @error('address') is-invalid @enderror">
                            @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Giờ mở cửa</label>
                            <input type="time" name="open_time"
                                   value="{{ old('open_time', $shop->details->open_time ?? '') }}"
                                   class="form-control rounded-3 @error('open_time') is-invalid @enderror">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Giờ đóng cửa</label>
                            <input type="time" name="close_time"
                                   value="{{ old('close_time', $shop->details->close_time ?? '') }}"
                                   class="form-control rounded-3 @error('close_time') is-invalid @enderror">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Mô tả</label>
                            <textarea name="description" rows="4"
                                      class="form-control rounded-3 @error('description') is-invalid @enderror"
                                      placeholder="Giới thiệu về shop...">{{ old('description', $shop->details->description ?? '') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Cột phải --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4 text-center">
                    @php 
                        $logo = $shop->details->logo ?? null; 
                        $logoUrl = $logo ? (str_starts_with($logo, 'http') ? $logo : asset('storage/' . $logo)) : null;
                    @endphp
                    @if($logoUrl)
                        <img src="{{ $logoUrl }}" class="rounded-circle mb-2 shadow"
                             width="80" height="80" style="object-fit:cover">
                    @else
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto mb-2"
                             style="width:80px;height:80px">
                            <i class="fas fa-store fs-2 text-muted"></i>
                        </div>
                    @endif
                    <h6 class="fw-bold mb-0">{{ $shop->name }}</h6>
                    <small class="text-muted">Email: {{ $shop->user->email ?? 'N/A' }}</small>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary rounded-3 py-2 fw-semibold">
                    <i class="fas fa-save me-2"></i>Lưu thay đổi
                </button>
                <a href="{{ route('admin.shops.show', $shop->id) }}"
                   class="btn btn-outline-secondary rounded-3 py-2">
                    <i class="fas fa-times me-2"></i>Hủy
                </a>
            </div>
        </div>
    </div>
</form>
@endsection
