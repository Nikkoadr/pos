<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Angel Cell, konter HP di Jangga, Losarang. Pulsa, paket data, kartu perdana, dan servis HP segala merek.">
    <title>Angel Cell, Konter HP Jangga Losarang</title>
    <style>
        :root {
            --ink: #0c2d48;
            --muted: #45566a;
            --paper: #fff;
            --paper-warm: #eaf4fb;
            --line: #cfe3f2;
            --accent: #0369a1;
            --accent-dark: #075985;
            --radius: 14px;
        }
        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body {
            margin: 0;
            color: var(--ink);
            background: var(--paper);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            line-height: 1.6;
        }
        .skip {
            position: absolute;
            left: -999px;
            top: 0;
            background: var(--ink);
            color: #fff;
            padding: 12px 18px;
            z-index: 100;
        }
        .skip:focus { left: 8px; top: 8px; }
        a:focus-visible, button:focus-visible {
            outline: 3px solid var(--accent);
            outline-offset: 3px;
        }
        .wrap { max-width: 1080px; margin: 0 auto; padding: 0 20px; }

        header.site {
            border-bottom: 1px solid var(--line);
            background: var(--paper);
        }
        .bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            min-height: 72px;
        }
        .brand img { height: 44px; width: auto; display: block; }
        nav.site { display: flex; align-items: center; gap: 4px; }
        nav.site a {
            color: var(--ink);
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            padding: 12px 14px;
            min-height: 44px;
            display: inline-flex;
            align-items: center;
        }
        nav.site a:hover { color: var(--accent); }
        nav.site a.kasir {
            background: var(--ink);
            color: #fff;
            border-radius: 10px;
            padding: 12px 20px;
            margin-left: 8px;
        }
        nav.site a.kasir:hover { background: var(--accent-dark); color: #fff; }

        .hero { padding: clamp(40px, 7vw, 88px) 0 clamp(32px, 5vw, 64px); }
        .hero-grid {
            display: grid;
            grid-template-columns: 1.15fr .85fr;
            gap: clamp(24px, 4vw, 56px);
            align-items: start;
        }
        .kicker {
            font-size: 14px;
            font-weight: 700;
            letter-spacing: .04em;
            color: var(--accent);
            margin: 0 0 12px;
        }
        h1 {
            font-family: Georgia, "Times New Roman", serif;
            font-style: italic;
            font-weight: 700;
            font-size: clamp(34px, 5.2vw, 56px);
            line-height: 1.12;
            margin: 0 0 16px;
            letter-spacing: -0.01em;
        }
        .lede { font-size: clamp(16px, 2vw, 19px); color: var(--muted); margin: 0 0 28px; max-width: 34em; }
        .cta-row { display: flex; flex-wrap: wrap; gap: 12px; }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 48px;
            padding: 12px 26px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 16px;
            text-decoration: none;
        }
        .btn-utama { background: var(--accent); color: #fff; }
        .btn-utama:hover { background: var(--accent-dark); }
        .btn-kedua { border: 2px solid var(--ink); color: var(--ink); background: transparent; }
        .btn-kedua:hover { background: var(--ink); color: #fff; }

        .nota {
            background: var(--paper-warm);
            border: 1px solid var(--line);
            border-radius: var(--radius);
            padding: 22px 22px 18px;
        }
        .nota h2 {
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: var(--muted);
            margin: 0 0 10px;
            font-weight: 700;
        }
        .nota ol { margin: 0; padding-left: 20px; font-size: 15px; }
        .nota li { margin-bottom: 8px; }
        .nota li::marker { color: var(--accent); font-weight: 700; }

        .perforasi {
            height: 22px;
            background-image: radial-gradient(circle at 11px 11px, var(--paper-warm) 7px, transparent 7.5px);
            background-size: 26px 22px;
            background-repeat: repeat-x;
            border: 0;
            margin: 8px 0 0;
        }

        section.blok { padding: clamp(36px, 6vw, 72px) 0; }
        section.blok.tint { background: var(--paper-warm); border-top: 1px solid var(--line); border-bottom: 1px solid var(--line); }
        .judul-blok { font-family: Georgia, serif; font-style: italic; font-size: clamp(26px, 3.4vw, 36px); margin: 0 0 8px; }
        .sub-blok { color: var(--muted); margin: 0 0 28px; max-width: 40em; }

        .servis {
            border: 1px solid var(--line);
            border-left: 6px solid var(--accent);
            border-radius: var(--radius);
            background: var(--paper);
            padding: clamp(22px, 3.4vw, 36px);
            margin-bottom: 20px;
        }
        .servis h3 { margin: 0 0 6px; font-size: 22px; }
        .servis p.desk { color: var(--muted); margin: 0 0 16px; max-width: 44em; }
        .servis ul { margin: 0; padding: 0; list-style: none; display: grid; gap: 10px; }
        .servis li { padding-left: 26px; position: relative; font-size: 15px; }
        .servis li::before {
            content: "";
            position: absolute;
            left: 2px;
            top: 8px;
            width: 12px;
            height: 7px;
            border-left: 3px solid var(--accent);
            border-bottom: 3px solid var(--accent);
            transform: rotate(-45deg);
        }
        .kompak {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .kompak article {
            border: 1px solid var(--line);
            border-radius: var(--radius);
            padding: 20px 22px;
            background: var(--paper);
        }
        .kompak h3 { margin: 0 0 4px; font-size: 18px; }
        .kompak p { margin: 0; color: var(--muted); font-size: 15px; }

        .kunjungan-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .kartu {
            border: 1px solid var(--line);
            border-radius: var(--radius);
            padding: 22px;
            background: var(--paper);
        }
        .kartu h3 { margin: 0 0 8px; font-size: 18px; }
        .kartu p { margin: 0 0 6px; color: var(--muted); font-size: 15px; }
        .kartu p strong { color: var(--ink); }

        footer.site {
            border-top: 1px solid var(--line);
            padding: 26px 0 34px;
            color: var(--muted);
            font-size: 14px;
        }
        footer.site .wrap { display: flex; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
        footer.site a { color: var(--accent); }

        @media (max-width: 760px) {
            .hero-grid, .kompak, .kunjungan-grid { grid-template-columns: 1fr; }
            nav.site a { padding: 12px 10px; }
            nav.site a.nav-sekunder { display: none; }
            .hero { padding-top: 32px; }
        }
        @media (prefers-reduced-motion: reduce) {
            html { scroll-behavior: auto; }
        }
    </style>
</head>
<body>
    <a class="skip" href="#konten">Lewati ke konten</a>

    <header class="site">
        <div class="wrap bar">
            <a class="brand" href="/" aria-label="Angel Cell, beranda">
                <img src="{{ asset('assets/dist/img/logo.png') }}" alt="Angel Cell">
            </a>
            <nav class="site" aria-label="Navigasi utama">
                <a class="nav-sekunder" href="#layanan">Layanan</a>
                <a class="nav-sekunder" href="#kunjungan">Lokasi</a>
                @auth
                    <a class="kasir" href="{{ url('/home') }}">Dashboard</a>
                @else
                    <a class="kasir" href="{{ route('login') }}">Masuk kasir</a>
                @endauth
            </nav>
        </div>
    </header>

    <main id="konten">
        <div class="wrap hero">
            <div class="hero-grid">
                <div>
                    <p class="kicker">Konter HP, Jangga, Losarang</p>
                    <h1>Pulsa, perdana, dan servis HP di satu tempat.</h1>
                    <p class="lede">Angel Cell melayani isi pulsa dan paket data semua operator, kartu perdana, serta perbaikan HP segala merek. Servis ditangani teknisi dan dicatat dengan nota pengambilan.</p>
                    <div class="cta-row">
                        <a class="btn btn-utama" href="#layanan">Lihat layanan</a>
                        @auth
                            <a class="btn btn-kedua" href="{{ url('/home') }}">Buka dashboard</a>
                        @else
                            <a class="btn btn-kedua" href="{{ route('login') }}">Masuk kasir</a>
                        @endauth
                    </div>
                </div>
                <aside class="nota" aria-label="Aturan pengambilan servis">
                    <h2>Ambil servis bawa ini</h2>
                    <ol>
                        <li>Nota servis wajib dibawa saat pengambilan.</li>
                        <li>Nota hilang, wajib membawa KTP.</li>
                        <li>Segel rusak, garansi hangus.</li>
                    </ol>
                </aside>
            </div>
        </div>

        <div class="perforasi" aria-hidden="true"></div>

        <section class="blok" id="layanan" aria-labelledby="judul-layanan">
            <div class="wrap">
                <h2 class="judul-blok" id="judul-layanan">Layanan</h2>
                <p class="sub-blok">Servis ditulis paling lengkap karena ada syarat pengambilannya. Pulsa dan perdana tinggal sebut kebutuhanmu di toko.</p>

                <div class="servis">
                    <h3>Servis HP segala merek</h3>
                    <p class="desk">Ganti LCD, baterai, konektor cas, sampai perbaikan software seperti flash dan lupa pola. Estimasi biaya dihitung di depan sebelum dikerjakan.</p>
                    <ul>
                        <li>Garansi sesuai kesepakatan, segel rusak garansi hangus.</li>
                        <li>Pengambilan wajib membawa nota, atau KTP bila nota hilang.</li>
                        <li>Sparepart yang diganti tercatat di nota.</li>
                    </ul>
                </div>

                <div class="kompak">
                    <article>
                        <h3>Pulsa dan paket data</h3>
                        <p>Semua operator. Sebut nomor dan nominalnya, diproses di tempat.</p>
                    </article>
                    <article>
                        <h3>Kartu perdana</h3>
                        <p>Nomor cantik dan perdana kuota, ecer maupun grosir.</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="blok tint" id="kunjungan" aria-labelledby="judul-kunjungan">
            <div class="wrap">
                <h2 class="judul-blok" id="judul-kunjungan">Datang langsung</h2>
                <p class="sub-blok">Toko fisik, bukan toko online. Untuk servis, unitnya perlu diperiksa langsung.</p>
                <div class="kunjungan-grid">
                    <div class="kartu">
                        <h3>Alamat</h3>
                        <p><strong>Angel Cell</strong></p>
                        <p>Jalan Jangga-Terisi, Desa Jangga, Kecamatan Losarang.</p>
                    </div>
                    <div class="kartu">
                        <h3>Yang perlu dibawa</h3>
                        <p><strong>Isi pulsa:</strong> nomor HP dan nominalnya.</p>
                        <p><strong>Ambil servis:</strong> nota servis, atau KTP bila nota hilang.</p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="site">
        <div class="wrap">
            <span>Copyright &copy; 2025 - {{ date('Y') }} Angel Cell.</span>
            <span><a href="https://instagram.com/nikkoadr">Nikko Adrian</a>. All rights reserved.</span>
        </div>
    </footer>
</body>
</html>
