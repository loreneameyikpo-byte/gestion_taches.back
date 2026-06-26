<script setup lang="ts">
import type { Task, TaskStatus } from '~/types'

defineProps<{
  label: string
  status: TaskStatus
  tasks: Task[]
}>()

defineEmits<{
  dragStart: [Task]
  drop: []
  edit: [Task]
  delete: [Task]
}>()

const isDragOver = ref(false)

const columnAccent: Record<TaskStatus, string> = {
  todo: 'border-t-gray-400',
  in_progress: 'border-t-amber-400',
  done: 'border-t-green-400',
}
</script>

<template>
  <div
    :class="[
      'rounded-xl border-t-4 bg-gray-50 p-3 transition',
      columnAccent[status],
      isDragOver ? 'ring-2 ring-primary-400' : '',
    ]"
    @dragover.prevent="isDragOver = true"
    @dragleave="isDragOver = false"
    @drop.prevent="isDragOver = false; $emit('drop')"
  >
    <div class="flex items-center justify-between px-1">
      <h3 class="text-sm font-semibold text-gray-700">{{ label }}</h3>
      <span class="rounded-full bg-gray-200 px-2 py-0.5 text-xs text-gray-600">{{ tasks.length }}</span>
    </div>

    <div class="mt-3 min-h-[80px] space-y-3">
      <div
        v-for="task in tasks"
        :key="task.id"
        draggable="true"
        class="cursor-move"
        @dragstart="$emit('dragStart', task)"
      >
        <TaskCard :task="task" @edit="$emit('edit', task)" @delete="$emit('delete', task)" />
      </div>

      <p v-if="tasks.length === 0" class="px-1 py-4 text-center text-xs text-gray-400">
        Aucune tâche
      </p>
    </div>
  </div>
</template>