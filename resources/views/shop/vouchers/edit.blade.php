@extends('layouts.shop')

@section('title', 'Sửa Voucher')

@section('content')
<div class="page-header mb-4">
    <div class="mb-2">
        <a href="{{ route('shop.vouchers.index') }}" class="text-decoration-none text-muted"><i class="bi bi-arrow-left me-1"></i> Trở về danh sách</a>
    </div>
    <h2>Sửa Mã Giảm Giá: {{ $voucher->code }}</h2>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form action="{{ route('shop.vouchers.update', $voucher->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Mã Voucher <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control text-uppercase @error('code') is-invalid @enderror" value="{{ old('code', $voucher->code) }}" required>
                        @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Loại Giảm Giá <span class="text-danger">*</span></label>
                            <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                                <option value="fixed" {{ old('type', $voucher->type) == 'fixed' ? 'selected' : '' }}>Giảm số tiền cố định (VNĐ)</option>
                                <option value="percent" {{ old('type', $voucher->type) == 'percent' ? 'selected' : '' }}>Giảm theo phần trăm (%)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Giá Trị Giảm <span class="text-danger">*</span></label>
                            <input type="number" name="value" class="form-control @error('value') is-invalid @enderror" value="{{ old('value', $voucher->value) }}" required min="0">
                            @error('value') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Đơn Tối Thiểu (VNĐ)</label>
                            <input type="number" name="min_order_amount" class="form-control @error('min_order_amount') is-invalid @enderror" value="{{ old('min_order_amount', $voucher->min_order_amount) }}" min="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Giảm Tối Đa (VNĐ)</label>
                            <input type="number" name="max_discount" class="form-control @error('max_discount') is-invalid @enderror" value="{{ old('max_discount', $voucher->max_discount) }}" min="0">
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Giới hạn lượt dùng</label>
                            <input type="number" name="usage_limit" class="form-control @error('usage_limit') is-invalid @enderror" value="{{ old('usage_limit', $voucher->usage_limit) }}" min="1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Ngày hết hạn</label>
                            <input type="date" name="expires_at" class="form-control @error('expires_at') is-invalid @enderror" value="{{ old('expires_at', $voucher->expires_at ? \Carbon\Carbon::parse($voucher->expires_at)->format('Y-m-d') : '') }}">
                        </div>
                    </div>

                    <div class="form-check form-switch mb-4">
                        <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" {{ old('is_active', $voucher->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold" for="is_active">Kích hoạt Voucher</label>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('shop.vouchers.index') }}" class="btn btn-secondary">Hủy bỏ</a>
                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
