import { defineStore } from 'pinia'
import type { DashboardStats } from '../../types'
import { ref } from 'vue'
import { useApi } from '../../composables/useApi'

export const useDashboardStore = defineStore('dashboard', () => {
  const stats = ref<DashboardStats | null>(null)
  const isLoading = ref(false)

  async function fetchStats(): Promise<void> {
    isLoading.value = true
    try {
      stats.value = await useApi<DashboardStats>('/api/dashboard')
    } finally {
      isLoading.value = false
    }
  }

  return {
    stats,
    isLoading,
    fetchStats,
  }
})
