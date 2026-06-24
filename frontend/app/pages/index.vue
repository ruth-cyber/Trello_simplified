<script setup>
import { useAuth } from '../composables/useAuth.ts'
const { user, logout } = useAuth()
</script>

<template>
  <div class="p-8">
    <h1 class="text-2xl font-bold mb-4">Bienvenue, {{ user?.name }} !</h1>
    <p class="mb-4">Tu es connecté avec l'adresse : {{ user?.email }}</p>
    
    <div class="space-x-4">
      <NuxtLink to="/profile" class="bg-blue-500 text-white px-4 py-2 rounded">Mon Profil</NuxtLink>
      <button @click="logout" class="bg-red-500 text-white px-4 py-2 rounded">Se déconnecter</button>
    </div>
  </div>
</template>