@extends('layouts.admin')

@section('title', 'Sửa User')
@section('page-title', 'Sửa User')
@section('page-subtitle', 'Cập nhật thông tin tài khoản')

@section('content')
@php
    $userName = $user->customer?->full_name ?? ($user->shop?->name ?? $user->email);
    $userPhone = $user->customer?->phone ?? ($user->shop?->details?->phone ?? '');
    $userAddress = $user->customer?->addresses?->where('is_default', 1)?->first()?->address_line ?? ($user->shop?->details?->address ?? '');
@endphp
    <div class="admin-card" style="max-width:760px;">
        <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="name" class="form-label">Tên</label>
                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $userName) }}" required>
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Mật khẩu mới (bỏ trống nếu không đổi)</label>
                <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror">
                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                <div class="form-text">Nếu nhập, mật khẩu phải ít nhất 8 ký tự.</div>
            </div>

            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Xác nhận mật khẩu</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control">
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="role" class="form-label">Vai trò</label>
                    <select id="role" name="role" class="form-select @error('role') is-invalid @enderror" required>
                        <option value="">-- Chọn vai trò --</option>
                        <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="shop" {{ old('role', $user->role) === 'shop' ? 'selected' : '' }}>Shop</option>
                        <option value="customer" {{ old('role', $user->role) === 'customer' ? 'selected' : '' }}>Customer</option>
                    </select>
                    @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label for="phone" class="form-label">Điện thoại</label>
                    <input type="text" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $userPhone) }}">
                    @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="mb-3 mt-3">
                <label for="address" class="form-label">Địa chỉ</label>
                <textarea id="address" name="address" rows="3" class="form-control @error('address') is-invalid @enderror">{{ old('address', $userAddress) }}</textarea>
                @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="form-check mb-4">
                <input type="checkbox" id="is_active" name="is_active" class="form-check-input" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">Hoạt động</label>
            </div>

            <div class="alert alert-info">
                <small>
                    <strong>ID:</strong> {{ $user->id }} | 
                    <strong>Tạo:</strong> {{ $user->created_at->format('d/m/Y H:i') }} | 
                    <strong>Cập nhật:</strong> {{ $user->updated_at->format('d/m/Y H:i') }}
                </small>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Hủy</a>
                <button type="submit" class="btn btn-success">Cập nhật</button>
            </div>
        </form>
    </div>
@endsection
