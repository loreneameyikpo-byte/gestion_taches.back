<script setup lang="ts">
import { reactive, ref } from 'vue'
import { useAuthStore } from '../stores/auth'

type ApiValidationError = {
  errors?: Record<string, string[]>
}

const authStore = useAuthStore()

const profileForm = reactive({
  name: authStore.user?.name ?? '',
  email: authStore.user?.email ?? '',
})

const passwordForm = reactive({
  current_password: '',
  password: '',
  password_confirmation: '',
})

const profileErrors = ref<Record<string, string[]>>({})
const passwordErrors = ref<Record<string, string[]>>({})
const profileSuccess = ref(false)
const passwordSuccess = ref(false)
const isSavingProfile = ref(false)
const isSavingPassword = ref(false)

async function handleProfileSubmit(): Promise<void> {
  profileErrors.value = {}
  profileSuccess.value = false
  isSavingProfile.value = true

  try {
    await authStore.updateProfile({
      name: profileForm.name,
      email: profileForm.email,
    })
    profileSuccess.value = true
  } catch (error) {
    const fetchError = error as { data?: ApiValidationError }
    profileErrors.value = fetchError.data?.errors ?? {}
  } finally {
    isSavingProfile.value = false
  }
}

async function handlePasswordSubmit(): Promise<void> {
  passwordErrors.value = {}
  passwordSuccess.value = false
  isSavingPassword.value = true

  try {
    await fetch('/api/password', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(passwordForm),
    })
    passwordSuccess.value = true
    passwordForm.current_password = ''
    passwordForm.password = ''
    passwordForm.password_confirmation = ''
  } catch (error) {
    const fetchError = error as { data?: ApiValidationError }
    passwordErrors.value = fetchError.data?.errors ?? {}
  } finally {
    isSavingPassword.value = false
  }
}
</script>

<template>
  <div class="space-y-8">
    <h1 class="text-4xl font-bold text-slate-900">Mon profil</h1>

    <section class="rounded-xl bg-white p-8 shadow-sm border border-slate-200">
      <h2 class="text-2xl font-bold text-slate-900">👤 Informations personnelles</h2>

      <form class="mt-6 space-y-5" @submit.prevent="handleProfileSubmit">
        <div>
          <label for="name" class="label">Nom complet</label>
          <input
            id="name"
            v-model="profileForm.name"
            type="text"
            required
            class="input-field"
          >
          <p v-if="profileErrors.name" class="mt-2 text-sm text-red-600">{{ profileErrors.name[0] }}</p>
        </div>

        <div>
          <label for="email" class="label">Email</label>
          <input
            id="email"
            v-model="profileForm.email"
            type="email"
            required
            class="input-field"
          >
          <p v-if="profileErrors.email" class="mt-2 text-sm text-red-600">{{ profileErrors.email[0] }}</p>
        </div>

        <p v-if="profileSuccess" class="text-sm text-emerald-600 font-semibold">✅ Profil mis à jour avec succès.</p>

        <button
          type="submit"
          :disabled="isSavingProfile"
          class="btn-primary w-full sm:w-auto"
        >
          {{ isSavingProfile ? '⏳ Enregistrement...' : '💾 Enregistrer' }}
        </button>
      </form>
    </section>

    <section class="rounded-xl bg-white p-8 shadow-sm border border-slate-200">
      <h2 class="text-2xl font-bold text-slate-900">🔐 Changer le mot de passe</h2>

      <form class="mt-6 space-y-5" @submit.prevent="handlePasswordSubmit">
        <div>
          <label for="current_password" class="label">
            Mot de passe actuel
          </label>
          <input
            id="current_password"
            v-model="passwordForm.current_password"
            type="password"
            autocomplete="current-password"
            class="input-field"
          >
          <p v-if="passwordErrors.current_password" class="mt-2 text-sm text-red-600">
            {{ passwordErrors.current_password[0] }}
          </p>
        </div>

        <div>
          <label for="new_password" class="label">
            Nouveau mot de passe
          </label>
          <input
            id="new_password"
            v-model="passwordForm.password"
            type="password"
            autocomplete="new-password"
            class="input-field"
          >
          <p v-if="passwordErrors.password" class="mt-2 text-sm text-red-600">{{ passwordErrors.password[0] }}</p>
        </div>

        <div>
          <label for="new_password_confirmation" class="label">
            Confirmer le nouveau mot de passe
          </label>
          <input
            id="new_password_confirmation"
            v-model="passwordForm.password_confirmation"
            type="password"
            autocomplete="new-password"
            class="input-field"
          >
        </div>

        <p v-if="passwordSuccess" class="text-sm text-emerald-600 font-semibold">✅ Mot de passe modifié avec succès.</p>

        <button
          type="submit"
          :disabled="isSavingPassword"
          class="btn-primary w-full sm:w-auto"
        >
          {{ isSavingPassword ? '⏳ Enregistrement...' : '🔄 Changer le mot de passe' }}
        </button>
      </form>
    </section>
  </div>
</template>
