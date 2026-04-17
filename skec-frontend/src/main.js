import { createApp }    from 'vue'
import { createPinia }  from 'pinia'
import Toast, { POSITION } from 'vue-toastification'
import 'vue-toastification/dist/index.css'

import App    from './App.vue'
import router from './router'

import './assets/css/main.css'

import { useSettingsStore } from './stores/settings'
import { useAuthStore }     from './stores/auth'

async function bootstrap() {
  const app   = createApp(App)
  const pinia = createPinia()

  app.use(pinia)
  app.use(router)
  app.use(Toast, {
    position: POSITION.TOP_RIGHT,
    timeout: 4000,
    closeOnClick: true,
    pauseOnHover: true,
  })

  // Initialize settings and auth BEFORE mounting
  const settingsStore = useSettingsStore(pinia)
  const authStore     = useAuthStore(pinia)

  await settingsStore.fetchPublicSettings()
  await authStore.initializeAuth()

  // Dynamic page title
  document.title = settingsStore.get('app_name', import.meta.env.VITE_APP_NAME || 'SKEC')

  app.mount('#app')
}

bootstrap()
