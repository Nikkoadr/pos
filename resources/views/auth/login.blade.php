<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Angel Cell</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,600,700&display=swap" rel="stylesheet" />

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                }
            }
        }
    </script>
</head>
<body class="antialiased bg-gray-50 font-sans text-gray-900">

    <div class="min-h-screen flex flex-col items-center justify-center p-6">
        
        <div class="mb-8 flex flex-col items-center text-center">
            <div class="mb-4">
                <img src="{{ asset('assets/dist/img/logo.png') }}" alt="Logo" class="h-20 w-auto object-contain">
            </div>
        </div>

        <div class="w-full max-w-md bg-white rounded-3xl shadow-xl shadow-gray-100 border border-gray-100 p-8 md:p-10">
            <form action="{{ route('login') }}" method="POST">
                @csrf
                
                <div class="mb-6">
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Alamat Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                            </svg>
                        </div>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" 
                            class="w-full pl-11 pr-4 py-3 bg-gray-50 border @error('email') border-red-500 @else border-gray-200 @enderror rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all text-sm" 
                            placeholder="nama@email.com" required autofocus>
                    </div>
                    @error('email')
                        <p class="mt-2 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input type="password" name="password" id="password" 
                            class="w-full pl-11 pr-4 py-3 bg-gray-50 border @error('password') border-red-500 @else border-gray-200 @enderror rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all text-sm" 
                            placeholder="••••••••" required>
                    </div>
                    @error('password')
                        <p class="mt-2 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between mb-8">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-600">Ingat saya</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700">Lupa Password?</a>
                    @endif
                </div>

                <button type="submit" class="w-full bg-blue-600 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-blue-200 hover:bg-blue-700 hover:scale-[1.02] transition transform active:scale-95">
                    Masuk Sekarang
                </button>
            </form>

            @if (Route::has('register'))
                <p class="mt-8 text-center text-sm text-gray-500">
                    Belum punya akun? 
                    <a href="{{ route('register') }}" class="font-bold text-blue-600 hover:text-blue-700 underline underline-offset-4">Daftar Toko</a>
                </p>
            @endif
        </div>

        <a href="/" class="mt-8 flex items-center gap-2 text-sm text-gray-400 hover:text-blue-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Beranda
        </a>

    </div>

</body>
</html>