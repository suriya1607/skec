<template>
  <div class="stats-card">
    <div class="flex items-start justify-between gap-2">
      <div class="min-w-0">
        <p class="text-xs sm:text-sm font-medium text-gray-500 truncate">{{ label }}</p>
        <p class="mt-1 sm:mt-2 text-2xl sm:text-3xl font-bold text-gray-900">{{ value }}</p>
        <p v-if="sub" class="mt-0.5 sm:mt-1 text-xs text-gray-400">{{ sub }}</p>
      </div>
      <div :class="['w-9 h-9 sm:w-12 sm:h-12 rounded-xl flex items-center justify-center flex-shrink-0', iconBg || 'bg-primary-50']">
        <component :is="icon" :class="['w-4 h-4 sm:w-6 sm:h-6', iconColor || 'text-primary-600']" />
      </div>
    </div>
    <div v-if="trend !== undefined" class="mt-3 sm:mt-4 flex items-center gap-1 text-xs sm:text-sm">
      <ArrowTrendingUpIcon v-if="trend >= 0" class="w-3.5 h-3.5 text-green-500" />
      <ArrowTrendingDownIcon v-else class="w-3.5 h-3.5 text-red-500" />
      <span :class="trend >= 0 ? 'text-green-600' : 'text-red-600'" class="font-medium">
        {{ Math.abs(trend) }}%
      </span>
      <span class="text-gray-400 hidden sm:inline">vs last month</span>
    </div>
  </div>
</template>

<script setup>
import { ArrowTrendingUpIcon, ArrowTrendingDownIcon } from '@heroicons/vue/24/outline'
defineProps({
  label:     { type: String, required: true },
  value:     { required: true },
  sub:       { type: String, default: '' },
  icon:      { default: null },
  iconBg:    { type: String, default: '' },
  iconColor: { type: String, default: '' },
  trend:     { type: Number, default: undefined },
})
</script>