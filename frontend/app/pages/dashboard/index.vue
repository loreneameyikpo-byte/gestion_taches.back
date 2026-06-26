<script setup lang="ts">
import { useDashboardStore } from './dashboard'

const authStore = useAuthStore()
const dashboardStore = useDashboardStore()

onMounted(() => {
  dashboardStore.fetchStats()
})

const completionPercent = computed(() => {
  const stats = dashboardStore.stats
  if (!stats || stats.total_tasks === 0) return 0
  return Math.round((stats.completed_tasks / stats.total_tasks) * 100)
})
</script>

<template>
  <div>
    <p class="text-xs font-semibold uppercase tracking-wide text-primary-600">Vue d'ensemble</p>
    <h1 class="mt-1 text-2xl font-bold text-gray-900">Dashboard</h1>
    <p class="mt-1 text-sm text-gray-500">Bonjour {{ authStore.user?.name }} 👋</p>

    <p v-if="dashboardStore.isLoading" class="mt-6 text-sm text-gray-500">Chargement des statistiques...</p>

    <template v-else-if="dashboardStore.stats">
      <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-100">
          <div class="flex items-center justify-between">
            <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary-50 text-lg">📁</span>
            <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-500">total</span>
          </div>
          <p class="mt-3 text-3xl font-bold text-gray-900">{{ dashboardStore.stats.total_projects }}</p>
          <p class="text-sm text-gray-500">Projets actifs</p>
        </div>

        <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-100">
          <div class="flex items-center justify-between">
            <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-brown-100 text-lg">☰</span>
            <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-500">total</span>
          </div>
          <p class="mt-3 text-3xl font-bold text-gray-900">{{ dashboardStore.stats.total_tasks }}</p>
          <p class="text-sm text-gray-500">Tâches créées</p>
        </div>

        <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-100">
          <div class="flex items-center justify-between">
            <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-50 text-lg">✅</span>
            <span class="rounded-full bg-green-100 px-2 py-0.5 text-xs text-green-700">done</span>
          </div>
          <p class="mt-3 text-3xl font-bold text-gray-900">{{ dashboardStore.stats.completed_tasks }}</p>
          <p class="text-sm text-gray-500">Tâches terminées</p>
          <div class="mt-2 h-1.5 rounded-full bg-gray-100">
            <div class="h-1.5 rounded-full bg-green-500" :style="{ width: completionPercent + '%' }" />
          </div>
          <p class="mt-1 text-xs text-gray-400">{{ completionPercent }}% du total</p>
        </div>

        <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-100">
          <div class="flex items-center justify-between">
            <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-50 text-lg">🕐</span>
            <span class="flex items-center gap-1 rounded-full bg-amber-100 px-2 py-0.5 text-xs text-amber-700">
              <span class="h-1.5 w-1.5 rounded-full bg-amber-500" /> live
            </span>
          </div>
          <p class="mt-3 text-3xl font-bold text-gray-900">{{ dashboardStore.stats.in_progress_tasks }}</p>
          <p class="text-sm text-gray-500">En progression</p>
        </div>
      </div>

      <div class="mt-6 rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-100">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-sm font-semibold text-gray-700">Progression globale</h2>
            <p class="text-xs text-gray-400">Tâches terminées sur le total</p>
          </div>
          <p class="text-2xl font-bold text-primary-600">
            {{ completionPercent }}<span class="text-sm text-gray-400">%</span>
          </p>
        </div>

        <div class="mt-4 h-3 rounded-full bg-gray-100">
          <div class="h-3 rounded-full bg-primary-500" :style="{ width: completionPercent + '%' }" />
        </div>

        <div class="mt-2 flex justify-between text-xs text-gray-400">
          <span>{{ dashboardStore.stats.completed_tasks }} terminées</span>
          <span>{{ dashboardStore.stats.total_tasks }} au total</span>
        </div>
      </div>
    </template>
  </div>
</template>