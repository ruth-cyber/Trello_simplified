<script setup>
definePageMeta({ layout: 'auth' })
import { ref } from 'vue'
const { login } = useAuth()
const form = ref({ email: '', password: '' })
const errorMessage = ref('')
const showPassword = ref(false)
const loading = ref(false)

const handleLogin = async () => {
  loading.value = true
  try {
    await login(form.value)
  } catch (err) {
    errorMessage.value = err.message || 'Identifiants incorrects'
  } finally {
    loading.value = false
  }
}
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
        <h1 class="form-title">Connexion</h1>
        <p class="form-subtitle">Bienvenue ! Connectez-vous pour gérer vos projets.</p>

        <p v-if="errorMessage" class="error-msg">{{ errorMessage }}</p>

        <form @submit.prevent="handleLogin" class="form">
          <div class="field">
            <label>Email</label>
            <input
              v-model="form.email"
              type="email"
              placeholder="exemple@email.com"
              required
            />
          </div>

          <div class="field">
            <label>Mot de passe</label>
            <div class="password-wrap">
              <input
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                placeholder="Entrez votre mot de passe"
                required
              />
              <button type="button" class="eye-btn" @click="showPassword = !showPassword">
                <svg v-if="!showPassword" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                  <circle cx="12" cy="12" r="3"/>
                </svg>
                <svg v-else viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
                  <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
                  <line x1="1" y1="1" x2="23" y2="23"/>
                </svg>
              </button>
            </div>
          </div>

          <button type="submit" class="btn-primary" :disabled="loading">
            <span v-if="loading" class="spinner"></span>
            <span v-else>Se connecter</span>
          </button>
        </form>

        <p class="switch-link">
          Pas encore de compte ?
          <NuxtLink to="/register">S'inscrire</NuxtLink>
        </p>
      </div>
    </div>

    <!-- Côté droit : illustration -->
    <div class="auth-illustration-side">
      <!-- Formes de fond -->
      <div class="blob blob-1"></div>
      <div class="blob blob-2"></div>
      <div class="blob blob-3"></div>

      <!-- Illustration SVG centrale (dashboard + personnages) -->
      <svg class="illustration" viewBox="0 0 520 420" fill="none" xmlns="http://www.w3.org/2000/svg">
        <!-- Dashboard card -->
        <rect x="100" y="60" width="290" height="210" rx="14" fill="white" fill-opacity="0.7" stroke="#c7d7f5" stroke-width="1.5"/>

        <!-- Sidebar dots -->
        <circle cx="126" cy="100" r="6" fill="#a0b4e8"/>
        <circle cx="126" cy="122" r="6" fill="#7b9de0"/>
        <circle cx="126" cy="144" r="6" fill="#c7d7f5"/>
        <circle cx="126" cy="166" r="6" fill="#c7d7f5"/>

        <!-- Barres de progression -->
        <rect x="150" y="94" width="80" height="8" rx="4" fill="#c7d7f5"/>
        <rect x="150" y="94" width="50" height="8" rx="4" fill="#7b9de0"/>
        <rect x="150" y="110" width="80" height="8" rx="4" fill="#c7d7f5"/>
        <rect x="150" y="110" width="65" height="8" rx="4" fill="#7b9de0"/>
        <rect x="150" y="126" width="80" height="8" rx="4" fill="#c7d7f5"/>
        <rect x="150" y="126" width="30" height="8" rx="4" fill="#a0b4e8"/>
        <rect x="150" y="142" width="80" height="8" rx="4" fill="#c7d7f5"/>
        <rect x="150" y="142" width="55" height="8" rx="4" fill="#7b9de0"/>

        <!-- Donut chart -->
        <circle cx="310" cy="140" r="46" stroke="#e8eef8" stroke-width="18" fill="none"/>
        <circle cx="310" cy="140" r="46" stroke="#4f7fe8" stroke-width="18" fill="none"
          stroke-dasharray="145 145" stroke-dashoffset="36" stroke-linecap="round"/>
        <circle cx="310" cy="140" r="46" stroke="#a0c4ff" stroke-width="18" fill="none"
          stroke-dasharray="72 218" stroke-dashoffset="-109" stroke-linecap="round"/>
        <text x="310" y="146" text-anchor="middle" fill="#4f7fe8" font-size="13" font-weight="700">68%</text>

        <!-- Ligne du bas -->
        <rect x="150" y="200" width="200" height="8" rx="4" fill="#e8eef8"/>
        <rect x="150" y="218" width="140" height="8" rx="4" fill="#e8eef8"/>
        <rect x="150" y="236" width="170" height="8" rx="4" fill="#e8eef8"/>

        <!-- Petit graphe bar en bas à gauche (tenu par personnage) -->
        <rect x="60" y="220" width="80" height="70" rx="10" fill="white" fill-opacity="0.8" stroke="#c7d7f5" stroke-width="1"/>
        <rect x="75" y="255" width="10" height="25" rx="3" fill="#7b9de0"/>
        <rect x="90" y="245" width="10" height="35" rx="3" fill="#4f7fe8"/>
        <rect x="105" y="260" width="10" height="20" rx="3" fill="#a0b4e8"/>
        <rect x="120" y="238" width="10" height="42" rx="3" fill="#4f7fe8"/>

        <!-- Personnage gauche (femme) -->
        <!-- corps -->
        <ellipse cx="105" cy="310" rx="18" ry="26" fill="#b8caf0"/>
        <!-- tête -->
        <circle cx="105" cy="278" r="14" fill="#f5c5a3"/>
        <!-- cheveux -->
        <path d="M91 275 Q105 260 119 275 Q119 270 105 265 Q91 270 91 275Z" fill="#3a3a5c"/>
        <!-- jambes -->
        <rect x="97" y="334" width="8" height="22" rx="4" fill="#3a3a5c"/>
        <rect x="108" y="334" width="8" height="22" rx="4" fill="#3a3a5c"/>
        <!-- chaussures -->
        <ellipse cx="101" cy="358" rx="8" ry="4" fill="#2d2d4a"/>
        <ellipse cx="112" cy="358" rx="8" ry="4" fill="#2d2d4a"/>
        <!-- bras gauche tenant la carte -->
        <path d="M87 300 Q70 310 62 290" stroke="#b8caf0" stroke-width="8" stroke-linecap="round" fill="none"/>

        <!-- Personnage droit (homme) -->
        <ellipse cx="390" cy="310" rx="16" ry="24" fill="#7b9de0"/>
        <circle cx="390" cy="280" r="13" fill="#f5c5a3"/>
        <path d="M377 277 Q390 264 403 277 Q402 272 390 267 Q378 272 377 277Z" fill="#2d2d4a"/>
        <rect x="382" y="332" width="8" height="20" rx="4" fill="#2d2d4a"/>
        <rect x="393" y="332" width="8" height="20" rx="4" fill="#2d2d4a"/>
        <ellipse cx="386" cy="354" rx="8" ry="4" fill="#1a1a35"/>
        <ellipse cx="397" cy="354" rx="8" ry="4" fill="#1a1a35"/>
        <!-- bras droit tenant quelque chose -->
        <path d="M406 298 Q420 290 425 278" stroke="#7b9de0" stroke-width="7" stroke-linecap="round" fill="none"/>

        <!-- Petite boule décorative flottante -->
        <circle cx="430" cy="85" r="18" fill="url(#ballGrad)" opacity="0.7"/>
        <defs>
          <radialGradient id="ballGrad" cx="35%" cy="35%">
            <stop offset="0%" stop-color="#e8f0ff"/>
            <stop offset="100%" stop-color="#7b9de0"/>
          </radialGradient>
        </defs>

        <!-- Petite boule verte -->
        <circle cx="380" cy="48" r="9" fill="#c7d7f5" opacity="0.8"/>

        <!-- Colonnes bleues décoratives sol -->
        <rect x="270" y="298" width="14" height="40" rx="4" fill="#7b9de0" opacity="0.6"/>
        <rect x="290" y="310" width="14" height="28" rx="4" fill="#4f7fe8" opacity="0.5"/>
        <rect x="310" y="304" width="14" height="34" rx="4" fill="#a0b4e8" opacity="0.5"/>
      </svg>

      <!-- Tagline -->
      <p class="illus-tagline">Gérez vos projets avec clarté</p>
    </div>
  </div>
</template>

<style scoped>
* { box-sizing: border-box; margin: 0; padding: 0; }

.auth-wrapper {
  display: flex;
  min-height: 100vh;
  font-family: 'Inter', system-ui, sans-serif;
  background: #f0f4ff;
}

/* ── Côté gauche ── */
.auth-form-side {
  width: 42%;
  min-width: 340px;
  background: #fff;
  display: flex;
  flex-direction: column;
  padding: 40px 48px;
  box-shadow: 4px 0 30px rgba(79, 127, 232, 0.07);
}

.brand {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 60px;
}
.brand-dot {
  width: 10px;
  height: 10px;
  background: #4f7fe8;
  border-radius: 50%;
}
.brand-name {
  font-size: 15px;
  font-weight: 700;
  color: #1a2540;
  letter-spacing: 0.5px;
}

.form-container {
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: center;
  max-width: 360px;
}

.form-title {
  font-size: 32px;
  font-weight: 700;
  color: #1a2540;
  margin-bottom: 8px;
}
.form-subtitle {
  font-size: 13px;
  color: #8a97b0;
  margin-bottom: 32px;
  line-height: 1.5;
}

.error-msg {
  background: #fff0f0;
  border-left: 3px solid #f87171;
  color: #c0392b;
  font-size: 13px;
  padding: 10px 14px;
  border-radius: 6px;
  margin-bottom: 20px;
}

.form { display: flex; flex-direction: column; gap: 20px; }

.field { display: flex; flex-direction: column; gap: 6px; }

.field label {
  font-size: 12px;
  font-weight: 600;
  color: #4a5568;
  letter-spacing: 0.3px;
}

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
  box-shadow: 0 0 0 3px rgba(79, 127, 232, 0.12);
  background: #fff;
}
.field input::placeholder { color: #b0bcd4; }

.password-wrap { position: relative; }
.password-wrap input { width: 100%; padding-right: 44px; }
.eye-btn {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  cursor: pointer;
  color: #8a97b0;
  padding: 4px;
  display: flex;
  align-items: center;
}
.eye-btn svg { width: 18px; height: 18px; }
.eye-btn:hover { color: #4f7fe8; }

.btn-primary {
  background: #4f7fe8;
  color: white;
  border: none;
  border-radius: 8px;
  padding: 13px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s, transform 0.1s, box-shadow 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-top: 6px;
  letter-spacing: 0.3px;
}
.btn-primary:hover:not(:disabled) {
  background: #3a6ad4;
  box-shadow: 0 4px 16px rgba(79, 127, 232, 0.35);
  transform: translateY(-1px);
}
.btn-primary:disabled { opacity: 0.6; cursor: not-allowed; }

.spinner {
  width: 18px;
  height: 18px;
  border: 2px solid rgba(255,255,255,0.4);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

.switch-link {
  text-align: center;
  margin-top: 28px;
  font-size: 13px;
  color: #8a97b0;
}
.switch-link a {
  color: #4f7fe8;
  font-weight: 600;
  text-decoration: none;
  margin-left: 4px;
}
.switch-link a:hover { text-decoration: underline; }

/* ── Côté droit illustration ── */
.auth-illustration-side {
  flex: 1;
  background: linear-gradient(135deg, #eef3ff 0%, #dce8ff 100%);
  position: relative;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}

.blob {
  position: absolute;
  border-radius: 50%;
  opacity: 0.45;
}
.blob-1 {
  width: 380px;
  height: 320px;
  background: radial-gradient(circle, #b8cef5 0%, #8aaee8 100%);
  border-radius: 60% 40% 70% 30% / 50% 60% 40% 50%;
  top: -60px;
  right: -80px;
}
.blob-2 {
  width: 260px;
  height: 240px;
  background: radial-gradient(circle, #c7d7f5 0%, #9ab5e8 100%);
  border-radius: 40% 60% 30% 70% / 60% 40% 50% 50%;
  bottom: 20px;
  left: -40px;
}
.blob-3 {
  width: 160px;
  height: 140px;
  background: #d4e3ff;
  border-radius: 50%;
  top: 40%;
  left: 10%;
  opacity: 0.3;
}

.illustration {
  width: 82%;
  max-width: 500px;
  position: relative;
  z-index: 2;
  filter: drop-shadow(0 16px 40px rgba(79, 127, 232, 0.18));
}

.illus-tagline {
  position: absolute;
  bottom: 40px;
  font-size: 13px;
  color: #6b84b8;
  font-weight: 500;
  letter-spacing: 0.3px;
  z-index: 2;
}

/* ── Responsive ── */
@media (max-width: 768px) {
  .auth-wrapper { flex-direction: column; }
  .auth-form-side { width: 100%; padding: 32px 24px; }
  .auth-illustration-side { display: none; }
}
</style>