@extends('layouts.auth')

@section('title', 'Đăng nhập')

@section('content')
<div class="container-fluid p-0">
    <div class="row g-0 vh-100">
        <div class="col-lg-7 d-none d-lg-block">
            <div class="bg-image">
                <div class="overlay-text">
                    <h1>Food<span>Hub</span></h1>
                    <p>Hương vị yêu thích, giao tận cửa nhà.</p>
                </div>
            </div>
        </div>

        <div class="col-lg-5 d-flex align-items-center justify-content-center bg-white">
            <div class="login-form-wrapper p-4 p-md-5">
                <div class="text-center mb-4">

                    <h2 class="fw-bold">Đăng Nhập</h2>
                    <p class="text-muted">Vui lòng nhập thông tin tài khoản của bạn</p>
                </div>

                <form action="{{ route('login.post') }}" method="POST" novalidate>
                    @csrf
                    
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
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
                            <input type="password" name="password" class="form-control border-start-0 @error('password') is-invalid @enderror" placeholder="********" required>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="remember" name="remember">
                            <label class="form-check-label" for="remember">Ghi nhớ</label>
                        </div>
                        <a href="#" class="text-decoration-none text-brand">Quên mật khẩu?</a>
                    </div>

                    <button type="submit" class="btn btn-brand w-100 py-2 fw-bold mb-3">ĐĂNG NHẬP</button>

                    <div class="divider text-center my-3">
                        <span class="px-2 text-muted">Hoặc</span>
                    </div>

                    <button type="button" class="btn btn-outline-dark w-100 mb-3">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/c/c1/Google_%22G%22_logo.svg" width="18" class="me-2">
                        Đăng nhập với Google
                    </button>
                </form>

                <p class="text-center mt-4">
                    Bạn chưa có tài khoản? <a href="{{ route('register') }}" class="text-brand fw-bold text-decoration-none">Đăng ký ngay</a>
                </p>
                <p class="text-center mt-2">
                    Bạn là chủ cửa hàng? <a href="{{ route('shop.create') }}" class="text-brand text-decoration-none">Đăng kí cửa hàng</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
