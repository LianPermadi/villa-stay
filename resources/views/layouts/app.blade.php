<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield("title", "VilaStay - Booking Villa Premium")</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Source+Sans+Pro:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2d5a27;
            --primary-light: #3d7a37;
            --primary-dark: #1e3d1a;
            --secondary: #8b6b4a;
            --secondary-light: #a88a6a;
            --accent: #c9a962;
            --light: #f8f5f0;
            --dark: #2c2c2c;
            --gray: #6b7280;
            --gray-light: #f3f4f6;
        }
        * { font-family: "Source Sans Pro", sans-serif; }
        h1, h2, h3, h4, h5, h6, .font-display { font-family: "Playfair Display", serif; }
        .bg-primary { background-color: var(--primary); }
        .bg-primary-light { background-color: var(--primary-light); }
        .bg-primary-dark { background-color: var(--primary-dark); }
        .bg-secondary { background-color: var(--secondary); }
        .bg-accent { background-color: var(--accent); }
        .bg-light { background-color: var(--light); }
        .text-primary { color: var(--primary); }
        .text-secondary { color: var(--secondary); }
        .text-accent { color: var(--accent); }
        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white; padding: 12px 28px; border-radius: 8px; font-weight: 600;
            transition: all 0.3s ease; border: none; cursor: pointer;
            display: inline-flex; align-items: center; gap: 8px;
        }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(45,90,39,0.3); }
        .btn-secondary {
            background: transparent; color: var(--primary); padding: 12px 28px;
            border-radius: 8px; font-weight: 600; transition: all 0.3s ease;
            border: 2px solid var(--primary); cursor: pointer;
        }
        .btn-secondary:hover { background: var(--primary); color: white; }
        .card-villa {
            background: white; border-radius: 16px; overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08); transition: all 0.4s ease;
        }
        .card-villa:hover { transform: translateY(-8px); box-shadow: 0 12px 40px rgba(0,0,0,0.15); }
        .hero-section {
            background: linear-gradient(135deg, rgba(45,90,39,0.9) 0%, rgba(30,61,26,0.95) 100%),
                url("https://images.unsplash.com/photo-1566073771259-6a8506099945?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80");
            background-size: cover; background-position: center; background-attachment: fixed;
        }
        .nav-link { position: relative; transition: color 0.3s ease; }
        .nav-link::after {
            content: ""; position: absolute; bottom: -4px; left: 0; width: 0; height: 2px;
            background: var(--accent); transition: width 0.3s ease;
        }
        .nav-link:hover::after { width: 100%; }
        .badge {
            display: inline-block; padding: 4px 12px; border-radius: 20px;
            font-size: 12px; font-weight: 600; text-transform: uppercase;
        }
        .badge-available { background: #d1fae5; color: #065f46; }
        .badge-pending { background: #fef3c7; color: #92400e; }
        .badge-confirmed { background: #dbeafe; color: #1e40af; }
        .badge-completed { background: #d1fae5; color: #065f46; }
        .badge-cancelled { background: #fee2e2; color: #991b1b; }
    </style>
    @yield("styles")
</head>
<body class="bg-light text-dark">
    <nav class="fixed top-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <a href="{{ route("home") }}" class="flex items-center gap-2">
                    <svg class="w-8 h-8 text-primary" viewBox="0 0 32 32" fill="none">
                        <path d="M16 2L4 12V28H10V20H22V28H28V12L16 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M10 20V28H22V20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span class="font-display text-xl font-bold text-primary">VilaStay</span>
                </a>
                <div class="hidden md:flex items-center gap-8">
                    <a href="{{ route("home") }}" class="nav-link text-gray-600 hover:text-primary font-medium">Beranda</a>
                    <a href="{{ route("villas.index") }}" class="nav-link text-gray-600 hover:text-primary font-medium">Villa</a>
                    @auth
                        <a href="{{ route("bookings.index") }}" class="nav-link text-gray-600 hover:text-primary font-medium">Pesanan Saya</a>
                        @if (Auth::user()->role === "admin")
                            <a href="{{ route("admin.dashboard") }}" class="nav-link text-gray-600 hover:text-primary font-medium">Admin</a>
                        @endif
                        <a href="{{ route("profile") }}" class="nav-link text-gray-600 hover:text-primary font-medium">Profil</a>
                        <form action="{{ route("logout") }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="nav-link text-gray-600 hover:text-primary font-medium">Keluar</button>
                        </form>
                    @else
                        <a href="{{ route("login") }}" class="btn-primary text-sm">Masuk</a>
                        <a href="{{ route("register") }}" class="btn-secondary text-sm">Daftar</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>
    <main class="pt-16">
        @yield("content")
    </main>
    <footer class="bg-primary-dark text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8">
                <div class="col-span-1">
                    <div class="flex items-center gap-2 mb-4">
                        <svg class="w-8 h-8 text-accent" viewBox="0 0 32 32" fill="none">
                            <path d="M16 2L4 12V28H10V20H22V28H28V12L16 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span class="font-display text-xl font-bold">VilaStay</span>
                    </div>
                    <p class="text-gray-400 text-sm">Solusi premium untuk pengalaman menginap villa terbaik di komplek wisata pilihan.</p>
                </div>
                <div class="col-span-1">
                    <h4 class="font-display font-semibold mb-4">Layanan</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="#" class="hover:text-white transition">Pemesanan Villa</a></li>
                        <li><a href="#" class="hover:text-white transition">Layanan Kamar</a></li>
                        <li><a href="#" class="hover:text-white transition">Fasilitas Umum</a></li>
                        <li><a href="#" class="hover:text-white transition">Area Rekreasi</a></li>
                    </ul>
                </div>
                <div class="col-span-1">
                    <h4 class="font-display font-semibold mb-4">Bantuan</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="#" class="hover:text-white transition">FAQ</a></li>
                        <li><a href="#" class="hover:text-white transition">Syarat & Ketentuan</a></li>
                        <li><a href="#" class="hover:text-white transition">Kebijakan Privasi</a></li>
                        <li><a href="#" class="hover:text-white transition">Hubungi Kami</a></li>
                    </ul>
                </div>
                <div class="col-span-1">
                    <h4 class="font-display font-semibold mb-4">Kontak</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            info@vila-stay.com
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            +62 812-3456-7890
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Komplek Vila Paradise
                        </li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-sm text-gray-400">
                <p>&copy; 2026 VilaStay. Hak Cipta Dilindungi.</p>
            </div>
        </div>
    </footer>
    @yield("scripts")
</body>
</html>
