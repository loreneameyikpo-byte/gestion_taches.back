<script setup lang="ts">
const authStore = useAuthStore()
const router = useRouter()
const route = useRoute()

async function handleLogout(): Promise<void> {
  try {
    await authStore.logout()
  } catch {
    authStore.logoutLocally()
  } finally {
    router.push('/login')
  }
}

const links = [
  { to: '/dashboard', label: 'Dashboard', icon: '▦' },
  { to: '/projects', label: 'Projets', icon: '📁' },
  { to: '/tasks', label: 'Tâches', icon: '☰' },
  { to: '/kanban', label: 'Kanban', icon: '🗂️' },
  { to: '/profile', label: 'Profil', icon: '👤' },
]

function isActive(path: string): boolean {
  return route.path === path || route.path.startsWith(`${path}/`)
}
</script>

<template>
  <nav class="border-b border-gray-200 bg-white">
    <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-3 px-4 py-3">
      <NuxtLink to="/dashboard" class="flex items-center gap-2 text-lg font-bold text-gray-900">
        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary-600 text-white">✓</span>
        Gestion <span class="text-primary-600">des Tâches</span>
      </NuxtLink>

      <div class="flex flex-wrap items-center gap-1 sm:gap-2">
        <NuxtLink
          v-for="link in links"
          :key="link.to"
          :to="link.to"
          :class="[
            'flex items-center gap-1.5 rounded-md px-3 py-1.5 text-sm font-medium transition',
            isActive(link.to)
              ? 'bg-primary-50 text-primary-700'
              : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900',
          ]"
        >
          <span>{{ link.icon }}</span>
          {{ link.label }}
        </NuxtLink>

        <button
          type="button"
          class="ml-1 rounded-md bg-red-500 px-3 py-1.5 text-sm font-semibold text-white transition hover:bg-red-600"
          @click="handleLogout"
        >
          Déconnexion
        </button>
      </div>
    </div>
  </nav>
</template>