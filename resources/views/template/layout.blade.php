<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Median Edge')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('me.png') }}">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #ffffff;
            color: #6e6c6c;
            margin: 0;
        }

         /* Theme mode  */
.theme-toggle-btn {
    border: 1px solid #d1d5db;
    background: #f8fafc;
    color: #111827;
    padding: 6px 12px;
    border-radius: 999px;
    font-size: 13px;
    cursor: pointer;
    transition: background .2s, color .2s, border-color .2s;
}

.theme-toggle-btn:hover {
    background: #e2e8f0;
}

body.dark-theme {
    background: #0f172a;
    color: #e2e8f0;
}

body.dark-theme .topnav {
    background: #111827;
    border-color: #1f2937;
}

body.dark-theme .market-selector,
body.dark-theme .assets-region,
body.dark-theme .asset-item,
body.dark-theme .section,
body.dark-theme .box,
body.dark-theme .standout-card,
body.dark-theme .dev-card,
body.dark-theme .side-col {
    background: #111827;
    border-color: #334155;
}

body.dark-theme .topnav-tab,
body.dark-theme .market-status,
body.dark-theme .assets-label,
body.dark-theme .asset-name,
body.dark-theme .asset-price,
body.dark-theme .asset-change,
body.dark-theme .asset-abs,
body.dark-theme .section-title,
body.dark-theme .space-name,
body.dark-theme .space-desc,
body.dark-theme .sc-name,
body.dark-theme .sc-ticker,
body.dark-theme .wl-name,
body.dark-theme .dev-title,
body.dark-theme .wl-ticker {
    color: #e2e8f0 !important;
}

body.dark-theme .topnav-tab:hover,
body.dark-theme .rp-title,
body.dark-theme .ai-name,
body.dark-theme .topnav-tab.active {
    color: #fff;
}

body.dark-theme .heatmap-wrap {
    background: #0f172a !important;
    border-color: #334155 !important;
}

body.dark-theme .ai-price,
body.dark-theme .market-selector,
body.dark-theme .name,
body.dark-theme .hm-label
 {
    color: #12d9e7;
}

/* <!-- Theme mode end --> */
    </style>

</head>

<body>

    <!-- Navbar -->
   <!-- <nav style="padding:10px 20px; border-bottom:1px solid #222;">
        <div style="display:flex; align-items:center; gap:10px;">
            <img src="{{ asset('me.png') }}" style="height:28px; border-radius:6px;">
            <strong>Median Edge</strong>
        </div>

        <button id="themeToggleBtn" class="theme-toggle-btn" onclick="toggleTheme()" aria-label="Toggle theme">
            🌙
        </button>
    </nav> -->

    <nav style="padding:10px 60px; border-bottom:1px solid #222; display:flex; justify-content:space-between; align-items:center;">
    
    <div style="display:flex; align-items:center; gap:10px;">
        <img src="{{ asset('me.png') }}" style="height:28px; border-radius:6px;">
        <strong>Median Edge</strong>
    </div>

    <button id="themeToggleBtn" class="theme-toggle-btn" onclick="toggleTheme()" aria-label="Toggle theme">
        🌙
    </button>

</nav>

    <!-- Page Content -->
    <main>
        @yield('content')
    </main>

    <!-- Scripts -->
     
    <script>
        console.log("App Loaded");
    </script>


<script>
const THEME_KEY = 'finance-theme';

function applyTheme(theme) {
    document.body.classList.toggle('dark-theme', theme === 'dark');
    const btn = document.getElementById('themeToggleBtn');
    if (btn) btn.textContent = theme === 'dark' ? '☀️' : '🌙';
}

function toggleTheme() {
    const next = document.body.classList.contains('dark-theme') ? 'light' : 'dark';
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