/** @type {import('tailwindcss').Config} */
import defaultTheme from 'tailwindcss/defaultTheme';
import preset from './vendor/filament/support/tailwind.config.preset'
const plugin = require('tailwindcss/plugin')


export default {
  presets: [preset],
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    './storage/framework/views/*.php',
    './resources/views/**/*.blade.php',
    './app/Filament/**/*.php',
    './resources/views/filament/**/*.blade.php',
    './vendor/filament/**/*.blade.php',
  ],
  theme: {
        screens: {
        'sm': '768px',
        'md': '1024px',
        'lg': '1280px',
        'xl': '1536px',
    },
    extend: {
        fontFamily: {
            sans: ['DM Sans', ...defaultTheme.fontFamily.sans],
        },
        colors: {
            'primary-600': 'rgba(253, 174, 75, 1)',
            'primary-400': '#FEBD69',
            'gray-100': '#F4F4F0',
            'muted': 'rgba(0, 0, 0, 0.5)',
            'primary-400': 'rgba(254, 189, 105, 0.8)',
            'gray-300': '#D9D9D9',
        },
        backgroundImage: (theme) => ({
            'price-gradient': `linear-gradient(to left, transparent 1em, ${theme('colors.primary-600')} 1em)`,
        }),
        boxShadow: {
            'custom': '3px 0 0 black, 0 3px 0 black'
        },
    },
  },
  plugins: [
    plugin(function ({ addVariant, e }) {
        addVariant('classic', ({ modifySelectors, separator }) => {
          modifySelectors(({ className }) => {
            return `.classic .${e(`classic${separator}${className}`)}`
          })
        });
    }),
    require('@tailwindcss/typography'),
    require('@tailwindcss/forms'),
  ],
}

