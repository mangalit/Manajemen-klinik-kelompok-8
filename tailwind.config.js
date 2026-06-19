import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            
            // PENTING: Tambahkan palet warna kustom Anda di sini
            colors: {
              klinik: {
                'primary': '#0D47A1',   // Contoh: Biru Gelap (untuk navigasi, judul)
                'secondary': '#10B981', // Contoh: Hijau (untuk tombol aksi)
                'accent': '#F59E0B',    // Contoh: Kuning (untuk notifikasi atau lencana)
                'light': '#F3F4F6',     // Contoh: Abu-abu muda (untuk latar belakang halaman)
              },
            },
        },
    },

    plugins: [forms],
};