import { useAuthStore } from "../stores/auth"
import { navigateTo } from 'nuxt/app'

/**
 * S'exécute avant chaque navigation (fichier suffixé ".global.ts").
 * - Restaure la session au premier chargement (fetchUser via le cookie).
 * - Bloque l'accès aux pages privées si non connecté -> redirige vers /login.
 * - Empêche un utilisateur déjà connecté de revoir /login ou /register.
 */
export default defineNuxtRouteMiddleware(async (to) => {
  const authStore = useAuthStore()

  if (!authStore.user) {
    await authStore.fetchUser()
  }

  const publicPages = ['/', '/login', '/register']
  const isPublicPage = publicPages.includes(to.path)

  if (!authStore.user) {
  await authStore.fetchUser()
}

  // 🔐 si pas connecté
  if (!authStore.isAuthenticated && !isPublicPage) {
    if (to.path !== '/login') {
      return navigateTo('/login')
    }
  }

  // 🔐 si connecté
  //if (authStore.isAuthenticated && isPublicPage) {
  //  if (to.path !== '/dashboard') {
  //    return navigateTo('/dashboard')
  //  }
  //}

  /*if (import.meta.client) {
    const token = localStorage.getItem('token')
    const publicRoutes = ['/login', '/register']
    if (!token && !publicRoutes.includes(to.path)) {
      return navigateTo('/login')
    }
    if (token && publicRoutes.includes(to.path)) {
      return navigateTo('/')
    }
  }*/
})
/*
  if (!authStore.user) {
    await authStore.fetchUser()
  }

  if (!authStore.isAuthenticated && !isPublicPage) {
    return navigateTo('/login')
  }

  if (authStore.isAuthenticated && (isPublicPage || to.path === '/')) {
    return navigateTo('/dashboard')
  }
})
//function defineNuxtRouteMiddleware<T extends (to: any) => Promise<any>>(middleware: T): T {
//  return middleware
//}
//*/
