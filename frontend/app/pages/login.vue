<script setup>
import { ref } from 'vue'
const { login } = useAuth()
const form = ref({ email: '', password: '' })
const errorMessage = ref('')

const handleLogin = async () => {
  try {
    await login(form.value)
  } catch (err) {
    errorMessage.value = err.message || "Erreur de connexion"
  }
}
</script>

<template>
  <div class="flex min-h-screen items-center justify-center bg-gray-100">
    <div class="w-full max-w-md bg-white p-8 rounded shadow">
      <h2 class="text-xl font-bold mb-6 text-center">Connexion</h2>
      <p v-if="errorMessage" class="text-red-500 text-sm mb-4">{{ errorMessage }}</p>
      
      <form @submit.prevent="handleLogin" class="space-y-4">
        <div>
          <label class="block text-sm">Email</label>
          <input v-model="form.email" type="email" class="w-full border p-2 rounded" required />
        </div>
        <div>
          <label class="block text-sm">Mot de passe</label>
          <input v-model="form.password" type="password" class="w-full border p-2 rounded" required />
        </div>
        <button type="submit" class="w-full bg-indigo-600 text-white p-2 rounded">Se connecter</button>
      </form>
      <p class="text-xs text-center mt-4">
        Pas de compte ? <NuxtLink to="/register" class="text-indigo-600">S'inscrire</NuxtLink>
      </p>
    </div>
  </div>
</template>