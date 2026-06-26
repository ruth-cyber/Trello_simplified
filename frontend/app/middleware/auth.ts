// app/middleware/auth.ts
export default defineNuxtRouteMiddleware((to, from) => {
  const token = useCookie('auth_token') // Le nom du cookie contenant ton jeton Sanctum

  // Si l'utilisateur n'est pas connecté et essaie d'accéder à autre chose que login ou register
  if (!token.value && to.path !== '/login' && to.path !== '/register') {
    return navigateTo('/login')
  }

  // Si l'utilisateur est déjà connecté et essaie d'aller sur login ou register, on le renvoie au dashboard
  if (token.value && (to.path === '/login' || to.path === '/register')) {
    return navigateTo('/')
  }
})