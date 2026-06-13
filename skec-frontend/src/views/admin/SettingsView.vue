<template>
  <div class="max-w-3xl">
    <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mb-5 sm:mb-6">Platform Settings</h1>

    <AppAlert v-if="saved"      type="success" message="Settings saved successfully!" class="mb-5" dismissible />
    <AppAlert v-if="saveError"  type="error"   :message="saveError"                  class="mb-5" dismissible />

    <div v-if="loading" class="py-12"><AppLoader /></div>

    <template v-else>
      <!-- Group tabs -->
      <div class="flex gap-2 flex-wrap mb-5 sm:mb-6">
        <button
          v-for="g in groupList"
          :key="g"
          @click="activeGroup = g"
          :class="[
            'px-3 sm:px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-all capitalize border',
            activeGroup === g
              ? 'bg-primary-700 text-white border-primary-700 shadow-sm'
              : 'bg-white text-gray-600 border-gray-200 hover:border-primary-300',
          ]"
        >
          {{ g }}
        </button>
      </div>

      <!-- Active group panel -->
      <div v-for="(groupSettings, group) in groupedSettings" :key="group">
        <div v-if="activeGroup === group" class="card space-y-6">
          <h2 class="font-bold text-gray-800 capitalize pb-3 border-b border-gray-100 text-base">
            {{ group }} Settings
          </h2>

          <template v-for="(setting, key) in groupSettings" :key="key">

            <!-- Single image upload (logo, favicon, about_image) -->
            <AppImageUpload
              v-if="isImageField(key)"
              :label="formatKey(key)"
              :hint="setting.description"
              :model-value="form[key]"
              :folder="imageFolderFor(key)"
              :preview-class="key === 'app_logo' ? 'max-h-24 object-contain bg-gray-100 p-2' : 'max-h-40 object-cover'"
              @update:model-value="v => { form[key] = v; isDirty = true }"
            />

            <!-- Slider / gallery multi-image manager -->
            <SliderManager
              v-else-if="isSliderField(key)"
              :label="formatKey(key)"
              :hint="setting.description"
              :model-value="form[key]"
              :folder="key === 'gallery_images' ? 'gallery' : 'slider'"
              :show-subcaption="key === 'slider_images'"
              @update:model-value="v => { form[key] = v; isDirty = true }"
            />

            <!-- Boolean toggle -->
            <div v-else-if="setting.type === 'boolean'" class="flex items-start justify-between gap-4 py-1">
              <div class="min-w-0">
                <p class="text-sm font-medium text-gray-800">{{ formatKey(key) }}</p>
                <p v-if="setting.description" class="text-xs text-gray-400 mt-0.5 pr-4">{{ setting.description }}</p>
              </div>
              <label class="relative inline-flex items-center cursor-pointer flex-shrink-0 mt-0.5">
                <input
                  type="checkbox"
                  :checked="isTruthy(form[key])"
                  @change="e => { form[key] = e.target.checked; isDirty = true }"
                  class="sr-only peer"
                />
                <div class="w-10 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-primary-300 rounded-full peer peer-checked:bg-primary-600
                            after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-4" />
              </label>
            </div>

            <!-- Number -->
            <AppInput
              v-else-if="setting.type === 'integer' || setting.type === 'float'"
              :label="formatKey(key)"
              :hint="setting.description"
              type="number"
              :step="setting.type === 'float' ? '0.01' : '1'"
              :model-value="form[key]"
              @update:model-value="v => { form[key] = v; isDirty = true }"
            />

            <!-- JSON array (raw textarea) -->
            <div v-else-if="isJsonField(key)">
              <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ formatKey(key) }}</label>
              <p v-if="setting.description" class="text-xs text-gray-400 mb-2">{{ setting.description }}</p>
              <textarea
                :value="form[key]"
                @input="e => { form[key] = e.target.value; isDirty = true }"
                rows="5"
                class="input-base font-mono text-xs resize-y"
                spellcheck="false"
              />
              <p class="text-xs text-amber-600 mt-1 flex items-center gap-1">
                <ExclamationTriangleIcon class="w-3.5 h-3.5 flex-shrink-0" />
                Must be valid JSON array
              </p>
            </div>

            <!-- Pipe-separated list -->
            <div v-else-if="isPipeField(key)">
              <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ formatKey(key) }}</label>
              <p v-if="setting.description" class="text-xs text-gray-400 mb-2">{{ setting.description }}</p>
              <textarea
                :value="form[key]"
                @input="e => { form[key] = e.target.value; isDirty = true }"
                rows="4"
                class="input-base resize-y text-sm"
                placeholder="Point 1|Point 2|Point 3"
              />
              <p class="text-xs text-gray-400 mt-1">
                Separate items with a pipe <code class="bg-gray-100 px-1 rounded font-mono">|</code>
              </p>
            </div>

            <!-- Long text -->
            <div v-else-if="isLongTextField(key)">
              <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ formatKey(key) }}</label>
              <p v-if="setting.description" class="text-xs text-gray-400 mb-2">{{ setting.description }}</p>
              <textarea
                :value="form[key] ?? ''"
                @input="e => { form[key] = e.target.value; isDirty = true }"
                rows="3"
                class="input-base resize-y text-sm"
              />
            </div>

            <!-- Plain string -->
            <AppInput
              v-else
              :label="formatKey(key)"
              :hint="setting.description"
              :model-value="form[key] ?? ''"
              @update:model-value="v => { form[key] = v; isDirty = true }"
            />

          </template>

          <!-- Save -->
          <div class="pt-3 border-t border-gray-100 flex items-center gap-4 flex-wrap">
            <AppButton variant="primary" :loading="saving" @click="saveSettings">
              Save {{ activeGroup }} Settings
            </AppButton>
            <Transition name="fade">
              <span v-if="isDirty && !saving" class="text-xs text-amber-600 font-medium flex items-center gap-1">
                <span class="w-1.5 h-1.5 bg-amber-500 rounded-full inline-block" />
                Unsaved changes
              </span>
            </Transition>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue'
import { adminSettingsApi }        from '../../api/admin/settings'
import { ExclamationTriangleIcon } from '@heroicons/vue/24/outline'
import AppInput       from '../../components/common/AppInput.vue'
import AppButton      from '../../components/common/AppButton.vue'
import AppAlert       from '../../components/common/AppAlert.vue'
import AppLoader      from '../../components/common/AppLoader.vue'
import AppImageUpload from '../../components/common/Appimageupload.vue'
import SliderManager  from '../../components/common/SliderManager.vue'

// ── Field type detection ────────────────────────────────────────────────
const IMAGE_FIELDS     = ['app_logo', 'app_favicon', 'hero_image']
const SLIDER_FIELDS    = ['slider_images', 'gallery_images']
const JSON_FIELDS      = ['batch_items','openings_items','achievements_items','testimonials_items']
const PIPE_FIELDS      = ['about_points']
const LONG_TEXT_FIELDS = [
  'hero_description','about_description','batch_description',
  'openings_description','achievements_description','cta_description','address','about_cards'
]

function isImageField(key)    { return IMAGE_FIELDS.includes(key) }
function isSliderField(key)   { return SLIDER_FIELDS.includes(key) }
function isJsonField(key)     { return JSON_FIELDS.includes(key) }
function isPipeField(key)     { return PIPE_FIELDS.includes(key) }
function isLongTextField(key) { return LONG_TEXT_FIELDS.includes(key) }

function imageFolderFor(key) {
  if (['app_logo','app_favicon'].includes(key)) return 'logo'
  return 'general'
}

function isTruthy(v) {
  return v === true || v === 'true' || v === 1 || v === '1'
}

// ── State ───────────────────────────────────────────────────────────────
const loading     = ref(true)
const saving      = ref(false)
const saved       = ref(false)
const saveError   = ref('')
const isDirty     = ref(false)
const rawSettings = ref({})
const form        = reactive({})
const activeGroup = ref('general')

const groupedSettings = computed(() => {
  const groups = {}
  for (const [key, data] of Object.entries(rawSettings.value)) {
    const g = data.group || 'general'
    if (!groups[g]) groups[g] = {}
    groups[g][key] = data
  }
  return groups
})

const groupList = computed(() => Object.keys(groupedSettings.value))

function formatKey(key) {
  return key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())
}

// ── Save ────────────────────────────────────────────────────────────────
async function saveSettings() {
  saving.value    = true
  saveError.value = ''
  try {
    const payload = {}
    for (const [key, val] of Object.entries(form)) {
      if (typeof val === 'boolean') {
        payload[key] = val ? 'true' : 'false'
      } else if (val === null || val === undefined) {
        payload[key] = ''
      } else {
        payload[key] = String(val)
      }
    }
    await adminSettingsApi.update(payload)
    saved.value   = true
    isDirty.value = false
    setTimeout(() => { saved.value = false }, 4000)
  } catch (err) {
    saveError.value = err.response?.data?.message || 'Failed to save settings.'
  } finally {
    saving.value = false
  }
}

// ── Load ────────────────────────────────────────────────────────────────
onMounted(async () => {
  try {
    const res = await adminSettingsApi.get()
    rawSettings.value = res.data.data
    for (const [key, data] of Object.entries(rawSettings.value)) {
      if (data.type === 'boolean') {
        form[key] = isTruthy(data.value)
      } else {
        form[key] = data.value ?? ''
      }
    }
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>