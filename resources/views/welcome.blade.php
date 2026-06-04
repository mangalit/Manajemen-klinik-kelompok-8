<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Klinik Sehat') }} - Solusi Kesehatan Anda</title>
    <link rel="icon" href="{{ Vite::asset('public/images/logo.png') }}" type="png">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">

    <!-- Navbar -->
    <header class="bg-white dark:bg-gray-900 shadow-sm fixed w-full z-50">
        <div class="max-w-7xl mx-auto flex justify-between items-center p-4">
            <a href="#" class="flex items-center">
                <img src="{{ asset('images/logo.png') }}" alt="Klinik Sehat" class="h-20 w-auto">
                <span class="ml-3 text-xl font-bold text-gray-800 dark:text-white">Klinik Sehat</span>
            </a>
            <div class="space-x-4">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white font-semibold">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white font-semibold">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white font-semibold">Register</a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </header>

    <!-- Hero Section dengan Background Image -->

    <section id="hero" class="relative h-screen bg-cover bg-center transition-all duration-1000">

        <!-- Overlay gelap supaya teks terlihat jelas -->
        <div class="absolute inset-0 bg-black bg-opacity-50"></div>

        <!-- Konten teks di tengah -->
        <div class="relative z-10 flex flex-col justify-center items-center h-full text-center px-6">
            <h1 class="text-4xl md:text-5xl font-bold text-white">Selamat Datang di Klinik Sehat</h1>
            <p class="mt-4 text-lg md:text-xl text-gray-200">
                Solusi kesehatan terpercaya untuk Anda dan keluarga. Buat janji temu dengan dokter kami secara mudah dan cepat.
            </p>
            <div class="mt-8 flex justify-center gap-4 flex-wrap">
                <a href="{{ route('register') }}" class="px-8 py-3 bg-klinik-secondary text-white rounded-md hover:opacity-90">Daftar Sekarang</a>
                <a href="{{ route('login') }}" class="px-8 py-3 bg-white text-klinik-secondary rounded-md shadow-sm hover:bg-gray-50">Masuk</a>
            </div>
        </div>
    </section>

    <!-- Layanan Section -->
    <section class="bg-white dark:bg-gray-800 py-16">
        <div class="max-w-7xl mx-auto px-6">
            <h2 class="text-3xl font-bold text-center text-gray-800 dark:text-white">Layanan Kami</h2>
            <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="p-6 bg-gray-50 dark:bg-gray-900 rounded-lg shadow-md">
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-2">Booking Online</h3>
                    <p class="text-gray-500 dark:text-gray-400">Buat janji temu dengan dokter pilihan Anda kapan saja tanpa antri.</p>
                </div>
                <div class="p-6 bg-gray-50 dark:bg-gray-900 rounded-lg shadow-md">
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-2">Antrian Digital</h3>
                    <p class="text-gray-500 dark:text-gray-400">Dapatkan nomor antrian digital dan pantau status antrian Anda real-time.</p>
                </div>
                <div class="p-6 bg-gray-50 dark:bg-gray-900 rounded-lg shadow-md">
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-2">Riwayat Medis</h3>
                    <p class="text-gray-500 dark:text-gray-400">Akses riwayat kunjungan dan hasil diagnosa dokter Anda dengan aman.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Tentang Kami Section -->
    <section class="bg-gray-100 dark:bg-gray-900 py-16">
        <div class="max-w-4xl mx-auto text-center px-6">
            <h2 class="text-3xl font-bold text-gray-800 dark:text-white">Tentang Klinik Sehat</h2>
            <p class="mt-4 text-gray-600 dark:text-gray-400">Klinik Sehat hadir untuk memberikan layanan kesehatan terbaik dengan fasilitas modern dan tenaga medis profesional. Kami berkomitmen memberikan kenyamanan dan keamanan bagi pasien kami.</p>
        </div>
    </section>

    <!-- Tim Dokter Section -->
    <section class="bg-white dark:bg-gray-800 py-16">
        <div class="max-w-7xl mx-auto px-6">
            <h2 class="text-3xl font-bold text-center text-gray-800 dark:text-white">Tim Dokter Kami</h2>
            <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="p-6 bg-gray-50 dark:bg-gray-900 rounded-lg shadow-md text-center">
                    <img src="{{ asset('images/dokter1.png') }}" alt="Dokter 1" class="mx-auto h-32 w-32 rounded-full mb-4">
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-white">Dr. Ahmad</h3>
                    <p class="text-gray-500 dark:text-gray-400">Spesialis Umum</p>
                </div>
                <div class="p-6 bg-gray-50 dark:bg-gray-900 rounded-lg shadow-md text-center">
                    <img src="{{ asset('images/dokter2.png') }}" alt="Dokter 2" class="mx-auto h-32 w-32 rounded-full mb-4">
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-white">Dr. Siti</h3>
                    <p class="text-gray-500 dark:text-gray-400">Spesialis Gigi</p>
                </div>
                <div class="p-6 bg-gray-50 dark:bg-gray-900 rounded-lg shadow-md text-center">
                    <img src="{{ asset('images/dokter3.png') }}" alt="Dokter 3" class="mx-auto h-32 w-32 rounded-full mb-4">
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-white">Dr. Budi</h3>
                    <p class="text-gray-500 dark:text-gray-400">Spesialis Anak</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimoni Section -->
    <section class="bg-gray-100 dark:bg-gray-900 py-16">
        <div class="max-w-5xl mx-auto px-6 text-center">
            <h2 class="text-3xl font-bold text-gray-800 dark:text-white">Apa Kata Pasien Kami</h2>
            <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                    <p class="text-gray-500 dark:text-gray-400">"Pelayanan cepat dan dokter ramah. Sangat membantu!"</p>
                    <h3 class="mt-4 font-semibold text-gray-800 dark:text-white">- Andi</h3>
                </div>
                <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                    <p class="text-gray-500 dark:text-gray-400">"Mudah membuat janji online, tidak perlu antri panjang."</p>
                    <h3 class="mt-4 font-semibold text-gray-800 dark:text-white">- Rina</h3>
                </div>
                <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                    <p class="text-gray-500 dark:text-gray-400">"Fasilitas modern dan nyaman. Sangat direkomendasikan."</p>
                    <h3 class="mt-4 font-semibold text-gray-800 dark:text-white">- Budi</h3>
                </div>
            </div>
        </div>
    </section>

    <!-- Kontak Section -->
    <section class="bg-white dark:bg-gray-800 py-16">
        <div class="max-w-4xl mx-auto px-6 text-center">
            <h2 class="text-3xl font-bold text-gray-800 dark:text-white">Hubungi Kami</h2>
            <p class="mt-4 text-gray-600 dark:text-gray-400">Email: info@kliniksehat.com | Telp: (021) 123-4567 | Alamat: Jl. Sehat No.1, Jakarta</p>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-100 dark:bg-gray-900 py-6">
        <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row justify-between text-center md:text-left text-gray-500 dark:text-gray-400">
            <div>&copy; {{ date('Y') }} Klinik Sehat. All Rights Reserved.</div>
            <div class="mt-2 md:mt-0">Laravel v{{ Illuminate\Foundation\Application::VERSION }} | PHP v{{ PHP_VERSION }}</div>
        </div>
    </footer>
    <script>
    const hero = document.getElementById('hero');
    const images = [
        "{{ asset('images/banner1.png') }}",
        "{{ asset('images/banner2.png') }}",
        "{{ asset('images/banner3.png') }}"
    ];
    let index = 0;

    setInterval(() => {
        index = (index + 1) % images.length;
        hero.style.backgroundImage = `url('${images[index]}')`;
    }, 3000); // ganti tiap 3 detik
</script>


</body>
</html>
