<template>
  <header class="bg-white border-b border-gray-100 sticky top-0 z-40 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-16">
        <!-- Brand -->
        <div class="flex items-center gap-3">
          <div class="w-9 h-9  rounded-xl flex items-center justify-center overflow-hidden">
            <img
              v-if="settingsStore.get('app_logo')"
              :src="settingsStore.get('app_logo')"
              :alt="settingsStore.get('app_name', 'SKEC')"
              class="w-full h-full object-contain p-0.5"
            />
            <span v-else class="text-white font-bold text-sm">SK</span>
          </div>
          <span class="font-bold text-primary-900 hidden sm:block">
            {{ settingsStore.get('app_name', 'SKEC') }}
          </span>
        </div>

        <!-- Nav -->
        <nav class="flex items-center gap-1">
          <RouterLink to="/notes" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-primary-700 hover:bg-primary-50 rounded-lg transition-all" active-class="text-primary-700 bg-primary-50">
            My Notes
          </RouterLink>
          <RouterLink to="/profile" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-primary-700 hover:bg-primary-50 rounded-lg transition-all" active-class="text-primary-700 bg-primary-50">
            Profile
          </RouterLink>
        </nav>

        <!-- User + Logout -->
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-full bg-primary-100 flex items-center justify-center">
            <span class="text-primary-700 font-bold text-sm">{{ authStore.userInitials }}</span>
          </div>
          <button @click="handleLogout" class="text-sm text-gray-500 hover:text-red-600 transition-colors hidden sm:block">
            Logout
          </button>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { useRouter }        from 'vue-router'
import { useAuthStore }     from '../../stores/auth'
import { useSettingsStore } from '../../stores/settings'

const authStore     = useAuthStore()
const settingsStore = useSettingsStore()
const router        = useRouter()

async function handleLogout() {
  await authStore.logout()
  router.push('/login')
}
</script>
