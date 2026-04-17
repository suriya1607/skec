<template>
  <Transition name="alert">
    <div v-if="show" :class="['flex items-start gap-3 p-4 rounded-xl border', variantClass]">
      <component :is="iconComponent" class="w-5 h-5 mt-0.5 flex-shrink-0" />
      <div class="flex-1 min-w-0">
        <p v-if="title" class="font-semibold text-sm">{{ title }}</p>
        <p class="text-sm"><slot>{{ message }}</slot></p>
      </div>
      <button v-if="dismissible" @click="show = false" class="ml-2 opacity-60 hover:opacity-100 flex-shrink-0">
        <XMarkIcon class="w-4 h-4" />
      </button>
    </div>
  </Transition>
</template>

<script setup>
import { ref, computed } from 'vue'
import { CheckCircleIcon, ExclamationCircleIcon, InformationCircleIcon, ExclamationTriangleIcon, XMarkIcon } from '@heroicons/vue/20/solid'

const props = defineProps({
  type:        { type: String, default: 'info' },
  title:       { type: String, default: '' },
  message:     { type: String, default: '' },
  dismissible: { type: Boolean, default: false },
})
const show = ref(true)
const variantClass = computed(() => ({
  success: 'bg-green-50 border-green-200 text-green-800',
  error:   'bg-red-50 border-red-200 text-red-800',
  warning: 'bg-yellow-50 border-yellow-200 text-yellow-800',
  info:    'bg-blue-50 border-blue-200 text-blue-800',
}[props.type]))
const iconComponent = computed(() => ({
  success: CheckCircleIcon,
  error:   ExclamationCircleIcon,
  warning: ExclamationTriangleIcon,
  info:    InformationCircleIcon,
}[props.type]))
</script>

<style scoped>
.alert-enter-active, .alert-leave-active { transition: all 0.25s ease; }
.alert-enter-from, .alert-leave-to { opacity: 0; transform: translateY(-6px); }
</style>
