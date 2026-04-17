<template>
  <div>
    <div class="flex items-center gap-4 mb-6">
      <RouterLink to="/admin/students" class="text-gray-400 hover:text-gray-600">
        <ArrowLeftIcon class="w-5 h-5" />
      </RouterLink>
      <h1 class="text-2xl font-bold text-gray-900">Student Detail</h1>
    </div>

    <AppLoader v-if="loading" />

    <div v-else-if="student" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Profile card -->
      <div class="card text-center">
        <div class="w-16 h-16 rounded-full bg-primary-100 flex items-center justify-center mx-auto mb-3">
          <span class="text-primary-700 font-bold text-xl">{{ getInitials(student.name) }}</span>
        </div>
        <h2 class="font-bold text-gray-900 text-lg">{{ student.name }}</h2>
        <p class="text-gray-400 text-sm">{{ student.email }}</p>
        <AppBadge :variant="student.status" :label="student.status" dot class="mt-3" />

        <div class="mt-5 space-y-2">
          <AppButton
            :variant="student.status === 'active' ? 'danger' : 'success'"
            size="sm"
            class="w-full"
            @click="toggleStatus"
          >
            {{ student.status === 'active' ? 'Deactivate' : 'Activate' }}
          </AppButton>
          <AppButton variant="secondary" size="sm" class="w-full" @click="forceLogout">
            Force Logout
          </AppButton>
        </div>
      </div>

      <!-- Stats + logs -->
      <div class="lg:col-span-2 space-y-6">
        <div class="card">
          <h3 class="font-semibold text-gray-800 mb-4">Activity Info</h3>
          <dl class="grid grid-cols-2 gap-4 text-sm">
            <div><dt class="text-gray-400">Joined</dt><dd class="font-medium mt-0.5">{{ formatDate(student.created_at) }}</dd></div>
            <div><dt class="text-gray-400">Last Login</dt><dd class="font-medium mt-0.5">{{ formatDate(student.last_login_at) }}</dd></div>
            <div><dt class="text-gray-400">Active Sessions</dt><dd class="font-medium mt-0.5">{{ activeSessions }}</dd></div>
            <div><dt class="text-gray-400">Last IP</dt><dd class="font-medium mt-0.5">{{ student.last_login_ip || '—' }}</dd></div>
          </dl>
        </div>

        <div class="card">
          <h3 class="font-semibold text-gray-800 mb-4">Recent Activity</h3>
          <div v-if="!student.access_logs?.length" class="text-center py-6 text-gray-400 text-sm">No activity yet</div>
          <div v-else class="space-y-2">
            <div v-for="log in student.access_logs" :key="log.id" class="flex justify-between text-sm py-1.5 border-b border-gray-50 last:border-0">
              <span class="text-gray-600">Note #{{ log.note_id }} — <span class="capitalize">{{ log.action }}</span></span>
              <span class="text-gray-400">{{ formatDate(log.created_at) }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { adminStudentsApi } from '../../api/admin/students'
import { formatDate, getInitials } from '../../utils/helpers'
import { ArrowLeftIcon } from '@heroicons/vue/24/outline'
import AppLoader from '../../components/common/AppLoader.vue'
import AppBadge  from '../../components/common/AppBadge.vue'
import AppButton from '../../components/common/AppButton.vue'

const route   = useRoute()
const loading = ref(true)
const student = ref(null)
const activeSessions = ref(0)

onMounted(async () => {
  const res = await adminStudentsApi.get(route.params.id)
  student.value       = res.data.data.student
  activeSessions.value = res.data.data.active_sessions
  loading.value = false
})

async function toggleStatus() {
  const newStatus = student.value.status === 'active' ? 'inactive' : 'active'
  await adminStudentsApi.update(student.value.id, { status: newStatus })
  student.value.status = newStatus
}

async function forceLogout() {
  await adminStudentsApi.forceLogout(student.value.id)
  activeSessions.value = 0
}
</script>
