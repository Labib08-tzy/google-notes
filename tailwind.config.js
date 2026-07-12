import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',

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
                primary: '#4285F4',
                secondary: '#34A853',
                accent: '#FBBC05',
                danger: '#EA4335',
                background: '#F8F9FA',
                foreground: '#202124',
            },
            borderRadius: {
                card: '12px',
            },
        },
    },

    plugins: [forms],
};