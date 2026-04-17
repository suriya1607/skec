<template>
  <aside class="w-64 bg-white border-r border-gray-100 flex flex-col shadow-sm" :class="{ 'w-64': uiStore.sidebarOpen, 'w-16': !uiStore.sidebarOpen }">
    <!-- Brand -->
    <div class="flex items-center gap-3 px-5 py-5 border-b border-gray-100">
      <div class="flex-shrink-0 w-9 h-9 bg-primary-900 rounded-xl flex items-center justify-center">
        <span class="text-white font-bold text-sm">SK</span>
      </div>
      <div v-if="uiStore.sidebarOpen" class="min-w-0">
        <p class="text-sm font-bold text-primary-900 truncate">SKEC</p>
        <p class="text-xs text-gray-400 truncate">Admin Panel</p>
      </div>
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
      >
        <component :is="item.icon" class="w-5 h-5 flex-shrink-0" />
        <span v-if="uiStore.sidebarOpen" class="truncate">{{ item.label }}</span>
      </RouterLink>
    </nav>

    <!-- User at bottom -->
    <div class="px-3 py-4 border-t border-gray-100">
      <button
        @click="handleLogout"
        class="sidebar-link w-full text-red-500 hover:bg-red-50 hover:text-red-600"
      >
        <ArrowRightOnRectangleIcon class="w-5 h-5 flex-shrink-0" />
        <span v-if="uiStore.sidebarOpen">Logout</span>
      </button>
    </div>
  </aside>
</template>

<script setup>
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth'
import { useUiStore }   from '../../stores/ui'
import {
  HomeIcon, UsersIcon, DocumentTextIcon, TagIcon,
  EnvelopeIcon, ComputerDesktopIcon, CogIcon,
  ClipboardDocumentListIcon, ArrowRightOnRectangleIcon,
} from '@heroicons/vue/24/outline'

const route  = useRoute()
const router = useRouter()
const authStore = useAuthStore()
const uiStore   = useUiStore()

const navItems = [
  { name: 'dashboard',   label: 'Dashboard',      to: '/admin/dashboard',   icon: HomeIcon },
  { name: 'students',    label: 'Students',        to: '/admin/students',    icon: UsersIcon },
  { name: 'notes',       label: 'Notes',           to: '/admin/notes',       icon: DocumentTextIcon },
  { name: 'categories',  label: 'Categories',      to: '/admin/categories',  icon: TagIcon },
  { name: 'invitations', label: 'Invitations',     to: '/admin/invitations', icon: EnvelopeIcon },
  { name: 'sessions',    label: 'Sessions',        to: '/admin/sessions',    icon: ComputerDesktopIcon },
  { name: 'logs',        label: 'Activity Logs',   to: '/admin/logs',        icon: ClipboardDocumentListIcon },
  { name: 'settings',    label: 'Settings',        to: '/admin/settings',    icon: CogIcon },
]

function isActive(path) {
  return route.path.startsWith(path)
}

async function handleLogout() {
  await authStore.logout()
  router.push('/login')
}
</script>
