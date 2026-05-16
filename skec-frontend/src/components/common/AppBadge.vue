<template>
  <span :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium', variantClass]">
    <span v-if="dot" :class="['w-1.5 h-1.5 rounded-full mr-1.5', dotClass]" />
    <slot>{{ label }}</slot>
  </span>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  variant: { type: String, default: 'gray' },
  label:   { type: String, default: '' },
  dot:     { type: Boolean, default: false },
})

const variantMap = {
  published: 'bg-green-100 text-green-800',
  draft:     'bg-yellow-100 text-yellow-800',
  active:    'bg-green-100 text-green-800',
  inactive:  'bg-red-100 text-red-700',
  pending:   'bg-blue-100 text-blue-800',
  expired:   'bg-gray-100 text-gray-600',
  used:      'bg-purple-100 text-purple-800',
  admin:     'bg-indigo-100 text-indigo-800',
  student:   'bg-teal-100 text-teal-800',
  gray:      'bg-gray-100 text-gray-700',
  warning:   'bg-orange-100 text-orange-700',
  danger:    'bg-red-100 text-red-700',
}

const dotMap = {
  published: 'bg-green-500',
  active:    'bg-green-500',
  inactive:  'bg-red-500',
  pending:   'bg-blue-500',
  expired:   'bg-gray-400',
  draft:     'bg-yellow-500',
  used:      'bg-purple-500',
}

const variantClass = computed(() => variantMap[props.variant] || variantMap.gray)
const dotClass     = computed(() => dotMap[props.variant] || 'bg-gray-400')
</script>
