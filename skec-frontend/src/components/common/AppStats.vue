<template>
  <div class="stats-card">
    <div class="flex items-start justify-between">
      <div>
        <p class="text-sm font-medium text-gray-500">{{ label }}</p>
        <p class="mt-2 text-3xl font-bold text-gray-900">{{ value }}</p>
        <p v-if="sub" class="mt-1 text-xs text-gray-400">{{ sub }}</p>
      </div>
      <div :class="['w-12 h-12 rounded-xl flex items-center justify-center', iconBg || 'bg-primary-50']">
        <component :is="icon" :class="['w-6 h-6', iconColor || 'text-primary-600']" />
      </div>
    </div>
    <div v-if="trend !== undefined" class="mt-4 flex items-center gap-1 text-sm">
      <ArrowTrendingUpIcon v-if="trend >= 0" class="w-4 h-4 text-green-500" />
      <ArrowTrendingDownIcon v-else class="w-4 h-4 text-red-500" />
      <span :class="trend >= 0 ? 'text-green-600' : 'text-red-600'" class="font-medium">
        {{ Math.abs(trend) }}%
      </span>
      <span class="text-gray-400">vs last month</span>
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
