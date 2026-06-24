

<template>
  <NuxtLayout>
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-gray-900">Tableau de Bord</h1>
      <p class="text-gray-500 text-sm">Vue globale de l'avancement de vos activités.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
      <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <p class="text-xs text-gray-400 uppercase font-semibold">Projets</p>
        <p class="text-2xl font-bold mt-2 text-indigo-600">{{ stats?.total_projects || 0 }}</p>
      </div>
      <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <p class="text-xs text-gray-400 uppercase font-semibold">Tâches totales</p>
        <p class="text-2xl font-bold mt-2 text-gray-800">{{ stats?.total_tasks || 0 }}</p>
      </div>
      <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <p class="text-xs text-gray-400 uppercase font-semibold">À faire</p>
        <p class="text-2xl font-bold mt-2 text-blue-500">{{ stats?.todo_tasks || 0 }}</p>
      </div>
      <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <p class="text-xs text-gray-400 uppercase font-semibold">En cours</p>
        <p class="text-2xl font-bold mt-2 text-amber-500">{{ stats?.in_progress_tasks || 0 }}</p>
      </div>
      <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <p class="text-xs text-gray-400 uppercase font-semibold">Terminées</p>
        <p class="text-2xl font-bold mt-2 text-green-500">{{ stats?.done_tasks || 0 }}</p>
      </div>
    </div>
  </NuxtLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
const { fetchWithAuth } = useAuth()
const stats = ref(null)

onMounted(async () => {
  try {
    stats.value = await fetchWithAuth('/dashboard')
  } catch (error) {
    console.error("Erreur de récupération des stats:", error)
  }
})
</script>