@extends('layouts.auth')

@section('title', 'Hồ sơ cá nhân')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h2 class="mb-1">Hồ sơ cá nhân</h2>
                    <p class="text-muted">Cập nhật thông tin, ảnh đại diện, địa chỉ và mật khẩu.</p>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger">Đăng xuất</button>
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
                    <div class="card shadow-sm p-4 text-center">
                        <div class="mb-3">
                            @if($user->avatar)
                                <img src="{{ asset($user->avatar) }}" class="rounded-circle img-fluid" style="width: 180px; height: 180px; object-fit: cover;" alt="Avatar">
                            @else
                                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 180px; height: 180px;">
                                    <span class="text-muted">No Avatar</span>
                                </div>
                            @endif
                        </div>
                        <h5 class="mb-1">{{ $user->name }}</h5>
                        <p class="text-muted mb-1">{{ $user->email }}</p>
                        <p class="mb-1"><strong>SĐT:</strong> {{ $user->phone ?? '-' }}</p>
                        <p><strong>Địa chỉ:</strong><br>{{ $user->address ?? '-' }}</p>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card shadow-sm p-4 mb-4">
                        <h5 class="mb-3">Cập nhật thông tin cá nhân</h5>
                        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label">Họ và tên</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Số điện thoại</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}" placeholder="Ví dụ: 0912345678">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Địa chỉ</label>
                                <textarea name="address" class="form-control" rows="3" placeholder="Nhập địa chỉ của bạn">{{ old('address', $user->address) }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Ảnh đại diện</label>
                                <input type="file" name="avatar" class="form-control" accept="image/*">
                            </div>

                            <button type="submit" class="btn btn-danger">Lưu thông tin</button>
                        </form>
                    </div>

                    <div class="card shadow-sm p-4">
                        <h5 class="mb-3">Đổi mật khẩu</h5>
                        <form action="{{ route('profile.password.update') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label">Mật khẩu hiện tại</label>
                                <input type="password" name="current_password" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Mật khẩu mới</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Xác nhận mật khẩu mới</label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>

                            <button type="submit" class="btn btn-secondary">Cập nhật mật khẩu</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
