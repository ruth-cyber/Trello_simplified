<script setup>
import { useAuth } from '../composables/useAuth.ts'
const { fetchUser } = useAuth()
// Exécuté côté client et serveur pour restaurer la session
await fetchUser() 
</script>

<template>
  <div>
    <NuxtPage />
  </div>
</template>