<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SBL IT Inventory</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --sidebar-bg: #343a40; /* Warna gelap khas AdminLTE */
            --sidebar-hover: #495057;
            --sidebar-color: #c2c7d0;
            --body-bg: #f4f6f9; /* Abu-abu terang untuk konten */
            --primary-blue: #0d6efd;
        }
        body { background-color: var(--body-bg); font-family: 'Inter', sans-serif; font-size: 14.5px; overflow-x: hidden; }
        
        /* SIDEBAR ADMINLTE */
        .sidebar { background-color: var(--sidebar-bg); min-height: 100vh; position: fixed; top: 0; left: 0; width: 250px; z-index: 1040; transition: margin-left 0.3s; }
        .brand-link { display: flex; align-items: center; padding: 15px 20px; color: white; text-decoration: none; font-size: 1.25rem; font-weight: 300; border-bottom: 1px solid #4b545c; }
        .brand-link img { width: 30px; height: 30px; border-radius: 50%; margin-right: 10px; background: white; padding: 5px; }
        .brand-link span { font-weight: 600; letter-spacing: 0.5px; }
        
        .nav-sidebar { padding: 10px; list-style: none; margin: 0; }
        .nav-item { margin-bottom: 5px; }
        .nav-link { color: var(--sidebar-color); border-radius: 5px; padding: 10px 15px; display: flex; align-items: center; text-decoration: none; transition: all 0.2s; }
        .nav-link i { width: 25px; text-align: center; font-size: 1.1rem; margin-right: 8px; }
        .nav-link:hover { background-color: rgba(255,255,255,0.1); color: white; }
        .nav-link.active { background-color: var(--primary-blue); color: white; box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24); }

        /* HEADER / TOPBAR */
        .main-header { margin-left: 250px; background: white; border-bottom: 1px solid #dee2e6; padding: 10px 20px; display: flex; justify-content: space-between; align-items: center; transition: margin-left 0.3s; height: 57px; }
        .header-icon { color: #6c757d; font-size: 1.2rem; cursor: pointer; padding: 5px 10px; transition: color 0.2s; }
        .header-icon:hover { color: #343a40; }
        .user-panel { display: flex; align-items: center; gap: 10px; cursor: pointer; }
        .user-panel img { width: 30px; height: 30px; border-radius: 50%; background: #e9ecef; }
        .user-panel span { color: #343a40; font-weight: 500; font-size: 14px; }

        /* CONTENT AREA */
        .content-wrapper { margin-left: 250px; padding: 20px; transition: margin-left 0.3s; }
        .content-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .content-header h1 { margin: 0; font-size: 1.8rem; font-weight: 400; color: #343a40; }
        .breadcrumb { margin: 0; background: transparent; padding: 0; font-size: 14px; }
        .breadcrumb-item a { color: var(--primary-blue); text-decoration: none; }
        .breadcrumb-item.active { color: #6c757d; }

        /* Tombol umum rata-rata AdminLTE lebih bersudut (border-radius kecil) */
        .btn { border-radius: 4px; font-weight: 500; }

        /* SETTINGAN UNTUK MOBILE */
        @media (max-width: 768px) {
            .sidebar { margin-left: -250px; }
            .main-header, .content-wrapper { margin-left: 0; }
            body.sidebar-toggled .sidebar { margin-left: 0; }
        }

        /* SETTINGAN UNTUK DESKTOP */
        @media (min-width: 769px) {
            body.sidebar-toggled .sidebar { margin-left: -250px; }
            body.sidebar-toggled .main-header, 
            body.sidebar-toggled .content-wrapper { margin-left: 0; }
        }
        /* OVERLAY UNTUK MOBILE */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1030; /* Harus di bawah z-index sidebar (1040) tapi di atas konten lain */
        }

        /* Tampilkan overlay saat class sidebar-toggled aktif (hanya di mobile) */
        @media (max-width: 768px) {
            body.sidebar-toggled .sidebar-overlay {
                display: block;
            }
            body.sidebar-toggled {
                overflow: hidden; /* Mencegah background bisa di-scroll saat sidebar terbuka */
            }
        }
    </style>
</head>
<body>
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<aside class="sidebar" id="sidebar">
    <a href="#" class="brand-link">
        <i class="fas fa-layer-group text-primary ms-1 me-3 fs-4"></i>
        <span>SBL IT Assets</span>
    </a>
    <ul class="nav-sidebar mt-3">
        <li class="nav-item">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
        </li>
        
        <li class="nav-item mt-2 mb-2"><div style="border-top: 1px solid #4f5962; margin: 0 10px;"></div></li>
        
        <li class="nav-item" style="padding-left: 10px; font-size: 11px; text-transform: uppercase; color: #869099; font-weight: 700; margin-bottom: 5px;">Master Menu</li>
        
        <li class="nav-item">
            <a href="{{ route('barang.index') }}" class="nav-link {{ request()->routeIs('barang.index') ? 'active' : '' }}">
                <i class="fas fa-table"></i> Master Assets
            </a>
        </li>
        
        <li class="nav-item">
            <a href="{{ route('category.index') }}" class="nav-link {{ request()->routeIs('category.index') ? 'active' : '' }}">
                <i class="fas fa-tags"></i> Kategori Master
            </a>
        </li>
        
        <li class="nav-item">
            <a href="{{ route('handover.history') }}" class="nav-link {{ request()->routeIs('handover.history') ? 'active' : '' }}">
            <i class="fas fa-history"></i> Handover History
            </a>
        </li>

        <li class="nav-item">
    <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
        <i class="fas fa-users"></i> Master User
    </a>
</li>
    </ul>
</aside>

<nav class="main-header">
    <div class="d-flex align-items-center gap-3">
    <i class="fas fa-bars header-icon" id="btnToggleSidebar"></i>
    </div>
    
    <div class="d-flex align-items-center gap-3">
        <i class="fas fa-search header-icon"></i>
        <i class="far fa-comments header-icon"></i>
        <i class="far fa-bell header-icon"></i>
        <div class="border-start ms-2 me-2" style="height: 25px;"></div>
        
        <div class="user-panel dropdown" data-bs-toggle="dropdown">
            <span class="d-none d-md-inline">{{ Auth::user()->name }}</span>
            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=0D8ABC&color=fff" alt="User Image">
        </div>
        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2">
            <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item text-danger"><i class="fas fa-sign-out-alt me-2"></i> Sign out</button>
                </form>
            </li>
        </ul>
    </div>
</nav>

<div class="content-wrapper">
    <div class="content-header">
        <h1>@yield('title', 'Data Tables')</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item"><a href="#">Tables</a></li>
                <li class="breadcrumb-item active" aria-current="page">@yield('title', 'Data')</li>
            </ol>
        </nav>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <ul class="mb-0 mt-1">@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @yield('content')
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Ambil elemen yang dibutuhkan
    const btnToggle = document.getElementById('btnToggleSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const body = document.body;

    // Fungsi untuk mengubah status sidebar (buka/tutup)
    function toggleSidebar() {
        body.classList.toggle('sidebar-toggled');
    }

    // Event saat tombol hamburger di klik
    btnToggle.addEventListener('click', toggleSidebar);

    // Event saat area overlay (luar sidebar) di klik (khusus mobile)
    overlay.addEventListener('click', toggleSidebar);
</script>
@stack('scripts')
</body>
</html>