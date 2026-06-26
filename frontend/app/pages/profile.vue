<script setup>
definePageMeta({ middleware: 'auth' })
import { ref } from 'vue'
const { user, fetchWithAuth } = useAuth()

const form = ref({ name: user.value?.name || '', email: user.value?.email || '' })
const status = ref('')   // 'success' | 'error' | ''
const message = ref('')
const loading = ref(false)
const activeTab = ref('profile')  // 'profile' | 'password'
const passForm = ref({ current_password: '', password: '', password_confirmation: '' })

const handleUpdate = async () => {
  loading.value = true
  status.value = ''
  try {
    const res = await fetchWithAuth('/user', { method: 'PUT', body: form.value })
    user.value = res.user
    status.value = 'success'
    message.value = 'Profil mis à jour avec succès !'
  } catch (err) {
    status.value = 'error'
    message.value = 'Une erreur est survenue.'
  } finally {
    loading.value = false
    setTimeout(() => { status.value = '' }, 4000)
  }
}

const handlePasswordChange = async () => {
  if (passForm.value.password !== passForm.value.password_confirmation) {
    status.value = 'error'
    message.value = 'Les mots de passe ne correspondent pas'
    return
  }
  loading.value = true
  status.value = ''
  try {
    await fetchWithAuth('/user/password', { method: 'PUT', body: passForm.value })
    status.value = 'success'
    message.value = 'Mot de passe modifié !'
    passForm.value = { current_password: '', password: '', password_confirmation: '' }
  } catch (err) {
    status.value = 'error'
    message.value = err.message || 'Erreur lors du changement de mot de passe'
  } finally {
    loading.value = false
    setTimeout(() => { status.value = '' }, 4000)
  }
}

// Initiales pour l'avatar
const initials = computed(() => {
  const n = form.value.name || user.value?.name || '?'
  return n.split(' ').map(w => w[0]).join('').slice(0, 2).toUpperCase()
})
</script>

<template>
  <div class="profile-page">
    <!-- Header -->
    <div class="page-header">
      <NuxtLink to="/" class="back-btn">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M19 12H5M12 5l-7 7 7 7"/>
        </svg>
        Tableau de bord
      </NuxtLink>
      <h1 class="page-title">Mon profil</h1>
    </div>

    <div class="profile-layout">
      <!-- Carte gauche : avatar + info rapide -->
      <div class="profile-sidebar">
        <div class="avatar-section">
          <div class="avatar">{{ initials }}</div>
          <h2 class="user-name">{{ form.name || 'Votre nom' }}</h2>
          <p class="user-email">{{ form.email }}</p>
        </div>

        <div class="sidebar-nav">
          <button
            class="nav-item"
            :class="{ active: activeTab === 'profile' }"
            @click="activeTab = 'profile'"
          >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
              <circle cx="12" cy="7" r="4"/>
            </svg>
            Informations personnelles
          </button>

          <button
            class="nav-item"
            :class="{ active: activeTab === 'password' }"
            @click="activeTab = 'password'"
          >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
              <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
            Mot de passe
          </button>
        </div>
      </div>

      <!-- Carte droite : formulaire -->
      <div class="profile-card">
        <!-- Notification -->
        <Transition name="fade">
          <div v-if="status" :class="['notif', status]">
            <svg v-if="status === 'success'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
            <svg v-else viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
            </svg>
            {{ message }}
          </div>
        </Transition>

        <!-- Tab : Profil -->
        <div v-if="activeTab === 'profile'">
          <div class="card-header">
            <h3>Informations personnelles</h3>
            <p>Modifiez vos informations de base.</p>
          </div>
          <form @submit.prevent="handleUpdate" class="form">
            <div class="field-row">
              <div class="field">
                <label>Nom complet</label>
                <input v-model="form.name" type="text" placeholder="Jean Dupont" required />
              </div>
            </div>
            <div class="field">
              <label>Adresse email</label>
              <input v-model="form.email" type="email" placeholder="exemple@email.com" required />
              <span class="field-hint">L'email est utilisé pour la connexion.</span>
            </div>

            <div class="form-actions">
              <button type="button" class="btn-ghost" @click="form = { name: user?.name, email: user?.email }">
                Annuler
              </button>
              <button type="submit" class="btn-primary" :disabled="loading">
                <span v-if="loading" class="spinner"></span>
                <span v-else>Enregistrer</span>
              </button>
            </div>
          </form>
        </div>

        <!-- Tab : Mot de passe -->
        <div v-if="activeTab === 'password'">
          <div class="card-header">
            <h3>Changer le mot de passe</h3>
            <p>Assurez-vous d'utiliser un mot de passe fort.</p>
          </div>
          <form @submit.prevent="handlePasswordChange" class="form">
            <div class="field">
              <label>Mot de passe actuel</label>
              <input v-model="passForm.current_password" type="password" placeholder="••••••••" required />
            </div>
            <div class="field">
              <label>Nouveau mot de passe</label>
              <input v-model="passForm.password" type="password" placeholder="Minimum 8 caractères" required />
            </div>
            <div class="field">
              <label>Confirmer le nouveau mot de passe</label>
              <input v-model="passForm.password_confirmation" type="password" placeholder="Répétez le mot de passe" required />
            </div>

            <div class="form-actions">
              <button type="submit" class="btn-primary" :disabled="loading">
                <span v-if="loading" class="spinner"></span>
                <span v-else>Mettre à jour</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
* { box-sizing: border-box; margin: 0; padding: 0; }

.profile-page {
  min-height: 100vh;
  background: #f0f4ff;
  font-family: 'Inter', system-ui, sans-serif;
  padding: 32px 40px;
}

/* Header */
.page-header {
  display: flex;
  align-items: center;
  gap: 20px;
  margin-bottom: 36px;
}

.back-btn {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 13px;
  color: #6b84b8;
  text-decoration: none;
  transition: color 0.2s;
}
.back-btn:hover { color: #4f7fe8; }
.back-btn svg { width: 16px; height: 16px; }

.page-title { font-size: 22px; font-weight: 700; color: #1a2540; }

/* Layout */
.profile-layout {
  display: grid;
  grid-template-columns: 260px 1fr;
  gap: 24px;
  max-width: 860px;
}

/* Sidebar */
.profile-sidebar {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.avatar-section {
  background: white;
  border-radius: 16px;
  padding: 28px 20px;
  text-align: center;
  box-shadow: 0 2px 12px rgba(79,127,232,0.08);
}

.avatar {
  width: 72px;
  height: 72px;
  background: linear-gradient(135deg, #4f7fe8, #7b9de0);
  border-radius: 50%;
  font-size: 24px;
  font-weight: 700;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 14px;
  box-shadow: 0 4px 16px rgba(79,127,232,0.3);
}
.user-name { font-size: 15px; font-weight: 700; color: #1a2540; margin-bottom: 4px; }
.user-email { font-size: 12px; color: #8a97b0; }

.sidebar-nav {
  background: white;
  border-radius: 16px;
  padding: 10px;
  box-shadow: 0 2px 12px rgba(79,127,232,0.08);
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 11px 14px;
  border-radius: 10px;
  font-size: 13px;
  font-weight: 500;
  color: #6b7a9a;
  background: none;
  border: none;
  cursor: pointer;
  text-align: left;
  transition: background 0.15s, color 0.15s;
}
.nav-item svg { width: 16px; height: 16px; flex-shrink: 0; }
.nav-item:hover { background: #f0f4ff; color: #4f7fe8; }
.nav-item.active { background: #eef3ff; color: #4f7fe8; font-weight: 600; }

/* Card */
.profile-card {
  background: white;
  border-radius: 16px;
  padding: 36px 40px;
  box-shadow: 0 2px 12px rgba(79,127,232,0.08);
}

.card-header { margin-bottom: 28px; }
.card-header h3 { font-size: 17px; font-weight: 700; color: #1a2540; margin-bottom: 5px; }
.card-header p { font-size: 13px; color: #8a97b0; }

/* Notification */
.notif {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px 16px;
  border-radius: 10px;
  font-size: 13px;
  font-weight: 500;
  margin-bottom: 24px;
}
.notif svg { width: 16px; height: 16px; flex-shrink: 0; }
.notif.success { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
.notif.error { background: #fff0f0; color: #dc2626; border: 1px solid #fecaca; }

.fade-enter-active, .fade-leave-active { transition: opacity 0.3s, transform 0.3s; }
.fade-enter-from, .fade-leave-to { opacity: 0; transform: translateY(-6px); }

/* Form */
.form { display: flex; flex-direction: column; gap: 20px; }

.field-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.field { display: flex; flex-direction: column; gap: 6px; }

.field label { font-size: 12px; font-weight: 600; color: #4a5568; letter-spacing: 0.3px; }

.field input {
  border: 1.5px solid #e2e8f4;
  border-radius: 8px;
  padding: 11px 14px;
  font-size: 14px;
  color: #1a2540;
  outline: none;
  transition: border-color 0.2s, box-shadow 0.2s;
  background: #fafbff;
}
.field input:focus {
  border-color: #4f7fe8;
  box-shadow: 0 0 0 3px rgba(79,127,232,0.12);
  background: #fff;
}
.field input::placeholder { color: #b0bcd4; }

.field-hint { font-size: 11px; color: #a0aec0; margin-top: 2px; }

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  margin-top: 8px;
  padding-top: 20px;
  border-top: 1px solid #f0f4ff;
}

.btn-ghost {
  padding: 10px 20px;
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

.btn-primary {
  background: #4f7fe8;
  color: white;
  border: none;
  border-radius: 8px;
  padding: 10px 24px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s, transform 0.1s, box-shadow 0.2s;
  display: flex;
  align-items: center;
  gap: 8px;
}
.btn-primary:hover:not(:disabled) {
  background: #3a6ad4;
  box-shadow: 0 4px 16px rgba(79,127,232,0.3);
  transform: translateY(-1px);
}
.btn-primary:disabled { opacity: 0.6; cursor: not-allowed; }

.spinner {
  width: 16px;
  height: 16px;
  border: 2px solid rgba(255,255,255,0.4);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* Responsive */
@media (max-width: 768px) {
  .profile-page { padding: 20px 16px; }
  .profile-layout { grid-template-columns: 1fr; }
  .field-row { grid-template-columns: 1fr; }
  .profile-card { padding: 24px 20px; }
}
</style>