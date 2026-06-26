<script setup>
definePageMeta({ middleware: 'auth' })
import { ref, computed, onMounted } from 'vue'
const { fetchWithAuth } = useAuth()
const { addToast } = useToast()

const projects = ref([])
const showForm = ref(false)
const errorMsg = ref('')
const newProject = ref({ title: '', description: '' })
const searchQuery = ref('')

const filteredProjects = computed(() => {
  if (!searchQuery.value.trim()) return projects.value
  const q = searchQuery.value.toLowerCase()
  return projects.value.filter(p =>
    p.title?.toLowerCase().includes(q) || p.description?.toLowerCase().includes(q)
  )
})

const fetchProjects = async () => {
  projects.value = await fetchWithAuth('/projects')
}

const createProject = async () => {
  try {
    await fetchWithAuth('/projects', { method: 'POST', body: newProject.value })
    newProject.value = { title: '', description: '' }
    showForm.value = false
    await fetchProjects()
    addToast('Projet créé avec succès !', 'success')
  } catch (err) {
    console.error(err)
  }
}

const deleteProject = async (id) => {
  if (!confirm('Supprimer ce projet ?')) return
  try {
    errorMsg.value = ''
    await fetchWithAuth(`/projects/${id}`, { method: 'DELETE' })
    await fetchProjects()
    addToast('Projet supprimé.', 'success')
  } catch (err) {
    errorMsg.value = err?.message || 'Impossible de supprimer ce projet.'
    addToast(errorMsg.value, 'error')
  }
}

onMounted(fetchProjects)
</script>

<template>
  <NuxtLayout>
    <!-- Header -->
    <div class="page-header">
      <div>
        <h1 class="page-title">Mes Projets</h1>
        <p class="page-sub">Gérez et organisez vos espaces de travail.</p>
      </div>
      <button @click="showForm = !showForm" class="btn-primary">
        <svg v-if="!showForm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="16" height="16">
          <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        <svg v-else viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="16" height="16">
          <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
        </svg>
        {{ showForm ? 'Annuler' : 'Nouveau projet' }}
      </button>
    </div>

    <!-- Formulaire création -->
    <Transition name="slide-down">
      <div v-if="showForm" class="create-form">
        <h3 class="form-title">Nouveau projet</h3>
        <form @submit.prevent="createProject" class="form">
          <div class="field">
            <label>Titre du projet *</label>
            <input v-model="newProject.title" type="text" placeholder="Ex : Refonte site web" required />
          </div>
          <div class="field">
            <label>Description <span class="optional">(optionnel)</span></label>
            <textarea v-model="newProject.description" rows="3" placeholder="Décrivez le projet..."></textarea>
          </div>
          <div class="form-actions">
            <button type="button" class="btn-ghost" @click="showForm = false">Annuler</button>
            <button type="submit" class="btn-primary">Créer le projet</button>
          </div>
        </form>
      </div>
    </Transition>

    <!-- Barre de recherche -->
    <div class="search-bar">
      <div class="search-wrap">
        <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
          <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
        </svg>
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Rechercher un projet..."
          class="search-input"
        />
        <button v-if="searchQuery" @click="searchQuery = ''" class="clear-btn">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="14" height="14">
            <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
          </svg>
        </button>
      </div>
      <p class="results-count" v-if="searchQuery">
        {{ filteredProjects.length }} résultat{{ filteredProjects.length !== 1 ? 's' : '' }}
      </p>
    </div>

    <!-- Grille projets -->
    <div v-if="filteredProjects.length > 0" class="projects-grid">
      <div v-for="project in filteredProjects" :key="project.id" class="project-card">
        <div class="card-top">
          <div class="project-badge">{{ project.title?.slice(0, 2).toUpperCase() }}</div>
          <button @click="deleteProject(project.id)" class="delete-btn" title="Supprimer">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="15" height="15">
              <polyline points="3 6 5 6 21 6"/>
              <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
              <path d="M10 11v6M14 11v6"/>
              <path d="M9 6V4h6v2"/>
            </svg>
          </button>
        </div>

        <h3 class="project-title">{{ project.title }}</h3>
        <p class="project-desc">{{ project.description || 'Aucune description fournie.' }}</p>

        <div class="card-footer">
          <NuxtLink :to="`/projects/${project.id}`" class="see-tasks-btn">
            Voir les tâches
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="14" height="14">
              <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
          </NuxtLink>
        </div>
      </div>
    </div>

    <!-- État vide -->
    <div v-else class="empty-state">
      <div class="empty-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" width="40" height="40">
          <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
        </svg>
      </div>
      <p class="empty-title">{{ searchQuery ? 'Aucun projet trouvé' : 'Aucun projet pour l\'instant' }}</p>
      <p class="empty-sub">
        {{ searchQuery ? 'Essayez un autre terme de recherche.' : 'Créez votre premier projet pour commencer.' }}
      </p>
      <button v-if="!searchQuery" @click="showForm = true" class="btn-primary">
        Créer un projet
      </button>
    </div>
  </NuxtLayout>
</template>

<style scoped>
* { box-sizing: border-box; margin: 0; padding: 0; }

.page-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  margin-bottom: 24px;
}
.page-title { font-size: 24px; font-weight: 700; color: #1a2540; }
.page-sub { font-size: 13px; color: #8a97b0; margin-top: 4px; }

.btn-primary {
  display: flex;
  align-items: center;
  gap: 7px;
  background: #4f7fe8;
  color: white;
  border: none;
  border-radius: 10px;
  padding: 10px 18px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s, box-shadow 0.2s, transform 0.1s;
  white-space: nowrap;
}
.btn-primary:hover { background: #3a6ad4; box-shadow: 0 4px 14px rgba(79,127,232,0.3); transform: translateY(-1px); }

/* ── CREATE FORM ── */
.create-form {
  background: white;
  border-radius: 16px;
  padding: 24px 28px;
  margin-bottom: 24px;
  border: 1px solid #eef2fc;
  box-shadow: 0 4px 20px rgba(79,127,232,0.1);
}

.form-title { font-size: 16px; font-weight: 700; color: #1a2540; margin-bottom: 20px; }

.form { display: flex; flex-direction: column; gap: 16px; }

.field { display: flex; flex-direction: column; gap: 6px; }
.field label { font-size: 12px; font-weight: 600; color: #4a5568; }
.optional { color: #a0aec0; font-weight: 400; }

.field input,
.field textarea {
  border: 1.5px solid #e2e8f4;
  border-radius: 8px;
  padding: 10px 14px;
  font-size: 14px;
  color: #1a2540;
  outline: none;
  transition: border-color 0.2s, box-shadow 0.2s;
  background: #fafbff;
  font-family: inherit;
  resize: vertical;
}
.field input:focus,
.field textarea:focus {
  border-color: #4f7fe8;
  box-shadow: 0 0 0 3px rgba(79,127,232,0.12);
}
.field input::placeholder, .field textarea::placeholder { color: #b0bcd4; }

.form-actions { display: flex; justify-content: flex-end; gap: 10px; }

.btn-ghost {
  padding: 9px 18px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
  color: #6b7a9a;
  background: #f0f4ff;
  border: none;
  cursor: pointer;
  transition: background 0.2s;
}
.btn-ghost:hover { background: #e2e8f4; }

/* Transition */
.slide-down-enter-active,
.slide-down-leave-active { transition: all 0.25s ease; }
.slide-down-enter-from,
.slide-down-leave-to { opacity: 0; transform: translateY(-12px); }

/* ── SEARCH BAR ── */
.search-bar {
  display: flex;
  align-items: center;
  gap: 14px;
  margin-bottom: 24px;
}

.search-wrap {
  position: relative;
  flex: 1;
  max-width: 420px;
}
.search-icon {
  position: absolute;
  left: 13px;
  top: 50%;
  transform: translateY(-50%);
  color: #8a97b0;
  pointer-events: none;
}
.search-input {
  width: 100%;
  padding: 10px 40px 10px 38px;
  border: 1.5px solid #e2e8f4;
  border-radius: 10px;
  font-size: 14px;
  color: #1a2540;
  background: white;
  outline: none;
  transition: border-color 0.2s, box-shadow 0.2s;
  font-family: inherit;
}
.search-input:focus {
  border-color: #4f7fe8;
  box-shadow: 0 0 0 3px rgba(79,127,232,0.1);
}
.search-input::placeholder { color: #b0bcd4; }

.clear-btn {
  position: absolute;
  right: 10px;
  top: 50%;
  transform: translateY(-50%);
  background: #f0f4ff;
  border: none;
  border-radius: 6px;
  width: 22px;
  height: 22px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  color: #8a97b0;
  transition: background 0.15s, color 0.15s;
}
.clear-btn:hover { background: #e2e8f4; color: #4f7fe8; }

.results-count { font-size: 13px; color: #8a97b0; white-space: nowrap; }

/* ── PROJECTS GRID ── */
.projects-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 18px;
}

.project-card {
  background: white;
  border-radius: 16px;
  padding: 22px;
  border: 1px solid #eef2fc;
  box-shadow: 0 2px 8px rgba(79,127,232,0.06);
  display: flex;
  flex-direction: column;
  gap: 10px;
  transition: transform 0.15s, box-shadow 0.15s;
}
.project-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(79,127,232,0.13); }

.card-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.project-badge {
  width: 42px;
  height: 42px;
  border-radius: 12px;
  background: linear-gradient(135deg, #eef3ff, #dce8ff);
  color: #4f7fe8;
  font-size: 13px;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1px solid #d6e4ff;
}

.delete-btn {
  background: none;
  border: none;
  cursor: pointer;
  color: #c7d7f5;
  padding: 6px;
  border-radius: 8px;
  display: flex;
  transition: color 0.15s, background 0.15s;
}
.delete-btn:hover { color: #f87171; background: #fff0f0; }

.project-title {
  font-size: 15px;
  font-weight: 700;
  color: #1a2540;
  overflow: hidden;
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
}

.project-desc {
  font-size: 13px;
  color: #8a97b0;
  line-height: 1.5;
  overflow: hidden;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  flex: 1;
}

.card-footer { margin-top: auto; padding-top: 12px; border-top: 1px solid #f0f4ff; }

.see-tasks-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 13px;
  font-weight: 600;
  color: #4f7fe8;
  text-decoration: none;
  transition: gap 0.2s;
}
.see-tasks-btn:hover { gap: 10px; }

/* ── EMPTY STATE ── */
.empty-state {
  text-align: center;
  padding: 60px 20px;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 10px;
}
.empty-icon {
  width: 72px;
  height: 72px;
  background: #eef3ff;
  border-radius: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #7b9de0;
  margin-bottom: 6px;
}
.empty-title { font-size: 17px; font-weight: 700; color: #1a2540; }
.empty-sub { font-size: 13px; color: #8a97b0; margin-bottom: 8px; }

/* Responsive */
@media (max-width: 1100px) { .projects-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 640px) { .projects-grid { grid-template-columns: 1fr; } }
</style>