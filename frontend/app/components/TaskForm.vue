<script setup lang="ts">
import type { ApiValidationError, Task } from '~/types'

const props = defineProps<{
  projectId: number
  task?: Task | null
}>()

const emit = defineEmits<{
  close: []
  saved: [Task]
}>()

const tasksStore = useTasksStore()

const form = reactive({
  title: props.task?.title ?? '',
  description: props.task?.description ?? '',
  priority: props.task?.priority ?? 'medium',
  due_date: props.task?.due_date ?? '',
  status: props.task?.status ?? 'todo',
})

const errors = ref<Record<string, string[]>>({})
const isSaving = ref(false)

async function handleSubmit(): Promise<void> {
  errors.value = {}
  isSaving.value = true

  const payload = {
    title: form.title,
    description: form.description || null,
    priority: form.priority,
    due_date: form.due_date || null,
    status: form.status,
  }

  try {
    const saved = props.task
      ? await tasksStore.updateTask(props.task.id, payload)
      : await tasksStore.createTask(props.projectId, payload)
    emit('saved', saved)
  } catch (error) {
    const fetchError = error as { data?: ApiValidationError }
    errors.value = fetchError.data?.errors ?? {}
  } finally {
    isSaving.value = false
  }
}
</script>

<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 px-4 backdrop-blur">
    <div class="max-h-[90vh] w-full max-w-md overflow-y-auto rounded-2xl bg-gradient-to-br from-slate-800 to-slate-700 p-8 shadow-2xl border border-slate-600">
      <h2 class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-pink-400">
        {{ task ? '✏️ Modifier la tâche' : '✨ Nouvelle tâche' }}
      </h2>

      <form class="mt-6 space-y-5" @submit.prevent="handleSubmit">
        <div>
          <label for="title" class="label">Titre</label>
          <input
            id="title"
            v-model="form.title"
            type="text"
            required
            class="input-field"
          >
          <p v-if="errors.title" class="mt-2 text-sm text-red-400">{{ errors.title[0] }}</p>
        </div>

        <div>
          <label for="description" class="label">Description</label>
          <textarea
            id="description"
            v-model="form.description"
            rows="3"
            class="input-field"
          />
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
          <div>
            <label for="priority" class="label">Priorité</label>
            <select
              id="priority"
              v-model="form.priority"
              class="input-field"
            >
              <option value="low">Basse</option>
              <option value="medium">Moyenne</option>
              <option value="high">Haute</option>
            </select>
          </div>

          <div>
            <label for="status" class="label">Statut</label>
            <select
              id="status"
              v-model="form.status"
              class="input-field"
            >
              <option value="todo">À faire</option>
              <option value="in_progress">En cours</option>
              <option value="done">Terminée</option>
            </select>
          </div>
        </div>

        <div>
          <label for="due_date" class="label">Date limite</label>
          <input
            id="due_date"
            v-model="form.due_date"
            type="date"
            class="input-field"
          >
          <p v-if="errors.due_date" class="mt-2 text-sm text-red-400">{{ errors.due_date[0] }}</p>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-slate-600">
          <button
            type="button"
            class="btn-secondary"
            @click="emit('close')"
          >
            ✕ Annuler
          </button>
          <button
            type="submit"
            :disabled="isSaving"
            class="btn-primary"
          >
            {{ isSaving ? '⏳ Enregistrement...' : '💾 Enregistrer' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>