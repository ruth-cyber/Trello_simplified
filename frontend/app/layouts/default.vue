<template>
  <div class="app-shell">
    <!-- Sidebar -->
    <aside class="sidebar" :class="{ collapsed: sidebarCollapsed }">
      <div class="sidebar-header">
        <div class="brand">
          <div class="brand-icon">
            <svg viewBox="0 0 24 24" fill="none" width="18" height="18">
              <rect x="3" y="3" width="8" height="8" rx="2" fill="white" opacity="0.9"/>
              <rect x="13" y="3" width="8" height="8" rx="2" fill="white" opacity="0.6"/>
              <rect x="3" y="13" width="8" height="8" rx="2" fill="white" opacity="0.6"/>
              <rect x="13" y="13" width="8" height="8" rx="2" fill="white" opacity="0.9"/>
            </svg>
          </div>
          <span class="brand-name">TaskFlow</span>
        </div>
        <button class="collapse-btn" @click="sidebarCollapsed = !sidebarCollapsed">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
            <path d="M15 18l-6-6 6-6"/>
          </svg>
        </button>
      </div>

      <nav class="sidebar-nav">
        <NuxtLink to="/" class="nav-link" active-class="active">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="nav-icon">
            <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/>
            <rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>
          </svg>
          <span class="nav-label">Tableau de bord</span>
        </NuxtLink>

        <NuxtLink to="/projects" class="nav-link" active-class="active">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="nav-icon">
            <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
          </svg>
          <span class="nav-label">Mes Projets</span>
        </NuxtLink>

        <NuxtLink to="/profile" class="nav-link" active-class="active">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="nav-icon">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
            <circle cx="12" cy="7" r="4"/>
          </svg>
          <span class="nav-label">Mon Profil</span>
        </NuxtLink>
      </nav>

      <!-- User card en bas -->
      <div class="sidebar-footer">
        <div class="user-card">
          <div class="user-avatar">{{ initials }}</div>
          <div class="user-info">
            <p class="user-name-sm">{{ user?.name?.split(' ')[0] }}</p>
            <p class="user-role">Membre</p>
          </div>
        </div>
        <button @click="logout" class="logout-btn" title="Déconnexion">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
            <polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
          </svg>
        </button>
      </div>
    </aside>

    <!-- Main content -->
    <div class="main-wrapper">
      <!-- Topbar -->
      <header class="topbar">
        <button class="mobile-menu-btn" @click="sidebarCollapsed = !sidebarCollapsed">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">
            <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
          </svg>
        </button>

        <div class="topbar-right">
          <div class="topbar-avatar">{{ initials }}</div>
          <div>
            <p class="topbar-name">{{ user?.name }}</p>
            <p class="topbar-sub">Connecté</p>
          </div>
        </div>
      </header>

      <main class="page-content">
        <slot />
      </main>
    </div>
    <ToastContainer />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import ToastContainer from '~/components/ToastContainer.vue'
const { logout, user } = useAuth()
const sidebarCollapsed = ref(false)

const initials = computed(() => {
  const n = user.value?.name || '?'
  return n.split(' ').map(w => w[0]).join('').slice(0, 2).toUpperCase()
})
</script>

<style scoped>
* { box-sizing: border-box; margin: 0; padding: 0; }

.app-shell {
  display: flex;
  min-height: 100vh;
  background: #f0f4ff;
  font-family: 'Inter', system-ui, sans-serif;
}

/* ── SIDEBAR ── */
.sidebar {
  width: 240px;
  background: #1e2a4a;
  display: flex;
  flex-direction: column;
  flex-shrink: 0;
  transition: width 0.25s ease;
  overflow: hidden;
  position: sticky;
  top: 0;
  height: 100vh;
}
.sidebar.collapsed { width: 68px; }

.sidebar-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 20px 16px;
  border-bottom: 1px solid rgba(255,255,255,0.07);
}

.brand { display: flex; align-items: center; gap: 10px; overflow: hidden; }
.brand-icon {
  width: 34px;
  height: 34px;
  background: #4f7fe8;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.brand-name {
  font-size: 16px;
  font-weight: 700;
  color: white;
  white-space: nowrap;
}
.sidebar.collapsed .brand-name { display: none; }

.collapse-btn {
  background: none;
  border: none;
  color: rgba(255,255,255,0.4);
  cursor: pointer;
  padding: 4px;
  flex-shrink: 0;
  display: flex;
  transition: color 0.15s;
}
.collapse-btn:hover { color: white; }
.sidebar.collapsed .collapse-btn svg { transform: rotate(180deg); }

/* Nav */
.sidebar-nav {
  flex: 1;
  padding: 16px 10px;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.nav-link {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 12px;
  border-radius: 10px;
  color: rgba(255,255,255,0.55);
  text-decoration: none;
  font-size: 14px;
  font-weight: 500;
  transition: background 0.15s, color 0.15s;
  white-space: nowrap;
  overflow: hidden;
}
.nav-link:hover { background: rgba(255,255,255,0.07); color: rgba(255,255,255,0.9); }
.nav-link.active { background: #4f7fe8; color: white; }

.nav-icon { width: 18px; height: 18px; flex-shrink: 0; }
.nav-label { overflow: hidden; }
.sidebar.collapsed .nav-label { display: none; }

/* Footer */
.sidebar-footer {
  padding: 12px 10px;
  border-top: 1px solid rgba(255,255,255,0.07);
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.user-card { display: flex; align-items: center; gap: 10px; overflow: hidden; }
.user-avatar {
  width: 34px;
  height: 34px;
  background: linear-gradient(135deg, #4f7fe8, #7b9de0);
  border-radius: 50%;
  color: white;
  font-size: 13px;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.user-info { overflow: hidden; }
.sidebar.collapsed .user-info { display: none; }
.user-name-sm { font-size: 13px; font-weight: 600; color: white; white-space: nowrap; }
.user-role { font-size: 11px; color: rgba(255,255,255,0.4); }

.logout-btn {
  background: none;
  border: none;
  color: rgba(255,255,255,0.4);
  cursor: pointer;
  padding: 6px;
  border-radius: 8px;
  display: flex;
  transition: color 0.15s, background 0.15s;
  flex-shrink: 0;
}
.logout-btn:hover { color: #f87171; background: rgba(248,113,113,0.1); }
.sidebar.collapsed .logout-btn { display: none; }

/* ── MAIN ── */
.main-wrapper {
  flex: 1;
  display: flex;
  flex-direction: column;
  min-width: 0;
}

.topbar {
  background: white;
  border-bottom: 1px solid #e8eef8;
  padding: 14px 28px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  position: sticky;
  top: 0;
  z-index: 10;
}

.mobile-menu-btn {
  display: none;
  background: none;
  border: none;
  cursor: pointer;
  color: #6b7a9a;
  padding: 4px;
}

.topbar-right {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-left: auto;
}
.topbar-avatar {
  width: 36px;
  height: 36px;
  background: linear-gradient(135deg, #4f7fe8, #7b9de0);
  border-radius: 50%;
  color: white;
  font-size: 13px;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
}
.topbar-name { font-size: 13px; font-weight: 600; color: #1a2540; }
.topbar-sub { font-size: 11px; color: #8a97b0; }

.page-content { flex: 1; padding: 28px 32px; }

/* Responsive */
@media (max-width: 768px) {
  .sidebar { position: fixed; z-index: 100; height: 100vh; transform: translateX(0); }
  .sidebar.collapsed { transform: translateX(-100%); width: 240px; }
  .mobile-menu-btn { display: flex; }
  .page-content { padding: 20px 16px; }
  .topbar { padding: 12px 16px; }
}
</style>