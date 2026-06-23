// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  devtools: { enabled: true },
  runtimeConfig: {
    public: {
      apiBase: 'http://127.0.0.1:8000/api' // L'adresse de ton serveur Laravel
    }
  },
  // Si tu utilises Tailwind CSS, assure-toi qu'il soit bien configuré ici dans modules
  modules: ['@nuxtjs/tailwindcss'] 
})