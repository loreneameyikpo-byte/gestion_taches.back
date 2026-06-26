import { defineStore } from "pinia";
import { ref, computed } from "vue";
import type { User } from "../types";
import { useApi } from "../composables/useApi";

interface DashboardStats {
  total_projects: number;
  total_tasks: number;
  completed_tasks: number;
  pending_tasks: number;
  completion_rate: number;
}

export const useDashboardStore = defineStore("dashboard", () => {
  const stats = ref<DashboardStats | null>(null);
  const isLoading = ref(false);

  async function fetchStats(): Promise<void> {
    isLoading.value = true;
    try {
      stats.value = await useApi<DashboardStats>("/dashboard");
    } catch (error) {
      console.error("Failed to fetch dashboard stats:", error);
      stats.value = null;
    } finally {
      isLoading.value = false;
    }
  }

  return {
    stats,
    isLoading,
    fetchStats,
  };
});
