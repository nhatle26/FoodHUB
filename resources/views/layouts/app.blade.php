<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'FoodHUB - Khám phá món ngon')</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bs-body-bg);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        main {
            flex: 1; /* Đẩy footer xuống cuối trang */
        }
        .navbar-brand {
            color: #2b2b2b !important;
            font-weight: 800;
            font-size: 1.5rem;
        }
        .navbar-brand span {
            color: var(--brand-color);
        }
    </style>
    @yield('styles')
</head>
<body>

    @include('layouts.partials.header')
    @include('layouts.partials.toast')

    <main>
        @yield('content')
    </main>

    @include('layouts.partials.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toggleBtn = document.getElementById('darkModeToggle');
            const rootElement = document.documentElement;
            
            // Tải tuỳ chọn trước đó
            const currentTheme = localStorage.getItem('theme');
            if (currentTheme) {
                rootElement.setAttribute('data-bs-theme', currentTheme);
                updateToggleButton(currentTheme, toggleBtn);
            }

            if (toggleBtn) {
                toggleBtn.addEventListener('click', () => {
                    const isDark = rootElement.getAttribute('data-bs-theme') === 'dark';
                    const newTheme = isDark ? 'light' : 'dark';
                    
                    rootElement.setAttribute('data-bs-theme', newTheme);
                    localStorage.setItem('theme', newTheme);
                    updateToggleButton(newTheme, toggleBtn);
                });
            }

            function updateToggleButton(theme, btn) {
                if (!btn) return;
                if (theme === 'dark') {
                    btn.innerHTML = '<i class="fas fa-sun text-warning me-2"></i> Chuyển màu Sáng';
                } else {
                    btn.innerHTML = '<i class="fas fa-moon text-muted me-2"></i> Chuyển màu Tối';
                }
            }
        });
    </script>
    @yield('scripts')
</body>
</html>