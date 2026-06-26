<script setup lang="ts">
import type { Task } from '~/types'

const tasksStore = useTasksStore()

const search = ref('')
const statusFilter = ref('')
const priorityFilter = ref('')

const showForm = ref(false)
const editingTask = ref<Task | null>(null)

onMounted(() => {
  tasksStore.fetchAllTasks()
})

const filteredTasks = computed(() => {
  return tasksStore.allTasks.filter((task) => {
    const matchesSearch = !search.value || task.title.toLowerCase().includes(search.value.toLowerCase())
    const matchesStatus = !statusFilter.value || task.status === statusFilter.value
    const matchesPriority = !priorityFilter.value || task.priority === priorityFilter.value
    return matchesSearch && matchesStatus && matchesPriority
  })
})

const priorityLabels: Record<string, string> = { low: 'Basse', medium: 'Moyenne', high: 'Haute' }
const statusLabels: Record<string, string> = { todo: 'À faire', in_progress: 'En cours', done: 'Terminée' }
const statusClasses: Record<string, string> = {
  todo: 'bg-gray-100 text-gray-600',
  in_progress: 'bg-amber-100 text-amber-700',
  done: 'bg-green-100 text-green-700',
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

function formatDate(value: string | null): string {
  if (!value) return '—'
  return value
}
</script>

<template>
  <div>
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <h1 class="text-2xl font-bold text-gray-900">Tâches</h1>
      <p class="self-start rounded-md bg-white px-4 py-2 text-sm text-gray-500 ring-1 ring-gray-100 sm:self-auto">
        Pour créer une tâche, ouvrez le projet correspondant
      </p>
    </div>

    <div class="mt-6 flex flex-wrap items-center gap-3 rounded-xl bg-white p-3 shadow-sm ring-1 ring-gray-100">
      <input
        v-model="search"
        type="text"
        placeholder="Rechercher une tâche..."
        class="min-w-[180px] flex-1 rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
      >
      <select
        v-model="statusFilter"
        class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
      >
        <option value="">Tous les statuts</option>
        <option value="todo">À faire</option>
        <option value="in_progress">En cours</option>
        <option value="done">Terminée</option>
      </select>
      <select
        v-model="priorityFilter"
        class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
      >
        <option value="">Toutes les priorités</option>
        <option value="low">Basse</option>
        <option value="medium">Moyenne</option>
        <option value="high">Haute</option>
      </select>
    </div>

    <p v-if="tasksStore.isLoadingAll" class="mt-6 text-sm text-gray-500">Chargement...</p>

    <p v-else-if="filteredTasks.length === 0" class="mt-6 text-sm text-gray-500">
      Aucune tâche ne correspond.
    </p>

    <div v-else class="mt-6 overflow-x-auto rounded-xl bg-white shadow-sm ring-1 ring-gray-100">
      <table class="w-full min-w-[760px] text-left text-sm">
        <thead>
          <tr class="border-b border-gray-100 text-xs uppercase tracking-wide text-gray-400">
            <th class="px-4 py-3 font-medium">ID</th>
            <th class="px-4 py-3 font-medium">Titre</th>
            <th class="px-4 py-3 font-medium">Priorité</th>
            <th class="px-4 py-3 font-medium">Statut</th>
            <th class="px-4 py-3 font-medium">Date limite</th>
            <th class="px-4 py-3 font-medium">Projet</th>
            <th class="px-4 py-3 font-medium">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="task in filteredTasks" :key="task.id" class="border-b border-gray-50 last:border-0 hover:bg-brown-50">
            <td class="px-4 py-3 text-gray-400">{{ task.id }}</td>
            <td class="px-4 py-3 font-semibold text-gray-900">{{ task.title }}</td>
            <td class="px-4 py-3 text-gray-600">{{ priorityLabels[task.priority] }}</td>
            <td class="px-4 py-3">
              <span :class="['rounded-full px-2 py-0.5 text-xs font-medium', statusClasses[task.status]]">
                {{ statusLabels[task.status] }}
              </span>
            </td>
            <td class="px-4 py-3 text-gray-500">{{ formatDate(task.due_date) }}</td>
            <td class="px-4 py-3">
              <NuxtLink :to="`/projects/${task.project_id}`" class="text-gray-600 hover:text-primary-600 hover:underline">
                {{ task.project_name ?? '—' }}
              </NuxtLink>
            </td>
            <td class="px-4 py-3">
              <div class="flex gap-2">
                <button
                  type="button"
                  class="rounded-md bg-primary-100 px-3 py-1 text-xs font-semibold text-primary-700 transition hover:bg-primary-200"
                  @click="openEditForm(task)"
                >
                  Modifier
                </button>
                <button
                  type="button"
                  class="rounded-md bg-red-100 px-3 py-1 text-xs font-semibold text-red-700 transition hover:bg-red-200"
                  @click="handleDelete(task)"
                >
                  Supprimer
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <TaskForm
      v-if="showForm"
      :project-id="editingTask?.project_id ?? 0"
      :task="editingTask"
      @close="closeForm"
      @saved="closeForm"
    />
  </div>
</template>