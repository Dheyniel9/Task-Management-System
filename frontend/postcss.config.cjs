// postcss.config.js - Fixed for Tailwind v3
module.exports = {
  plugins: {
    tailwindcss: {}, // ✅ Changed from @tailwindcss/postcss to tailwindcss
    autoprefixer: {},
  },
};
