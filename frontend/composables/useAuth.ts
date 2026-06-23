import { computed } from 'vue'

// @ts-ignore
export const useAuth = () => {
  // @ts-ignore
  const config = useRuntimeConfig()
  // @ts-ignore
  const token = useCookie('auth_token') 
  // @ts-ignore
  const user = useState('auth_user', () => null) 

  const isAuthenticated = computed(() => !!token.value)

  const fetchWithAuth = async (url: string, options: any = {}) => {
    options.headers = {
      ...options.headers,
      Accept: 'application/json',
      Authorization: token.value ? `Bearer ${token.value}` : '',
    }
    // Petite correction ici : utilise bien $fetch (le client HTTP de Nuxt) et non fetch natif
    // @ts-ignore
    return await $fetch(`${config.public.apiBase}${url}`, options)
  }

  const login = async (credentials: any) => {
    try {
      // @ts-ignore
      const data: any = await $fetch(`${config.public.apiBase}/login`, {
        method: 'POST',
        body: credentials,
        headers: { Accept: 'application/json' }
      })
      token.value = data.token
      user.value = data.user
      // @ts-ignore
      navigateTo('/') 
    } catch (error: any) {
      throw error.data
    }
  }

  const register = async (userData: any) => {
    try {
      // @ts-ignore
      const data: any = await $fetch(`${config.public.apiBase}/register`, {
        method: 'POST',
        body: userData,
        headers: { Accept: 'application/json' }
      })
      token.value = data.token
      user.value = data.user
      // @ts-ignore
      navigateTo('/')
    } catch (error: any) {
      throw error.data
    }
  }

  const logout = async () => {
    try {
      await fetchWithAuth('/logout', { method: 'POST' })
    } catch (e) {
    } finally {
      token.value = null
      user.value = null
      // @ts-ignore
      navigateTo('/login')
    }
  }

  const fetchUser = async () => {
    if (token.value && !user.value) {
      try {
        user.value = await fetchWithAuth('/user')
      } catch (e) {
        token.value = null
        user.value = null
      }
    }
  }

  return {
    user,
    token,
    isAuthenticated,
    login,
    register,
    logout,
    fetchUser,
    fetchWithAuth
  }
}