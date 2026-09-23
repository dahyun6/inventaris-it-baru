<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('Lupa Kata Sandi') }} - Pandora IT Operations Hub</title>

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
            --phoenix-primary-hover: #2563eb;
            --phoenix-primary-subtle: #edf2ff;
            --phoenix-primary-border: #c7d8fe;
            --phoenix-border-color: #e2e8f0;
            --phoenix-text-emphasis: #0f172a;
            --phoenix-text-body: #334155;
            --phoenix-text-muted: #64748b;
        }

        body {
            font-family: var(--phoenix-font-sans);
            background-color: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            color: var(--phoenix-text-body);
        }

        .auth-card {
            background-color: #ffffff;
            border: 1px solid var(--phoenix-border-color);
            border-radius: 16px;
            box-shadow: 0 10px 30px -5px rgba(15, 23, 42, 0.08);
            width: 100%;
            max-width: 440px;
            padding: 2.5rem 2rem;
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
        }

        .btn-phoenix-submit {
            background: linear-gradient(135deg, #3874ff 0%, #2563eb 100%);
            color: #ffffff;
            border: 1px solid var(--phoenix-primary);
            font-weight: 700;
            border-radius: 10px;
            padding: 0.75rem 1.25rem;
            font-size: 0.925rem;
            width: 100%;
            box-shadow: 0 4px 14px rgba(56, 116, 255, 0.3);
            transition: all 0.2s ease;
        }

        .btn-phoenix-submit:hover {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #ffffff;
            box-shadow: 0 6px 20px rgba(56, 116, 255, 0.4);
        }
    </style>
</head>
<body>

    <div class="auth-card">
        <div class="text-center mb-4">
            <div class="brand-logo-icon mb-3">
                <i class="fas fa-boxes-stacked"></i>
            </div>
            <h4 class="fw-bold text-dark mb-1">{{ __('Lupa Kata Sandi?') }}</h4>
            <p class="text-muted small mb-0">
                {{ __('Masukkan alamat email yang terdaftar untuk menerima link reset kata sandi.') }}
            </p>
        </div>

        @if (session('status'))
            <div class="alert alert-success d-flex align-items-center gap-2 py-2.5 px-3 mb-3 border-0 rounded-3" style="background-color: #dcfce7; color: #15803d; font-size: 0.825rem;">
                <i class="fas fa-circle-check flex-shrink-0"></i>
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

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label small fw-bold" style="color: #334155;">{{ __('Alamat Email') }} *</label>
                <div class="input-icon-wrapper">
                    <i class="fas fa-envelope input-icon"></i>
                    <input type="email" class="form-control form-control-phoenix @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="nama@perusahaan.com" required autofocus>
                </div>
            </div>

            <button type="submit" class="btn-phoenix-submit mb-3">
                <i class="fas fa-paper-plane me-1"></i> {{ __('Kirim Link Reset') }}
            </button>

            <div class="text-center pt-2 border-top" style="border-color: var(--phoenix-border-color) !important;">
                <a href="{{ route('login') }}" class="small fw-bold text-decoration-none" style="color: var(--phoenix-primary);">
                    <i class="fas fa-arrow-left me-1"></i> {{ __('Kembali ke Halaman Login') }}
                </a>
            </div>
        </form>
    </div>

</body>
</html>
