<template>
  <header class="bg-white border-b border-gray-100 h-16 flex items-center justify-between px-6 flex-shrink-0">
    <!-- Left: toggle + breadcrumb -->
    <div class="flex items-center gap-4">
      <button @click="uiStore.toggleSidebar" class="p-1.5 rounded-lg hover:bg-gray-100 transition-colors">
        <Bars3Icon class="w-5 h-5 text-gray-500" />
      </button>
      <BreadCrumb />
    </div>

    <!-- Right: user info -->
    <div class="flex items-center gap-3">
      <div class="text-right hidden sm:block">
        <p class="text-sm font-semibold text-gray-800">{{ authStore.user?.name }}</p>
        <p class="text-xs text-purple-600 font-medium" v-if="authStore.isSuperAdmin">Super Administrator</p>
        <p class="text-xs text-gray-400" v-else>Administrator</p>
      </div>
      <div class="w-9 h-9 rounded-full bg-primary-100 flex items-center justify-center">
        <span class="text-primary-700 font-bold text-sm">{{ authStore.userInitials }}</span>
      </div>
    </div>
  </header>
</template>

<script setup>
import { useAuthStore } from '../../stores/auth'
import { useUiStore }   from '../../stores/ui'
import { Bars3Icon }    from '@heroicons/vue/24/outline'
import BreadCrumb       from './BreadCrumb.vue'

const authStore = useAuthStore()
const uiStore   = useUiStore()
</script>
