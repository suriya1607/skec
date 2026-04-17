import { defineStore } from 'pinia'
import { ref } from 'vue'
import { settingsApi } from '../api/settings'

export const useSettingsStore = defineStore('settings', () => {
  const settings = ref({})
  const isLoaded = ref(false)

  function get(key, defaultValue = null) {
    return settings.value[key] ?? defaultValue
  }

  async function fetchPublicSettings() {
    try {
      const res = await settingsApi.getPublic()
      settings.value = res.data.data || {}
      isLoaded.value = true
    } catch {
      // Use defaults if API unavailable
      isLoaded.value = true
    }
  }

  function setFromLogin(data) {
    if (data) {
      settings.value = data
      isLoaded.value = true
    }
  }

  return { settings, isLoaded, get, fetchPublicSettings, setFromLogin }
})
