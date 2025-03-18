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
                    50: '#f9f7f5',
                    100: '#f1ece7',
                    200: '#e2d5cb',
                    300: '#d2baa7',
                    400: '#c09c82',
                    500: '#ad8162',
                    600: '#9c6f4d',
                    700: '#815a40',
                    800: '#6a4b38',
                    900: '#583e30',
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
            },
        },
    },

    plugins: [forms],
};