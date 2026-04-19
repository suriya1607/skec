<template>
  <div class="flex items-center justify-between bg-gray-800 text-white px-3 sm:px-4 py-2.5 flex-shrink-0 border-b border-gray-700 z-10 gap-2">
    <!-- Left: Back -->
    <RouterLink
      to="/notes"
      class="flex items-center gap-1.5 text-sm text-gray-300 hover:text-white transition-colors flex-shrink-0"
    >
      <ArrowLeftIcon class="w-4 h-4" />
      <span class="hidden sm:inline text-sm">Back</span>
    </RouterLink>

    <!-- Center: Page controls -->
    <div class="flex items-center gap-1 sm:gap-1.5 flex-shrink-0">
      <button
        @click="$emit('prev')"
        :disabled="loading || currentPage <= 1"
        class="p-1.5 rounded hover:bg-gray-700 disabled:opacity-40 transition"
      >
        <ChevronLeftIcon class="w-4 h-4" />
      </button>

      <div class="flex items-center gap-1">
        <input
          type="number"
          :value="currentPage"
          :min="1"
          :max="totalPages"
          class="w-10 sm:w-12 text-center bg-gray-700 border border-gray-600 rounded text-sm py-0.5 text-white focus:outline-none focus:border-primary-400"
          @change="$emit('page-change', $event.target.value)"
        />
        <span class="text-gray-400 text-sm whitespace-nowrap">/ {{ totalPages }}</span>
      </div>

      <button
        @click="$emit('next')"
        :disabled="loading || currentPage >= totalPages"
        class="p-1.5 rounded hover:bg-gray-700 disabled:opacity-40 transition"
      >
        <ChevronRightIcon class="w-4 h-4" />
      </button>
    </div>

    <!-- Right: Zoom -->
    <div class="flex items-center gap-1 flex-shrink-0">
      <button @click="$emit('zoom-out')" class="p-1.5 rounded hover:bg-gray-700 transition text-base leading-none" title="Zoom Out">−</button>
      <span class="text-xs text-gray-400 min-w-[2.8rem] text-center hidden sm:block">{{ Math.round(zoom * 100) }}%</span>
      <button @click="$emit('zoom-in')" class="p-1.5 rounded hover:bg-gray-700 transition text-base leading-none" title="Zoom In">+</button>
      <button @click="$emit('fit-width')" class="p-1 rounded hover:bg-gray-700 transition text-xs text-gray-300 hidden sm:block" title="Fit Width">Fit</button>
    </div>
  </div>
</template>

<script setup>
import { ArrowLeftIcon, ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/24/outline'

defineProps({
  currentPage: { type: Number, default: 1 },
  totalPages:  { type: Number, default: 0 },
  zoom:        { type: Number, default: 1 },
  loading:     { type: Boolean, default: false },
  noteId:      { type: Number, default: null },
})
defineEmits(['prev', 'next', 'zoom-in', 'zoom-out', 'fit-width', 'page-change'])
</script>