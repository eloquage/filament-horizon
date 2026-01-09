import colors from 'tailwindcss/colors'
import forms from '@tailwindcss/forms'
import typography from '@tailwindcss/typography'

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/views/**/*.blade.php',
        './src/**/*.php',
    ],
    darkMode: 'class',
    theme: {
        extend: {
            colors: {
                danger: colors.red,
                gray: colors.zinc,
                info: colors.blue,
                primary: colors.amber,
                success: colors.green,
                warning: colors.yellow,
            },
        },
    },
    plugins: [forms, typography],
    // Don't conflict with Filament's classes
    corePlugins: {
        preflight: false,
    },
}
