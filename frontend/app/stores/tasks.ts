import { defineStore } from "pinia";
import type { Task, TaskStatus } from "../types";
import { ref } from "vue";
import { useApi } from "../composables/useApi";

interface TaskPayload {
  title: string;
  description?: string | null;
  priority?: string;
  due_date?: string | null;
  status?: TaskStatus;
}

interface TaskFilters {
  search?: string;
  status?: string;
  priority?: string;
}

export const useTasksStore = defineStore("tasks", () => {
  // Tâches d'UN projet (page de détail projet / Kanban, Étape 6).
  const tasks = ref<Task[]>([]);
  const isLoading = ref(false);

  // Toutes les tâches de l'utilisateur, tous projets confondus
  // (onglet "Tâches" du dashboard).
  const allTasks = ref<Task[]>([]);
  const isLoadingAll = ref(false);

  async function fetchTasks(projectId: number): Promise<void> {
    isLoading.value = true;
    try {
      // L'API renvoie un tableau à plat (pas de pagination, voir
      // TaskController::index) : pas de "data" à déballer ici.
      tasks.value = await useApi<Task[]>(`/projects/${projectId}/tasks`);
    } finally {
      isLoading.value = false;
    }
  }

  async function fetchAllTasks(filters: TaskFilters = {}): Promise<void> {
    isLoadingAll.value = true;
    try {
      allTasks.value = await useApi<Task[]>("/tasks", { query: filters });
    } finally {
      isLoadingAll.value = false;
    }
  }

  async function createTask(
    projectId: number,
    payload: TaskPayload,
  ): Promise<Task> {
    const task = await useApi<Task>(`/projects/${projectId}/tasks`, {
      method: "POST",
      body: payload,
    });
    tasks.value.push(task);
    return task;
  }

  async function updateTask(id: number, payload: TaskPayload): Promise<Task> {
    const updated = await useApi<Task>(`/tasks/${id}`, {
      method: "PUT",
      body: payload,
    });
    replaceInList(updated);
    return updated;
  }

  async function updateTaskStatus(
    id: number,
    status: TaskStatus,
  ): Promise<Task> {
    const updated = await useApi<Task>(`/tasks/${id}/status`, {
      method: "PATCH",
      body: { status },
    });
    replaceInList(updated);
    return updated;
  }

  async function deleteTask(id: number): Promise<void> {
    await useApi(`/tasks/${id}`, { method: "DELETE" });
    tasks.value = tasks.value.filter((task) => task.id !== id);
    allTasks.value = allTasks.value.filter((task) => task.id !== id);
  }

  /**
   * Une tâche peut être affichée à la fois dans `tasks` (vue projet) et
   * `allTasks` (vue dashboard) : on met à jour les deux pour qu'elles
   * restent cohérentes, peu importe d'où vient la modification.
   */
  function replaceInList(updated: Task): void {
    const indexInTasks = tasks.value.findIndex(
      (task) => task.id === updated.id,
    );
    if (indexInTasks !== -1) {
      tasks.value[indexInTasks] = updated;
    }

    const indexInAll = allTasks.value.findIndex(
      (task) => task.id === updated.id,
    );
    if (indexInAll !== -1) {
      allTasks.value[indexInAll] = updated;
    }
  }

  return {
    tasks,
    isLoading,
    allTasks,
    isLoadingAll,
    fetchTasks,
    fetchAllTasks,
    createTask,
    updateTask,
    updateTaskStatus,
    deleteTask,
  };
});
