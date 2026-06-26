import { defineStore } from "pinia";
import { ref } from "vue";
import type { PaginatedResponse, Project } from "../types";
import { useApi } from "../composables/useApi";

interface ProjectPayload {
  name: string;
  description?: string | null;
  due_date?: string | null;
}

export const useProjectsStore = defineStore("projects", () => {
  const projects = ref<Project[]>([]);
  const isLoading = ref(false);

  async function fetchProjects(): Promise<void> {
    isLoading.value = true;
    try {
      const response = await useApi<PaginatedResponse<Project>>("/projects");
      projects.value = response.data;
    } finally {
      isLoading.value = false;
    }
  }

  async function createProject(payload: ProjectPayload): Promise<Project> {
    try {
      const project = await useApi<Project>("/projects", {
        method: "POST",
        body: payload,
      });
      projects.value.unshift(project);
      return project;
    } catch (error) {
      throw error;
    }
  }

  async function updateProject(
    id: number,
    payload: ProjectPayload,
  ): Promise<Project> {
    const updated = await useApi<Project>(`/projects/${id}`, {
      method: "PUT",
      body: payload,
    });
    const index = projects.value.findIndex((project) => project.id === id);
    if (index !== -1) {
      projects.value[index] = updated;
    }
    return updated;
  }

  async function deleteProject(id: number): Promise<void> {
    await useApi(`/projects/${id}`, { method: "DELETE" });
    projects.value = projects.value.filter((project) => project.id !== id);
  }

  return {
    projects,
    isLoading,
    fetchProjects,
    createProject,
    updateProject,
    deleteProject,
  };
});
