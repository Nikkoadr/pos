<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Angel Cell - Pulsa & Service</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,600,700,800&display=swap" rel="stylesheet" />

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <style>
        .glass-effect {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }
    </style>
</head>
<body class="antialiased bg-gray-50 text-gray-900 font-sans">
    
    <nav class="fixed w-full z-50 top-0 glass-effect border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
            <div class="flex items-center">
                <img src="{{ asset('assets/dist/img/logo.png') }}" alt="Logo Angel Cell" class="h-10 w-auto object-contain">
            </div>

                @if (Route::has('login'))
                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ url('/home') }}" class="text-sm font-semibold text-gray-700 hover:text-blue-600 transition">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-gray-700 hover:text-blue-600 transition">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="bg-blue-600 text-white px-5 py-2 rounded-full text-sm font-semibold hover:bg-blue-700 transition shadow-lg shadow-blue-200">Daftar</a>
                        @endif
                    @endauth
                </div>
                @endif
            </div>
        </div>
    </nav>

    <section class="pt-32 pb-20 px-4 bg-gradient-to-b from-blue-50 to-white text-center">
        <div class="max-w-4xl mx-auto">
            <span class="bg-blue-100 text-blue-700 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">Solusi Gadget Anda</span>
            <h1 class="mt-6 text-5xl md:text-6xl font-extrabold text-gray-900 leading-tight">
                Layanan <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-cyan-500">Fast Response</span> Untuk HP Anda.
            </h1>
            <p class="mt-6 text-lg text-gray-600">Terima isi pulsa, paket data, kartu perdana, hingga perbaikan (servis) handphone segala merek dengan teknisi berpengalaman.</p>
            <div class="mt-10 flex flex-wrap justify-center gap-4">
                <a href="#layanan" class="bg-blue-600 text-white px-8 py-3 rounded-xl font-bold hover:scale-105 transition transform">Lihat Layanan</a>
                <a href="https://wa.me/6281290090xxx" class="bg-white border border-gray-200 text-gray-700 px-8 py-3 rounded-xl font-bold hover:bg-gray-50 transition flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                    Hubungi Admin
                </a>
            </div>
        </div>
    </section>

    <section id="layanan" class="py-20 max-w-7xl mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            
            <div class="group p-8 bg-white rounded-3xl border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-300">
                <div class="w-14 h-14 bg-orange-100 rounded-2xl flex items-center justify-center text-orange-600 mb-6 group-hover:bg-orange-600 group-hover:text-white transition">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                </div>
                <h3 class="text-xl font-bold mb-3">Pulsa & Data</h3>
                <p class="text-gray-500 text-sm leading-relaxed mb-6">Tersedia untuk semua operator (Telkomsel, XL, Indosat, dll) dengan harga kompetitif.</p>
                <ul class="text-sm text-gray-600 space-y-2">
                    <li class="flex items-center gap-2"><svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Proses Hitungan Detik</li>
                    <li class="flex items-center gap-2"><svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Online 24 Jam</li>
                </ul>
            </div>

            <div class="group p-8 bg-white rounded-3xl border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-300">
                <div class="w-14 h-14 bg-blue-100 rounded-2xl flex items-center justify-center text-blue-600 mb-6 group-hover:bg-blue-600 group-hover:text-white transition">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                </div>
                <h3 class="text-xl font-bold mb-3">Kartu Perdana</h3>
                <p class="text-gray-500 text-sm leading-relaxed mb-6">Pilihan nomor cantik dan kartu perdana kuota besar harga grosir maupun eceran.</p>
                <ul class="text-sm text-gray-600 space-y-2">
                    <li class="flex items-center gap-2"><svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Nomor Cantik</li>
                    <li class="flex items-center gap-2"><svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Aktivasi Mudah</li>
                </ul>
            </div>

            <div class="group p-8 bg-white rounded-3xl border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-300">
                <div class="w-14 h-14 bg-red-100 rounded-2xl flex items-center justify-center text-red-600 mb-6 group-hover:bg-red-600 group-hover:text-white transition">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </div>
                <h3 class="text-xl font-bold mb-3">Servis HP</h3>
                <p class="text-gray-500 text-sm leading-relaxed mb-6">Ganti LCD, Baterai, Konektor, hingga kerusakan software (Flash/Lupa Pola).</p>
                <ul class="text-sm text-gray-600 space-y-2">
                    <li class="flex items-center gap-2"><svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Garansi Servis</li>
                    <li class="flex items-center gap-2"><svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Suku Cadang Original</li>
                </ul>
            </div>

        </div>
    </section>

    <div class="bg-blue-600 py-16">
        <div class="max-w-7xl mx-auto px-4 grid grid-cols-2 md:grid-cols-4 gap-8 text-center text-white">
            <div>
                <p class="text-3xl font-bold">1000+</p>
                <p class="text-blue-100 text-sm">Transaksi / Hari</p>
            </div>
            <div>
                <p class="text-3xl font-bold">500+</p>
                <p class="text-blue-100 text-sm">Servis Selesai</p>
            </div>
            <div>
                <p class="text-3xl font-bold">100%</p>
                <p class="text-blue-100 text-sm">Amanah</p>
            </div>
            <div>
                <p class="text-3xl font-bold">10+</p>
                <p class="text-blue-100 text-sm">Tahun Melayani</p>
            </div>
        </div>
    </div>

    <footer class="py-10 text-center text-gray-400 text-sm border-t border-gray-100 mt-20">
        <strong>Copyright &copy; 2025 - {{ date('Y') }} <a href="https://instagram.com/nikkoadr" class="text-blue-500 hover:underline">Nikko Adrian</a>.</strong> All rights reserved.
    </footer>

</body>
</html>