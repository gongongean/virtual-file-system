
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'Virtual File System')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
          rel="stylesheet">

    <style>
        :root {
            --vfs-bg: #080e1a;
            --vfs-sidebar: #0d1728;
            --vfs-surface: #111e32;
            --vfs-surface-light: #192942;
            --vfs-blue: #2583ff;
            --vfs-blue-light: #59a7ff;
            --vfs-secondary: #192942;
            --vfs-secondary-hover: #203653;
            --vfs-text: #e8f0fc;
            --vfs-muted: #91a3bd;
            --vfs-border: rgba(110, 160, 230, 0.16);
            --sidebar-width: 230px;
        }

        * {
            box-sizing: border-box;
        }

        html, body {
            min-height: 100%;
            margin: 0;
        }

        body {
            color: var(--vfs-text);
            background:
                radial-gradient(
                    ellipse at top left,
                    rgba(37, 131, 255, 0.08),
                    transparent 40%
                ),
                var(--vfs-bg);

            font-family: "Segoe UI", Arial, sans-serif;
            overflow-x: hidden;
        }

        /* SIDEBAR */

        .vfs-sidebar {
            position: fixed;
            top: 12px;
            left: 12px;
            bottom: 12px;
            z-index: 1040;
            width: var(--sidebar-width);
            background: var(--vfs-sidebar);
            border: 1px solid var(--vfs-border);
            border-radius: 18px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.25);
            transition: transform 0.3s ease;
        }

        .vfs-sidebar-brand {
            min-height: 72px;
            padding: 0 18px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: white;
            font-size: 15px;
            font-weight: 700;
            border-bottom: 1px solid var(--vfs-border);
            white-space: nowrap;
            text-decoration: none;
        }

        .vfs-sidebar-brand i {
            color: var(--vfs-blue-light);
            font-size: 24px;
        }

        .vfs-sidebar-brand:hover {
            color: var(--vfs-blue-light);
        }

        .vfs-sidebar-content {
            flex: 1;
            padding: 25px 12px;
            overflow-y: auto;
        }

        .vfs-menu-heading {
            padding: 0 12px;
            margin: 0 0 12px;
            color: #7085a3;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        .vfs-nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 13px;
            margin-bottom: 7px;
            color: #a8bad2;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            border-radius: 11px;
            transition: all 0.25s ease;
        }

        .vfs-nav-link i {
            width: 20px;
            font-size: 17px;
            text-align: center;
        }

        .vfs-nav-link:hover {
            color: white;
            background: var(--vfs-surface-light);
            transform: translateX(3px);
        }

        .vfs-nav-link.active {
            color: white;
            background: linear-gradient(100deg, #1769ca, #174783);
            box-shadow: 0 5px 18px rgba(37, 131, 255, 0.2);
        }

        /* SIDEBAR FOOTER */

        .vfs-sidebar-footer {
            padding: 15px 12px;
            border-top: 1px solid var(--vfs-border);
        }

        .vfs-sidebar-user {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px;
            color: #dce8f8;
            font-size: 12px;
            overflow: hidden;
        }

        .vfs-sidebar-user i {
            font-size: 24px;
            color: var(--vfs-blue-light);
        }

        .vfs-sidebar-user span {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* LOGOUT BUTTON */

        .vfs-logout-btn {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 13px;
            color: #ff9292;
            background: rgba(255, 70, 70, 0.07);
            border: 1px solid rgba(255, 90, 90, 0.15);
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            text-align: left;
            transition: all 0.25s ease;
        }

        .vfs-logout-btn:hover {
            color: white;
            background: #dc3545;
            border-color: #dc3545;
            transform: translateY(-2px);
        }

        .vfs-logout-btn i {
            font-size: 17px;
        }

        /* MAIN CONTENT */

        .vfs-main {
            min-height: 100vh;
            margin-left: calc(var(--sidebar-width) + 24px);
            margin-right: 12px;
            padding: 30px;
            animation: vfsPageEnter 0.45s ease both;
        }

        /* MOBILE MENU */

        .vfs-mobile-toggle {
            display: none;
            position: fixed;
            top: 15px;
            right: 15px;
            z-index: 1060;
            width: 44px;
            height: 44px;
            align-items: center;
            justify-content: center;
            color: white;
            background: #1769ca;
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 12px;
            font-size: 22px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }

        .vfs-mobile-toggle:hover {
            background: var(--vfs-blue);
        }

        .vfs-sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 1035;
            background: rgba(0, 0, 0, 0.65);
        }

        /* GLOBAL DARK THEME */

        h1, h2, h3, h4, h5, h6 {
            color: #f1f6ff;
        }

        p, label {
            color: var(--vfs-text);
        }

        .text-muted {
            color: var(--vfs-muted) !important;
        }

        .text-dark {
            color: var(--vfs-text) !important;
        }

        .bg-light,
        .bg-white {
            background-color: var(--vfs-surface) !important;
        }

        /* CARDS */

        .card {
            color: var(--vfs-text);
            background: var(--vfs-surface);
            border: 1px solid var(--vfs-border);
            border-radius: 14px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.17);
            transition: all 0.25s ease;
        }

        .card:hover {
            border-color: rgba(37, 131, 255, 0.4);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.25);
        }

        .card-header {
            color: var(--vfs-text);
            background: var(--vfs-surface-light);
            border-bottom: 1px solid var(--vfs-border);
        }

        /* TABLES */

        .table {
            --bs-table-bg: transparent;
            --bs-table-color: var(--vfs-text);
            --bs-table-border-color: var(--vfs-border);
            --bs-table-hover-bg: rgba(37, 131, 255, 0.09);
            --bs-table-hover-color: white;
        }

        .table thead th {
            color: #91bfff;
            background: var(--vfs-surface-light);
        }

        .table-light {
            --bs-table-bg: var(--vfs-surface-light);
            --bs-table-color: var(--vfs-text);
        }

        /* FORMS */

        .form-control,
        .form-select {
            color: var(--vfs-text);
            background-color: #0c1525;
            border: 1px solid var(--vfs-border);
            border-radius: 8px;
        }

        .form-control:focus,
        .form-select:focus {
            color: white;
            background-color: #101d31;
            border-color: var(--vfs-blue);
            box-shadow: 0 0 0 0.2rem rgba(37, 131, 255, 0.15);
        }

        .form-control::placeholder {
            color: #71839d;
        }

        .form-select option {
            color: white;
            background: var(--vfs-surface);
        }

        /* =========================
           GLOBAL BUTTON THEME
        ========================= */

        .btn {
            border-radius: 9px !important;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        /* PRIMARY - BLUE */

        .btn-primary,
        .btn-info,
        .btn-success,
        .btn-warning,
        .btn-dark,
        .btn-outline-primary,
        .btn-outline-info,
        .btn-outline-success,
        .btn-outline-warning,
        .btn-outline-dark {
            color: #ffffff !important;
            background: var(--vfs-blue) !important;
            border-color: var(--vfs-blue) !important;
        }

        .btn-primary:hover,
        .btn-info:hover,
        .btn-success:hover,
        .btn-warning:hover,
        .btn-dark:hover,
        .btn-outline-primary:hover,
        .btn-outline-info:hover,
        .btn-outline-success:hover,
        .btn-outline-warning:hover,
        .btn-outline-dark:hover {
            color: #ffffff !important;
            background: #0869e8 !important;
            border-color: #0869e8 !important;
            box-shadow: 0 5px 16px rgba(37, 131, 255, 0.3);
        }

        /* SECONDARY - DARK BLUE */

        .btn-secondary,
        .btn-light,
        .btn-outline-secondary,
        .btn-outline-light {
            color: #ffffff !important;
            background: var(--vfs-secondary) !important;
            border-color: var(--vfs-blue-light) !important;
        }

        .btn-secondary:hover,
        .btn-light:hover,
        .btn-outline-secondary:hover,
        .btn-outline-light:hover {
            color: #ffffff !important;
            background: var(--vfs-secondary-hover) !important;
            border-color: var(--vfs-blue) !important;
            box-shadow: 0 5px 16px rgba(37, 131, 255, 0.2);
        }

        /* DELETE - RED */

        .btn-danger,
        .btn-outline-danger {
            color: #ffffff !important;
            background: #dc3545 !important;
            border-color: #dc3545 !important;
        }

        .btn-danger:hover,
        .btn-outline-danger:hover {
            color: #ffffff !important;
            background: #bb2d3b !important;
            border-color: #bb2d3b !important;
        }

        /* BUTTON FOCUS */

        .btn:focus,
        .btn:focus-visible {
            box-shadow: 0 0 0 0.2rem rgba(37, 131, 255, 0.25);
        }

        .btn-danger:focus,
        .btn-outline-danger:focus {
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }

        /* DISABLED BUTTONS */

        .btn:disabled,
        .btn.disabled {
            opacity: 0.55;
            transform: none;
            box-shadow: none;
        }

        /* MODALS */

        .modal-content {
            color: var(--vfs-text);
            background: var(--vfs-surface);
            border: 1px solid var(--vfs-border);
            border-radius: 14px;
        }

        .modal-header,
        .modal-footer {
            border-color: var(--vfs-border);
        }

        .modal-backdrop.show {
            opacity: 0.75;
        }

        /* DROPDOWNS */

        .dropdown-menu {
            color: var(--vfs-text);
            background: var(--vfs-surface);
            border: 1px solid var(--vfs-border);
            border-radius: 10px;
        }

        .dropdown-item {
            color: var(--vfs-text);
        }

        .dropdown-item:hover {
            color: white;
            background: var(--vfs-surface-light);
        }

        /* GLOBAL 4-SECOND ALERTS */

        .alert {
            position: relative;
            overflow: hidden;
            animation: alertFadeIn 0.35s ease;
            transition: opacity 0.5s ease, transform 0.5s ease;
        }

        .alert.vfs-alert-dismiss {
            opacity: 0;
            transform: translateY(-8px);
            pointer-events: none;
        }

        .alert.vfs-alert-dismiss::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            height: 3px;
            width: 0;
            background: #60A5FA;
            animation: alertProgress 4s linear forwards;
        }

        @keyframes alertProgress {
            from { width: 100%; }
            to { width: 0; }
        }

        @keyframes alertFadeIn {
            from {
                opacity: 0;
                transform: translateY(-8px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ANIMATIONS */

        @keyframes vfsPageEnter {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* RESPONSIVE DESIGN */

        @media (max-width: 768px) {
            .vfs-sidebar {
                top: 0;
                left: 0;
                bottom: 0;
                height: 100vh;
                border-radius: 0 18px 18px 0;
                transform: translateX(-100%);
            }

            body.vfs-sidebar-open .vfs-sidebar {
                transform: translateX(0);
            }

            body.vfs-sidebar-open .vfs-sidebar-overlay {
                display: block;
            }

            .vfs-mobile-toggle {
                display: flex;
            }

            .vfs-main {
                margin: 0;
                padding: 25px 14px 20px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>
</head>

<body>

{{-- SIDEBAR --}}

<aside class="vfs-sidebar">

    <a href="{{ route('dashboard') }}" class="vfs-sidebar-brand">
        <i class="bi bi-hdd-stack"></i>
        <span>Virtual File System</span>
    </a>

    <div class="vfs-sidebar-content">

        <div class="vfs-menu-heading">
            Main Menu
        </div>

        <a href="{{ route('dashboard') }}"
           class="vfs-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2-fill"></i>
            Dashboard
        </a>

        <a href="{{ route('virtual-disks.index') }}"
           class="vfs-nav-link {{ request()->routeIs('virtual-disks.*') ? 'active' : '' }}">
            <i class="bi bi-hdd-fill"></i>
            Virtual Disks
        </a>

        <a href="{{ route('permissions.shared') }}"
           class="vfs-nav-link {{ request()->routeIs('permissions.shared') || request()->routeIs('shared-files.*') ? 'active' : '' }}">
            <i class="bi bi-folder-symlink-fill"></i>
            Shared With Me
        </a>

    </div>

    <div class="vfs-sidebar-footer">

        <div class="vfs-sidebar-user">
            <i class="bi bi-person-circle"></i>
            <span>{{ auth()->user()->name }}</span>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="vfs-logout-btn">
                <i class="bi bi-box-arrow-right"></i>
                Logout
            </button>
        </form>

    </div>

</aside>

<div class="vfs-sidebar-overlay" id="vfsSidebarOverlay"></div>

<button type="button"
        class="vfs-mobile-toggle"
        id="vfsMenuToggle"
        aria-label="Open navigation menu">
    <i class="bi bi-list"></i>
</button>

<main class="vfs-main">
    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    const menuToggle = document.getElementById('vfsMenuToggle');
    const sidebarOverlay = document.getElementById('vfsSidebarOverlay');

    if (menuToggle) {
        menuToggle.addEventListener('click', function () {
            document.body.classList.toggle('vfs-sidebar-open');
        });
    }

    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', function () {
            document.body.classList.remove('vfs-sidebar-open');
        });
    }

    document.querySelectorAll('.vfs-nav-link').forEach(function (link) {
        link.addEventListener('click', function () {
            document.body.classList.remove('vfs-sidebar-open');
        });
    });
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.alert').forEach(function (alert) {

        setTimeout(function () {

            alert.classList.add('vfs-alert-dismiss');

            setTimeout(function () {
                alert.remove();
            }, 500);

        }, 4000);

    });

});
</script>

@stack('scripts')

</body>
</html>
