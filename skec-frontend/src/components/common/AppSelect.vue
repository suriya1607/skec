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
      :class="['input-base', error ? 'border-red-400 focus:border-red-500 focus:ring-red-500' : '']"
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
  modelValue: { default: '' },
  id:         { type: String, default: () => `select-${Math.random().toString(36).slice(2)}` },
  label:      { type: String, default: '' },
  options:    { type: Array, default: () => [] },
  placeholder:{ type: String, default: 'Select…' },
  error:      { type: String, default: '' },
  disabled:   { type: Boolean, default: false },
  required:   { type: Boolean, default: false },
})
defineEmits(['update:modelValue'])
</script>
