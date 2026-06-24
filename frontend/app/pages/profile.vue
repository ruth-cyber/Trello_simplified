<script setup>
import { ref } from 'vue'
const { user, fetchWithAuth } = useAuth()
const form = ref({ name: user.value?.name, email: user.value?.email })
const message = ref('')

const handleUpdate = async () => {
  try {
    const res = await fetchWithAuth('/user', {
      method: 'PUT',
      body: form.value
    })
    user.value = res.user
    message.value = "Profil mis à jour !"
  } catch (err) {
    message.value = "Une erreur est survenue."
  }
}
</script>

<template>
  <div class="p-8 max-w-md mx-auto">
    <h1 class="text-xl font-bold mb-4">Modifier mon Profil</h1>
    <p v-if="message" class="text-green-600 mb-4">{{ message }}</p>
    
    <form @submit.prevent="handleUpdate" class="space-y-4">
      <div>
        <label class="block text-sm">Nom</label>
        <input v-model="form.name" type="text" class="w-full border p-2 rounded" />
      </div>
      <div>
        <label class="block text-sm">Email</label>
        <input v-model="form.email" type="email" class="w-full border p-2 rounded" />
      </div>
      <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Enregistrer</button>
    </form>
    <br>
    <NuxtLink to="/" class="text-gray-600 underline">Retour à l'accueil</NuxtLink>
  </div>
</template>