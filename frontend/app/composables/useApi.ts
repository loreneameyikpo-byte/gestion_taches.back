import { $fetch } from "ofetch";
import { useRuntimeConfig, useCookie } from "#imports";
import { useAuthStore } from "../stores/auth";

type ApiFetchOptions = {
  method?: "GET" | "POST" | "PUT" | "PATCH" | "DELETE";
  body?: BodyInit | Record<string, any> | null;
  headers?: Record<string, string>;
  query?: Record<string, any>;
};

export async function ensureCsrfCookie(): Promise<void> {
  // Pour Sanctum Bearer tokens, on n'a pas besoin de CSRF cookie
  // Les tokens sont stockés localement et envoyés en header Authorization
  // Cette fonction est maintenue pour la compatibilité mais ne fait rien
  return Promise.resolve();
}

export function useApi<T = unknown>(
  url: string,
  options: ApiFetchOptions = {},
): Promise<T> {
  const config = useRuntimeConfig();
  const authStore = useAuthStore();

  // Récupérer le token du localStorage ou du store
  const token = process.client ? localStorage.getItem("auth_token") : null;

  const apiBaseUrl = config.public.apiBaseUrl ?? "http://localhost:8000";

  return $fetch<T>(url, {
    baseURL: apiBaseUrl,
    ...options,
    query: options.query as Record<string, any> | undefined,
    headers: {
      Accept: "application/json",
      "Content-Type": "application/json",
      // Ajouter le token Bearer s'il existe
      ...(token ? { Authorization: `Bearer ${token}` } : {}),
      ...options.headers,
    },
  });
}
