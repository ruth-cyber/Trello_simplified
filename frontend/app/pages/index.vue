<script setup>
import { ref, onMounted, computed, nextTick } from 'vue'

useHead({
  script: [
    {
      src: 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js'
    }
  ]
})
definePageMeta({ middleware: 'auth' })
const { user, fetchWithAuth } = useAuth()

const stats = ref({ projects: 0, tasks: 0, done: 0, inProgress: 0 })
const recentProjects = ref([])
const tasksByPriority = ref({ high: 0, medium: 0, low: 0 })
const loading = ref(true)
const donutCanvas = ref(null)
const barCanvas = ref(null)
let donutChartInstance = null
let barChartInstance = null

const greeting = computed(() => {
  const h = new Date().getHours()
  if (h < 12) return 'Bonjour'
  if (h < 18) return 'Bon après-midi'
  return 'Bonsoir'
})

const firstName = computed(() => user.value?.name?.split(' ')[0] || 'là')

const donePercent = computed(() =>
  stats.value.tasks ? Math.round((stats.value.done / stats.value.tasks) * 100) : 0
)

const todoTasks = computed(() =>
  Math.max(stats.value.tasks - stats.value.done - stats.value.inProgress, 0)
)

const renderCharts = async () => {
  await nextTick()
  if (typeof Chart === 'undefined') {
    setTimeout(renderCharts, 100)
    return
  }

  if (donutChartInstance) {
    donutChartInstance.destroy()
    donutChartInstance = null
  }
  if (barChartInstance) {
    barChartInstance.destroy()
    barChartInstance = null
  }

  if (donutCanvas.value) {
    donutChartInstance = new Chart(donutCanvas.value, {
      type: 'doughnut',
      data: {
        labels: ['À faire', 'En cours', 'Terminées'],
        datasets: [{
          data: [todoTasks.value, stats.value.inProgress, stats.value.done],
          backgroundColor: ['#4f7fe8', '#f59e0b', '#10b981'],
          borderWidth: 0,
          hoverOffset: 6
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '68%',
        plugins: {
          legend: { display: false },
          tooltip: {
            callbacks: {
              label: (ctx) => ` ${ctx.label} : ${ctx.raw} tâche(s)`
            }
          }
        }
      }
    })
  }

  if (barCanvas.value) {
    barChartInstance = new Chart(barCanvas.value, {
      type: 'bar',
      data: {
        labels: ['Haute', 'Moyenne', 'Faible'],
        datasets: [{
          data: [
            tasksByPriority.value.high,
            tasksByPriority.value.medium,
            tasksByPriority.value.low
          ],
          backgroundColor: ['#fecaca', '#fde68a', '#a7f3d0'],
          borderColor: ['#f87171', '#fbbf24', '#34d399'],
          borderWidth: 2,
          borderRadius: 8,
          borderSkipped: false
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          x: {
            grid: { display: false },
            ticks: { font: { size: 12 }, color: '#8a97b0' },
            border: { display: false }
          },
          y: {
            grid: { color: '#f0f4ff', drawBorder: false },
            ticks: { font: { size: 12 }, color: '#8a97b0', stepSize: 1 },
            border: { display: false },
            beginAtZero: true
          }
        }
      }
    })
  }
}

const initData = async () => {
  try {
    const [projects, tasks] = await Promise.all([
      fetchWithAuth('/projects'),
      fetchWithAuth('/tasks')
    ])

    stats.value.projects = projects.length
    stats.value.tasks = tasks.length
    stats.value.done = tasks.filter(t => t.status === 'done').length
    stats.value.inProgress = tasks.filter(t => t.status === 'in_progress').length
    tasksByPriority.value.high = tasks.filter(t => t.priority === 'high').length
    tasksByPriority.value.medium = tasks.filter(t => t.priority === 'medium').length
    tasksByPriority.value.low = tasks.filter(t => t.priority === 'low').length
    recentProjects.value = projects.slice(0, 4)

    await renderCharts()
  } catch (err) {
    console.error(err)
  } finally {
    loading.value = false
  }
}

onMounted(initData)
</script>

<template>
  <NuxtLayout>
    <!-- ══ BANNER DE BIENVENUE ══ -->
    <div class="welcome-banner">
      <div class="welcome-text">
        <p class="welcome-greeting">{{ greeting }}, <strong>{{ firstName }}</strong> 👋</p>
        <p class="welcome-sub">Voici un aperçu de votre activité aujourd'hui.</p>
        <NuxtLink to="/projects" class="banner-cta">Voir mes projets →</NuxtLink>
      </div>
      <div class="banner-illustration" aria-hidden="true">
        <svg viewBox="0 0 180 120" fill="none" xmlns="http://www.w3.org/2000/svg">
          <circle cx="140" cy="30" r="28" fill="rgba(255,255,255,0.12)"/>
          <circle cx="30" cy="90" r="18" fill="rgba(255,255,255,0.08)"/>
          <rect x="40" y="20" width="80" height="55" rx="8" fill="rgba(255,255,255,0.15)" stroke="rgba(255,255,255,0.3)" stroke-width="1"/>
          <rect x="50" y="30" width="30" height="5" rx="2.5" fill="rgba(255,255,255,0.6)"/>
          <rect x="50" y="40" width="50" height="5" rx="2.5" fill="rgba(255,255,255,0.4)"/>
          <rect x="50" y="50" width="40" height="5" rx="2.5" fill="rgba(255,255,255,0.4)"/>
          <circle cx="100" cy="55" r="18" fill="rgba(255,255,255,0.1)" stroke="rgba(255,255,255,0.3)" stroke-width="4"
            stroke-dasharray="28 57" stroke-dashoffset="0"/>
          <text x="97" y="59" text-anchor="middle" fill="white" font-size="10" font-weight="700">{{ donePercent }}%</text>
        </svg>
      </div>
    </div>

    <!-- ══ STAT CARDS ══ -->
    <div class="stats-grid" v-if="!loading">
      <div class="stat-card">
        <div class="stat-icon blue">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">
            <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
          </svg>
        </div>
        <div>
          <p class="stat-value">{{ stats.projects }}</p>
          <p class="stat-label">Projets</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon purple">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">
            <path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
          </svg>
        </div>
        <div>
          <p class="stat-value">{{ stats.tasks }}</p>
          <p class="stat-label">Tâches totales</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon green">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">
            <polyline points="20 6 9 17 4 12"/>
          </svg>
        </div>
        <div>
          <p class="stat-value">{{ stats.done }}</p>
          <p class="stat-label">Terminées</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon amber">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">
            <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
          </svg>
        </div>
        <div>
          <p class="stat-value">{{ stats.inProgress }}</p>
          <p class="stat-label">En cours</p>
        </div>
      </div>
    </div>

    <!-- Skeleton loader -->
    <div class="stats-grid" v-else>
      <div class="stat-card skeleton" v-for="i in 4" :key="i"></div>
    </div>

    <!-- ══ CHARTS + PROJETS RÉCENTS ══ -->
    <div class="bottom-grid">

      <!-- Chart 1 : Statut des tâches (Donut) -->
      <div class="chart-card">
        <div class="card-header">
          <h3>Avancement des tâches</h3>
        </div>
        <div class="donut-wrap">
          <div style="position:relative; width:100%; height:220px;">
            <canvas ref="donutCanvas" role="img" aria-label="Répartition des tâches par statut"></canvas>
          </div>
          <div class="donut-legend">
            <div class="legend-item">
              <span class="legend-dot" style="background:#4f7fe8"></span>
              <span>À faire</span>
              <strong>{{ todoTasks }}</strong>
            </div>
            <div class="legend-item">
              <span class="legend-dot" style="background:#f59e0b"></span>
              <span>En cours</span>
              <strong>{{ stats.inProgress }}</strong>
            </div>
            <div class="legend-item">
              <span class="legend-dot" style="background:#10b981"></span>
              <span>Terminées</span>
              <strong>{{ stats.done }}</strong>
            </div>
          </div>
        </div>
      </div>

      <!-- Chart 2 : Priorités (Barres horizontales) -->
      <div class="chart-card">
        <div class="card-header">
          <h3>Tâches par priorité</h3>
        </div>
        <div style="position:relative; width:100%; height:220px;">
          <canvas ref="barCanvas" role="img" aria-label="Nombre de tâches par niveau de priorité"></canvas>
        </div>
        <div class="donut-legend" style="margin-top:12px;">
          <div class="legend-item">
            <span class="legend-dot" style="background:#f87171"></span>
            <span>Haute</span>
            <strong>{{ tasksByPriority.high }}</strong>
          </div>
          <div class="legend-item">
            <span class="legend-dot" style="background:#fbbf24"></span>
            <span>Moyenne</span>
            <strong>{{ tasksByPriority.medium }}</strong>
          </div>
          <div class="legend-item">
            <span class="legend-dot" style="background:#34d399"></span>
            <span>Faible</span>
            <strong>{{ tasksByPriority.low }}</strong>
          </div>
        </div>
      </div>

      <!-- Projets récents -->
      <div class="chart-card recent-projects">
        <div class="card-header">
          <h3>Projets récents</h3>
          <NuxtLink to="/projects" class="view-all-link">Voir tout →</NuxtLink>
        </div>

        <div v-if="recentProjects.length === 0 && !loading" class="empty-state">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="36" height="36">
            <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
          </svg>
          <p>Aucun projet pour l'instant</p>
          <NuxtLink to="/projects">Créer un projet</NuxtLink>
        </div>

        <div class="project-list">
          <NuxtLink
            v-for="project in recentProjects"
            :key="project.id"
            :to="`/projects/${project.id}`"
            class="project-row"
          >
            <div class="project-avatar">{{ project.title?.slice(0, 2).toUpperCase() }}</div>
            <div class="project-info">
              <p class="project-name">{{ project.title }}</p>
              <p class="project-desc">{{ project.description || 'Aucune description' }}</p>
            </div>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16" class="project-arrow">
              <path d="M9 18l6-6-6-6"/>
            </svg>
          </NuxtLink>
        </div>
      </div>
    </div>

  </NuxtLayout>
</template>

<script>
export default {
  mounted() {
    this.$nextTick(() => {
      setTimeout(() => {
        const stats = this.stats
        const tasksByPriority = this.tasksByPriority

        const todo = stats.tasks - stats.done - stats.inProgress

        // Donut chart
        const donutCtx = document.getElementById('donutChart')
        if (donutCtx) {
          new Chart(donutCtx, {
            type: 'doughnut',
            data: {
              labels: ['À faire', 'En cours', 'Terminées'],
              datasets: [{
                data: [todo < 0 ? 0 : todo, stats.inProgress, stats.done],
                backgroundColor: ['#4f7fe8', '#f59e0b', '#10b981'],
                borderWidth: 0,
                hoverOffset: 6
              }]
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              cutout: '68%',
              plugins: {
                legend: { display: false },
                tooltip: {
                  callbacks: {
                    label: (ctx) => ` ${ctx.label} : ${ctx.raw} tâche(s)`
                  }
                }
              }
            }
          })
        }

        // Bar chart
        const barCtx = document.getElementById('barChart')
        if (barCtx) {
          barChartInstance = new Chart(barCtx, {
            type: 'bar',
            data: {
              labels: ['Haute', 'Moyenne', 'Faible'],
              datasets: [{
                data: [
                  tasksByPriority.value.high,
                  tasksByPriority.value.medium,
                  tasksByPriority.value.low
                ],
                backgroundColor: ['#fecaca', '#fde68a', '#a7f3d0'],
                borderColor: ['#f87171', '#fbbf24', '#34d399'],
                borderWidth: 2,
                borderRadius: 8,
                borderSkipped: false
              }]
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              plugins: { legend: { display: false } },
              scales: {
                x: {
                  grid: { display: false },
                  ticks: { font: { size: 12 }, color: '#8a97b0' },
                  border: { display: false }
                },
                y: {
                  grid: { color: '#f0f4ff', drawBorder: false },
                  ticks: { font: { size: 12 }, color: '#8a97b0', stepSize: 1 },
                  border: { display: false },
                  beginAtZero: true
                }
              }
            }
          })
        }
      })
    })
  }
};
</script>

<style scoped>
* { box-sizing: border-box; margin: 0; padding: 0; }

/* ── BANNER ── */
.welcome-banner {
  background: linear-gradient(135deg, #3a6ad4 0%, #4f7fe8 60%, #7b9de0 100%);
  border-radius: 16px;
  padding: 28px 32px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 24px;
  overflow: hidden;
  position: relative;
}

.welcome-greeting {
  font-size: 22px;
  color: white;
  font-weight: 400;
  margin-bottom: 6px;
}
.welcome-greeting strong { font-weight: 700; }

.welcome-sub { font-size: 13px; color: rgba(255,255,255,0.75); margin-bottom: 18px; }

.banner-cta {
  display: inline-block;
  background: rgba(255,255,255,0.2);
  backdrop-filter: blur(8px);
  border: 1px solid rgba(255,255,255,0.35);
  color: white;
  font-size: 13px;
  font-weight: 600;
  padding: 9px 20px;
  border-radius: 8px;
  text-decoration: none;
  transition: background 0.2s;
}
.banner-cta:hover { background: rgba(255,255,255,0.3); }

.banner-illustration { width: 160px; flex-shrink: 0; opacity: 0.9; }

/* ── STATS ── */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
  margin-bottom: 24px;
}

.stat-card {
  background: white;
  border-radius: 14px;
  padding: 20px;
  display: flex;
  align-items: center;
  gap: 16px;
  box-shadow: 0 2px 8px rgba(79,127,232,0.07);
  border: 1px solid #eef2fc;
  transition: transform 0.15s, box-shadow 0.15s;
}
.stat-card:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(79,127,232,0.12); }

.stat-card.skeleton {
  height: 80px;
  background: linear-gradient(90deg, #f0f4ff 25%, #e8eef8 50%, #f0f4ff 75%);
  background-size: 200% 100%;
  animation: shimmer 1.5s infinite;
}
@keyframes shimmer { to { background-position: -200% 0; } }

.stat-icon {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.stat-icon.blue { background: #eef3ff; color: #4f7fe8; }
.stat-icon.purple { background: #f0ebff; color: #7c5cbf; }
.stat-icon.green { background: #ecfdf5; color: #10b981; }
.stat-icon.amber { background: #fffbeb; color: #f59e0b; }

.stat-value { font-size: 26px; font-weight: 700; color: #1a2540; line-height: 1; }
.stat-label { font-size: 12px; color: #8a97b0; margin-top: 4px; font-weight: 500; }

/* ── BOTTOM GRID ── */
.bottom-grid {
  display: grid;
  grid-template-columns: 1fr 1fr 1.2fr;
  gap: 20px;
}

.chart-card {
  background: white;
  border-radius: 16px;
  padding: 24px;
  box-shadow: 0 2px 8px rgba(79,127,232,0.07);
  border: 1px solid #eef2fc;
}

.card-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 20px;
}
.card-header h3 { font-size: 15px; font-weight: 700; color: #1a2540; }

.view-all-link {
  font-size: 12px;
  color: #4f7fe8;
  text-decoration: none;
  font-weight: 600;
}
.view-all-link:hover { text-decoration: underline; }

/* Donut */
.donut-wrap { }
.donut-legend {
  display: flex;
  flex-direction: column;
  gap: 8px;
  margin-top: 16px;
}
.legend-item {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  color: #6b7a9a;
}
.legend-item strong { margin-left: auto; color: #1a2540; font-weight: 700; }
.legend-dot {
  width: 10px;
  height: 10px;
  border-radius: 3px;
  flex-shrink: 0;
}

/* Projects list */
.empty-state {
  text-align: center;
  padding: 32px 16px;
  color: #8a97b0;
}
.empty-state svg { margin: 0 auto 12px; display: block; color: #c7d7f5; }
.empty-state p { font-size: 14px; margin-bottom: 10px; }
.empty-state a { color: #4f7fe8; font-weight: 600; font-size: 13px; }

.project-list { display: flex; flex-direction: column; gap: 4px; }

.project-row {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 12px;
  border-radius: 10px;
  text-decoration: none;
  transition: background 0.15s;
  flex-wrap: wrap;
  color: #4f7fe8;
  font-size: 12px;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  border: 1px solid #d6e4ff;
}
.project-info { flex: 1; min-width: 0; }
.project-name { font-size: 13px; font-weight: 600; color: #1a2540; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.project-desc { font-size: 11px; color: #8a97b0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-top: 2px; }
.project-arrow { color: #c7d7f5; flex-shrink: 0; }

/* Responsive */
@media (max-width: 1200px) {
  .stats-grid { grid-template-columns: repeat(2, 1fr); }
  .bottom-grid { grid-template-columns: 1fr 1fr; }
  .recent-projects { grid-column: span 2; }
  .welcome-banner { flex-direction: column; align-items: flex-start; gap: 20px; }
  .banner-illustration { width: 140px; }
}
@media (max-width: 900px) {
  .stats-grid { grid-template-columns: 1fr; }
  .bottom-grid { grid-template-columns: 1fr; }
  .recent-projects { grid-column: span 1; }
  .donut-legend { flex-direction: row; flex-wrap: wrap; gap: 10px; }
  .legend-item { min-width: calc(50% - 10px); }
}
@media (max-width: 640px) {
  .stats-grid { grid-template-columns: 1fr; }
  .bottom-grid { grid-template-columns: 1fr; }
  .recent-projects { grid-column: span 1; }
  .welcome-banner { flex-direction: column; gap: 16px; }
  .banner-illustration { width: 120px; }
}
</style>