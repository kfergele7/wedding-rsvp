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
                    light: '#F2ECE3',
                    offwhite: '#F7F7F7',
                    text: '#0F1B1D',
                    muted: '#848484',
                    band: '#22363A',
                    primarygreen: '#466369',
                    black: '#0F1B1D',
                    danger: '#E66363',
                    dangerdark: '#B93F3F',
                    disabled: '#848484',
                    success: '#21C177',
                    successdark: '#1AA267',
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
