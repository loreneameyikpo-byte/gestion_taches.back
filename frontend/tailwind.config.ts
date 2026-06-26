import type { Config } from 'tailwindcss'

export default <Config>{
  content: [
    './components/**/*.{vue,js,ts}',
    './layouts/**/*.vue',
    './pages/**/*.vue',
    './composables/**/*.{js,ts}',
    './app.vue',
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          50: '#fdf2f8',
          100: '#fce7f3',
          200: '#fbcfe8',
          300: '#f9a8d4',
          400: '#f472b6',
          500: '#ec4899',
          600: '#db2777',
          700: '#be185d',
        },
        brown: {
          50: '#faf6f2',
          100: '#f0e6da',
          200: '#e0cab3',
          300: '#cda878',
          400: '#b08a5c',
          500: '#8c6843',
          600: '#6f5236',
          700: '#5a4229',
          800: '#43321f',
          900: '#2e2215',
        },
      },
    },
  },
  plugins: [],
} satisfies Config