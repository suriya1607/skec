<template>
  <div>
    <label v-if="label" :for="id" class="block text-sm font-medium text-gray-700 mb-1.5">
      {{ label }}
      <span v-if="required" class="text-red-500 ml-0.5">*</span>
    </label>
    <select
      :id="id"
      v-bind="$attrs"
      :value="modelValue"
      :disabled="disabled"
      :class="[
        'w-full rounded-xl border px-3.5 py-2.5 shadow-sm transition',
        'focus:outline-none focus:ring-2 focus:ring-offset-0',
        'disabled:bg-gray-100 disabled:cursor-not-allowed',
        'text-base sm:text-sm appearance-none bg-white',
        error
          ? 'border-red-400 focus:border-red-500 focus:ring-red-200'
          : 'border-gray-300 focus:border-primary-500 focus:ring-primary-100',
      ]"
      :style="selectStyle"
      @change="$emit('update:modelValue', $event.target.value)"
    >
      <option v-if="placeholder" value="">{{ placeholder }}</option>
      <option v-for="opt in options" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
    </select>
    <p v-if="error" class="mt-1.5 text-xs text-red-600">{{ error }}</p>
  </div>
</template>

<script setup>
defineProps({
  modelValue:  { default: '' },
  id:          { type: String,  default: () => `select-${Math.random().toString(36).slice(2)}` },
  label:       { type: String,  default: '' },
  options:     { type: Array,   default: () => [] },
  placeholder: { type: String,  default: 'Select…' },
  error:       { type: String,  default: '' },
  disabled:    { type: Boolean, default: false },
  required:    { type: Boolean, default: false },
})
defineEmits(['update:modelValue'])
const selectStyle = {
  backgroundImage: `url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e")`,
  backgroundPosition: 'right 0.75rem center',
  backgroundRepeat: 'no-repeat',
  backgroundSize: '1.25em'
}
</script>