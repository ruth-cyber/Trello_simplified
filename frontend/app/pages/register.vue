<script setup>
const { register } = useAuth()

const form = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: ''
})

const errorMessage = ref('')

const handleRegister = async () => {
  if (form.value.password !== form.value.password_confirmation) {
    errorMessage.value = "Les mots de passe ne correspondent pas"
    return
  }

  try {
    await register(form.value)
  } catch (err) {
    errorMessage.value = err.message || "Erreur lors de l'inscription"
  }
}
</script>
<template>
  <div class="flex min-h-screen items-center justify-center bg-gray-100">
    <div class="w-full max-w-md bg-white p-8 rounded shadow">
      <h2 class="text-xl font-bold mb-6 text-center">Inscription</h2>
      <p v-if="errorMessage" class="text-red-500 text-sm mb-4">{{ errorMessage }}</p>
      
      <form @submit.prevent="handleRegister" class="space-y-4">
        <div>
          <label class="block text-sm">Nom et prénom</label>
          <input v-model="form.name" type="text" class="w-full border p-2 rounded" required />
        </div>
        <div>
          <label class="block text-sm">Email</label>
          <input v-model="form.email" type="email" class="w-full border p-2 rounded" required />
        </div>
        <div>
          <label class="block text-sm">Mot de passe</label>
          <input v-model="form.password" type="password" class="w-full border p-2 rounded" required />
        </div>
        <div>
          <label class="block text-sm">Confirmer le mot de passe</label>
          <input v-model="form.password_confirmation" type="password" class="w-full border p-2 rounded" required />
        </div>
        <button type="submit" class="w-full bg-indigo-600 text-white p-2 rounded">S'inscrire</button>
      </form>
      <p class="text-xs text-center mt-4">
        Déjà un compte ? <NuxtLink to="/login" class="text-indigo-600">Se connecter</NuxtLink>
      </p>
    </div>
  </div>
</template>