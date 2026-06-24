<script setup>
const { fetchUser } = useAuth()
// Exécuté côté client et serveur pour restaurer la session
await fetchUser() 
</script>

<template>
  <div>
    <NuxtPage />
  </div>
</template>