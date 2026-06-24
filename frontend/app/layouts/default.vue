<template>
  <div class="flex min-h-screen bg-gray-50 text-gray-800">
    <aside class="w-64 bg-indigo-900 text-white p-6 flex flex-col justify-between">
      <div>
        <h2 class="text-xl font-bold mb-8 tracking-wider">TaskFlow</h2>
        <nav class="space-y-4">
          <NuxtLink to="/" class="block hover:text-indigo-200 transition">Tableau de bord</NuxtLink>
          <NuxtLink to="/projects" class="block hover:text-indigo-200 transition">Mes Projets</NuxtLink>
          <NuxtLink to="/profile" class="block hover:text-indigo-200 transition">Mon Profil</NuxtLink>
        </nav>
      </div>
      <button @click="logout" class="text-left text-red-400 hover:text-red-300 transition">
        Déconnexion
      </button>
    </aside>

    <main class="flex-1 p-8">
      <slot />
    </main>
  </div>
</template>

<script setup>
const { logout } = useAuth()
</script>