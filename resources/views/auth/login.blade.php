<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('Sign In') }} - SBL IT Assets</title>

    <!-- Google Fonts: Nunito Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:ital,opsz,wght@0,6..12,300..900;1,6..12,300..900&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5.3.3 & FontAwesome 6 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

    <style>
        :root {
            --phoenix-font-sans: 'Nunito Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            --phoenix-primary: #3874ff;
            --phoenix-primary-rgb: 56, 116, 255;
            --phoenix-primary-hover: #2563eb;
            --phoenix-primary-subtle: #edf2ff;
            --phoenix-primary-border: #c7d8fe;
            --phoenix-body-bg: #f8fafc;
            --phoenix-border-color: #e2e8f0;
            --phoenix-text-emphasis: #0f172a;
            --phoenix-text-body: #334155;
            --phoenix-text-muted: #64748b;
        }

        * {
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            font-family: var(--phoenix-font-sans);
            background-color: #ffffff;
            color: var(--phoenix-text-body);
            -webkit-font-smoothing: antialiased;
        }

        .login-layout-container {
            min-height: 100vh;
            display: flex;
            width: 100%;
        }

        /* -------------------------------------------------------------
           LEFT COLUMN: FORM LOGIN
           ------------------------------------------------------------- */
        .login-form-pane {
            width: 100%;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 2.5rem 2rem;
            background-color: #ffffff;
            z-index: 10;
        }

        @media (min-width: 992px) {
            .login-form-pane {
                width: 460px;
                min-width: 460px;
                max-width: 460px;
                padding: 3rem 3rem;
                border-right: 1px solid var(--phoenix-border-color);
                box-shadow: 4px 0 24px rgba(15, 23, 42, 0.03);
            }
        }

        @media (min-width: 1400px) {
            .login-form-pane {
                width: 500px;
                min-width: 500px;
                max-width: 500px;
                padding: 3.5rem 3.5rem;
            }
        }

        .brand-logo-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: linear-gradient(135deg, #3874ff 0%, #1d4ed8 100%);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 1.25rem;
            box-shadow: 0 8px 16px rgba(56, 116, 255, 0.28);
            flex-shrink: 0;
        }

        .brand-title {
            font-weight: 800;
            font-size: 1.2rem;
            color: var(--phoenix-text-emphasis);
            letter-spacing: -0.02em;
            line-height: 1.2;
        }

        .brand-subtitle {
            font-size: 0.75rem;
            color: var(--phoenix-text-muted);
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .form-heading {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--phoenix-text-emphasis);
            letter-spacing: -0.02em;
            margin-bottom: 0.35rem;
        }

        .form-subheading {
            font-size: 0.875rem;
            color: var(--phoenix-text-muted);
            margin-bottom: 1.75rem;
            line-height: 1.45;
        }

        .form-control-phoenix {
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            padding: 0.68rem 1rem 0.68rem 2.65rem;
            font-size: 0.875rem;
            color: var(--phoenix-text-emphasis);
            background-color: #f8fafc;
            transition: all 0.2s ease;
        }

        .form-control-phoenix:focus {
            background-color: #ffffff;
            border-color: var(--phoenix-primary);
            box-shadow: 0 0 0 3.5px rgba(56, 116, 255, 0.15);
            outline: none;
        }

        .form-control-phoenix.is-invalid {
            border-color: #ef4444;
            background-color: #fff8f8;
        }

        .form-control-phoenix.is-invalid:focus {
            box-shadow: 0 0 0 3.5px rgba(239, 68, 68, 0.15);
        }

        .input-icon-wrapper {
            position: relative;
        }

        .input-icon-wrapper .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 0.95rem;
            pointer-events: none;
            transition: color 0.2s ease;
        }

        .input-icon-wrapper:focus-within .input-icon {
            color: var(--phoenix-primary);
        }

        .btn-toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            color: #94a3b8;
            padding: 0.35rem 0.5rem;
            cursor: pointer;
            font-size: 0.9rem;
            border-radius: 6px;
            transition: color 0.15s ease;
        }

        .btn-toggle-password:hover {
            color: var(--phoenix-text-emphasis);
        }

        .btn-phoenix-submit {
            background: linear-gradient(135deg, #3874ff 0%, #2563eb 100%);
            color: #ffffff;
            border: 1px solid var(--phoenix-primary);
            font-weight: 700;
            border-radius: 10px;
            padding: 0.75rem 1.25rem;
            font-size: 0.925rem;
            transition: all 0.2s ease;
            box-shadow: 0 4px 14px rgba(56, 116, 255, 0.3);
            width: 100%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-phoenix-submit:hover {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #ffffff;
            box-shadow: 0 6px 20px rgba(56, 116, 255, 0.4);
            transform: translateY(-1px);
        }

        .btn-phoenix-submit:active {
            transform: translateY(0);
        }

        .form-check-input:checked {
            background-color: var(--phoenix-primary);
            border-color: var(--phoenix-primary);
        }

        .lang-switch-btn {
            font-size: 0.775rem;
            font-weight: 700;
            color: var(--phoenix-text-muted);
            text-decoration: none;
            padding: 0.3rem 0.65rem;
            border-radius: 8px;
            border: 1px solid var(--phoenix-border-color);
            background-color: #f8fafc;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            transition: all 0.15s ease;
        }

        .lang-switch-btn:hover {
            color: var(--phoenix-primary);
            background-color: var(--phoenix-primary-subtle);
            border-color: var(--phoenix-primary-border);
        }

        /* -------------------------------------------------------------
           RIGHT COLUMN: ENTERPRISE SHOWCASE
           ------------------------------------------------------------- */
        .login-hero-pane {
            flex: 1;
            display: none;
            position: relative;
            background-color: #0f172a;
            background-image: url('https://images.unsplash.com/photo-1558494949-ef010cbdcc31?q=80&w=2034&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            overflow: hidden;
        }

        @media (min-width: 992px) {
            .login-hero-pane {
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                padding: 4rem;
            }
        }

        @media (min-width: 1400px) {
            .login-hero-pane {
                padding: 5rem;
            }
        }

        .hero-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.94) 0%, rgba(30, 58, 138, 0.82) 50%, rgba(15, 23, 42, 0.95) 100%);
            backdrop-filter: blur(2px);
            z-index: 1;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            color: #ffffff;
            max-width: 640px;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background-color: rgba(56, 116, 255, 0.2);
            color: #93c5fd;
            border: 1px solid rgba(147, 197, 253, 0.3);
            padding: 0.4rem 0.9rem;
            border-radius: 30px;
            font-size: 0.75rem;
            font-weight: 800;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            margin-bottom: 1.5rem;
            backdrop-filter: blur(8px);
        }

        .hero-title {
            font-size: 2.35rem;
            font-weight: 900;
            line-height: 1.2;
            letter-spacing: -0.03em;
            margin-bottom: 1rem;
            color: #ffffff;
        }

        @media (min-width: 1400px) {
            .hero-title {
                font-size: 2.75rem;
            }
        }

        .hero-description {
            font-size: 1rem;
            color: #cbd5e1;
            line-height: 1.6;
            margin-bottom: 2.5rem;
        }

        .feature-card {
            background-color: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 14px;
            padding: 1.15rem 1.35rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            backdrop-filter: blur(10px);
            transition: all 0.2s ease;
        }

        .feature-card:hover {
            background-color: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.2);
            transform: translateX(4px);
        }

        .feature-icon-box {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: linear-gradient(135deg, rgba(56, 116, 255, 0.4) 0%, rgba(37, 99, 235, 0.2) 100%);
            border: 1px solid rgba(147, 197, 253, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #93c5fd;
            font-size: 1rem;
            flex-shrink: 0;
        }

        .feature-card-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 0.2rem;
        }

        .feature-card-desc {
            font-size: 0.8125rem;
            color: #94a3b8;
            line-height: 1.45;
            margin-bottom: 0;
        }

        .hero-footer {
            position: relative;
            z-index: 2;
            color: #94a3b8;
            font-size: 0.8rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
    </style>
</head>
<body>

    <div class="login-layout-container">

        <!-- ========================================================= -->
        <!-- LEFT COLUMN: AUTHENTICATION FORM                          -->
        <!-- ========================================================= -->
        <div class="login-form-pane">
            
            <!-- TOP BAR: BRAND & LOCALE -->
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="brand-logo-icon">
                        <i class="fas fa-boxes-stacked"></i>
                    </div>
                    <div>
                        <div class="brand-title">SBL Assets</div>
                        <div class="brand-subtitle">IT Inventory System</div>
                    </div>
                </div>

                <!-- Locale Selector -->
                <div>
                    @if(app()->getLocale() === 'en')
                        <a href="{{ route('lang.switch', 'id') }}" class="lang-switch-btn" title="Ganti ke Bahasa Indonesia">
                            <span class="fi fi-id">🇮🇩</span> <span>ID</span>
                        </a>
                    @else
                        <a href="{{ route('lang.switch', 'en') }}" class="lang-switch-btn" title="Switch to English">
                            <span class="fi fi-us">🇺🇸</span> <span>EN</span>
                        </a>
                    @endif
                </div>
            </div>

            <!-- FORM CORE -->
            <div class="my-auto py-3">
                <h1 class="form-heading">{{ __('Selamat Datang') }}</h1>
                <p class="form-subheading">{{ __('Masuk ke portal untuk mengelola aset, inventaris, dan serah terima IT.') }}</p>

                <!-- Status / Alert Message -->
                @if (session('status'))
                    <div class="alert alert-info d-flex align-items-center gap-2 py-2.5 px-3 mb-3 border-0 rounded-3" style="background-color: #e0f2fe; color: #0369a1; font-size: 0.825rem;">
                        <i class="fas fa-info-circle flex-shrink-0"></i>
                        <div>{{ session('status') }}</div>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger d-flex align-items-center gap-2 py-2.5 px-3 mb-3 border-0 rounded-3" style="background-color: #fee2e2; color: #991b1b; font-size: 0.825rem;">
                        <i class="fas fa-circle-exclamation flex-shrink-0"></i>
                        <div>
                            @foreach ($errors->all() as $error)
                                <span>{{ $error }}</span>@if(!$loop->last)<br>@endif
                            @endforeach
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" id="loginForm">
                    @csrf

                    <!-- Email Input -->
                    <div class="mb-3">
                        <label for="email" class="form-label small fw-bold text-slate-700 mb-1" style="color: #334155;">
                            {{ __('Alamat Email') }} <span class="text-danger">*</span>
                        </label>
                        <div class="input-icon-wrapper">
                            <i class="fas fa-envelope input-icon"></i>
                            <input type="email" 
                                   class="form-control form-control-phoenix @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   placeholder="nama@perusahaan.com" 
                                   required 
                                   autofocus 
                                   autocomplete="username">
                        </div>
                    </div>

                    <!-- Password Input -->
                    <div class="mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <label for="password" class="form-label small fw-bold mb-0" style="color: #334155;">
                                {{ __('Kata Sandi') }} <span class="text-danger">*</span>
                            </label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="small fw-semibold text-decoration-none" style="color: var(--phoenix-primary); font-size: 0.775rem;">
                                    {{ __('Lupa password?') }}
                                </a>
                            @endif
                        </div>
                        <div class="input-icon-wrapper">
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" 
                                   class="form-control form-control-phoenix @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password" 
                                   placeholder="••••••••" 
                                   required 
                                   autocomplete="current-password"
                                   style="padding-right: 2.75rem;">
                            <button type="button" class="btn-toggle-password" id="btnTogglePassword" aria-label="Toggle password visibility" tabindex="-1">
                                <i class="far fa-eye" id="togglePasswordIcon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="d-flex align-items-center justify-content-between mb-4 pt-1">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember_me" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label small" for="remember_me" style="color: #64748b; font-weight: 600; cursor: pointer;">
                                {{ __('Ingat sesi saya') }}
                            </label>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn-phoenix-submit" id="btnSubmit">
                        <i class="fas fa-right-to-bracket me-1" id="submitIcon"></i>
                        <span id="submitText">{{ __('Masuk ke Dashboard') }}</span>
                    </button>
                </form>

                <!-- Security Trust Note -->
                <div class="d-flex align-items-center justify-content-center gap-2 mt-4 pt-2 text-center" style="font-size: 0.75rem; color: #94a3b8;">
                    <i class="fas fa-shield-halved text-success"></i>
                    <span>{{ __('Sesi terenkripsi & diautentikasi aman') }}</span>
                </div>
            </div>

            <!-- FOOTER -->
            <div class="pt-3 border-top text-center" style="border-color: var(--phoenix-border-color) !important;">
                <span class="small text-muted" style="font-size: 0.75rem;">
                    &copy; {{ date('Y') }} SBL IT Inventory Management. All rights reserved.
                </span>
            </div>

        </div>

        <!-- ========================================================= -->
        <!-- RIGHT COLUMN: ENTERPRISE SHOWCASE HERO                    -->
        <!-- ========================================================= -->
        <div class="login-hero-pane">
            <div class="hero-overlay"></div>

            <!-- Top Tagline -->
            <div class="position-relative" style="z-index: 2;">
                <div class="hero-badge">
                    <i class="fas fa-microchip"></i>
                    <span>Enterprise IT Asset System</span>
                </div>
            </div>

            <!-- Middle Feature Showcase -->
            <div class="hero-content">
                <h2 class="hero-title">
                    Centralized IT Hardware Tracking & Digital Handover
                </h2>
                <p class="hero-description">
                    Solusi terpadu pencatatan aset inventaris IT kantor, mutasi perangkat karyawan, integrasi vendor, serta penerbitan Berita Acara Serah Terima (BAST) secara instan.
                </p>

                <!-- Glassmorphism Feature Cards -->
                <div class="feature-card">
                    <div class="feature-icon-box">
                        <i class="fas fa-laptop-code"></i>
                    </div>
                    <div>
                        <div class="feature-card-title">Real-time Asset Lifecycle</div>
                        <p class="feature-card-desc">Lacak spesifikasi lengkap, serial number, status ketersediaan, dan lokasi unit hardware secara transparan.</p>
                    </div>
                </div>

                <div class="feature-card">
                    <div class="feature-icon-box">
                        <i class="fas fa-file-signature"></i>
                    </div>
                    <div>
                        <div class="feature-card-title">Digital Handover & Surat Tanda Terima</div>
                        <p class="feature-card-desc">Terbitkan formulir serah terima multi-aset dan cetak tanda terima resmi dengan nomor surat otomatis.</p>
                    </div>
                </div>

                <div class="feature-card">
                    <div class="feature-icon-box">
                        <i class="fas fa-barcode"></i>
                    </div>
                    <div>
                        <div class="feature-card-title">Automated Asset Code Tagging</div>
                        <p class="feature-card-desc">Penomoran kode aset lokal terstandarisasi dengan kode prefix kategori untuk kemudahan inventarisasi.</p>
                    </div>
                </div>
            </div>

            <!-- Hero Footer -->
            <div class="hero-footer">
                <div class="d-flex align-items-center gap-2">
                    <span class="d-inline-block rounded-circle bg-success" style="width: 8px; height: 8px;"></span>
                    <span>System Online &bull; Version 1.24</span>
                </div>
                <div>
                    <span>Secure Internal Network</span>
                </div>
            </div>
        </div>

    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Password Show/Hide Toggle
            const togglePasswordBtn = document.getElementById('btnTogglePassword');
            const passwordInput = document.getElementById('password');
            const togglePasswordIcon = document.getElementById('togglePasswordIcon');

            if (togglePasswordBtn && passwordInput && togglePasswordIcon) {
                togglePasswordBtn.addEventListener('click', function() {
                    const isPassword = passwordInput.getAttribute('type') === 'password';
                    passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                    togglePasswordIcon.classList.toggle('fa-eye', !isPassword);
                    togglePasswordIcon.classList.toggle('fa-eye-slash', isPassword);
                });
            }

            // Form Submit Loading Feedback
            const loginForm = document.getElementById('loginForm');
            const btnSubmit = document.getElementById('btnSubmit');
            const submitIcon = document.getElementById('submitIcon');
            const submitText = document.getElementById('submitText');

            if (loginForm && btnSubmit) {
                loginForm.addEventListener('submit', function() {
                    btnSubmit.disabled = true;
                    if (submitIcon) {
                        submitIcon.className = 'fas fa-circle-notch fa-spin me-1';
                    }
                    if (submitText) {
                        submitText.textContent = "{{ __('Memproses...') }}";
                    }
                });
            }
        });
    </script>
</body>
</html>