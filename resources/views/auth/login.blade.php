<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - SBL IT Assets Phoenix</title>
    <!-- Google Fonts: Nunito Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:ital,opsz,wght@0,6..12,300..800;1,6..12,300..800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body {
            font-family: 'Nunito Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        .bg-login-image {
            background-image: url('https://images.unsplash.com/photo-1451187580459-43490279c0fa?q=80&w=2072&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        .protect-image {
            user-select: none;
            pointer-events: none;
        }
    </style>
</head>
<body class="text-slate-900 bg-slate-50 antialiased">
    <div class="flex min-h-screen">

        <!-- LEFT COLUMN: FORM LOGIN -->
        <div class="flex flex-col justify-center w-full lg:w-1/2 px-8 sm:px-16 md:px-24 xl:px-32 bg-white border-r border-slate-200">
            <div class="w-full max-w-sm mx-auto">
                
                <!-- Phoenix Logo & Brand -->
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-10 h-10 bg-blue-600 text-white rounded-xl flex items-center justify-center font-bold shadow-md shadow-blue-500/20">
                        <i class="fas fa-boxes-stacked text-lg"></i>
                    </div>
                    <div>
                        <span class="text-xl font-extrabold text-slate-900 tracking-tight block leading-tight">SBL Assets</span>
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">IT Inventory Management</span>
                    </div>
                </div>

                <!-- Header Text -->
                <div class="mb-8">
                    <h2 class="text-2xl font-black text-slate-900 mb-1">Sign In</h2>
                    <p class="text-sm text-slate-500">Welcome back! Masuk untuk mengelola aset inventaris IT.</p>
                </div>

                <!-- Session Alert -->
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <!-- Form Inputs -->
                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    <!-- Email Input -->
                    <div>
                        <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Email Address <span class="text-red-500">*</span></label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 outline-none transition-all"
                            placeholder="admin@sbl.com">
                        <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-red-500 text-xs font-semibold" />
                    </div>

                    <!-- Password Input -->
                    <div>
                        <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Password <span class="text-red-500">*</span></label>
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 outline-none transition-all"
                            placeholder="••••••••">
                        <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-red-500 text-xs font-semibold" />
                    </div>

                    <div class="flex items-center justify-between pt-1">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remember" class="w-4 h-4 rounded text-blue-600 border-slate-300 focus:ring-blue-500">
                            <span class="text-xs text-slate-600 font-medium">Ingat saya</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full mt-2 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 rounded-lg focus:ring-4 focus:ring-blue-600/20 transition-all shadow-md shadow-blue-600/20 text-sm">
                        Sign In to Dashboard
                    </button>
                </form>

                <div class="mt-8 text-center text-xs text-slate-400">
                    SBL IT Asset Management System &bull; Phoenix Vertical Edition
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: BACKGROUND -->
        <div class="hidden lg:block lg:w-1/2 bg-login-image relative">
            <div class="absolute inset-0 bg-gradient-to-tr from-slate-950/80 to-blue-900/40 backdrop-blur-[1px] protect-image flex flex-col justify-end p-12 text-white">
                <div class="max-w-md">
                    <span class="inline-block px-3 py-1 bg-blue-600/80 rounded-full text-xs font-bold uppercase tracking-wider mb-3">Enterprise IT Asset</span>
                    <h3 class="text-2xl font-black mb-2">Centralized IT Asset Tracking & Handover Management</h3>
                    <p class="text-sm text-slate-300">Pantau pergerakan hardware, status peminjaman perangkat kerja, dan terbitkan berita acara resmi serah terima secara cepat dan akurat.</p>
                </div>
            </div>
        </div>

    </div>
</body>
</html>