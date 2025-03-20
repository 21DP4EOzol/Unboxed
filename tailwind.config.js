import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Brown color palette
                coffee: {
                    50: '#fff8f0',
                    100: '#fff0e0',
                    200: '#ffe0c1',
                    300: '#ffc77e', 
                    400: '#ffa65f', 
                    500: '#DE8741', 
                    600: '#B96823', 
                    700: '#964B00', 
                    800: '#7A3D00',
                    900: '#5F2E00',
                },
                cream: {
                    50: '#fefefe',
                    100: '#fbf9f2',
                    200: '#f6efd9',
                    300: '#f0e5c0',
                    400: '#e6d59e',
                    500: '#dbc57c',
                    600: '#c9b05d',
                    700: '#b0964a',
                    800: '#8f7940',
                    900: '#756438',
                },
                accent: {
                    50: '#f5f6f8',
                    100: '#e6e9ef',
                    200: '#d1d8e2',
                    300: '#aebad0',
                    400: '#8497b9',
                    500: '#6679a1',
                    600: '#516087',
                    700: '#424e6e',
                    800: '#39435c',
                    900: '#323a4e',
                },
                amber: {
                    50: '#fffbeb',
                    100: '#fef3c7',
                    200: '#fde68a',
                    300: '#fcd34d',
                    400: '#fbbf24',
                    500: '#f59e0b',
                    600: '#d97706',
                    700: '#b45309',
                    800: '#92400e',
                    900: '#78350f',
            },
        },
    },
},

    plugins: [forms],
};