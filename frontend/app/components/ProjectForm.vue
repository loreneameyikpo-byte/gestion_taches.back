<script setup lang="ts">
import type { ApiValidationError, Project } from '~/types'

const props = defineProps<{
  project?: Project | null
}>()

const emit = defineEmits<{
  close: []
  saved: [Project]
}>()

const projectsStore = useProjectsStore()

const form = reactive({
  name: props.project?.name ?? '',
  description: props.project?.description ?? '',
  due_date: props.project?.due_date ?? '',
})

const errors = ref<Record<string, string[]>>({})
const isSaving = ref(false)

async function handleSubmit(): Promise<void> {
  errors.value = {}
  isSaving.value = true

  const payload = {
    name: form.name,
    description: form.description || null,
    due_date: form.due_date || null,
  }

  try {
    const saved = props.project
      ? await projectsStore.updateProject(props.project.id, payload)
      : await projectsStore.createProject(payload)
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
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4">
    <div class="max-h-[90vh] w-full max-w-md overflow-y-auto rounded-xl bg-white p-6 shadow-xl">
      <h2 class="text-lg font-semibold text-gray-900">
        {{ project ? 'Modifier le projet' : 'Nouveau projet' }}
      </h2>

      <form class="mt-4 space-y-4" @submit.prevent="handleSubmit">
        <div>
          <label for="name" class="block text-sm font-medium text-gray-700">Nom</label>
          <input
            id="name"
            v-model="form.name"
            type="text"
            required
            class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
          >
          <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name[0] }}</p>
        </div>

        <div>
          <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
          <textarea
            id="description"
            v-model="form.description"
            rows="3"
            class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
          />
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-1">
          <div>
            <label for="due_date" class="block text-sm font-medium text-gray-700">Date limite</label>
            <input
              id="due_date"
              v-model="form.due_date"
              type="date"
              class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
            >
            <p v-if="errors.due_date" class="mt-1 text-sm text-red-600">{{ errors.due_date[0] }}</p>
          </div>
        </div>
        <p class="text-xs text-gray-400">
          Un rappel par mail est envoyé automatiquement 3 jours avant la date de fin.
        </p>

        <div class="flex justify-end gap-2 pt-2">
          <button
            type="button"
            class="rounded-md px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100"
            @click="emit('close')"
          >
            Annuler
          </button>
          <button
            type="submit"
            :disabled="isSaving"
            class="rounded-md bg-primary-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-primary-700 disabled:opacity-60"
          >
            {{ isSaving ? 'Enregistrement...' : 'Enregistrer' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>