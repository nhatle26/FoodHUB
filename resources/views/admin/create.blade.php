@extends('layouts.admin')

@section('title', 'Tạo User Mới')
@section('page-title', 'Tạo User Mới')
@section('page-subtitle', 'Thêm tài khoản mới cho hệ thống')

@section('content')
    <div class="admin-card" style="max-width:760px;">
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">Tên</label>
                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="password" class="form-label">Mật khẩu</label>
                    <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label for="password_confirmation" class="form-label">Xác nhận mật khẩu</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
                </div>
            </div>

            <div class="row g-3 mt-3">
                <div class="col-md-6">
                    <label for="role" class="form-label">Vai trò</label>
                    <select id="role" name="role" class="form-select @error('role') is-invalid @enderror" required>
                        <option value="">-- Chọn vai trò --</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="shop" {{ old('role') === 'shop' ? 'selected' : '' }}>Shop</option>
                        <option value="customer" {{ old('role') === 'customer' ? 'selected' : '' }}>Customer</option>
                    </select>
                    @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label for="phone" class="form-label">Điện thoại</label>
                    <input type="text" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}">
                    @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="mb-3 mt-3">
                <label for="address" class="form-label">Địa chỉ</label>
                <textarea id="address" name="address" rows="3" class="form-control @error('address') is-invalid @enderror">{{ old('address') }}</textarea>
                @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="form-check mb-4">
                <input type="checkbox" id="is_active" name="is_active" class="form-check-input" value="1" {{ old('is_active') ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">Hoạt động</label>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Hủy</a>
                <button type="submit" class="btn btn-primary">Lưu</button>
            </div>
        </form>
    </div>
@endsection
