<template>
  <div :class="['flex items-center justify-center', fullscreen ? 'fixed inset-0 bg-white/80 backdrop-blur-sm z-50' : 'py-12']">
    <div class="flex flex-col items-center gap-3">
      <div class="relative">
        <div :class="['rounded-full border-4 border-primary-100 border-t-primary-600 animate-spin', sizeClass]" />
      </div>
      <p v-if="text" class="text-sm text-gray-500">{{ text }}</p>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
const props = defineProps({
  fullscreen: { type: Boolean, default: false },
  size:       { type: String, default: 'md' },
  text:       { type: String, default: '' },
})
const sizeClass = computed(() => ({ sm: 'w-6 h-6', md: 'w-10 h-10', lg: 'w-14 h-14' }[props.size]))
</script>
