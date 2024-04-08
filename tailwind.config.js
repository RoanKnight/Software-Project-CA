import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
    "./storage/framework/views/*.php",
    "./resources/views/**/*.blade.php",
  ],
  theme: {
    extend: {
      gridTemplateColumns: {
        '20': 'repeat(20, minmax(0, 1fr))',
      },
      gridColumn: {
        'span-13': 'span 13 / span 13',
        'span-14': 'span 14 / span 14',
        'span-15': 'span 15 / span 15',
        'span-16': 'span 16 / span 16',
        'span-17': 'span 17 / span 17',
        'span-18': 'span 18 / span 18',
        'span-19': 'span 19 / span 19',
        'span-20': 'span 20 / span 20',
      },
      colors: {
        // Body stylings
        background: "rgba(var(--background))",

        // Index page stylings
        tableHeadingText: "rgba(var(--tableHeadingText))",
        tableHeadingBG: "rgba(var(--tableHeadingBG))",
        tableRowText: "rgba(var(--tableRowText))",
        tableRowBG: "rgba(var(--tableRowBG))",

        // Dashboard page stylings
        dashboardSidebarBG: "rgba(var(--dashboardSidebarBG))",
        dashboardsidebarHeading: "rgba(var(--dashboardsidebarHeading))",
      },
      fontFamily: {
        'sans': ['Open Sans', ...defaultTheme.fontFamily.sans],
      },
    },
  },
  plugins: [forms],
};