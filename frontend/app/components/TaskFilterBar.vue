<script setup lang="ts">
import { computed } from 'vue'

const search = defineModel<string>('search', { default: '' })
const status = defineModel<string>('status', { default: '' })
const priority = defineModel<string>('priority', { default: '' })

const hasActiveFilter = computed(() => !!(search.value || status.value || priority.value))

function reset(): void {
  search.value = ''
  status.value = ''
  priority.value = ''
}
</script>

<template>
  <div class="flex flex-wrap items-center gap-3 rounded-lg bg-white p-3 shadow-sm border border-slate-200">
    <input
      v-model="search"
      type="text"
      placeholder="🔍 Rechercher une tâche..."
      class="min-w-[200px] flex-1 input-field"
    >

    <select
      v-model="status"
      class="input-field min-w-[140px]"
    >
      <option value="">Tous les statuts</option>
      <option value="todo">À faire</option>
      <option value="in_progress">En cours</option>
      <option value="done">Terminée</option>
    </select>

    <select
      v-model="priority"
      class="input-field min-w-[140px]"
    >
      <option value="">Toutes les priorités</option>
      <option value="low">Basse</option>
      <option value="medium">Moyenne</option>
      <option value="high">Haute</option>
    </select>

    <button
      v-if="hasActiveFilter"
      type="button"
      class="font-semibold text-slate-600 hover:text-slate-900 transition-colors"
      @click="reset"
    >
      ↺ Réinitialiser
    </button>
  </div>
</template>