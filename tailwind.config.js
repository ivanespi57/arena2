/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./resources/js/**/*.{js,jsx}",
  ],
  theme: {
    extend: {
      colors: {
        primary: '#DC2626', // Rojo para Roig Arena
        dark: '#1F2937',
        light: '#F3F4F6',
      },
      fontFamily: {
        sans: ['ui-sans-serif', 'system-ui', 'sans-serif'],
      },
    },
  },
  plugins: [],
}
