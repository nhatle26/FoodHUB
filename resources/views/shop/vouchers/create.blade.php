@extends('layouts.shop')

@section('title', 'Tạo Voucher')

@section('content')
<div class="page-header mb-4">
    <div class="mb-2">
        <a href="{{ route('shop.vouchers.index') }}" class="text-decoration-none text-muted"><i class="bi bi-arrow-left me-1"></i> Trở về danh sách</a>
    </div>
    <h2>Thêm Mã Giảm Giá Mới</h2>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form action="{{ route('shop.vouchers.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Mã Voucher <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control text-uppercase @error('code') is-invalid @enderror" value="{{ old('code') }}" required placeholder="VD: GIAM20K, FREESHIP...">
                        @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="form-text">Mã phải là duy nhất, không dấu, không khoảng trắng.</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Loại Giảm Giá <span class="text-danger">*</span></label>
                            <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                                <option value="fixed" {{ old('type') == 'fixed' ? 'selected' : '' }}>Giảm số tiền cố định (VNĐ)</option>
                                <option value="percent" {{ old('type') == 'percent' ? 'selected' : '' }}>Giảm theo phần trăm (%)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Giá Trị Giảm <span class="text-danger">*</span></label>
                            <input type="number" name="value" class="form-control @error('value') is-invalid @enderror" value="{{ old('value') }}" required min="0" placeholder="Ví dụ: 20000 hoặc 10">
                            @error('value') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Đơn Tối Thiểu (VNĐ)</label>
                            <input type="number" name="min_order_amount" class="form-control @error('min_order_amount') is-invalid @enderror" value="{{ old('min_order_amount') }}" min="0" placeholder="VD: 50000">
                            <div class="form-text">Bỏ trống nếu không yêu cầu đơn tối thiểu.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Giảm Tối Đa (VNĐ)</label>
                            <input type="number" name="max_discount" class="form-control @error('max_discount') is-invalid @enderror" value="{{ old('max_discount') }}" min="0" placeholder="VD: 50000">
                            <div class="form-text">Chỉ áp dụng khi chọn giảm theo %.</div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Giới hạn lượt dùng</label>
                            <input type="number" name="usage_limit" class="form-control @error('usage_limit') is-invalid @enderror" value="{{ old('usage_limit') }}" min="1">
                            <div class="form-text">Bỏ trống nếu không giới hạn lượt sử dụng.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Ngày hết hạn</label>
                            <input type="date" name="expires_at" class="form-control @error('expires_at') is-invalid @enderror" value="{{ old('expires_at') }}">
                        </div>
                    </div>

                    <div class="form-check form-switch mb-4">
                        <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" checked>
                        <label class="form-check-label fw-bold" for="is_active">Kích hoạt Voucher ngay lập tức</label>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('shop.vouchers.index') }}" class="btn btn-secondary">Hủy bỏ</a>
                        <button type="submit" class="btn btn-primary">Tạo Mã Giảm Giá</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
