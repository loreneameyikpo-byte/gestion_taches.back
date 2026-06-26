<script setup lang="ts">
import type { Project, Task, TaskStatus } from '~/types'

const route = useRoute()
const projectId = Number(route.params.id)

const project = ref<Project | null>(null)
const isLoadingProject = ref(true)

const tasksStore = useTasksStore()
const showForm = ref(false)
const editingTask = ref<Task | null>(null)

const searchQuery = ref('')
const statusFilter = ref('')
const priorityFilter = ref('')

const filteredTasks = computed(() => {
  return tasksStore.tasks.filter((task) => {
    const matchesSearch = !searchQuery.value
      || task.title.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchesStatus = !statusFilter.value || task.status === statusFilter.value
    const matchesPriority = !priorityFilter.value || task.priority === priorityFilter.value
    return matchesSearch && matchesStatus && matchesPriority
  })
})

onMounted(async () => {
  isLoadingProject.value = true
  try {
    project.value = await useApi<Project>(`/api/projects/${projectId}`)
  } finally {
    isLoadingProject.value = false
  }
  await tasksStore.fetchTasks(projectId)
})

function openCreateForm(): void {
  editingTask.value = null
  showForm.value = true
}

function openEditForm(task: Task): void {
  editingTask.value = task
  showForm.value = true
}

function closeForm(): void {
  showForm.value = false
  editingTask.value = null
}

async function handleDelete(task: Task): Promise<void> {
  if (!confirm(`Supprimer la tâche "${task.title}" ?`)) return
  await tasksStore.deleteTask(task.id)
}

async function handleStatusChange(task: Task, status: TaskStatus): Promise<void> {
  await tasksStore.updateTaskStatus(task.id, status)
}
</script>

<template>
  <div>
    <NuxtLink to="/projects" class="text-sm text-gray-500 hover:text-primary-600">
      ← Retour aux projets
    </NuxtLink>

    <div v-if="isLoadingProject" class="mt-4 text-sm text-gray-500">Chargement...</div>

    <template v-else-if="project">
      <div class="mt-2 flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-semibold text-gray-900">{{ project.name }}</h1>
          <p v-if="project.description" class="mt-1 text-sm text-gray-500">{{ project.description }}</p>
        </div>
        <button
          type="button"
          class="rounded-md bg-primary-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-primary-700"
          @click="openCreateForm"
        >
          + Nouvelle tâche
        </button>
      </div>

      <p v-if="tasksStore.isLoading" class="mt-6 text-sm text-gray-500">Chargement des tâches...</p>

      <p v-else-if="tasksStore.tasks.length === 0" class="mt-6 text-sm text-gray-500">
        Aucune tâche pour le moment. Ajoutez-en une !
      </p>

      <template v-else>
        <TaskFilterBar
          v-model:search="searchQuery"
          v-model:status="statusFilter"
          v-model:priority="priorityFilter"
          class="mt-6"
        />

        <p v-if="filteredTasks.length === 0" class="mt-6 text-sm text-gray-500">
          Aucune tâche ne correspond à ces filtres.
        </p>

        <KanbanBoard
          v-else
          class="mt-6"
          :tasks="filteredTasks"
          @edit="openEditForm"
          @delete="handleDelete"
          @status-change="handleStatusChange"
        />
      </template>

      <TaskForm
        v-if="showForm"
        :project-id="projectId"
        :task="editingTask"
        @close="closeForm"
        @saved="closeForm"
      />
    </template>
  </div>
</template>