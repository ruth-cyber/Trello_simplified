<template>
  <NuxtLayout>
    <div class="flex justify-between items-center mb-6">
      <div>
        <NuxtLink to="/projects" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition uppercase tracking-wider">
          ← Retour aux projets
        </NuxtLink>
        <div class="flex items-center gap-3 mt-2">
          <h1 class="text-3xl font-bold text-slate-900 tracking-tight">{{ project?.title }}</h1>
          <span :class="roleBadgeClass(projectRole)" class="text-xs font-bold px-2.5 py-1 rounded-md tracking-wide uppercase border">
            {{ projectRole === 'owner' ? 'Chef de projet' : projectRole === 'member' ? 'Développeur' : 'Lecteur' }}
          </span>
        </div>
        <p class="text-slate-500 text-sm mt-1 max-w-xl leading-relaxed">{{ project?.description || 'Aucune description fournie.' }}</p>
      </div>
      
      <button 
        v-if="projectRole !== 'viewer'"
        @click="showTaskForm = !showTaskForm" 
        class="bg-indigo-600 text-white px-5 py-2.5 rounded-xl text-sm font-semibold shadow-lg shadow-indigo-600/10 hover:shadow-lg hover:shadow-indigo-600/20 hover:bg-indigo-700 active:scale-95 transition-all duration-150"
      >
        {{ showTaskForm ? 'Annuler' : 'Nouvelle tâche' }}
      </button>
    </div>

    <div v-if="showTaskForm && projectRole !== 'viewer'" class="bg-white p-6 rounded-2xl shadow-xl border border-slate-100 mb-8 max-w-xl transition-all duration-300">
      <h3 class="font-bold text-slate-800 text-base mb-4 tracking-tight">Créer une nouvelle tâche</h3>
      <form @submit.prevent="addTask" class="space-y-4">
        <div>
          <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Titre de la tâche</label>
          <input v-model="newTask.title" type="text" class="w-full border border-slate-200 px-4 py-2.5 rounded-xl text-sm focus:outline-none focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/10 transition" required placeholder="Ex: Concevoir la base de données" />
        </div>
        <div>
          <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Description</label>
          <textarea v-model="newTask.description" class="w-full border border-slate-200 px-4 py-2.5 rounded-xl text-sm focus:outline-none focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/10 transition" rows="3" placeholder="Détails de la tâche..."></textarea>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Priorité</label>
            <select v-model="newTask.priority" class="w-full border border-slate-200 px-4 py-2.5 rounded-xl text-sm focus:outline-none focus:border-indigo-600 transition bg-white">
              <option value="low">🟢 Faible</option>
              <option value="medium">🟡 Moyenne</option>
              <option value="high">🔴 Haute</option>
            </select>
          </div>
          <div>
            <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Date limite</label>
            <input v-model="newTask.due_date" type="date" class="w-full border border-slate-200 px-4 py-2.5 rounded-xl text-sm focus:outline-none focus:border-indigo-600 transition" />
          </div>
        </div>
        <div class="pt-2">
          <button type="submit" class="w-full bg-slate-900 text-white py-2.5 rounded-xl text-sm font-semibold hover:bg-slate-800 transition">Créer la tâche</button>
        </div>
      </form>
    </div>

    <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-sm mb-6 flex flex-col sm:flex-row gap-4 items-center">
      <div class="relative flex-1 w-full">
        <span class="absolute left-3.5 top-3 text-slate-400 text-sm">🔍</span>
        <input 
          v-model="searchQuery" 
          type="text" 
          placeholder="Rechercher une tâche par son titre ou sa description..." 
          class="w-full pl-10 pr-4 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/10 transition"
        />
      </div>
      <div class="w-full sm:w-48">
        <select v-model="filterPriority" class="w-full border border-slate-200 px-3 py-2 rounded-xl text-sm focus:outline-none focus:border-indigo-600 transition bg-white">
          <option value="">All Priorities</option>
          <option value="low">🟢 Faible</option>
          <option value="medium">🟡 Moyenne</option>
          <option value="high">🔴 Haute</option>
        </select>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      
      <div 
        v-for="col in columns" 
        :key="col.status" 
        class="bg-slate-50/80 backdrop-blur-sm p-4 rounded-2xl border border-slate-200/60 min-h-[600px] flex flex-col transition-all duration-200"
        @dragover.prevent
        @drop="projectRole !== 'viewer' ? onDrop($event, col.status) : null"
      >
        <div class="flex justify-between items-center mb-4 px-1">
          <h2 class="font-bold text-slate-700 uppercase text-xs tracking-wider">{{ col.title }}</h2>
          <span :class="col.badgeClass" class="px-2.5 py-0.5 rounded-full text-xs font-bold shadow-sm">
            {{ filteredTasksByStatus(col.status).length }}
          </span>
        </div>

        <div class="flex-1 space-y-3 drop-zone">
          <div 
            v-for="task in filteredTasksByStatus(col.status)" 
            :key="task.id" 
            class="bg-white p-4 rounded-xl shadow-sm hover:shadow-md border border-slate-100 relative overflow-hidden group transition-all duration-200"
            :class="projectRole !== 'viewer' ? 'cursor-grab active:cursor-grabbing' : 'cursor-default'"
            :draggable="projectRole !== 'viewer'"
            @dragstart="projectRole !== 'viewer' ? onDragStart($event, task) : null"
          >
            <div :class="priorityBarClass(task.priority)" class="absolute left-0 top-0 bottom-0 w-1"></div>
            
            <div class="pl-1">
              <h4 class="font-semibold text-slate-800 text-sm tracking-tight group-hover:text-indigo-600 transition-colors duration-150">
                {{ task.title }}
              </h4>
              <p class="text-slate-500 text-xs mt-1 line-clamp-2 leading-relaxed">
                {{ task.description || 'Aucune description.' }}
              </p>
              
              <div class="flex justify-between items-center mt-4 pt-2 border-t border-slate-50">
                <span :class="priorityBadgeClass(task.priority)" class="text-[10px] font-bold px-2 py-0.5 rounded-full tracking-wide uppercase">
                  {{ task.priority }}
                </span>
                
                <button 
                  v-if="projectRole !== 'viewer'"
                  @click="deleteTask(task.id)" 
                  class="text-xs text-slate-400 hover:text-red-500 transition opacity-0 group-hover:opacity-100 duration-150"
                >
                  Supprimer
                </button>
              </div>
            </div>
          </div>

          <div v-if="filteredTasksByStatus(col.status).length === 0" class="h-32 border-2 border-dashed border-slate-200 rounded-xl flex items-center justify-center text-xs text-slate-400">
            {{ searchQuery || filterPriority ? 'Aucun résultat trouvé' : 'Déposer une tâche ici' }}
          </div>
        </div>

      </div>
    </div>
  </NuxtLayout>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'

const { addToast } = useToast()
const route = useRoute()
const projectId = route.params.id
const { fetchWithAuth } = useAuth()

const project = ref(null)
const projectRole = ref('viewer') // Rôle par défaut sécurisé
const tasks = ref([])
const showTaskForm = ref(false)

// États de recherche et de filtrage
const searchQuery = ref('')
const filterPriority = ref('')

const newTask = ref({
  title: '',
  description: '',
  priority: 'medium',
  due_date: ''
})

const columns = [
  { status: 'todo', title: 'À faire', badgeClass: 'bg-slate-200 text-slate-700' },
  { status: 'in_progress', title: 'En cours', badgeClass: 'bg-indigo-100 text-indigo-700' },
  { status: 'done', title: 'Terminé', badgeClass: 'bg-emerald-100 text-emerald-700' }
]

const initData = async () => {
  try {
    const projectResponse = await fetchWithAuth(`/projects/${projectId}`)
    project.value = projectResponse
    
    // Extraction du rôle envoyé par la table pivot de Laravel
    projectRole.value = projectResponse.pivot?.role || 'viewer'

    tasks.value = await fetchWithAuth(`/projects/${projectId}/tasks`)
  } catch (err) {
    console.error("Erreur de chargement du projet:", err)
    addToast("Impossible de charger les données du projet.", "error")
  }
}

// Propriété calculée pour filtrer en temps réel
const filteredTasksByStatus = (status) => {
  return tasks.value.filter(task => {
    const matchesStatus = task.status === status
    const matchesQuery = searchQuery.value 
      ? task.title.toLowerCase().includes(searchQuery.value.toLowerCase()) || 
        task.description?.toLowerCase().includes(searchQuery.value.toLowerCase())
      : true
    const matchesPriority = filterPriority.value ? task.priority === filterPriority.value : true
    
    return matchesStatus && matchesQuery && matchesPriority
  })
}

// Classes des badges de rôles
const roleBadgeClass = (role) => {
  if (role === 'owner') return 'bg-amber-50 border-amber-200 text-amber-800'
  if (role === 'member') return 'bg-indigo-50 border-indigo-200 text-indigo-800'
  return 'bg-slate-50 border-slate-200 text-slate-600'
}

const priorityBarClass = (priority) => {
  if (priority === 'high') return 'bg-rose-500'
  if (priority === 'medium') return 'bg-amber-400'
  return 'bg-emerald-400'
}

const priorityBadgeClass = (priority) => {
  if (priority === 'high') return 'bg-rose-500/10 text-rose-600'
  if (priority === 'medium') return 'bg-amber-500/10 text-amber-700'
  return 'bg-emerald-500/10 text-emerald-600'
}

// --- LOGIQUE DU DRAG AND DROP ---
const onDragStart = (event, task) => {
  event.dataTransfer.effectAllowed = 'move'
  event.dataTransfer.setData('text/plain', task.id.toString())
}

const onDrop = async (event, newStatus) => {
  const taskId = event.dataTransfer.getData('text/plain')
  if (!taskId) return

  const taskIndex = tasks.value.findIndex(t => t.id === parseInt(taskId))
  const oldStatus = tasks.value[taskIndex].status
  
  if (oldStatus === newStatus) return 
  
  tasks.value[taskIndex].status = newStatus
  addToast('Statut de la tâche mis à jour.', 'success')

  try {
    await fetchWithAuth(`/tasks/${taskId}`, {
      method: 'PUT',
      body: { status: newStatus }
    })
  } catch (err) {
    console.error("Échec de la mise à jour du statut:", err)
    tasks.value[taskIndex].status = oldStatus
    addToast('Impossible de déplacer la tâche. Erreur réseau.', 'error')
  }
}

// --- CRUD ---
const addTask = async () => {
  if (projectRole.value === 'viewer') return
  try {
    await fetchWithAuth(`/projects/${projectId}/tasks`, {
      method: 'POST',
      body: newTask.value
    })
    newTask.value = { title: '', description: '', priority: 'medium', due_date: '' }
    showTaskForm.value = false
    await initData()
    addToast('Nouvelle tâche créée avec succès !', 'success')
  } catch (err) {
    addToast("Erreur lors de l'ajout de la tâche.", "error")
    console.error(err)
  }
}

const deleteTask = async (taskId) => {
  if (projectRole.value === 'viewer') return
  if (confirm("Supprimer définitivement cette tâche ?")) {
    try {
      await fetchWithAuth(`/tasks/${taskId}`, { method: 'DELETE' })
      await initData()
      addToast('Tâche supprimée avec succès !', 'success')
    } catch (err) {
      addToast("Erreur lors de la suppression de la tâche.", "error")
      console.error(err)
    }
  }
}

onMounted(initData)
</script>