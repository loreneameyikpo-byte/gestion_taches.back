<script setup lang="ts">
import { computed } from 'vue';
import type { Project } from '../types'

const props = defineProps<{
  project: Project
}>()

defineEmits<{
  edit: [Project]
  delete: [Project]
}>()

const today = new Date(new Date().toDateString())

const isOverdue = computed(() => {
  if (!props.project.due_date) return false
  return new Date(props.project.due_date) < today
})

const isDueSoon = computed(() => {
  if (!props.project.due_date || isOverdue.value) return false
  const due = new Date(props.project.due_date)
  const diffDays = Math.ceil((due.getTime() - today.getTime()) / 86_400_000)
  return diffDays <= 3
})

const formattedDueDate = computed(() => {
  if (!props.project.due_date) return null
  return new Date(props.project.due_date).toLocaleDateString('fr-FR')
})
</script>

<template>
  <div class="rounded-lg bg-white p-5 shadow-sm border border-slate-200 hover:shadow-md transition-all">
    <div class="flex items-start justify-between gap-2">
      <h3 class="font-medium text-slate-900">{{ project.name }}</h3>
      <span
        v-if="formattedDueDate"
        :class="[
          'shrink-0 rounded-full px-2 py-0.5 text-xs font-semibold',
          isOverdue
            ? 'bg-red-100 text-red-700'
            : isDueSoon
              ? 'bg-amber-100 text-amber-700'
              : 'bg-slate-100 text-slate-600',
        ]"
      >
        {{ formattedDueDate }}
      </span>
    </div>

    <p v-if="project.description" class="mt-2 line-clamp-2 text-sm text-slate-600">
      {{ project.description }}
    </p>

    <div class="mt-4 flex gap-3 text-sm">
      <button type="button" class="font-medium text-purple-600 hover:text-purple-700 transition-colors" @click="$emit('edit', project)">
        ✏️ Modifier
      </button>
      <button type="button" class="font-medium text-red-600 hover:text-red-700 transition-colors" @click="$emit('delete', project)">
        🗑️ Supprimer
      </button>
    </div>
  </div>
</template>