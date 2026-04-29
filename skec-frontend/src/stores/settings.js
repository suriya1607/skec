import { defineStore } from 'pinia'
import { ref, watch } from 'vue'
import { settingsApi } from '../api/settings'

export const useSettingsStore = defineStore('settings', () => {
  const settings = ref({})
  const isLoaded = ref(false)

  function get(key, defaultValue = null) {
    return settings.value[key] ?? defaultValue
  }

  /** Dynamically update browser favicon and page title from settings */
  function applyBrowserMeta() {
    // Favicon — prefer app_favicon, fall back to app_logo
    const faviconUrl = settings.value.app_favicon || settings.value.app_logo
    if (faviconUrl) {
      let link = document.querySelector("link[rel~='icon']")
      if (!link) {
        link = document.createElement('link')
        link.rel = 'icon'
        document.head.appendChild(link)
      }
      link.href = faviconUrl
    }

    // Page title
    const appName = settings.value.app_name
    if (appName) {
      document.title = appName
    }
  }

  // Watch for changes and apply
  watch(settings, applyBrowserMeta, { deep: true })

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
