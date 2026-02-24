import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                heading: ['"Playfair Display"', 'serif'],
                script: ['"Great Vibes"', 'cursive'],
            },
            colors: {
                wedding: {
                    bg: '#F7F5F2',
                    text: '#1E1E1E',
                    muted: '#6B6B6B',
                    band: '#22363A',
                },
            },
            maxWidth: {
                content: '1160px',
            },
            borderColor: {
                soft: 'rgba(0, 0, 0, 0.12)',
            },
            boxShadow: {
                soft: '0 12px 34px rgba(0, 0, 0, 0.07)',
            },
            letterSpacing: {
                luxe: '0.26em',
            },
        },
    },
    plugins: [],
};
