<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('description', 'Professional finance dashboard and market overview.')">

    <title>@yield('title', 'Median Edge - Finance')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('me.png') }}">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Phosphor Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/finance.css') }}?v={{ env('APP_VERSION', '1.0.0') }}">
    @stack('styles')
</head>

<body>

    <!-- Navbar -->
    @include('template.partials.navbar')

    @yield('subnav')

    <!-- Page Content -->
    <main>
        @yield('content')
    </main>

    <!-- Scripts -->
    @stack('scripts')

    <script>
        const THEME_KEY = 'finance-theme';

        function applyTheme(theme) {
            const isDark = theme === "dark"; document.body.classList.toggle("dark-theme", isDark); document.documentElement.classList.toggle("dark", isDark);
            const btn = document.getElementById('themeToggleBtn');
            if (btn) btn.innerHTML = theme === 'dark' ? '<i class="ph ph-sun" style="font-size:18px;"></i>' : '<i class="ph ph-moon" style="font-size:18px;"></i>';
        }

        function toggleTheme() {
            const next = document.documentElement.classList.contains("dark") ? 'light' : 'dark';
            localStorage.setItem(THEME_KEY, next);
            applyTheme(next);
        }

        document.addEventListener('DOMContentLoaded', () => {
            const saved = localStorage.getItem(THEME_KEY) || 'light';
            applyTheme(saved);
        });
    </script>
</body>

</html>