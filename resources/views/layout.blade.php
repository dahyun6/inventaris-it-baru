<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SBL IT Assets')</title>

    <!-- Google Fonts: Nunito Sans & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:ital,wght@0,400..700;1,400..700&family=Nunito+Sans:ital,opsz,wght@0,6..12,300..800;1,6..12,300..800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5.3 & FontAwesome 6 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

    <style>
        :root {
            --phoenix-font-sans: 'Nunito Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            --phoenix-font-mono: 'JetBrains Mono', ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
            --bs-font-monospace: var(--phoenix-font-mono);
            --phoenix-primary: #3874ff;
            --phoenix-primary-rgb: 56, 116, 255;
            --phoenix-primary-hover: #2563eb;
            --phoenix-primary-subtle: #edf2ff;
            --phoenix-primary-border: #c7d8fe;
            --phoenix-body-bg: #f5f7fa;
            --phoenix-card-bg: #ffffff;
            --phoenix-border-color: #e3e6ed;
            --phoenix-sidebar-width: 256px;
            --phoenix-header-height: 64px;
            --phoenix-text-emphasis: #141824;
            --phoenix-text-body: #31374a;
            --phoenix-text-muted: #6e7891;
            --phoenix-success: #25b865;
            --phoenix-success-subtle: #e8f8ee;
            --phoenix-warning: #e5780b;
            --phoenix-warning-subtle: #fdf5ea;
            --phoenix-danger: #ec1f00;
            --phoenix-danger-subtle: #fde8e8;
            --phoenix-info: #0097ec;
            --phoenix-info-subtle: #e0f4ff;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: var(--phoenix-font-sans);
            background-color: var(--phoenix-body-bg);
            color: var(--phoenix-text-body);
            font-size: 0.875rem;
            line-height: 1.5;
            overflow-x: hidden;
            letter-spacing: -0.01em;
        }

        .font-monospace, code, kbd, samp, pre, .font-mono {
            font-family: var(--phoenix-font-mono) !important;
            font-feature-settings: "zero" 1, "tnum" 1;
            letter-spacing: -0.015em;
        }

        /* -------------------------------------------------------------
           PHOENIX SIDEBAR
           ------------------------------------------------------------- */
        .phoenix-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: var(--phoenix-sidebar-width);
            background-color: #ffffff;
            border-right: 1px solid var(--phoenix-border-color);
            z-index: 1045;
            display: flex;
            flex-direction: column;
            transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            will-change: transform;
        }

        .sidebar-brand {
            height: var(--phoenix-header-height);
            display: flex;
            align-items: center;
            padding: 0 1.25rem;
            border-bottom: 1px solid var(--phoenix-border-color);
            text-decoration: none;
            gap: 0.75rem;
        }

        .brand-logo-icon {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            background: linear-gradient(135deg, #3874ff 0%, #1d4ed8 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 1rem;
            box-shadow: 0 4px 10px rgba(56, 116, 255, 0.25);
            flex-shrink: 0;
        }

        .brand-title {
            font-weight: 800;
            font-size: 1.05rem;
            color: var(--phoenix-text-emphasis);
            letter-spacing: -0.02em;
            line-height: 1.2;
        }

        .brand-subtitle {
            font-size: 0.7rem;
            color: var(--phoenix-text-muted);
            font-weight: 600;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .sidebar-nav-container {
            flex: 1;
            padding: 1rem 0.75rem;
            overflow-y: auto;
        }

        .nav-label {
            font-size: 0.6875rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--phoenix-text-muted);
            padding: 0.75rem 0.75rem 0.375rem;
            margin-top: 0.5rem;
        }

        .nav-label:first-child {
            margin-top: 0;
        }

        .phoenix-nav-link {
            display: flex;
            align-items: center;
            padding: 0.55rem 0.75rem;
            border-radius: 8px;
            color: #525b75;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.85rem;
            margin-bottom: 2px;
            transition: all 0.15s ease-in-out;
            gap: 0.65rem;
        }

        .phoenix-nav-link i {
            width: 20px;
            text-align: center;
            font-size: 0.95rem;
            color: #8592a3;
            transition: color 0.15s ease-in-out;
        }

        .phoenix-nav-link:hover {
            color: var(--phoenix-primary);
            background-color: #f1f5f9;
        }

        .phoenix-nav-link:hover i {
            color: var(--phoenix-primary);
        }

        .phoenix-nav-link.active {
            color: var(--phoenix-primary);
            background-color: var(--phoenix-primary-subtle);
            font-weight: 700;
        }

        .phoenix-nav-link.active i {
            color: var(--phoenix-primary);
        }

        /* -------------------------------------------------------------
           PHOENIX TOPBAR / HEADER
           ------------------------------------------------------------- */
        .phoenix-header {
            position: fixed;
            top: 0;
            left: var(--phoenix-sidebar-width);
            right: 0;
            height: var(--phoenix-header-height);
            background-color: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border-bottom: 1px solid var(--phoenix-border-color);
            z-index: 1030;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            transition: left 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .header-search-bar {
            position: relative;
            width: 280px;
        }

        .header-search-bar .search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #8592a3;
            font-size: 0.85rem;
            pointer-events: none;
        }

        .header-search-input {
            width: 100%;
            border-radius: 30px;
            border: 1px solid var(--phoenix-border-color);
            padding: 0.45rem 0.85rem 0.45rem 2.2rem;
            font-size: 0.8125rem;
            background-color: #f8fafc;
            color: var(--phoenix-text-body);
            transition: all 0.2s ease;
        }

        .header-search-input:focus {
            background-color: #ffffff;
            border-color: var(--phoenix-primary);
            box-shadow: 0 0 0 3px rgba(56, 116, 255, 0.15);
            outline: none;
        }

        .btn-nav-action {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: 1px solid var(--phoenix-border-color);
            background-color: #ffffff;
            color: #525b75;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.875rem;
            transition: all 0.15s ease-in-out;
            cursor: pointer;
            text-decoration: none;
            flex-shrink: 0;
        }

        .btn-nav-action:hover {
            color: var(--phoenix-primary);
            border-color: var(--phoenix-primary-border);
            background-color: var(--phoenix-primary-subtle);
        }

        .user-dropdown-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.65rem;
            padding: 0.2rem 0.55rem;
            height: 38px;
            border-radius: 30px;
            border: 1px solid var(--phoenix-border-color);
            background-color: #ffffff;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.15s ease;
        }

        .user-dropdown-btn:hover {
            background-color: #f8fafc;
            border-color: #cbd5e1;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            min-width: 32px;
            min-height: 32px;
            flex-shrink: 0;
            aspect-ratio: 1 / 1;
            border-radius: 50%;
            object-fit: cover;
            display: block;
            border: 1.5px solid var(--phoenix-primary-subtle);
        }

        .phoenix-dropdown-menu,
        .phoenix-user-dropdown-menu {
            border-radius: 12px !important;
            border: 1px solid var(--phoenix-border-color) !important;
            box-shadow: 0 10px 30px -5px rgba(20, 24, 36, 0.12), 0 4px 6px -2px rgba(20, 24, 36, 0.04) !important;
            padding: 0.4rem !important;
            margin-top: 0.65rem !important;
            z-index: 1040 !important;
            background-color: #ffffff !important;
        }

        .phoenix-user-dropdown-menu {
            min-width: 250px;
            padding: 0.5rem !important;
        }

        .phoenix-dropdown-menu {
            min-width: 185px;
        }

        .dropdown-item-phoenix {
            padding: 0.55rem 0.75rem;
            border-radius: 8px;
            font-size: 0.825rem;
            font-weight: 600;
            color: #525b75;
            display: flex;
            align-items: center;
            gap: 0.65rem;
            transition: all 0.15s ease;
            text-decoration: none;
            width: 100%;
            border: none;
            background: transparent;
        }

        .dropdown-item-phoenix:hover {
            background-color: #f1f5f9;
            color: var(--phoenix-primary);
        }

        .dropdown-item-phoenix.active,
        .dropdown-item-phoenix.active-item {
            background-color: var(--phoenix-primary-subtle) !important;
            color: var(--phoenix-primary) !important;
        }

        .dropdown-item-phoenix.text-danger:hover {
            background-color: var(--phoenix-danger-subtle);
            color: var(--phoenix-danger) !important;
        }

        /* -------------------------------------------------------------
           CONTENT CONTAINER & PHOENIX CARDS
           ------------------------------------------------------------- */
        .phoenix-main-wrapper {
            margin-left: var(--phoenix-sidebar-width);
            padding-top: var(--phoenix-header-height);
            min-height: 100vh;
            transition: margin-left 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .phoenix-content {
            padding: 1.5rem 1.75rem 2.5rem;
        }

        .page-header-title {
            font-size: 1.35rem;
            font-weight: 800;
            color: var(--phoenix-text-emphasis);
            letter-spacing: -0.02em;
            margin-bottom: 0.15rem;
            line-height: 1.25;
        }

        .phoenix-breadcrumb {
            font-size: 0.775rem;
            font-weight: 600;
            color: var(--phoenix-text-muted);
            margin-bottom: 0;
            display: flex;
            align-items: center;
            gap: 0.35rem;
        }

        .phoenix-breadcrumb a {
            color: var(--phoenix-primary);
            text-decoration: none;
        }

        .phoenix-breadcrumb a:hover {
            text-decoration: underline;
        }

        .phoenix-card {
            background-color: var(--phoenix-card-bg);
            border: 1px solid var(--phoenix-border-color);
            border-radius: 10px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
            margin-bottom: 1.5rem;
            overflow: hidden;
        }

        .phoenix-card-header {
            padding: 0.95rem 1.25rem;
            border-bottom: 1px solid var(--phoenix-border-color);
            background-color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .phoenix-card-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--phoenix-text-emphasis);
            margin-bottom: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .phoenix-card-body {
            padding: 1.25rem;
        }

        /* -------------------------------------------------------------
           PHOENIX BADGES & BUTTONS & TABLES
           ------------------------------------------------------------- */
        .badge-phoenix {
            padding: 0.35em 0.7em;
            font-size: 0.75rem;
            font-weight: 700;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            letter-spacing: 0.02em;
            flex-shrink: 0;
        }

        .badge-phoenix-primary {
            background-color: var(--phoenix-primary-subtle);
            color: var(--phoenix-primary);
            border: 1px solid var(--phoenix-primary-border);
        }

        .badge-phoenix-success {
            background-color: var(--phoenix-success-subtle);
            color: #15803d;
            border: 1px solid #bbf7d0;
        }

        .badge-phoenix-warning {
            background-color: var(--phoenix-warning-subtle);
            color: #b45309;
            border: 1px solid #fde68a;
        }

        .badge-phoenix-danger {
            background-color: var(--phoenix-danger-subtle);
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        .badge-phoenix-info {
            background-color: var(--phoenix-info-subtle);
            color: #0369a1;
            border: 1px solid #bae6fd;
        }

        .btn-phoenix-primary {
            background-color: var(--phoenix-primary);
            color: #ffffff;
            border: 1px solid var(--phoenix-primary);
            font-weight: 600;
            border-radius: 8px;
            padding: 0.45rem 1rem;
            font-size: 0.85rem;
            transition: all 0.15s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-phoenix-primary:hover {
            background-color: var(--phoenix-primary-hover);
            border-color: var(--phoenix-primary-hover);
            color: #ffffff;
            box-shadow: 0 4px 10px rgba(56, 116, 255, 0.25);
        }

        .btn-phoenix-secondary {
            background-color: #ffffff;
            color: #525b75;
            border: 1px solid var(--phoenix-border-color);
            font-weight: 600;
            border-radius: 8px;
            padding: 0.45rem 1rem;
            font-size: 0.85rem;
            transition: all 0.15s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-phoenix-secondary:hover {
            background-color: #f8fafc;
            color: var(--phoenix-text-emphasis);
            border-color: #cbd5e1;
        }

        /* Unified Button Group in Tables */
        .btn-group.btn-group-sm > .btn,
        .btn-group.btn-group-sm > form > .btn {
            border-radius: 0;
        }
        .btn-group.btn-group-sm > :first-child,
        .btn-group.btn-group-sm > form:first-child .btn {
            border-top-left-radius: 6px !important;
            border-bottom-left-radius: 6px !important;
        }
        .btn-group.btn-group-sm > :last-child,
        .btn-group.btn-group-sm > form:last-child .btn {
            border-top-right-radius: 6px !important;
            border-bottom-right-radius: 6px !important;
        }

        /* Custom Modern Scrollbars for Tables */
        .table-responsive::-webkit-scrollbar {
            height: 6px;
            width: 6px;
        }
        .table-responsive::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 4px;
        }
        .table-responsive::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* -------------------------------------------------------------
           RESPONSIVE & MOBILE DRAWER
           ------------------------------------------------------------- */
        .sidebar-backdrop {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(15, 23, 42, 0.45);
            backdrop-filter: blur(2px);
            z-index: 1040;
        }

        @media (max-width: 991.98px) {
            .phoenix-sidebar {
                transform: translateX(-100%);
            }
            .phoenix-header {
                left: 0;
            }
            .phoenix-main-wrapper {
                margin-left: 0;
            }
            body.sidebar-show .phoenix-sidebar {
                transform: translateX(0);
            }
            body.sidebar-show .sidebar-backdrop {
                display: block;
            }
            body.sidebar-show {
                overflow: hidden;
            }
            .header-search-bar {
                display: none;
            }
        }

        @media (min-width: 992px) {
            body.sidebar-collapsed .phoenix-sidebar {
                transform: translateX(-100%);
            }
            body.sidebar-collapsed .phoenix-header {
                left: 0;
            }
            body.sidebar-collapsed .phoenix-main-wrapper {
                margin-left: 0;
            }
        }

        /* -------------------------------------------------------------
           SWEETALERT2 PHOENIX TOAST & POP-UP THEME (Top-Right Toast)
           ------------------------------------------------------------- */
        .phoenix-swal-toast {
            font-family: var(--phoenix-font-sans) !important;
            border-radius: 14px !important;
            border: 1px solid var(--phoenix-border-color) !important;
            box-shadow: 0 12px 32px -4px rgba(15, 23, 42, 0.15), 0 4px 12px -2px rgba(15, 23, 42, 0.08) !important;
            padding: 0.85rem 1.15rem !important;
            background-color: #ffffff !important;
            margin-top: 16px !important;
            margin-right: 16px !important;
        }

        .phoenix-swal-toast .swal2-title {
            font-size: 0.9rem !important;
            color: var(--phoenix-text-emphasis) !important;
            font-weight: 700 !important;
            margin: 0 !important;
            line-height: 1.3 !important;
            text-align: left !important;
        }

        .phoenix-swal-toast .swal2-html-container {
            font-size: 0.825rem !important;
            color: var(--phoenix-text-body) !important;
            margin: 0.25rem 0 0 0 !important;
            line-height: 1.45 !important;
            text-align: left !important;
        }

        .phoenix-swal-toast .swal2-icon {
            margin: 0 0.85rem 0 0 !important;
            width: 30px !important;
            height: 30px !important;
            border-width: 2.5px !important;
            flex-shrink: 0 !important;
        }

        .phoenix-swal-toast .swal2-icon.swal2-success {
            border-color: var(--phoenix-success) !important;
            color: var(--phoenix-success) !important;
        }
        .phoenix-swal-toast .swal2-icon.swal2-success [class^='swal2-success-line'] {
            background-color: var(--phoenix-success) !important;
        }
        .phoenix-swal-toast .swal2-icon.swal2-error {
            border-color: var(--phoenix-danger) !important;
            color: var(--phoenix-danger) !important;
        }
        .phoenix-swal-toast .swal2-icon.swal2-error [class^='swal2-x-mark-line'] {
            background-color: var(--phoenix-danger) !important;
        }
        .phoenix-swal-toast .swal2-icon.swal2-warning {
            border-color: var(--phoenix-warning) !important;
            color: var(--phoenix-warning) !important;
        }
        .phoenix-swal-toast .swal2-icon.swal2-info {
            border-color: var(--phoenix-primary) !important;
            color: var(--phoenix-primary) !important;
        }

        .phoenix-swal-toast .swal2-close {
            font-size: 1.15rem !important;
            color: #94a3b8 !important;
            margin: 0 !important;
            padding: 0 0.25rem !important;
            box-shadow: none !important;
            transition: color 0.15s ease !important;
        }
        .phoenix-swal-toast .swal2-close:hover {
            color: var(--phoenix-danger) !important;
        }

        .phoenix-swal-toast .swal2-timer-progress-bar {
            height: 3.5px !important;
            background: var(--phoenix-primary) !important;
        }
        .phoenix-swal-toast.swal2-icon-success .swal2-timer-progress-bar {
            background: var(--phoenix-success) !important;
        }
        .phoenix-swal-toast.swal2-icon-error .swal2-timer-progress-bar {
            background: var(--phoenix-danger) !important;
        }
        .phoenix-swal-toast.swal2-icon-warning .swal2-timer-progress-bar {
            background: var(--phoenix-warning) !important;
        }

        /* Modal Dialog for Confirmations */
        .phoenix-swal-modal {
            font-family: var(--phoenix-font-sans) !important;
            border-radius: 18px !important;
            border: 1px solid var(--phoenix-border-color) !important;
            box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.25), 0 0 0 1px rgba(227, 230, 237, 0.6) !important;
            padding: 2rem 1.75rem 1.75rem !important;
            background-color: #ffffff !important;
            max-width: 440px !important;
            width: 90% !important;
        }
        .phoenix-swal-title {
            font-size: 1.25rem !important;
            font-weight: 800 !important;
            color: var(--phoenix-text-emphasis) !important;
            letter-spacing: -0.02em !important;
            margin: 0.25rem 0 0.5rem 0 !important;
            padding: 0 !important;
            line-height: 1.3 !important;
        }
        .phoenix-swal-html {
            font-size: 0.9rem !important;
            color: var(--phoenix-text-body) !important;
            line-height: 1.55 !important;
            margin: 0.5rem 0 1.25rem 0 !important;
            padding: 0 !important;
        }
        .phoenix-swal-actions {
            margin-top: 1.25rem !important;
            width: 100% !important;
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
            gap: 0.75rem !important;
        }

        @media (prefers-reduced-motion: reduce) {
            .phoenix-sidebar, .phoenix-header, .phoenix-main-wrapper, .phoenix-nav-link {
                transition: none !important;
            }
        }
    </style>
</head>
<body>

    <!-- Mobile Drawer Overlay -->
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <!-- PHOENIX VERTICAL SIDEBAR -->
    <aside class="phoenix-sidebar" id="phoenixSidebar" aria-label="Phoenix Main Navigation">
        <a href="{{ Auth::user()?->isStaff() ? route('barang.index') : route('dashboard') }}" class="sidebar-brand">
            <div class="brand-logo-icon">
                <i class="fas fa-boxes-stacked"></i>
            </div>
            <div>
                <div class="brand-title">SBL Assets</div>
                <div class="brand-subtitle">IT Inventory v1.24</div>
            </div>
        </a>

        <div class="sidebar-nav-container">
            @if(Auth::user()?->isStaff())
                <div class="nav-label">{{ __('Menu Utama') }}</div>
                <a href="{{ route('barang.index') }}" class="phoenix-nav-link {{ request()->routeIs('barang.index') || request()->routeIs('barang.show') ? 'active' : '' }}">
                    <i class="fas fa-laptop-code"></i>
                    <span>{{ __('Master Assets') }}</span>
                </a>
                <a href="{{ route('ticket.index') }}" class="phoenix-nav-link {{ request()->routeIs('ticket.*') ? 'active' : '' }}">
                    <i class="fas fa-headset"></i>
                    <span>{{ __('IT Helpdesk') }}</span>
                </a>
            @else
                <div class="nav-label">{{ __('Main Dashboard') }}</div>
                <a href="{{ route('dashboard') }}" class="phoenix-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-chart-pie"></i>
                    <span>{{ __('Dashboard') }}</span>
                </a>

                <div class="nav-label">{{ __('Inventory Management') }}</div>
                <a href="{{ route('barang.index') }}" class="phoenix-nav-link {{ request()->routeIs('barang.index') || request()->routeIs('barang.show') ? 'active' : '' }}">
                    <i class="fas fa-laptop-code"></i>
                    <span>{{ __('Master Assets') }}</span>
                </a>
                <a href="{{ route('barang.barcode.batch') }}" class="phoenix-nav-link {{ request()->routeIs('barang.barcode*') || request()->routeIs('barang.qrcode*') ? 'active' : '' }}">
                    <i class="fas fa-qrcode"></i>
                    <span>{{ __('Cetak QR Code') }}</span>
                </a>
                <a href="{{ route('category.index') }}" class="phoenix-nav-link {{ request()->routeIs('category.*') ? 'active' : '' }}">
                    <i class="fas fa-tags"></i>
                    <span>{{ __('Kategori Assets') }}</span>
                </a>
                <a href="{{ route('departemen.index') }}" class="phoenix-nav-link {{ request()->routeIs('departemen.*') ? 'active' : '' }}">
                    <i class="fas fa-building"></i>
                    <span>{{ __('Master Departemen') }}</span>
                </a>
                <a href="{{ route('lokasi.index') }}" class="phoenix-nav-link {{ request()->routeIs('lokasi.*') ? 'active' : '' }}">
                    <i class="fas fa-location-dot"></i>
                    <span>{{ __('Master Lokasi') }}</span>
                </a>
                <a href="{{ route('vendor.index') }}" class="phoenix-nav-link {{ request()->routeIs('vendor.*') ? 'active' : '' }}">
                    <i class="fas fa-truck-field"></i>
                    <span>{{ __('Master Vendor') }}</span>
                </a>

                <div class="nav-label">{{ __('Operations & Handover') }}</div>
                <a href="{{ route('handover.history') }}" class="phoenix-nav-link {{ request()->routeIs('handover.*') ? 'active' : '' }}">
                    <i class="fas fa-file-invoice"></i>
                    <span>{{ __('Handover & Receipt') }}</span>
                </a>
                <a href="{{ route('handover.create') }}" class="phoenix-nav-link {{ request()->routeIs('handover.create') ? 'active' : '' }}">
                    <i class="fas fa-file-circle-plus"></i>
                    <span>{{ __('Terbitkan Tanda Terima') }}</span>
                </a>
                <a href="{{ route('maintenance.index') }}" class="phoenix-nav-link {{ request()->routeIs('maintenance.*') ? 'active' : '' }}">
                    <i class="fas fa-screwdriver-wrench"></i>
                    <span>{{ __('Maintenance Aset') }}</span>
                </a>
                <a href="{{ route('ticket.index') }}" class="phoenix-nav-link {{ request()->routeIs('ticket.*') ? 'active' : '' }}">
                    <i class="fas fa-headset"></i>
                    <span>{{ __('IT Helpdesk') }}</span>
                </a>

                <div class="nav-label">{{ __('System Admin') }}</div>
                <a href="{{ route('users.index') }}" class="phoenix-nav-link {{ request()->routeIs('users.*') || request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="fas fa-user-gear"></i>
                    <span>{{ __('User Management') }}</span>
                </a>
                <a href="{{ route('activity_logs.index') }}" class="phoenix-nav-link {{ request()->routeIs('activity_logs.*') ? 'active' : '' }}">
                    <i class="fas fa-clock-rotate-left"></i>
                    <span>{{ __('Audit Activity Log') }}</span>
                </a>
            @endif
        </div>
    </aside>

    <!-- PHOENIX TOPBAR / HEADER -->
    <header class="phoenix-header" id="phoenixHeader">
        <div class="d-flex align-items-center gap-3">
            <button type="button" class="btn-nav-action" id="btnToggleSidebar" aria-label="Toggle Sidebar Navigation">
                <i class="fas fa-bars"></i>
            </button>
            <div class="header-search-bar">
                <i class="fas fa-search search-icon"></i>
                <input type="text" id="globalQuickSearch" class="header-search-input" placeholder="{{ __('Quick search assets, SN, user...') }}" autocomplete="off">
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <!-- Notification & Quick Link -->
            <a href="{{ route('barang.index') }}" class="btn-nav-action d-none d-sm-flex" title="{{ __('Master Assets') }}">
                <i class="fas fa-layer-group"></i>
            </a>
            @if(!Auth::user()?->isStaff())
                <a href="{{ route('handover.create') }}" class="btn-nav-action d-none d-sm-flex" title="{{ __('Terbitkan Tanda Terima') }}">
                    <i class="fas fa-plus"></i>
                </a>
            @endif

            <!-- Language Switcher Dropdown -->
            <div class="dropdown">
                <button type="button" class="btn btn-nav-action d-inline-flex align-items-center justify-content-center px-2" style="width: auto; min-width: 52px; height: 38px; border-radius: 20px; font-weight: 700; font-size: 0.775rem;" data-bs-toggle="dropdown" aria-expanded="false" title="{{ __('Bahasa') }}">
                    @if(app()->getLocale() == 'en')
                        <svg class="rounded flex-shrink-0 me-1" width="16" height="11" viewBox="0 0 18 13" style="border: 1px solid rgba(0,0,0,0.12);">
                            <rect width="18" height="13" fill="#B22234"/>
                            <rect y="1" width="18" height="1" fill="#FFFFFF"/>
                            <rect y="3" width="18" height="1" fill="#FFFFFF"/>
                            <rect y="5" width="18" height="1" fill="#FFFFFF"/>
                            <rect y="7" width="18" height="1" fill="#FFFFFF"/>
                            <rect y="9" width="18" height="1" fill="#FFFFFF"/>
                            <rect y="11" width="18" height="1" fill="#FFFFFF"/>
                            <rect width="7.5" height="7" fill="#3C3B6E"/>
                        </svg>
                        <span class="d-none d-sm-inline">EN</span>
                    @else
                        <svg class="rounded flex-shrink-0 me-1" width="16" height="11" viewBox="0 0 18 13" style="border: 1px solid rgba(0,0,0,0.12);">
                            <rect width="18" height="6.5" fill="#E70011"/>
                            <rect y="6.5" width="18" height="6.5" fill="#FFFFFF"/>
                        </svg>
                        <span class="d-none d-sm-inline">ID</span>
                    @endif
                    <i class="fas fa-chevron-down ms-1 text-muted" style="font-size: 0.6rem;"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end phoenix-dropdown-menu">
                    <li>
                        <a class="dropdown-item-phoenix {{ app()->getLocale() == 'id' ? 'active-item' : '' }}" href="{{ route('lang.switch', 'id') }}">
                            <svg class="rounded flex-shrink-0" width="18" height="13" viewBox="0 0 18 13" style="border: 1px solid rgba(0,0,0,0.12);">
                                <rect width="18" height="6.5" fill="#E70011"/>
                                <rect y="6.5" width="18" height="6.5" fill="#FFFFFF"/>
                            </svg>
                            <span class="flex-grow-1">Bahasa Indonesia</span>
                            @if(app()->getLocale() == 'id')
                                <i class="fas fa-check ms-auto text-primary" style="font-size: 0.75rem;"></i>
                            @endif
                        </a>
                    </li>
                    <li class="mt-1">
                        <a class="dropdown-item-phoenix {{ app()->getLocale() == 'en' ? 'active-item' : '' }}" href="{{ route('lang.switch', 'en') }}">
                            <svg class="rounded flex-shrink-0" width="18" height="13" viewBox="0 0 18 13" style="border: 1px solid rgba(0,0,0,0.12);">
                                <rect width="18" height="13" fill="#B22234"/>
                                <rect y="1" width="18" height="1" fill="#FFFFFF"/>
                                <rect y="3" width="18" height="1" fill="#FFFFFF"/>
                                <rect y="5" width="18" height="1" fill="#FFFFFF"/>
                                <rect y="7" width="18" height="1" fill="#FFFFFF"/>
                                <rect y="9" width="18" height="1" fill="#FFFFFF"/>
                                <rect y="11" width="18" height="1" fill="#FFFFFF"/>
                                <rect width="7.5" height="7" fill="#3C3B6E"/>
                            </svg>
                            <span class="flex-grow-1">English</span>
                            @if(app()->getLocale() == 'en')
                                <i class="fas fa-check ms-auto text-primary" style="font-size: 0.75rem;"></i>
                            @endif
                        </a>
                    </li>
                </ul>
            </div>

            <div class="vr mx-1 text-muted opacity-25" style="height: 24px;"></div>

            <!-- User Menu -->
            <div class="dropdown">
                <div class="user-dropdown-btn" data-bs-toggle="dropdown" aria-expanded="false" role="button">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'User') }}&background=3874ff&color=fff&bold=true&size=128" 
                         alt="{{ Auth::user()->name ?? 'User' }}" 
                         class="user-avatar">
                    <div class="d-none d-md-block text-start">
                        <div class="fw-bold text-dark" style="font-size: 0.8125rem; line-height: 1.2;">{{ Auth::user()->name ?? 'User' }}</div>
                        <div class="text-muted" style="font-size: 0.7rem;">{{ Auth::user()?->getRoleDisplayName() ?? __('User') }}</div>
                    </div>
                    <i class="fas fa-chevron-down ms-1 text-muted" style="font-size: 0.65rem;"></i>
                </div>
                <ul class="dropdown-menu dropdown-menu-end phoenix-user-dropdown-menu">
                    <li class="p-2 mb-1 border-bottom bg-light rounded-3">
                        <div class="d-flex align-items-center gap-2">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'User') }}&background=3874ff&color=fff&bold=true&size=128" 
                                 alt="{{ Auth::user()->name ?? 'User' }}" 
                                 class="rounded-circle flex-shrink-0" width="36" height="36" style="object-fit: cover; aspect-ratio: 1 / 1;">
                            <div class="overflow-hidden">
                                <div class="fw-bold text-dark text-truncate" style="font-size: 0.825rem;">{{ Auth::user()->name ?? 'User' }}</div>
                                <div class="text-muted text-truncate" style="font-size: 0.725rem;">{{ Auth::user()->email ?? '' }}</div>
                            </div>
                        </div>
                    </li>
                    <li>
                        <a class="dropdown-item-phoenix" href="{{ route('profile.edit') }}">
                            <i class="fas fa-user-gear text-primary" style="width: 16px;"></i>
                            <span>{{ __('Account Settings') }}</span>
                        </a>
                    </li>
                    @if(!Auth::user()?->isStaff())
                        <li>
                            <a class="dropdown-item-phoenix" href="{{ route('users.index') }}">
                                <i class="fas fa-users text-secondary" style="width: 16px;"></i>
                                <span>{{ __('User Management') }}</span>
                            </a>
                        </li>
                    @endif
                    <li><hr class="dropdown-divider my-1 border-light"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}" class="m-0">
                            @csrf
                            <button type="submit" class="dropdown-item-phoenix text-danger">
                                <i class="fas fa-right-from-bracket" style="width: 16px;"></i>
                                <span>{{ __('Log Out') }}</span>
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <!-- PHOENIX MAIN CONTENT -->
    <main class="phoenix-main-wrapper">
        <div class="phoenix-content">
            <!-- Breadcrumb / Header bar -->
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <div>
                    <h1 class="page-header-title">@yield('title', 'Data')</h1>
                    <div class="phoenix-breadcrumb">
                        <a href="{{ Auth::user()?->isStaff() ? route('barang.index') : route('dashboard') }}"><i class="fas fa-home me-1"></i> {{ Auth::user()?->isStaff() ? __('Aset') : __('Dashboard') }}</a>
                        <span class="mx-1 text-muted">/</span>
                        <span class="text-dark">@yield('title', 'Overview')</span>
                    </div>
                </div>
                <div>
                    @yield('header_actions')
                </div>
            </div>

            @yield('content')
        </div>
    </main>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 Pop Up Notifications -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Phoenix Toast Configuration (Top-Right Toast, Auto-Dismiss)
        const PhoenixToast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3500,
            timerProgressBar: true,
            showCloseButton: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            },
            customClass: {
                popup: 'phoenix-swal-toast'
            }
        });

        // Top-Right Auto-Dismiss Notification Helper
        window.showToast = function(type, message, title = '', customTimer = null) {
            let defaultTitle = '';
            if (type === 'success') defaultTitle = '{{ __("Berhasil!") }}';
            else if (type === 'error') defaultTitle = '{{ __("Gagal!") }}';
            else if (type === 'warning') defaultTitle = '{{ __("Peringatan!") }}';
            else defaultTitle = '{{ __("Informasi") }}';

            let timerMs = customTimer || (type === 'error' ? 5000 : 3500);

            return PhoenixToast.fire({
                icon: type,
                title: title || defaultTitle,
                html: message,
                timer: timerMs
            });
        };

        // Convenience Aliases (pointing to Top-Right Auto-Dismiss Toast)
        window.showSuccessModal = function(message, title = '{{ __("Berhasil!") }}') {
            return window.showToast('success', message, title);
        };

        window.showErrorModal = function(message, title = '{{ __("Gagal!") }}') {
            return window.showToast('error', message, title, 5000);
        };

        window.showWarningModal = function(message, title = '{{ __("Peringatan!") }}') {
            return window.showToast('warning', message, title);
        };

        window.showInfoModal = function(message, title = '{{ __("Informasi") }}') {
            return window.showToast('info', message, title);
        };

        window.showPopup = function(type, title, text = '') {
            return window.showToast(type, text, title);
        };

        document.addEventListener('DOMContentLoaded', function() {
            const btnToggle = document.getElementById('btnToggleSidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            const body = document.body;

            function toggleSidebar() {
                if (window.innerWidth < 992) {
                    body.classList.toggle('sidebar-show');
                } else {
                    body.classList.toggle('sidebar-collapsed');
                }
            }

            if (btnToggle) btnToggle.addEventListener('click', toggleSidebar);
            if (backdrop) backdrop.addEventListener('click', toggleSidebar);

            // Global Quick Search redirect to Barang page if user presses enter
            const quickSearch = document.getElementById('globalQuickSearch');
            if (quickSearch) {
                quickSearch.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter' && this.value.trim() !== '') {
                        window.location.href = "{{ route('barang.index') }}?q=" + encodeURIComponent(this.value.trim());
                    }
                });
            }

            // Universal delete / confirmation pop-up modal for all forms with onsubmit="return confirm(...)"
            document.addEventListener('click', function(e) {
                const submitBtn = e.target.closest('form button[type="submit"], form input[type="submit"]');
                if (submitBtn) {
                    const form = submitBtn.closest('form');
                    const onsubmitStr = form ? form.getAttribute('onsubmit') : null;
                    if (onsubmitStr && onsubmitStr.includes('confirm(')) {
                        e.preventDefault();
                        e.stopImmediatePropagation();
                        
                        let confirmText = '{{ __("Apakah Anda yakin ingin melanjutkan tindakan ini? Tindakan ini tidak dapat dibatalkan.") }}';
                        const match = onsubmitStr.match(/confirm\(['"](.+?)['"]\)/);
                        if (match && match[1]) {
                            confirmText = match[1];
                        }

                        Swal.fire({
                            title: '{{ __("Konfirmasi Tindakan") }}',
                            html: `<div class="text-secondary" style="font-size:0.925rem;">${confirmText}</div>`,
                            icon: 'warning',
                            iconColor: '#e5780b',
                            showCancelButton: true,
                            confirmButtonText: '<i class="fas fa-trash-can me-1.5"></i> {{ __("Ya, Lanjutkan") }}',
                            cancelButtonText: '{{ __("Batal") }}',
                            customClass: {
                                popup: 'phoenix-swal-modal',
                                title: 'phoenix-swal-title',
                                htmlContainer: 'phoenix-swal-html',
                                confirmButton: 'btn btn-danger px-4 py-2 rounded-3 fw-bold shadow-sm',
                                cancelButton: 'btn btn-phoenix-secondary px-4 py-2 rounded-3 fw-semibold ms-2',
                                actions: 'phoenix-swal-actions gap-2',
                                closeButton: 'phoenix-swal-close-btn'
                            },
                            buttonsStyling: false,
                            focusCancel: true
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.removeAttribute('onsubmit');
                                form.submit();
                            }
                        });
                    }
                }
            }, true);
        });
    </script>

    <!-- Auto Top-Right Pop-up Notifications from Session Flashes & Errors -->
    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                window.showToast('success', {!! json_encode(session('success')) !!}, '{{ __("Berhasil!") }}');
            });
        </script>
    @endif

    @if(session('status'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                let statusMsg = {!! json_encode(session('status')) !!};
                if (statusMsg === 'profile-updated') {
                    statusMsg = '{{ __("Informasi profil Anda telah berhasil diperbarui.") }}';
                } else if (statusMsg === 'password-updated') {
                    statusMsg = '{{ __("Kata sandi akun Anda telah berhasil diubah.") }}';
                }
                window.showToast('success', statusMsg, '{{ __("Berhasil!") }}');
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                window.showToast('error', {!! json_encode(session('error')) !!}, '{{ __("Gagal!") }}');
            });
        </script>
    @endif

    @if($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const errorsList = {!! json_encode($errors->all()) !!};
                let errorHtml = '<ul class="mb-0 text-start ps-3" style="font-size:0.8rem; line-height:1.4;">' + 
                    errorsList.map(err => `<li>${err}</li>`).join('') + 
                    '</ul>';
                window.showToast('error', errorHtml, '{{ __("Terjadi Kesalahan Input") }}', 6000);
            });
        </script>
    @endif

    @stack('scripts')
</body>
</html>