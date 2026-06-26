<script setup lang="ts">
import type { Task } from '~/types'
import { computed } from 'vue'

const props = defineProps<{
  task: Task
}>()

defineEmits<{
  edit: [Task]
  delete: [Task]
}>()

const priorityLabels: Record<string, string> = {
  low: 'Basse',
  medium: 'Moyenne',
  high: 'Haute',
}

const priorityClasses: Record<string, string> = {
  low: 'bg-blue-100 text-blue-700',
  medium: 'bg-amber-100 text-amber-700',
  high: 'bg-red-100 text-red-700',
}

const statusLabels: Record<string, string> = {
  todo: 'À faire',
  in_progress: 'En cours',
  done: 'Terminée',
}

const formattedDueDate = computed(() => {
  if (!props.task.due_date) return null
  return new Date(props.task.due_date).toLocaleDateString('fr-FR')
})
</script>

<template>
  <div class="rounded-lg bg-white p-4 shadow-sm border border-slate-200 hover:shadow-md transition-all">
    <div class="flex items-start justify-between gap-2">
      <div>
        <h4 class="font-medium text-slate-900">{{ task.title }}</h4>
        <NuxtLink
          v-if="task.project_name"
          :to="`/projects/${task.project_id}`"
          class="text-xs text-slate-500 hover:text-purple-600 hover:underline transition-colors"
        >
          📁 {{ task.project_name }}
        </NuxtLink>
      </div>
      <span :class="['shrink-0 rounded-full px-2 py-0.5 text-xs font-semibold', priorityClasses[task.priority]]">
        {{ priorityLabels[task.priority] }}
      </span>
    </div>

    <p v-if="task.description" class="mt-1 line-clamp-2 text-sm text-slate-600">
      {{ task.description }}
    </p>

    <div class="mt-3 flex items-center justify-between text-xs text-slate-600">
      <span class="rounded-full px-2 py-0.5 bg-slate-100 font-semibold">{{ statusLabels[task.status] }}</span>
      <span v-if="formattedDueDate">📅 {{ formattedDueDate }}</span>
    </div>

    <div class="mt-3 flex gap-3 text-sm">
      <button type="button" class="font-medium text-purple-600 hover:text-purple-700 transition-colors" @click="$emit('edit', task)">
        ✏️ Modifier
      </button>
      <button type="button" class="font-medium text-red-600 hover:text-red-700 transition-colors" @click="$emit('delete', task)">
        🗑️ Supprimer
      </button>
    </div>
  </div>
</template>