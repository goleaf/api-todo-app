/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  darkMode: 'class',
  theme: {
    extend: {
      colors: {
        primary: {
          DEFAULT: '#8b5cf6', // Purple 500
          dark: '#7c3aed', // Purple 600
        },
        secondary: {
          DEFAULT: '#f97316', // Orange 500
          dark: '#ea580c', // Orange 600
        }
      }
    },
  },
  plugins: [],
} 