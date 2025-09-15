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
                // Primary Colors (60% - Main backgrounds, large areas)
                'primary-bg': '#f8fafc', // slate-50
                'primary-surface': '#ffffff', // white
                'primary-text': '#1e293b', // slate-800
                'primary-border': '#e2e8f0', // slate-200

                // Secondary Colors (30% - Interactive elements, navigation)
                'secondary-brand': '#3b82f6', // blue-500
                'secondary-accent': '#1d4ed8', // blue-700
                'secondary-surface': '#f1f5f9', // slate-100
                'secondary-text': '#475569', // slate-600

                // Accent Colors (10% - Highlights, CTAs, status indicators)
                'accent-success': '#10b981', // emerald-500
                'accent-warning': '#f59e0b', // amber-500
                'accent-danger': '#ef4444', // red-500
                'accent-info': '#06b6d4', // cyan-500
                'accent-purple': '#8b5cf6', // violet-500
            },
        },
    },

    plugins: [forms],
};
