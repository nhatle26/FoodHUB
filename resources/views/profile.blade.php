@extends('layouts.auth')

@section('title', 'Hồ sơ cá nhân')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="mb-3">
                <a href="{{ route('home') }}" class="text-decoration-none text-muted"><i class="fas fa-arrow-left me-1"></i> Trở về Trang chủ</a>
            </div>
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h2 class="mb-1 fw-bold text-dark">Hồ sơ cá nhân</h2>
                    <p class="text-muted">Cập nhật thông tin, ảnh đại diện, địa chỉ và mật khẩu.</p>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-brand fw-medium px-4 rounded-pill">Đăng xuất</button>
                </form>
            </div>

            @if(session('success_info'))
                <div class="alert alert-success">{{ session('success_info') }}</div>
            @endif
            @if(session('success_password'))
                <div class="alert alert-success">{{ session('success_password') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card shadow-sm border-0 rounded-4 p-4 text-center">
                        <div class="mb-3 d-flex justify-content-center">
                            @if($user->avatar)
                                <img src="{{ asset($user->avatar) }}" class="rounded-circle shadow-sm" style="width: 150px; height: 150px; object-fit: cover;" alt="Avatar">
                            @else
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=ff5a36&color=fff&size=150" class="rounded-circle shadow-sm" alt="Avatar">
                            @endif
                        </div>
                        <h5 class="mb-1">{{ $user->name }}</h5>
                        <p class="text-muted mb-1">{{ $user->email }}</p>
                        <p class="mb-1"><strong>SĐT:</strong> {{ $user->phone ?? '-' }}</p>
                        <p><strong>Địa chỉ:</strong><br>{{ $user->address ?? '-' }}</p>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card shadow-sm border-0 rounded-4 p-4 mb-4">
                        <h5 class="mb-4 fw-bold">Cập nhật thông tin cá nhân</h5>
                        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label text-muted fw-medium">Họ và tên</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted fw-medium">Số điện thoại</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}" placeholder="Ví dụ: 0912345678">
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted fw-medium">Địa chỉ</label>
                                <textarea name="address" class="form-control" rows="3" placeholder="Nhập địa chỉ của bạn">{{ old('address', $user->address) }}</textarea>
                            </div>

                            <div class="mb-4">
                                <label class="form-label text-muted fw-medium">Ảnh đại diện</label>
                                <input type="file" name="avatar" class="form-control" accept="image/*">
                            </div>

                            <button type="submit" class="btn btn-brand px-4 py-2 fw-medium rounded-pill">Lưu thông tin</button>
                        </form>
                    </div>

                    <div class="card shadow-sm border-0 rounded-4 p-4">
                        <h5 class="mb-4 fw-bold">Đổi mật khẩu</h5>
                        <form action="{{ route('profile.password.update') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label text-muted fw-medium">Mật khẩu hiện tại</label>
                                <input type="password" name="current_password" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted fw-medium">Mật khẩu mới</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>

                            <div class="mb-4">
                                <label class="form-label text-muted fw-medium">Xác nhận mật khẩu mới</label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>

                            <button type="submit" class="btn btn-outline-brand px-4 py-2 fw-medium rounded-pill">Cập nhật mật khẩu</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
