<script setup lang="ts">
import type { Task, TaskStatus } from '~/types'

const props = defineProps<{
  tasks: Task[]
}>()

const emit = defineEmits<{
  edit: [Task]
  delete: [Task]
  statusChange: [Task, TaskStatus]
}>()

const columns: { status: TaskStatus; label: string }[] = [
  { status: 'todo', label: 'À faire' },
  { status: 'in_progress', label: 'En cours' },
  { status: 'done', label: 'Terminée' },
]

function tasksForStatus(status: TaskStatus): Task[] {
  return props.tasks.filter((task) => task.status === status)
}

const draggedTask = ref<Task | null>(null)

function onDragStart(task: Task): void {
  draggedTask.value = task
}

function onDrop(status: TaskStatus): void {
  if (draggedTask.value && draggedTask.value.status !== status) {
    emit('statusChange', draggedTask.value, status)
  }
  draggedTask.value = null
}
</script>

<template>
  <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
    <KanbanColumn
      v-for="column in columns"
      :key="column.status"
      :label="column.label"
      :status="column.status"
      :tasks="tasksForStatus(column.status)"
      @drag-start="onDragStart"
      @drop="onDrop(column.status)"
      @edit="(task: Task) => emit('edit', task)"
      @delete="(task: Task) => emit('delete', task)"
    />
  </div>
</template>