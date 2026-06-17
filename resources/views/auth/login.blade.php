<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log in - SBL IT Inventory</title>
    <!-- Memanggil Tailwind dari Vite bawaan Laravel Breeze -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        /* Pengaturan Gambar Kanan (Anti-Download) */
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
<body class="font-sans text-gray-900 bg-white antialiased">
    <div class="flex min-h-screen">

        <!-- ==========================================================
             BAGIAN KIRI: FORM LOGIN
             ========================================================== -->
        <div class="flex flex-col justify-center w-full lg:w-1/2 px-8 sm:px-16 md:px-24 xl:px-32">
            <div class="w-full max-w-sm mx-auto">
                
                <!-- Logo & Judul Perusahaan (Icon Box/Inventory) -->
                <div class="flex items-center gap-2 mb-10">
                    <div class="w-8 h-8 bg-blue-600 text-white rounded flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <span class="text-xl font-bold text-gray-900">SBL IT Inventory</span>
                </div>

                <!-- Header Teks -->
                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">Log in</h2>
                    <p class="text-gray-500">Welcome back! Please enter your details.</p>
                </div>

                <!-- Alert Error bawaan Breeze -->
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <!-- Form Inputs -->
                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <!-- Email Input -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-blue-600 outline-none transition-colors shadow-sm"
                            placeholder="admin@scg.com">
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500 text-sm" />
                    </div>

                    <!-- Password Input -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password <span class="text-red-500">*</span></label>
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-blue-600 outline-none transition-colors shadow-sm"
                            placeholder="••••••••">
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500 text-sm" />
                    </div>

                    <!-- Remember Me (Forgot Password Dihapus) -->

                    <!-- Submit Button -->
                    <button type="submit" class="w-full mt-2 bg-blue-600 text-white font-semibold py-2.5 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-200 transition-all shadow">
                        Sign in
                    </button>
                </form>
            </div>
        </div>

        <!-- ==========================================================
             BAGIAN KANAN: GAMBAR BACKGROUND
             ========================================================== -->
        <div class="hidden lg:block lg:w-1/2 bg-login-image relative">
            <div class="absolute inset-0 bg-blue-900/10 protect-image"></div>
        </div>

    </div>
</body>
</html>