<script setup lang="ts">
import type { Task, TaskStatus } from '~/types'

const tasksStore = useTasksStore()
const showForm = ref(false)
const editingTask = ref<Task | null>(null)

onMounted(() => {
  tasksStore.fetchAllTasks()
})

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
    <h1 class="text-2xl font-bold text-gray-900">Kanban</h1>
    <p class="mt-1 text-sm text-gray-500">
      Toutes vos tâches, tous projets confondus. Glissez une carte pour changer son statut.
    </p>

    <p v-if="tasksStore.isLoadingAll" class="mt-6 text-sm text-gray-500">Chargement...</p>

    <p v-else-if="tasksStore.allTasks.length === 0" class="mt-6 text-sm text-gray-500">
      Aucune tâche pour le moment.
    </p>

    <KanbanBoard
      v-else
      class="mt-6"
      :tasks="tasksStore.allTasks"
      @edit="openEditForm"
      @delete="handleDelete"
      @status-change="handleStatusChange"
    />

    <TaskForm
      v-if="showForm"
      :project-id="editingTask?.project_id ?? 0"
      :task="editingTask"
      @close="closeForm"
      @saved="closeForm"
    />
  </div>
</template>