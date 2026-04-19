<template>
  <div>
    <label v-if="label" class="block text-sm font-medium text-gray-700 mb-1.5">{{ label }}</label>

    <!-- Drop zone -->
    <div
      :class="[
        'relative border-2 border-dashed rounded-2xl transition-all duration-200 p-6 sm:p-8 text-center cursor-pointer',
        isDragging ? 'border-primary-400 bg-primary-50' : 'border-gray-300 hover:border-primary-400 hover:bg-gray-50',
        error ? 'border-red-400 bg-red-50' : '',
      ]"
      @dragover.prevent="isDragging = true"
      @dragleave.prevent="isDragging = false"
      @drop.prevent="onDrop"
      @click="fileInput.click()"
    >
      <input
        ref="fileInput"
        type="file"
        accept=".pdf"
        class="hidden"
        @change="onFileChange"
      />

      <div v-if="!file">
        <ArrowUpTrayIcon class="w-10 h-10 text-gray-400 mx-auto mb-3" />
        <p class="text-sm font-medium text-gray-700 mb-1">
          <span class="sm:hidden">Tap to select a PDF</span>
          <span class="hidden sm:inline">Drop PDF here, or <span class="text-primary-600">click to browse</span></span>
        </p>
        <p class="text-xs text-gray-400">PDF only · Max {{ maxSizeMb }}MB</p>
      </div>

      <div v-else class="flex items-center justify-center gap-3 flex-wrap">
        <DocumentIcon class="w-8 h-8 text-primary-600 flex-shrink-0" />
        <div class="text-left min-w-0 flex-1">
          <p class="text-sm font-medium text-gray-800 truncate">{{ file.name }}</p>
          <p class="text-xs text-gray-400">{{ formatBytes(file.size) }}</p>
        </div>
        <button
          type="button"
          class="text-red-500 hover:text-red-700 flex-shrink-0 p-1"
          @click.stop="clearFile"
        >
          <XCircleIcon class="w-6 h-6" />
        </button>
      </div>
    </div>

    <!-- Progress -->
    <div v-if="progress > 0 && progress < 100" class="mt-3">
      <div class="flex justify-between text-xs text-gray-500 mb-1">
        <span>Uploading…</span>
        <span>{{ progress }}%</span>
      </div>
      <div class="w-full bg-gray-200 rounded-full h-2">
        <div
          class="bg-primary-600 h-2 rounded-full transition-all duration-200"
          :style="{ width: progress + '%' }"
        />
      </div>
    </div>

    <p v-if="error" class="mt-1.5 text-xs text-red-600">{{ error }}</p>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { ArrowUpTrayIcon, DocumentIcon, XCircleIcon } from '@heroicons/vue/24/outline'
import { formatBytes } from '../../utils/helpers'

const props = defineProps({
  modelValue: { default: null },
  label:      { type: String,  default: '' },
  maxSizeMb:  { type: Number,  default: 50 },
  error:      { type: String,  default: '' },
  progress:   { type: Number,  default: 0 },
})

const emit      = defineEmits(['update:modelValue'])
const fileInput = ref(null)
const isDragging = ref(false)
const file      = ref(props.modelValue)

function onDrop(e) {
  isDragging.value = false
  const f = e.dataTransfer.files[0]
  if (f) setFile(f)
}
function onFileChange(e) {
  const f = e.target.files[0]
  if (f) setFile(f)
}
function setFile(f) {
  file.value = f
  emit('update:modelValue', f)
}
function clearFile() {
  file.value = null
  if (fileInput.value) fileInput.value.value = ''
  emit('update:modelValue', null)
}
</script>