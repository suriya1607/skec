<template>
  <div class="max-w-xl">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">My Profile</h1>
    <div class="card">
      <div class="flex items-center gap-4 mb-6">
        <div class="w-16 h-16 rounded-full bg-primary-100 flex items-center justify-center">
          <span class="text-primary-700 font-bold text-xl">{{ authStore.userInitials }}</span>
        </div>
        <div>
          <h2 class="font-bold text-gray-900 text-lg">{{ authStore.user?.name }}</h2>
          <p class="text-gray-500 text-sm">{{ authStore.user?.email }}</p>
          <AppBadge variant="student" label="Student" class="mt-1" />
        </div>
      </div>
      <dl class="space-y-3 text-sm">
        <div class="flex justify-between py-2 border-b border-gray-50">
          <dt class="text-gray-400">Account Status</dt>
          <dd><AppBadge :variant="authStore.user?.status" :label="authStore.user?.status" dot /></dd>
        </div>
        <div class="flex justify-between py-2 border-b border-gray-50">
          <dt class="text-gray-400">Last Login</dt>
          <dd class="font-medium">{{ formatDate(authStore.user?.last_login_at) }}</dd>
        </div>
      </dl>
    </div>
  </div>
</template>

<script setup>
import { useAuthStore }  from '../../stores/auth'
import { formatDate }    from '../../utils/helpers'
import AppBadge from '../../components/common/AppBadge.vue'
const authStore = useAuthStore()
</script>
