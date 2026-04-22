@extends('layouts.auth')

@section('title', 'Đăng ký tài khoản')

@section('content')
<div class="register-full-bg vh-100 d-flex align-items-center justify-content-center p-3">
    
    <div class="register-card shadow-lg p-4 p-md-5">
        <div class="text-center mb-4">
            <h1 class="fw-bold">Food<span class="text-danger">Hub</span></h1>
            <h3 class="fw-bold">Đăng Ký</h3>
            <p class="text-muted">Chào mừng bạn gia nhập FoodHub</p>
        </div>

        <form action="{{ route('register.post') }}" method="POST" novalidate>
            @csrf
            
            <div class="mb-3">
                <label class="form-label fw-semibold">Họ và Tên</label>
                <div class="input-group has-validation">
                    <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-user"></i></span>
                    <input type="text" name="name" class="form-control border-start-0 @error('name') is-invalid @enderror" placeholder="Nguyễn Văn A" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Email</label>
                <div class="input-group has-validation">
                    <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-envelope"></i></span>
                    <input type="email" name="email" class="form-control border-start-0 @error('email') is-invalid @enderror" placeholder="example@gmail.com" value="{{ old('email') }}" required>
                    @error('email')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Mật khẩu</label>
                <div class="input-group has-validation">
                    <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-lock"></i></span>
                    <input type="password" name="password" class="form-control border-start-0 @error('password') is-invalid @enderror" placeholder="Tối thiểu 8 ký tự" required>
                    @error('password')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Xác nhận mật khẩu</label>
                <div class="input-group has-validation">
                    <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-lock"></i></span>
                    <input type="password" name="password_confirmation" class="form-control border-start-0" placeholder="Nhập lại mật khẩu" required>
                </div>
            </div>

            <button type="submit" class="btn btn-danger w-100 py-2 fw-bold mb-3 mt-3">ĐĂNG KÝ NGAY</button>

            <div class="divider text-center my-3">
                <span class="px-2 text-muted">Hoặc</span>
            </div>

            <button type="button" class="btn btn-outline-dark w-100 mb-3">
                <img src="https://upload.wikimedia.org/wikipedia/commons/c/c1/Google_%22G%22_logo.svg" width="18" class="me-2">
                Đăng ký với Google
            </button>
        </form>

        <p class="text-center mt-4">
            Đã có tài khoản? <a href="{{ route('login') }}" class="text-danger fw-bold text-decoration-none">Đăng nhập</a>
        </p>
    </div>
</div>
@endsection