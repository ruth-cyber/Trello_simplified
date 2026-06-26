<script setup>
definePageMeta({ layout: 'auth' })
import { ref, computed } from 'vue'
const { register } = useAuth()

const form = ref({ name: '', email: '', password: '', password_confirmation: '' })
const errorMessage = ref('')
const loading = ref(false)
const showPass = ref(false)
const showConfirm = ref(false)

const handleRegister = async () => {
  if (form.value.password !== form.value.password_confirmation) {
    errorMessage.value = 'Les mots de passe ne correspondent pas'
    return
  }
  loading.value = true
  try {
    await register(form.value)
  } catch (err) {
    errorMessage.value = err.message || "Erreur lors de l'inscription"
  } finally {
    loading.value = false
  }
}

const strengthScore = computed(() => {
  const p = form.value.password
  let score = 0
  if (p.length >= 8) score++
  if (/[A-Z]/.test(p)) score++
  if (/[0-9]/.test(p)) score++
  if (/[^A-Za-z0-9]/.test(p)) score++
  return score
})

const strengthPercent = computed(() => [0, 33, 55, 80, 100][strengthScore.value])
const strengthColor = computed(() => ['', '#f87171', '#fb923c', '#facc15', '#4ade80'][strengthScore.value])
const strengthLabel = computed(() => ['', 'Faible', 'Moyen', 'Bon', 'Très fort'][strengthScore.value])
</script>

<template>
  <div class="auth-wrapper">
    <!-- Côté gauche : formulaire -->
    <div class="auth-form-side">
      <div class="brand">
        <span class="brand-dot"></span>
        <span class="brand-name">TaskFlow</span>
      </div>

      <div class="form-container">
        <h1 class="form-title">Créer un compte</h1>
        <p class="form-subtitle">Rejoignez TaskFlow et organisez vos projets efficacement.</p>

        <p v-if="errorMessage" class="error-msg">{{ errorMessage }}</p>

        <form @submit.prevent="handleRegister" class="form">
          <div class="field">
            <label>Nom complet</label>
            <input v-model="form.name" type="text" placeholder="Jean Dupont" required />
          </div>

          <div class="field">
            <label>Email</label>
            <input v-model="form.email" type="email" placeholder="exemple@email.com" required />
          </div>

          <div class="field">
            <label>Mot de passe</label>
            <div class="password-wrap">
              <input
                v-model="form.password"
                :type="showPass ? 'text' : 'password'"
                placeholder="Minimum 8 caractères"
                required
              />
              <button type="button" class="eye-btn" @click="showPass = !showPass">
                <svg v-if="!showPass" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                </svg>
                <svg v-else viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
                  <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
                  <line x1="1" y1="1" x2="23" y2="23"/>
                </svg>
              </button>
            </div>
          </div>

          <div class="field">
            <label>Confirmer le mot de passe</label>
            <div class="password-wrap">
              <input
                v-model="form.password_confirmation"
                :type="showConfirm ? 'text' : 'password'"
                placeholder="Répétez le mot de passe"
                required
              />
              <button type="button" class="eye-btn" @click="showConfirm = !showConfirm">
                <svg v-if="!showConfirm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                </svg>
                <svg v-else viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
                  <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
                  <line x1="1" y1="1" x2="23" y2="23"/>
                </svg>
              </button>
            </div>
          </div>

          <!-- Indicateur de force du mot de passe -->
          <div v-if="form.password" class="strength-bar">
            <div class="strength-track">
              <div
                class="strength-fill"
                :style="{ width: strengthPercent + '%', background: strengthColor }"
              ></div>
            </div>
            <span :style="{ color: strengthColor }">{{ strengthLabel }}</span>
          </div>

          <button type="submit" class="btn-primary" :disabled="loading">
            <span v-if="loading" class="spinner"></span>
            <span v-else>Créer mon compte</span>
          </button>
        </form>

        <p class="switch-link">
          Déjà un compte ?
          <NuxtLink to="/login">Se connecter</NuxtLink>
        </p>
      </div>
    </div>

    <!-- Côté droit : illustration -->
    <div class="auth-illustration-side">
      <div class="iso-container">
        <svg class="illustration" viewBox="0 0 500 400" fill="none" xmlns="http://www.w3.org/2000/svg">
          <ellipse cx="250" cy="340" rx="200" ry="50" fill="#3a5fcf" opacity="0.25"/>
          <g>
            <rect x="60" y="120" width="110" height="130" rx="4" fill="#2952c8"/>
            <rect x="75" y="135" width="22" height="16" rx="2" fill="#7aaeff" opacity="0.6"/>
            <rect x="105" y="135" width="22" height="16" rx="2" fill="#7aaeff" opacity="0.6"/>
            <rect x="135" y="135" width="22" height="16" rx="2" fill="#7aaeff" opacity="0.3"/>
            <rect x="75" y="160" width="22" height="16" rx="2" fill="#7aaeff" opacity="0.8"/>
            <rect x="105" y="160" width="22" height="16" rx="2" fill="#7aaeff" opacity="0.5"/>
            <rect x="135" y="160" width="22" height="16" rx="2" fill="#7aaeff" opacity="0.3"/>
            <rect x="75" y="185" width="22" height="16" rx="2" fill="#7aaeff" opacity="0.4"/>
            <rect x="105" y="185" width="22" height="16" rx="2" fill="#7aaeff" opacity="0.7"/>
            <rect x="135" y="185" width="22" height="16" rx="2" fill="#7aaeff" opacity="0.5"/>
          </g>
          <g>
            <rect x="180" y="80" width="170" height="190" rx="6" fill="#3461e0"/>
            <rect x="200" y="95" width="130" height="90" rx="4" fill="#1e3fa0" opacity="0.6"/>
            <polyline points="210,170 230,145 255,158 275,130 300,140 320,118" stroke="#5cffb0" stroke-width="2.5" fill="none" stroke-linecap="round"/>
            <rect x="200" y="198" width="38" height="28" rx="3" fill="#7aaeff" opacity="0.5"/>
            <rect x="246" y="198" width="38" height="28" rx="3" fill="#7aaeff" opacity="0.7"/>
            <rect x="292" y="198" width="38" height="28" rx="3" fill="#7aaeff" opacity="0.4"/>
          </g>
          <g>
            <rect x="360" y="100" width="90" height="160" rx="4" fill="#2952c8"/>
            <rect x="370" y="115" width="18" height="14" rx="2" fill="#7aaeff" opacity="0.6"/>
            <rect x="396" y="115" width="18" height="14" rx="2" fill="#7aaeff" opacity="0.5"/>
            <rect x="370" y="138" width="18" height="14" rx="2" fill="#7aaeff" opacity="0.4"/>
            <rect x="396" y="138" width="18" height="14" rx="2" fill="#7aaeff" opacity="0.7"/>
            <rect x="370" y="161" width="18" height="14" rx="2" fill="#7aaeff" opacity="0.6"/>
            <rect x="396" y="161" width="18" height="14" rx="2" fill="#7aaeff" opacity="0.3"/>
          </g>
          <ellipse cx="250" cy="295" rx="68" ry="26" fill="#1e3fa0" opacity="0.8"/>
          <circle cx="250" cy="268" r="9" fill="#4f7fe8" opacity="0.9"/>
          <circle cx="218" cy="278" r="9" fill="#4f7fe8" opacity="0.8"/>
          <circle cx="282" cy="278" r="9" fill="#4f7fe8" opacity="0.8"/>
          <circle cx="210" cy="300" r="9" fill="#6a93f5" opacity="0.7"/>
          <circle cx="290" cy="300" r="9" fill="#6a93f5" opacity="0.7"/>
          <circle cx="250" cy="322" r="9" fill="#4f7fe8" opacity="0.9"/>
          <circle cx="250" cy="264" r="5" fill="#c7d7f5"/>
          <circle cx="218" cy="274" r="5" fill="#c7d7f5"/>
          <circle cx="282" cy="274" r="5" fill="#c7d7f5"/>
          <rect x="390" y="270" width="70" height="50" rx="4" fill="#1e3fa0"/>
          <rect x="394" y="274" width="62" height="38" rx="2" fill="#2952c8"/>
          <polyline points="398,302 408,290 420,296 432,284 450,292" stroke="#5cffb0" stroke-width="1.8" fill="none" stroke-linecap="round"/>
        </svg>

        <p class="illus-tagline">Bienvenue dans le futur du travail.</p>

        <div class="already-account">
          Déjà un compte ?
          <NuxtLink to="/login">Se connecter</NuxtLink>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
* { box-sizing: border-box; margin: 0; padding: 0; }

.auth-wrapper {
  display: flex;
  min-height: 100vh;
  font-family: 'Inter', system-ui, sans-serif;
}

.auth-form-side {
  width: 42%;
  min-width: 340px;
  background: #fff;
  display: flex;
  flex-direction: column;
  padding: 36px 48px;
  box-shadow: 4px 0 30px rgba(79, 127, 232, 0.07);
}

.brand { display: flex; align-items: center; gap: 8px; margin-bottom: 44px; }
.brand-dot { width: 10px; height: 10px; background: #4f7fe8; border-radius: 50%; }
.brand-name { font-size: 15px; font-weight: 700; color: #1a2540; letter-spacing: 0.5px; }

.form-container { flex: 1; display: flex; flex-direction: column; justify-content: center; max-width: 360px; }

.form-title { font-size: 30px; font-weight: 700; color: #1a2540; margin-bottom: 8px; }
.form-subtitle { font-size: 13px; color: #8a97b0; margin-bottom: 28px; line-height: 1.5; }

.error-msg {
  background: #fff0f0;
  border-left: 3px solid #f87171;
  color: #c0392b;
  font-size: 13px;
  padding: 10px 14px;
  border-radius: 6px;
  margin-bottom: 18px;
}

.form { display: flex; flex-direction: column; gap: 16px; }
.field { display: flex; flex-direction: column; gap: 5px; }
.field label { font-size: 12px; font-weight: 600; color: #4a5568; letter-spacing: 0.3px; }

.field input {
  border: 1.5px solid #e2e8f4;
  border-radius: 8px;
  padding: 10px 14px;
  font-size: 14px;
  color: #1a2540;
  outline: none;
  transition: border-color 0.2s, box-shadow 0.2s;
  background: #fafbff;
}
.field input:focus {
  border-color: #4f7fe8;
  box-shadow: 0 0 0 3px rgba(79, 127, 232, 0.12);
  background: #fff;
}
.field input::placeholder { color: #b0bcd4; }

.password-wrap { position: relative; }
.password-wrap input { width: 100%; padding-right: 44px; }
.eye-btn {
  position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
  background: none; border: none; cursor: pointer; color: #8a97b0; padding: 4px; display: flex;
}
.eye-btn svg { width: 17px; height: 17px; }
.eye-btn:hover { color: #4f7fe8; }

.strength-bar { display: flex; align-items: center; gap: 10px; }
.strength-track { flex: 1; height: 4px; background: #e8eef8; border-radius: 2px; overflow: hidden; }
.strength-fill { height: 100%; border-radius: 2px; transition: width 0.3s, background 0.3s; }
.strength-bar span { font-size: 11px; font-weight: 600; min-width: 60px; }

.btn-primary {
  background: #4f7fe8; color: white; border: none; border-radius: 8px;
  padding: 13px; font-size: 14px; font-weight: 600; cursor: pointer;
  transition: background 0.2s, transform 0.1s, box-shadow 0.2s;
  display: flex; align-items: center; justify-content: center; margin-top: 4px;
}
.btn-primary:hover:not(:disabled) {
  background: #3a6ad4;
  box-shadow: 0 4px 16px rgba(79, 127, 232, 0.35);
  transform: translateY(-1px);
}
.btn-primary:disabled { opacity: 0.6; cursor: not-allowed; }

.spinner {
  width: 18px; height: 18px;
  border: 2px solid rgba(255,255,255,0.4); border-top-color: white;
  border-radius: 50%; animation: spin 0.7s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

.switch-link { text-align: center; margin-top: 24px; font-size: 13px; color: #8a97b0; }
.switch-link a { color: #4f7fe8; font-weight: 600; text-decoration: none; margin-left: 4px; }
.switch-link a:hover { text-decoration: underline; }

.auth-illustration-side {
  flex: 1; background: #2952c8;
  display: flex; align-items: center; justify-content: center;
  position: relative; overflow: hidden;
}

.iso-container {
  width: 100%; height: 100%;
  display: flex; flex-direction: column; align-items: center; justify-content: center;
  position: relative;
}

.illustration {
  width: 85%; max-width: 480px;
  filter: drop-shadow(0 20px 50px rgba(0,0,50,0.3));
}

.illus-tagline {
  position: absolute; bottom: 60px; left: 50%; transform: translateX(-50%);
  font-size: 13px; color: rgba(255,255,255,0.7); font-weight: 400; white-space: nowrap;
}

.already-account {
  position: absolute; bottom: 24px; right: 28px;
  background: rgba(255,255,255,0.12);
  border: 1px solid rgba(255,255,255,0.2);
  color: rgba(255,255,255,0.85); font-size: 12px;
  padding: 10px 16px; border-radius: 20px;
}
.already-account a { color: white; font-weight: 700; text-decoration: none; margin-left: 6px; }

@media (max-width: 768px) {
  .auth-wrapper { flex-direction: column; }
  .auth-form-side { width: 100%; padding: 32px 24px; }
  .auth-illustration-side { display: none; }
}
</style>