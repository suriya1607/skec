<template>
  <div class="flex flex-col items-center justify-center py-16 text-center">
    <div class="w-16 h-16 mb-4 rounded-2xl bg-gray-100 flex items-center justify-center">
      <component :is="icon || DocumentTextIcon" class="w-8 h-8 text-gray-400" />
    </div>
    <h3 class="text-base font-semibold text-gray-700 mb-1">{{ title }}</h3>
    <p class="text-sm text-gray-400 max-w-xs">{{ description }}</p>
    <div v-if="$slots.action" class="mt-6">
      <slot name="action" />
    </div>
  </div>
</template>

<script setup>
import { DocumentTextIcon } from '@heroicons/vue/24/outline'
defineProps({
  title:       { type: String, default: 'Nothing here yet' },
  description: { type: String, default: 'No items to display.' },
  icon:        { default: null },
})
</script>
