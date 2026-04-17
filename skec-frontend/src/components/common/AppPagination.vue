<template>
  <div v-if="meta && meta.last_page > 1" class="flex items-center justify-between px-1 py-4">
    <p class="text-sm text-gray-500">
      Showing <span class="font-medium">{{ meta.from }}</span>–<span class="font-medium">{{ meta.to }}</span>
      of <span class="font-medium">{{ meta.total }}</span>
    </p>
    <div class="flex items-center gap-1">
      <button
        :disabled="meta.current_page === 1"
        @click="$emit('change', meta.current_page - 1)"
        class="px-3 py-1.5 rounded-lg text-sm font-medium border border-gray-200 hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed transition"
      >
        ← Prev
      </button>
      <template v-for="page in visiblePages" :key="page">
        <button
          v-if="page !== '…'"
          @click="$emit('change', page)"
          :class="[
            'px-3 py-1.5 rounded-lg text-sm font-medium border transition',
            page === meta.current_page
              ? 'bg-primary-700 text-white border-primary-700'
              : 'border-gray-200 hover:bg-gray-50',
          ]"
        >{{ page }}</button>
        <span v-else class="px-2 text-gray-400">…</span>
      </template>
      <button
        :disabled="meta.current_page === meta.last_page"
        @click="$emit('change', meta.current_page + 1)"
        class="px-3 py-1.5 rounded-lg text-sm font-medium border border-gray-200 hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed transition"
      >
        Next →
      </button>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
const props = defineProps({ meta: { type: Object, default: null } })
defineEmits(['change'])

const visiblePages = computed(() => {
  if (!props.meta) return []
  const { current_page: c, last_page: l } = props.meta
  const pages = []
  for (let i = 1; i <= l; i++) {
    if (i === 1 || i === l || (i >= c - 1 && i <= c + 1)) pages.push(i)
    else if (pages[pages.length - 1] !== '…') pages.push('…')
  }
  return pages
})
</script>
