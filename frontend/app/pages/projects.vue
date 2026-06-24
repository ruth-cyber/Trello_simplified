<template>
  <NuxtLayout>
    <div class="flex justify-between items-center mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Mes Projets</h1>
        <p class="text-gray-500 text-sm">Gérez et organisez vos différents espaces de travail.</p>
      </div>
      <button @click="showForm = !showForm" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition">
        {{ showForm ? 'Annuler' : 'Nouveau Projet' }}
      </button>
    </div>

    <div v-if="showForm" class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 mb-8 max-w-xl">
      <h3 class="font-semibold mb-4 text-gray-800">Créer un nouveau projet</h3>
      <form @submit.prevent="createProject" class="space-y-4">
        <div>
          <label class="block text-xs font-semibold text-gray-500 mb-1">Titre du projet</label>
          <input v-model="newProject.title" type="text" class="w-full border border-gray-200 p-2 rounded-lg text-sm focus:outline-indigo-600" required />
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-500 mb-1">Description (Optionnelle)</label>
          <textarea v-model="newProject.description" class="w-full border border-gray-200 p-2 rounded-lg text-sm focus:outline-indigo-600" rows="3"></textarea>
        </div>
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm">Enregistrer</button>
      </form>
    </div>

    <p v-if="errorMsg" class="text-sm text-red-600 bg-red-50 p-3 rounded-lg mb-4">{{ errorMsg }}</p>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="project in projects" :key="project.id" class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between">
        <div>
          <h3 class="font-bold text-gray-800 text-lg mb-2">{{ project.title }}</h3>
          <p class="text-gray-500 text-sm line-clamp-3 mb-4">{{ project.description || 'Aucune description fournie.' }}</p>
        </div>
        <div class="flex justify-between items-center mt-4 pt-4 border-t border-gray-50">
          <NuxtLink :to="`/projects/${project.id}`" class="text-indigo-600 text-sm font-medium hover:underline">
            Voir les tâches →
          </NuxtLink>
          <button @click="deleteProject(project.id)" class="text-red-500 hover:text-red-700 text-sm">
            Supprimer
          </button>
        </div>
      </div>
    </div>
  </NuxtLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
const { fetchWithAuth } = useAuth()

const projects = ref([])
const showForm = ref(false)
const errorMsg = ref('')
const newProject = ref({ title: '', description: '' })

const fetchProjects = async () => {
  projects.value = await fetchWithAuth('/projects')
}

const createProject = async () => {
  try {
    await fetchWithAuth('/projects', {
      method: 'POST',
      body: newProject.value
    })
    newProject.value = { title: '', description: '' }
    showForm.value = false
    await fetchProjects()
  } catch (err) {
    console.error(err)
  }
}

const deleteProject = async (id) => {
  try {
    errorMsg.value = ''
    await fetchWithAuth(`/projects/${id}`, { method: 'DELETE' })
    await fetchProjects()
  } catch (err) {
    errorMsg.value = err?.message || "Impossible de supprimer ce projet."
  }
}

onMounted(fetchProjects)
</script>
