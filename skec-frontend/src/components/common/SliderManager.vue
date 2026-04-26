<template>
  <div>
    <label v-if="label" class="block text-sm font-medium text-gray-700 mb-1.5">{{ label }}</label>
    <p v-if="hint" class="text-xs text-gray-400 mb-3">{{ hint }}</p>

    <!-- Current slides list -->
    <div v-if="items.length" class="space-y-3 mb-4">
      <div
        v-for="(item, idx) in items"
        :key="idx"
        class="flex items-start gap-3 bg-gray-50 border border-gray-200 rounded-xl p-3"
      >
        <!-- Thumbnail -->
        <img
          :src="item.url"
          class="w-20 h-14 object-cover rounded-lg flex-shrink-0 border border-gray-200"
          :alt="item.caption || `Slide ${idx + 1}`"
        />
        <!-- Fields -->
        <div class="flex-1 min-w-0 space-y-2">
          <input
            v-model="item.caption"
            type="text"
            :placeholder="captionLabel || 'Caption (optional)'"
            class="w-full text-xs border border-gray-300 rounded-lg px-3 py-1.5 focus:outline-none focus:border-primary-500"
            @input="emitUpdate"
          />
          <input
            v-if="showSubcaption"
            v-model="item.subcaption"
            type="text"
            placeholder="Sub-caption (optional)"
            class="w-full text-xs border border-gray-300 rounded-lg px-3 py-1.5 focus:outline-none focus:border-primary-500"
            @input="emitUpdate"
          />
        </div>
        <!-- Controls -->
        <div class="flex flex-col gap-1 flex-shrink-0">
          <button
            type="button"
            :disabled="idx === 0"
            @click="moveUp(idx)"
            class="p-1 rounded hover:bg-gray-200 disabled:opacity-30 transition"
            title="Move up"
          >
            <ChevronUpIcon class="w-4 h-4 text-gray-500" />
          </button>
          <button
            type="button"
            :disabled="idx === items.length - 1"
            @click="moveDown(idx)"
            class="p-1 rounded hover:bg-gray-200 disabled:opacity-30 transition"
            title="Move down"
          >
            <ChevronDownIcon class="w-4 h-4 text-gray-500" />
          </button>
          <button
            type="button"
            @click="removeItem(idx)"
            class="p-1 rounded hover:bg-red-100 transition"
            title="Remove"
          >
            <XMarkIcon class="w-4 h-4 text-red-500" />
          </button>
        </div>
      </div>
    </div>

    <!-- Empty state -->
    <div v-else-if="!uploading" class="text-center py-6 text-gray-400 text-sm bg-gray-50 rounded-xl border border-dashed border-gray-200 mb-4">
      No images added yet
    </div>

    <!-- Upload zone -->
    <div
      :class="[
        'border-2 border-dashed rounded-xl transition-all duration-200 cursor-pointer text-center p-5',
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
        accept="image/jpeg,image/png,image/webp"
        multiple
        class="hidden"
        @change="onFilesChange"
      />

      <div v-if="!uploading">
        <ArrowUpTrayIcon class="w-7 h-7 text-gray-400 mx-auto mb-2" />
        <p class="text-sm font-medium text-gray-700">
          <span class="sm:hidden">Tap to add images</span>
          <span class="hidden sm:inline">Drop images here or <span class="text-primary-600">click to browse</span></span>
        </p>
        <p class="text-xs text-gray-400 mt-1">JPG, PNG, WebP · Max 5MB each · Multiple allowed</p>
      </div>

      <div v-else class="py-2">
        <div class="w-7 h-7 border-2 border-primary-200 border-t-primary-600 rounded-full animate-spin mx-auto mb-2" />
        <p class="text-sm text-gray-600 mb-1">Uploading {{ uploadCount }} image(s)… {{ progress }}%</p>
        <div class="w-full bg-gray-200 rounded-full h-1.5">
          <div class="bg-primary-600 h-1.5 rounded-full transition-all" :style="{ width: progress + '%' }" />
        </div>
      </div>
    </div>

    <p v-if="error" class="mt-1.5 text-xs text-red-600">{{ error }}</p>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import {
  ArrowUpTrayIcon, XMarkIcon,
  ChevronUpIcon, ChevronDownIcon,
} from '@heroicons/vue/24/outline'
import { adminMediaApi } from '../../api/admin/media'

const props = defineProps({
  /**
   * v-model = JSON string: '[{"url":"...","caption":"...","subcaption":"..."}]'
   * or an array directly
   */
  modelValue:    { default: '[]' },
  label:         { type: String,  default: '' },
  hint:          { type: String,  default: '' },
  folder:        { type: String,  default: 'slider' },
  captionLabel:  { type: String,  default: 'Caption (optional)' },
  showSubcaption:{ type: Boolean, default: true },
})

const emit = defineEmits(['update:modelValue'])

const fileInput  = ref(null)
const isDragging = ref(false)
const uploading  = ref(false)
const progress   = ref(0)
const uploadCount = ref(0)
const error      = ref('')

// Internal parsed array
const items = ref([])

// Parse incoming modelValue
function parseValue(val) {
  if (!val) return []
  if (Array.isArray(val)) return val
  try { return JSON.parse(val) } catch { return [] }
}

watch(() => props.modelValue, (v) => {
  const parsed = parseValue(v)
  // Only update if different to avoid infinite loop
  if (JSON.stringify(parsed) !== JSON.stringify(items.value)) {
    items.value = parsed
  }
}, { immediate: true })

function emitUpdate() {
  emit('update:modelValue', JSON.stringify(items.value))
}

function removeItem(idx) {
  items.value.splice(idx, 1)
  emitUpdate()
}

function moveUp(idx) {
  if (idx === 0) return
  const arr = [...items.value]
  ;[arr[idx - 1], arr[idx]] = [arr[idx], arr[idx - 1]]
  items.value = arr
  emitUpdate()
}

function moveDown(idx) {
  if (idx === items.value.length - 1) return
  const arr = [...items.value]
  ;[arr[idx], arr[idx + 1]] = [arr[idx + 1], arr[idx]]
  items.value = arr
  emitUpdate()
}

function onDrop(e) {
  isDragging.value = false
  const files = Array.from(e.dataTransfer.files).filter(f => f.type.startsWith('image/'))
  if (files.length) processFiles(files)
}

function onFilesChange(e) {
  const files = Array.from(e.target.files)
  if (files.length) processFiles(files)
  e.target.value = ''
}

async function processFiles(files) {
  error.value    = ''
  uploading.value = true
  progress.value  = 0
  uploadCount.value = files.length

  try {
    // Upload one at a time to show accurate progress
    for (let i = 0; i < files.length; i++) {
      const res = await adminMediaApi.upload(files[i], props.folder, (p) => {
        // Weighted progress across all files
        progress.value = Math.round(((i / files.length) + (p / 100) / files.length) * 100)
      })
      items.value.push({
        url:        res.data.data.url,
        caption:    '',
        subcaption: '',
      })
    }
    progress.value = 100
    emitUpdate()
  } catch (err) {
    error.value = err.response?.data?.message || 'Upload failed.'
  } finally {
    uploading.value  = false
    progress.value   = 0
    uploadCount.value = 0
  }
}
</script>