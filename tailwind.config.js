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
                sans: ['Poppins', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    50: '#eef2ff',
                    100: '#e0e7ff',
                    500: '#6366f1',
                    600: '#4f46e5',
                    700: '#4338ca',
                },
            },
            keyframes: {
                // Blob 1: Warna Ungu (Fokus muter di area Kiri Atas ke Tengah)
                'aurora-blob-1': {
                    '0%, 100%': { transform: 'translate(-10vw, -10vh) scale(1) rotate(0deg)' },
                    '33%': { transform: 'translate(20vw, 15vh) scale(1.1) rotate(120deg)' },
                    '66%': { transform: 'translate(-5vw, 30vh) scale(0.9) rotate(240deg)' },
                },
                // Blob 2: Warna Biru (Fokus muter di area Kanan Bawah ke Atas)
                'aurora-blob-2': {
                    '0%, 100%': { transform: 'translate(10vw, 10vh) scale(1) rotate(0deg)' },
                    '33%': { transform: 'translate(-25vw, -20vh) scale(1.15) rotate(120deg)' },
                    '66%': { transform: 'translate(15vw, -35vh) scale(0.95) rotate(240deg)' },
                },
                // Blob 3: Warna Hijau (Fokus muter di area Kiri Bawah ke Kanan)
                'aurora-blob-3': {
                    '0%, 100%': { transform: 'translate(-15vw, 20vh) scale(1) rotate(0deg)' },
                    '33%': { transform: 'translate(30vw, -10vh) scale(0.9) rotate(120deg)' },
                    '66%': { transform: 'translate(10vw, 25vh) scale(1.1) rotate(240deg)' },
                },
                'float-slow': {
                    '0%, 100%': { transform: 'translateY(0px) rotate(0deg)' },
                    '50%': { transform: 'translateY(-14px) rotate(3deg)' },
                },
                'float-reverse': {
                    '0%, 100%': { transform: 'translateY(0px) rotate(0deg)' },
                    '50%': { transform: 'translateY(16px) rotate(-4deg)' },
                },
                'float-drift': {
                    '0%, 100%': { transform: 'translate(0px, 0px) rotate(0deg)' },
                    '50%': { transform: 'translate(12px, -12px) rotate(5deg)' },
                },
                'card-zoom-in-up': {
                    '0%': {
                        opacity: '0',
                        transform: 'translateY(32px) scale(0.95)',
                    },
                    '100%': {
                        opacity: '1',
                        transform: 'translateY(0) scale(1)',
                    },
                },
            },
            animation: {
                'aurora-1': 'aurora-blob-1 25s ease-in-out infinite',
                'aurora-2': 'aurora-blob-2 35s ease-in-out infinite',
                'aurora-3': 'aurora-blob-3 45s ease-in-out infinite',
                'float-slow': 'float-slow 8s ease-in-out infinite',
                'float-reverse': 'float-reverse 10s ease-in-out infinite',
                'float-drift': 'float-drift 9s ease-in-out infinite',
                'card-entry': 'card-zoom-in-up 650ms cubic-bezier(0.16, 1, 0.3, 1) both',
            },
        },
    },

    plugins: [forms],
};