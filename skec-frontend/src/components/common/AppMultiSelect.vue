<template>
  <div>
    <label v-if="label" class="block text-sm font-medium text-gray-700 mb-1.5">
      {{ label }}
      <span v-if="required" class="text-red-500 ml-0.5">*</span>
    </label>

    <!-- Selected tags -->
    <div
      class="min-h-[42px] w-full rounded-xl border px-3 py-2 shadow-sm transition cursor-pointer bg-white"
      :class="[
        open ? 'border-primary-500 ring-2 ring-primary-100' : 'border-gray-300',
        error ? 'border-red-400 ring-2 ring-red-100' : ''
      ]"
      @click="toggleDropdown"
    >
      <div v-if="selectedItems.length" class="flex flex-wrap gap-1.5">
        <span
          v-for="item in selectedItems"
          :key="item.value"
          class="inline-flex items-center gap-1 text-xs font-medium px-2 py-1 rounded-lg bg-primary-50 text-primary-700 border border-primary-200"
        >
          <span v-if="item.color" class="w-2 h-2 rounded-full flex-shrink-0" :style="{ background: item.color }" />
          {{ item.label }}
          <button
            type="button"
            class="ml-0.5 hover:text-red-500 transition-colors"
            @click.stop="removeItem(item.value)"
          >
            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </span>
      </div>
      <span v-else class="text-gray-400 text-sm">{{ placeholder }}</span>
    </div>

    <!-- Dropdown -->
    <div v-if="open" class="relative z-50">
      <div class="absolute top-1 left-0 right-0 bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-y-auto">
        <label
          v-for="opt in options"
          :key="opt.value"
          class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer transition-colors"
        >
          <input
            type="checkbox"
            :value="opt.value"
            :checked="modelValue.includes(opt.value)"
            class="rounded border-gray-300 text-primary-600 focus:ring-primary-500"
            @change="toggleItem(opt.value)"
          />
          <span v-if="opt.color" class="w-2.5 h-2.5 rounded-full flex-shrink-0" :style="{ background: opt.color }" />
          <span class="text-sm text-gray-700">{{ opt.label }}</span>
        </label>
        <div v-if="!options.length" class="px-3 py-4 text-sm text-gray-400 text-center">No options available</div>
      </div>
    </div>

    <!-- Click-outside overlay -->
    <div v-if="open" class="fixed inset-0 z-40" @click="open = false" />

    <p v-if="error" class="mt-1.5 text-xs text-red-600">{{ error }}</p>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  modelValue:  { type: Array, default: () => [] },
  label:       { type: String, default: '' },
  options:     { type: Array, default: () => [] },
  placeholder: { type: String, default: 'Select categories…' },
  error:       { type: String, default: '' },
  required:    { type: Boolean, default: false },
})

const emit = defineEmits(['update:modelValue'])

const open = ref(false)

const selectedItems = computed(() =>
  props.modelValue
    .map(v => props.options.find(o => o.value === v))
    .filter(Boolean)
)

function toggleDropdown() {
  open.value = !open.value
}

function toggleItem(value) {
  const current = [...props.modelValue]
  const idx = current.indexOf(value)
  if (idx > -1) {
    current.splice(idx, 1)
  } else {
    current.push(value)
  }
  emit('update:modelValue', current)
}

function removeItem(value) {
  emit('update:modelValue', props.modelValue.filter(v => v !== value))
}
</script>
