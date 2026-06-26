<template>
    <NuxtLayout>
        <NuxtPage />
    </NuxtLayout>
</template>

<script setup lang="ts">
import { useAuthStore } from "~/stores/auth";

const authStore = useAuthStore();

onMounted(() => {
    // Charger le token depuis localStorage
    authStore.initializeFromStorage();
});
</script>
