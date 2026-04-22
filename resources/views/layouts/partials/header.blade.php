<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
    @php
        $cartCount = Auth::check()
            ? \App\Models\Cart::where('user_id', Auth::id())->sum('quantity')
            : 0;
    @endphp

    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
            <i class="fas fa-hamburger me-2 text-brand"></i> Food<span>Hub</span>
        </a>

        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMain">
            <div class="d-flex align-items-center gap-3 ms-auto mt-3 mt-lg-0">
                <a href="{{ route('cart.index') }}" class="position-relative text-dark text-decoration-none me-3">
                    <i class="fas fa-shopping-cart fs-5"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-brand" style="font-size: 0.65rem;">
                        {{ $cartCount }}
                    </span>
                </a>

                @auth
                    <div class="dropdown">
                        <a class="text-decoration-none text-dark dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="me-2 fw-medium">{{ Auth::user()->customer->full_name ?? (Auth::user()->shop->name ?? Auth::user()->email) }}</span>
                            @if (Auth::user()->customer->avatar ?? (Auth::user()->shop->details->logo ?? null))
                                <img src="{{ asset(Auth::user()->customer->avatar ?? Auth::user()->shop->details->logo) }}" class="rounded-circle shadow-sm" width="38" height="38" style="object-fit: cover;" alt="Avatar">
                            @else
                                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->customer->full_name ?? (Auth::user()->shop->name ?? Auth::user()->email)) }}&background=ff5a36&color=fff" class="rounded-circle shadow-sm" width="38" height="38" alt="Avatar">
                            @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 rounded-3">
                            <li>
                                <a class="dropdown-item py-2" href="{{ route('profile') }}">
                                    <i class="fas fa-user-circle text-muted me-2"></i> Ho so ca nhan
                                </a>
                            </li>
                            <li>
                                <button class="dropdown-item py-2" type="button" id="darkModeToggle">
                                    <i class="fas fa-moon text-muted me-2"></i> Doi mau toi
                                </button>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button class="dropdown-item py-2 text-brand" type="submit">
                                        <i class="fas fa-sign-out-alt me-2"></i> Dang xuat
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-brand px-4 rounded-pill fw-medium">Dang nhap</a>
                    <a href="{{ route('register') }}" class="btn btn-brand px-4 rounded-pill fw-medium d-none d-lg-block">Dang ky</a>
                @endauth
            </div>
        </div>
    </div>
</nav>
