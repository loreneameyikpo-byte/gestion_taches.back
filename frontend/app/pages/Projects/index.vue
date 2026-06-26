<script setup lang="ts">
import type { Project } from '~/types'

const projectsStore = useProjectsStore()
const showForm = ref(false)
const editingProject = ref<Project | null>(null)

onMounted(() => {
  projectsStore.fetchProjects()
})

function openCreateForm(): void {
  editingProject.value = null
  showForm.value = true
}

function openEditForm(project: Project): void {
  editingProject.value = project
  showForm.value = true
}

function closeForm(): void {
  showForm.value = false
  editingProject.value = null
}

async function handleDelete(project: Project): Promise<void> {
  if (!confirm(`Supprimer le projet "${project.name}" ?`)) return

  try {
    await projectsStore.deleteProject(project.id)
  } catch (error) {
    const fetchError = error as { data?: { message?: string } }
    alert(fetchError.data?.message ?? 'Impossible de supprimer ce projet.')
  }
}

function formatDate(value: string | null): string {
  if (!value) return '—'
  return new Date(value).toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' })
}
</script>

<template>
  <div>
    <p class="text-xs font-semibold uppercase tracking-wide text-primary-600">Gestion</p>
    <div class="mt-1 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <h1 class="text-2xl font-bold text-gray-900">Projets</h1>
      <button
        type="button"
        class="self-start rounded-md bg-primary-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-primary-700 sm:self-auto"
        @click="openCreateForm"
      >
        + Nouveau projet
      </button>
    </div>

    <p v-if="projectsStore.isLoading" class="mt-6 text-sm text-gray-500">Chargement...</p>

    <p v-else-if="projectsStore.projects.length === 0" class="mt-6 text-sm text-gray-500">
      Aucun projet pour le moment. Créez votre premier projet !
    </p>

    <div v-else class="mt-6 overflow-x-auto rounded-xl bg-white shadow-sm ring-1 ring-gray-100">
      <table class="w-full min-w-[640px] text-left text-sm">
        <thead>
          <tr class="border-b border-gray-100 text-xs uppercase tracking-wide text-gray-400">
            <th class="px-4 py-3 font-medium">#</th>
            <th class="px-4 py-3 font-medium">Titre</th>
            <th class="px-4 py-3 font-medium">Description</th>
            <th class="px-4 py-3 font-medium">Fin</th>
            <th class="px-4 py-3 font-medium">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="project in projectsStore.projects"
            :key="project.id"
            class="border-b border-gray-50 last:border-0 hover:bg-brown-50"
          >
            <td class="px-4 py-3 text-gray-400">{{ project.id }}</td>
            <td class="px-4 py-3">
              <NuxtLink :to="`/projects/${project.id}`" class="font-semibold text-gray-900 hover:text-primary-600">
                {{ project.name }}
              </NuxtLink>
            </td>
            <td class="max-w-xs truncate px-4 py-3 text-gray-500">{{ project.description ?? '—' }}</td>
            <td class="px-4 py-3 text-gray-500">{{ formatDate(project.due_date) }}</td>
            <td class="px-4 py-3">
              <div class="flex gap-2">
                <button
                  type="button"
                  class="rounded-md bg-primary-100 px-3 py-1 text-xs font-semibold text-primary-700 transition hover:bg-primary-200"
                  @click="openEditForm(project)"
                >
                  Modifier
                </button>
                <button
                  type="button"
                  class="rounded-md bg-red-100 px-3 py-1 text-xs font-semibold text-red-700 transition hover:bg-red-200"
                  @click="handleDelete(project)"
                >
                  Supprimer
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
      <p class="px-4 py-3 text-xs text-gray-400">{{ projectsStore.projects.length }} projet(s) au total</p>
    </div>

    <ProjectForm
      v-if="showForm"
      :project="editingProject"
      @close="closeForm"
      @saved="closeForm"
    />
  </div>
</template>