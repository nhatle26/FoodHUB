<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="{{ route('home') ?? '/' }}">
            <i class="fas fa-hamburger me-2 text-brand"></i> Food<span>Hub</span>
        </a>
        
        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link fw-semibold {{ request()->is('/') ? 'text-brand' : '' }}" href="{{ route('home') ?? '/' }}">Trang chủ</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-semibold" href="#">Khuyến mãi</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-semibold" href="#">Quán yêu thích</a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-3 mt-3 mt-lg-0">
                <a href="#" class="position-relative text-dark text-decoration-none me-3">
                    <i class="fas fa-shopping-cart fs-5"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-brand" style="font-size: 0.65rem;">
                        0
                    </span>
                </a>

                @auth
                    <div class="dropdown">
                        <a class="text-decoration-none text-dark dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                            <span class="me-2 fw-medium">{{ Auth::user()->name }}</span>
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=dc3545&color=fff" class="rounded-circle" width="36" height="36" alt="Avatar">
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 rounded-3">
                            <li><a class="dropdown-item py-2" href="{{ route('profile') }}"><i class="fas fa-user text-muted me-2"></i> Hồ sơ của tôi</a></li>
                            <li><a class="dropdown-item py-2" href="#"><i class="fas fa-clipboard-list text-muted me-2"></i> Đơn hàng</a></li>
                            @if(Auth::user()->role === 'admin')
                                <li><a class="dropdown-item py-2" href="#"><i class="fas fa-cogs text-muted me-2"></i> Quản trị hệ thống</a></li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button class="dropdown-item py-2 text-brand" type="submit">
                                        <i class="fas fa-sign-out-alt me-2"></i> Đăng xuất
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-brand px-4 rounded-pill fw-medium">Đăng nhập</a>
                    <a href="{{ route('register') }}" class="btn btn-brand px-4 rounded-pill fw-medium d-none d-lg-block">Đăng ký</a>
                @endauth
            </div>
        </div>
    </div>
</nav>