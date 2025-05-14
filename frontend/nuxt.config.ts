import tailwindcss from "@tailwindcss/vite";

// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: "2024-11-01",
  devtools: { enabled: true },
  modules: ["@pinia/nuxt"],
  // nitro: {
  //   routeRules: {
  //     "/backend/**": {
  //       proxy: "http://127.0.0.1:8000/**",
  //       headers: {
  //         Accept: "application/json",
  //         "Content-Type": "application/json",
  //       },
  //     },
  //   },
  // },
  runtimeConfig: {
    public: {
      backendUrl: process.env.BACKEND_URL,
    },
  },
  vite: {
    plugins: [tailwindcss()],
  },
  css: ["~/assets/app.css"],
});
