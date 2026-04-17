<template>
  <div class="max-w-3xl">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Platform Settings</h1>
    <AppAlert v-if="saved" type="success" message="Settings saved!" class="mb-5" dismissible />

    <div v-if="loading" class="py-12"><AppLoader /></div>

    <template v-else>
      <div v-for="(groupSettings, group) in groupedSettings" :key="group" class="card mb-5">
        <h2 class="font-semibold text-gray-800 capitalize mb-4 pb-2 border-b border-gray-100">
          {{ group }} Settings
        </h2>
        <div class="space-y-4">
          <div v-for="(setting, key) in groupSettings" :key="key">
            <!-- Boolean toggle -->
            <div v-if="setting.type === 'boolean'" class="flex items-center justify-between py-1">
              <div>
                <p class="text-sm font-medium text-gray-800">{{ formatKey(key) }}</p>
                <p v-if="setting.description" class="text-xs text-gray-400">{{ setting.description }}</p>
              </div>
              <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" :checked="form[key]" @change="form[key] = $event.target.checked" class="sr-only peer" />
                <div class="w-10 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-primary-300 rounded-full peer peer-checked:bg-primary-600 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-4"></div>
              </label>
            </div>

            <!-- Number/float -->
            <AppInput
              v-else-if="setting.type === 'integer' || setting.type === 'float'"
              :label="formatKey(key)"
              :hint="setting.description"
              :type="'number'"
              :step="setting.type === 'float' ? '0.01' : '1'"
              :model-value="form[key]"
              @update:model-value="form[key] = $event"
            />

            <!-- String -->
            <AppInput
              v-else
              :label="formatKey(key)"
              :hint="setting.description"
              :model-value="form[key] ?? ''"
              @update:model-value="form[key] = $event"
            />
          </div>
        </div>
      </div>

      <AppButton variant="primary" :loading="saving" @click="saveSettings">
        Save All Settings
      </AppButton>
    </template>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue'
import { adminSettingsApi } from '../../api/admin/settings'
import AppInput  from '../../components/common/AppInput.vue'
import AppButton from '../../components/common/AppButton.vue'
import AppAlert  from '../../components/common/AppAlert.vue'
import AppLoader from '../../components/common/AppLoader.vue'

const loading  = ref(true)
const saving   = ref(false)
const saved    = ref(false)
const rawSettings = ref({})
const form     = reactive({})

const groupedSettings = computed(() => {
  const groups = {}
  for (const [key, data] of Object.entries(rawSettings.value)) {
    const g = data.group || 'general'
    if (!groups[g]) groups[g] = {}
    groups[g][key] = data
  }
  return groups
})

function formatKey(key) {
  return key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())
}

async function saveSettings() {
  saving.value = true
  try {
    await adminSettingsApi.update(form)
    saved.value = true
    setTimeout(() => { saved.value = false }, 3000)
  } finally { saving.value = false }
}

onMounted(async () => {
  const res = await adminSettingsApi.get()
  rawSettings.value = res.data.data
  for (const [key, data] of Object.entries(rawSettings.value)) {
    form[key] = data.value
  }
  loading.value = false
})
</script>
