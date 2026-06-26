<script setup lang="ts">
withDefaults(
  defineProps<{
    label: string
    value: number
    icon?: string
    accent?: 'gray' | 'blue' | 'amber' | 'green'
  }>(),
  { accent: 'gray', icon: '' },
)

const accentTextClasses: Record<string, string> = {
  gray: 'text-slate-900',
  blue: 'text-blue-600',
  amber: 'text-amber-600',
  green: 'text-emerald-600',
}

const accentBgClasses: Record<string, string> = {
  gray: 'bg-slate-100',
  blue: 'bg-blue-100',
  amber: 'bg-amber-100',
  green: 'bg-emerald-100',
}
</script>

<template>
  <div class="flex flex-col items-center gap-3 rounded-xl bg-white p-6 shadow-sm border border-slate-200 hover:shadow-md transition-shadow">
    <div
      v-if="icon"
      :class="['flex h-10 w-10 shrink-0 items-center justify-center rounded-lg text-xl font-bold', accentBgClasses[accent]]"
    >
      {{ icon }}
    </div>
    <div class="text-center">
      <p class="text-sm font-medium text-slate-500">{{ label }}</p>
      <p :class="['mt-1 text-2xl font-bold', accentTextClasses[accent]]">{{ value }}</p>
    </div>
  </div>
</template>