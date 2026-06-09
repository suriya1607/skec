<template>
  <!-- Mobile overlay -->
    <Transition name="fade">
      <div
        v-if="uiStore.sidebarOpen && isMobile"
        class="fixed inset-0 bg-black/50 z-30 lg:hidden"
        @click="uiStore.sidebarOpen = false"
      />
    </Transition>

    <!-- Sidebar -->
    <Transition name="slide">
      <aside
        v-show="uiStore.sidebarOpen || !isMobile"
        :class="[
          'bg-white border-r border-gray-100 flex flex-col shadow-sm z-40 flex-shrink-0',
          'fixed lg:relative inset-y-0 left-0',
          isMobile ? 'w-64' : (uiStore.sidebarOpen ? 'w-64' : 'w-16'),
        ]"
      >
        <!-- Brand -->
        <div class="flex items-center gap-3 px-5 py-5 border-b border-gray-100 flex-shrink-0">
          <div class="flex-shrink-0 w-9 h-9 rounded-xl flex items-center justify-center overflow-hidden">
            <img
              v-if="settingsStore.get('app_logo')"
              :src="settingsStore.get('app_logo')"
              :alt="settingsStore.get('app_name', 'SKEC')"
              class="w-full h-full object-contain p-0.5"
            />
            <span v-else class="text-white font-bold text-sm">SK</span>
          </div>
          <div v-if="uiStore.sidebarOpen || isMobile" class="min-w-0">
            <p class="text-sm font-bold text-primary-900 truncate">SKEC</p>
            <p class="text-xs text-gray-400 truncate">Admin Panel</p>
          </div>
          <!-- Close button on mobile -->
          <button
            v-if="isMobile"
            class="ml-auto p-1 rounded-lg hover:bg-gray-100"
            @click="uiStore.sidebarOpen = false"
          >
            <XMarkIcon class="w-4 h-4 text-gray-400" />
          </button>
        </div>

        <!-- Nav -->
        <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">
          <RouterLink
            v-for="item in navItems"
            :key="item.name"
            :to="item.to"
            class="sidebar-link"
            :class="{ active: isActive(item.to) }"
            :title="item.label"
            @click="isMobile && (uiStore.sidebarOpen = false)"
          >
            <component :is="item.icon" class="w-5 h-5 flex-shrink-0" />
            <span v-if="uiStore.sidebarOpen || isMobile" class="truncate">{{ item.label }}</span>
          </RouterLink>
        </nav>

        <!-- Logout -->
        <div class="px-3 py-4 border-t border-gray-100 flex-shrink-0">
          <button
            @click="handleLogout"
            class="sidebar-link w-full text-red-500 hover:bg-red-50 hover:text-red-600"
          >
            <ArrowRightOnRectangleIcon class="w-5 h-5 flex-shrink-0" />
            <span v-if="uiStore.sidebarOpen || isMobile">Logout</span>
          </button>
        </div>
      </aside>
    </Transition>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth'
import { useUiStore }   from '../../stores/ui'
import { useSettingsStore } from '../../stores/settings'
import {
  HomeIcon, UsersIcon, DocumentTextIcon, TagIcon, BookOpenIcon,
  EnvelopeIcon, ComputerDesktopIcon, CogIcon,
  ClipboardDocumentListIcon, ArrowRightOnRectangleIcon,
} from '@heroicons/vue/24/outline'

const route  = useRoute()
const router = useRouter()
const authStore     = useAuthStore()
const uiStore       = useUiStore()
const settingsStore = useSettingsStore()

const isMobile = ref(false)

function checkMobile() {
  isMobile.value = window.innerWidth < 1024
  if (isMobile.value) uiStore.sidebarOpen = false
  else uiStore.sidebarOpen = true
}

onMounted(() => {
  checkMobile()
  window.addEventListener('resize', checkMobile)
})
onUnmounted(() => window.removeEventListener('resize', checkMobile))

const navItems = [
  { name: 'dashboard',   label: 'Dashboard',      to: '/admin/dashboard',   icon: HomeIcon },
  { name: 'students',    label: 'Students',        to: '/admin/students',    icon: UsersIcon },
  { name: 'notes',       label: 'Notes',           to: '/admin/notes',       icon: DocumentTextIcon },
  { name: 'categories',  label: 'Batch',      to: '/admin/batch',  icon: TagIcon },
  { name: 'subjects',    label: 'Subjects',         to: '/admin/subjects',    icon: BookOpenIcon },
  { name: 'invitations', label: 'Invitations',     to: '/admin/invitations', icon: EnvelopeIcon },
  { name: 'sessions',    label: 'Sessions',        to: '/admin/sessions',    icon: ComputerDesktopIcon },
  { name: 'logs',        label: 'Activity Logs',   to: '/admin/logs',        icon: ClipboardDocumentListIcon },
  { name: 'settings',    label: 'Settings',        to: '/admin/settings',    icon: CogIcon },
]

function isActive(path) { return route.path.startsWith(path) }
async function handleLogout() {
  uiStore.sidebarOpen = false
  await authStore.logout()
  router.push('/login')
}
</script>
