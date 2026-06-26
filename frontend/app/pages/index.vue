<script setup lang="ts">
import type { Project, Task, TaskStatus } from '~/types'
import { useDashboardStore } from '~/pages/dashboard/dashboard'

const authStore = useAuthStore()
const dashboardStore = useDashboardStore()
const projectsStore = useProjectsStore()
const tasksStore = useTasksStore()

type TabId = 'overview' | 'projects' | 'tasks'

const tabs: { id: TabId; label: string }[] = [
  { id: 'overview', label: "Vue d'ensemble" },
  { id: 'projects', label: 'Projets' },
  { id: 'tasks', label: 'Tâches' },
]

const activeTab = ref<TabId>('overview')

const showProjectForm = ref(false)
const editingProject = ref<Project | null>(null)

const showTaskForm = ref(false)
const editingTask = ref<Task | null>(null)

const taskSearch = ref('')
const taskStatusFilter = ref('')
const taskPriorityFilter = ref('')

onMounted(() => {
  dashboardStore.fetchStats()
  projectsStore.fetchProjects()
  tasksStore.fetchAllTasks()
})

const priorityTotal = computed(() => {
  const priority = dashboardStore.stats?.tasks_by_priority
  if (!priority) return 0
  return priority.low + priority.medium + priority.high
})

function priorityPercent(count: number): number {
  if (priorityTotal.value === 0) return 0
  return Math.round((count / priorityTotal.value) * 100)
}

function openCreateProjectForm(): void {
  editingProject.value = null
  showProjectForm.value = true
}

function openEditProjectForm(project: Project): void {
  editingProject.value = project
  showProjectForm.value = true
}

function closeProjectForm(): void {
  showProjectForm.value = false
  editingProject.value = null
}

async function handleProjectSaved(): Promise<void> {
  closeProjectForm()
  await dashboardStore.fetchStats()
}

async function handleDeleteProject(project: Project): Promise<void> {
  if (!confirm(`Supprimer le projet "${project.name}" ?`)) return

  try {
    await projectsStore.deleteProject(project.id)
    await dashboardStore.fetchStats()
  } catch (error) {
    const fetchError = error as { data?: { message?: string } }
    alert(fetchError.data?.message ?? 'Impossible de supprimer ce projet.')
  }
}

const filteredTasks = computed(() => {
  return tasksStore.allTasks.filter((task) => {
    const matchesSearch = !taskSearch.value
      || task.title.toLowerCase().includes(taskSearch.value.toLowerCase())
    const matchesStatus = !taskStatusFilter.value || task.status === taskStatusFilter.value
    const matchesPriority = !taskPriorityFilter.value || task.priority === taskPriorityFilter.value
    return matchesSearch && matchesStatus && matchesPriority
  })
})

function openEditTaskForm(task: Task): void {
  editingTask.value = task
  showTaskForm.value = true
}

function closeTaskForm(): void {
  showTaskForm.value = false
  editingTask.value = null
}

async function handleTaskSaved(): Promise<void> {
  closeTaskForm()
  await dashboardStore.fetchStats()
}

async function handleDeleteTask(task: Task): Promise<void> {
  if (!confirm(`Supprimer la tâche "${task.title}" ?`)) return
  await tasksStore.deleteTask(task.id)
  await dashboardStore.fetchStats()
}

async function handleTaskStatusChange(task: Task, status: TaskStatus): Promise<void> {
  await tasksStore.updateTaskStatus(task.id, status)
  await dashboardStore.fetchStats()
}
</script>

<template>
  <div>
    <div class="flex items-center justify-between mb-8">
      <div>
        <p class="text-sm font-semibold text-slate-500 uppercase tracking-wide">VUE D'ENSEMBLE</p>
        <h1 class="text-4xl font-bold text-slate-900 mt-1">Dashboard</h1>
      </div>
      <div class="flex items-center gap-2 px-4 py-2 bg-emerald-50 rounded-lg border border-emerald-200">
        <span class="text-lg">🟢</span>
        <span class="text-sm font-semibold text-emerald-700">Données en temps réel</span>
      </div>
    </div>

    <section class="mt-8">
      <p v-if="dashboardStore.isLoading" class="text-sm text-slate-500">
        Chargement des statistiques...
      </p>

      <template v-else-if="dashboardStore.stats">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-8">
          <StatCard label="Projets actifs" :value="dashboardStore.stats.total_projects" icon="📁" accent="blue" />
          <StatCard label="Tâches créées" :value="dashboardStore.stats.total_tasks" icon="📋" accent="gray" />
          <StatCard label="Tâches terminées" :value="dashboardStore.stats.completed_tasks" icon="✅" accent="green" />
          <StatCard label="En progression" :value="dashboardStore.stats.in_progress_tasks" icon="🕐" accent="amber" />
        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
          <h2 class="text-lg font-bold text-slate-900 mb-2">Progression globale</h2>
          <p class="text-sm text-slate-600 mb-4">Tâches terminées sur le total</p>
          
          <div class="flex items-center gap-4">
            <div class="flex-1">
              <div class="h-3 rounded-full bg-slate-200 overflow-hidden">
                <div
                  class="h-3 rounded-full bg-gradient-to-r from-emerald-500 to-emerald-400 transition-all duration-500"
                  :style="{ width: dashboardStore.stats.total_tasks > 0 ? (dashboardStore.stats.completed_tasks / dashboardStore.stats.total_tasks * 100) + '%' : '0%' }"
                />
              </div>
            </div>
            <div class="text-right">
              <p class="text-2xl font-bold text-slate-900">{{ dashboardStore.stats.completed_tasks }}</p>
              <p class="text-sm text-slate-600">au total</p>
            </div>
          </div>
        </div>
      </template>
    </section>

    <ProjectForm
      v-if="showProjectForm"
      :project="editingProject"
      @close="closeProjectForm"
      @saved="handleProjectSaved"
    />

    <TaskForm
      v-if="showTaskForm"
      :project-id="editingTask?.project_id ?? 0"
      :task="editingTask"
      @close="closeTaskForm"
      @saved="handleTaskSaved"
    />
  </div>
</template>