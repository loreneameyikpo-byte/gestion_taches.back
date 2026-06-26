import { defineStore } from "pinia";
import { ref, computed } from "vue";
import { useApi, ensureCsrfCookie } from "../composables/useApi";
import type { User } from "~/types";

interface RegisterPayload extends Record<string, unknown> {
  name: string;
  email: string;
  password: string;
  password_confirmation: string;
}

interface LoginPayload extends Record<string, unknown> {
  email: string;
  password: string;
}

interface AuthResponse {
  message: string;
  user: User;
  token: string;
}

export const useAuthStore = defineStore("auth", () => {
  const user = ref<User | null>(null);
  const token = ref<string | null>(null);
  const isAuthenticated = computed(
    () => user.value !== null && token.value !== null,
  );

  // Charger le token depuis localStorage au démarrage
  function initializeFromStorage() {
    if (process.client) {
      const storedToken = localStorage.getItem("auth_token");
      const storedUser = localStorage.getItem("auth_user");
      if (storedToken && storedUser) {
        token.value = storedToken;
        try {
          user.value = JSON.parse(storedUser);
        } catch {
          user.value = null;
        }
      }
    }
  }

  // Sauvegarder le token et l'utilisateur
  function saveToStorage() {
    if (process.client) {
      if (token.value && user.value) {
        localStorage.setItem("auth_token", token.value);
        localStorage.setItem("auth_user", JSON.stringify(user.value));
      } else {
        localStorage.removeItem("auth_token");
        localStorage.removeItem("auth_user");
      }
    }
  }

  /**
   * Récupère l'utilisateur actuel depuis l'API
   */
  async function fetchUser(): Promise<void> {
    try {
      const response = await useApi<{ user: User }>("/me");
      user.value = response.user;
    } catch {
      user.value = null;
      token.value = null;
      saveToStorage();
    }
  }

  /**
   * Enregistrement d'un nouvel utilisateur
   */
  async function register(payload: RegisterPayload): Promise<void> {
    try {
      const response = await useApi<AuthResponse>("/register", {
        method: "POST",
        body: payload,
      });
      user.value = response.user;
      token.value = response.token;
      saveToStorage();
    } catch (error) {
      console.error("Registration failed:", error);
      throw error;
    }
  }

  /**
   * Connexion utilisateur
   */
  async function login(payload: LoginPayload): Promise<void> {
    try {
      const response = await useApi<AuthResponse>("/login", {
        method: "POST",
        body: payload,
      });
      user.value = response.user;
      token.value = response.token;
      saveToStorage();
    } catch (error) {
      console.error("Login failed:", error);
      throw error;
    }
  }

  /**
   * Déconnexion utilisateur
   */
  async function logout(): Promise<void> {
    try {
      await useApi<void>("/logout", { method: "POST" });
    } catch (error) {
      console.error("Logout failed:", error);
    } finally {
      user.value = null;
      token.value = null;
      saveToStorage();
    }
  }

  /**
   * Déconnexion locale uniquement (sans appel API)
   * Utilisé en dernier recours si l'appel serveur échoue
   */
  function logoutLocally(): void {
    user.value = null;
    token.value = null;
    saveToStorage();
  }

  /**
   * Mise à jour du profil utilisateur
   */
  async function updateProfile(
    payload: Record<string, unknown>,
  ): Promise<void> {
    try {
      const response = await useApi<{ user: User }>("/profile", {
        method: "PUT",
        body: payload,
      });
      user.value = response.user;
      saveToStorage();
    } catch (error) {
      console.error("Profile update failed:", error);
      throw error;
    }
  }

  return {
    user,
    token,
    isAuthenticated,
    initializeFromStorage,
    fetchUser,
    register,
    login,
    logout,
    logoutLocally,
    updateProfile,
  };
});
