<template>
  <div>
    <label v-if="label" class="block text-sm font-medium text-gray-700 mb-1.5">{{ label }}</label>
    <p v-if="hint" class="text-xs text-gray-400 mb-2">{{ hint }}</p>

    <!-- Current image preview -->
    <div v-if="modelValue" class="mb-3 relative group">
      <img
        :src="modelValue"
        :alt="label || 'Uploaded image'"
        class="w-full object-cover rounded-xl border border-gray-200"
        :class="previewClass || 'max-h-40'"
      />
      <!-- Remove overlay -->
      <div class="absolute inset-0 bg-black/40 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
        <button
          type="button"
          @click="openChange"
          class="px-3 py-1.5 bg-white text-gray-800 rounded-lg text-xs font-semibold hover:bg-gray-100 transition"
        >
          Change
        </button>
        <button
          type="button"
          @click="removeImage"
          class="px-3 py-1.5 bg-red-600 text-white rounded-lg text-xs font-semibold hover:bg-red-700 transition"
        >
          Remove
        </button>
      </div>
    </div>

    <!-- Upload dropzone (shown when no image or always) -->
    <div
      v-show="!modelValue || alwaysShowUpload"
      :class="[
        'relative border-2 border-dashed rounded-xl transition-all duration-200 cursor-pointer text-center p-5',
        isDragging
          ? 'border-primary-400 bg-primary-50'
          : 'border-gray-300 hover:border-primary-400 hover:bg-gray-50',
        uploading ? 'pointer-events-none opacity-60' : '',
      ]"
      @dragover.prevent="isDragging = true"
      @dragleave.prevent="isDragging = false"
      @drop.prevent="onDrop"
      @click="fileInput.click()"
    >
      <input
        ref="fileInput"
        type="file"
        :accept="accept"
        class="hidden"
        @change="onFileChange"
      />

      <div v-if="!uploading">
        <PhotoIcon class="w-8 h-8 text-gray-400 mx-auto mb-2" />
        <p class="text-sm font-medium text-gray-700">
          <span class="sm:hidden">Tap to upload image</span>
          <span class="hidden sm:inline">Drop image here or <span class="text-primary-600">click to browse</span></span>
        </p>
        <p class="text-xs text-gray-400 mt-1">{{ acceptLabel }} · Max {{ maxSizeMb }}MB</p>
      </div>

      <!-- Upload progress -->
      <div v-else class="py-2">
        <div class="w-8 h-8 border-3 border-primary-200 border-t-primary-600 rounded-full animate-spin mx-auto mb-2" />
        <p class="text-sm text-gray-600">Uploading… {{ progress }}%</p>
        <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2">
          <div class="bg-primary-600 h-1.5 rounded-full transition-all" :style="{ width: progress + '%' }" />
        </div>
      </div>
    </div>

    <p v-if="error" class="mt-1.5 text-xs text-red-600">{{ error }}</p>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { PhotoIcon } from '@heroicons/vue/24/outline'
import { adminMediaApi } from '../../api/admin/media'

const props = defineProps({
  modelValue:      { type: String,  default: null },     // current image URL
  label:           { type: String,  default: '' },
  hint:            { type: String,  default: '' },
  folder:          { type: String,  default: 'general' }, // 'logo'|'slider'|'gallery'|'general'
  accept:          { type: String,  default: 'image/jpeg,image/png,image/webp,image/gif,image/svg+xml' },
  maxSizeMb:       { type: Number,  default: 5 },
  previewClass:    { type: String,  default: 'max-h-40' },
  alwaysShowUpload:{ type: Boolean, default: false },
})

const emit = defineEmits(['update:modelValue'])

const fileInput  = ref(null)
const isDragging = ref(false)
const uploading  = ref(false)
const progress   = ref(0)
const error      = ref('')

const acceptLabel = computed(() => {
  if (props.accept.includes('svg')) return 'JPG, PNG, WebP, SVG'
  return 'JPG, PNG, WebP'
})

function openChange() {
  fileInput.value?.click()
}

function removeImage() {
  emit('update:modelValue', null)
}

function onDrop(e) {
  isDragging.value = false
  const file = e.dataTransfer.files[0]
  if (file) processFile(file)
}

function onFileChange(e) {
  const file = e.target.files[0]
  if (file) processFile(file)
  // Reset input so same file can be re-selected
  e.target.value = ''
}

async function processFile(file) {
  // Validate size
  if (file.size > props.maxSizeMb * 1024 * 1024) {
    error.value = `File too large. Max size is ${props.maxSizeMb}MB.`
    return
  }
  error.value  = ''
  uploading.value = true
  progress.value  = 0

  try {
    const res = await adminMediaApi.upload(file, props.folder, (p) => {
      progress.value = p
    })
    emit('update:modelValue', res.data.data.url)
  } catch (err) {
    error.value = err.response?.data?.message || 'Upload failed. Please try again.'
  } finally {
    uploading.value = false
    progress.value  = 0
  }
}
</script>