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
            colors: {
                'primary-dark': '#1a1a1a',
                'secondary-dark': '#2d2d2d',
                'border-color': '#404040',
                'accent': {
                    DEFAULT: '#9333ea',
                    dark: '#7e22ce'
                }
            },
        },
    },

    plugins: [forms({
        strategy: 'class',
    })],
};
