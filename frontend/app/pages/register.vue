<script setup lang="ts">
import { reactive, ref } from 'vue'
///import { definePageMeta } from '#app'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

type ApiValidationError = { errors?: Record<string, string[]> }

definePageMeta({ layout: 'auth' })

const authStore = useAuthStore()
const router = useRouter()

const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
})

const errors = ref<Record<string, string[]>>({})
const generalError = ref('')
const isLoading = ref(false)

async function handleSubmit(): Promise<void> {
  errors.value = {}
  generalError.value = ''
  isLoading.value = true

  try {
    await authStore.register(form)
    await router.push('/dashboard')
  } catch (error) {
    const fetchError = error as { data?: ApiValidationError }
    if (fetchError.data?.errors) {
      errors.value = fetchError.data.errors
    } else {
      generalError.value = 'Une erreur est survenue. Veuillez réessayer.'
    }
  } finally {
    isLoading.value = false
  }
}
</script>

<template>
  <div class="rounded-xl bg-white p-8 shadow-sm border border-slate-200 max-w-md w-full">
    <h1 class="text-3xl font-bold text-slate-900">Créer un compte</h1>
    <p class="mt-2 text-sm text-slate-600">Commencez à organiser vos projets en quelques secondes.</p>

    <form class="mt-6 space-y-4" @submit.prevent="handleSubmit">
      <div>
        <label for="name" class="label">Nom complet</label>
        <input
          id="name"
          v-model="form.name"
          type="text"
          autocomplete="name"
          required
          class="input-field"
        >
        <p v-if="errors.name" class="mt-2 text-sm text-red-600">{{ errors.name[0] }}</p>
      </div>

      <div>
        <label for="email" class="label">Email</label>
        <input
          id="email"
          v-model="form.email"
          type="email"
          autocomplete="email"
          required
          class="input-field"
        >
        <p v-if="errors.email" class="mt-2 text-sm text-red-600">{{ errors.email[0] }}</p>
      </div>

      <div>
        <label for="password" class="label">Mot de passe</label>
        <input
          id="password"
          v-model="form.password"
          type="password"
          autocomplete="new-password"
          required
          class="input-field"
        >
        <p v-if="errors.password" class="mt-2 text-sm text-red-600">{{ errors.password[0] }}</p>
      </div>

      <div>
        <label for="password_confirmation" class="label">
          Confirmer le mot de passe
        </label>
        <input
          id="password_confirmation"
          v-model="form.password_confirmation"
          type="password"
          autocomplete="new-password"
          required
          class="input-field"
        >
      </div>

      <p v-if="generalError" class="text-sm text-red-600 font-semibold">⚠️ {{ generalError }}</p>

      <button
        type="submit"
        :disabled="isLoading"
        class="btn-primary w-full"
      >
        {{ isLoading ? ' Création...' : ' Créer mon compte' }}
      </button>
    </form>

    <p class="mt-6 text-center text-sm text-slate-600">
      Déjà un compte ?
      <NuxtLink to="/login" class="font-semibold text-purple-600 hover:text-purple-700 transition-colors">
        Se connecter
      </NuxtLink>
    </p>
  </div>
</template>
