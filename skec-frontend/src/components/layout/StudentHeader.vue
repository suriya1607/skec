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
          <RouterLink to="/notes" class="px-2.5 sm:px-4 py-2 text-sm font-medium text-gray-600 hover:text-primary-700 hover:bg-primary-50 rounded-lg transition-all" active-class="text-primary-700 bg-primary-50">
            My Notes
          </RouterLink>
          <RouterLink to="/profile" class="px-2.5 sm:px-4 py-2 text-sm font-medium text-gray-600 hover:text-primary-700 hover:bg-primary-50 rounded-lg transition-all" active-class="text-primary-700 bg-primary-50">
            Profile
          </RouterLink>
          <RouterLink to="/review" class="px-2.5 sm:px-4 py-2 text-sm font-medium text-gray-600 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all" active-class="text-amber-600 bg-amber-50">
            ⭐ My Review
          </RouterLink>
        </nav>

        <!-- User + Notification Bell + Logout -->
        <div class="flex items-center gap-2 sm:gap-3">

          <!-- ── Bell button with red badge ── -->
          <div class="relative" ref="bellRef">
            <button
              id="notification-bell-btn"
              type="button"
              class="relative inline-flex items-center justify-center w-9 h-9 rounded-lg text-gray-500 hover:bg-primary-50 hover:text-primary-700 transition-colors focus:outline-none"
              :title="notifStore.unreadCount > 0 ? `${notifStore.unreadCount} unread notification(s)` : 'Notifications'"
              @click="togglePanel"
            >
              <BellIcon class="w-5 h-5" />
              <!-- Red dot badge -->
              <span
                v-if="notifStore.unreadCount > 0"
                class="absolute -top-0.5 -right-0.5 flex h-4 w-4 items-center justify-center"
              >
                <!-- Ping animation ring -->
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75" />
                <span class="relative inline-flex rounded-full h-4 w-4 bg-red-500 items-center justify-center">
                  <span class="text-[9px] font-bold text-white leading-none">
                    {{ notifStore.unreadCount > 9 ? '9+' : notifStore.unreadCount }}
                  </span>
                </span>
              </span>
            </button>

            <!-- Notification panel dropdown -->
            <NotificationPanel
              :open="panelOpen"
              :anchor-el="bellRef"
              @close="panelOpen = false"
            />
          </div>

          <!-- Avatar -->
          <img
            v-if="authStore.user?.photo_url"
            :src="authStore.user.photo_url"
            :alt="authStore.user?.name || 'Profile image'"
            class="hidden sm:block w-9 h-9 rounded-full object-cover border border-gray-200"
          />
          <div v-else class="hidden sm:flex w-9 h-9 rounded-full bg-primary-100 items-center justify-center">
            <span class="text-primary-700 font-bold text-sm">{{ authStore.userInitials }}</span>
          </div>

          <!-- Logout -->
          <button
            type="button"
            class="inline-flex items-center justify-center rounded-lg p-2 text-gray-500 hover:bg-red-50 hover:text-red-600 transition-colors sm:px-3 sm:py-2"
            title="Logout"
            @click="handleLogout"
          >
            <ArrowRightOnRectangleIcon class="w-5 h-5 sm:hidden" />
            <span class="hidden sm:inline text-sm">Logout</span>
            <span class="sr-only">Logout</span>
          </button>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { useRouter }            from 'vue-router'
import { useAuthStore }         from '../../stores/auth'
import { useSettingsStore }     from '../../stores/settings'
import { useNotificationsStore } from '../../stores/notifications'
import { BellIcon, ArrowRightOnRectangleIcon } from '@heroicons/vue/24/outline'
import NotificationPanel from '../common/NotificationPanel.vue'

const authStore     = useAuthStore()
const settingsStore = useSettingsStore()
const notifStore    = useNotificationsStore()
const router        = useRouter()

const panelOpen = ref(false)
const bellRef   = ref(null)

function togglePanel(e) {
  // Stop propagation so the click doesn't bubble to body and trigger close
  e.stopPropagation()
  panelOpen.value = !panelOpen.value
}

onMounted(() => {
  notifStore.startPolling()
})

onBeforeUnmount(() => {
  notifStore.stopPolling()
})

async function handleLogout() {
  notifStore.stopPolling()
  await authStore.logout()
  router.push('/login')
}
</script>
