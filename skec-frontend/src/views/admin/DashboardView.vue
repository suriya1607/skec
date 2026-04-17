<template>
  <div>
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Dashboard</h1>

    <!-- Stats grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
      <AppStats label="Total Students"   :value="stats.total_students"   :icon="UsersIcon"          icon-bg="bg-blue-50"   icon-color="text-blue-600" />
      <AppStats label="Total Notes"      :value="stats.total_notes"      :icon="DocumentTextIcon"   icon-bg="bg-purple-50" icon-color="text-purple-600" />
      <AppStats label="Active Sessions"  :value="stats.active_sessions"  :icon="ComputerDesktopIcon" icon-bg="bg-green-50"  icon-color="text-green-600" />
      <AppStats label="Notes This Month" :value="stats.notes_this_month" :icon="CalendarIcon"        icon-bg="bg-amber-50"  icon-color="text-amber-600" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Recent Uploads -->
      <div class="card">
        <h2 class="font-semibold text-gray-800 mb-4">Recent Uploads</h2>
        <div v-if="loading" class="py-8"><AppLoader /></div>
        <div v-else-if="!recentUploads.length" class="py-8"><AppEmptyState title="No notes yet" /></div>
        <div v-else class="space-y-3">
          <div v-for="note in recentUploads" :key="note.id" class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
            <div class="min-w-0">
              <p class="text-sm font-medium text-gray-800 truncate">{{ note.title }}</p>
              <p class="text-xs text-gray-400 mt-0.5">{{ formatDate(note.created_at) }}</p>
            </div>
            <AppBadge :variant="note.status" :label="note.status" />
          </div>
        </div>
      </div>

      <!-- Recent Registrations -->
      <div class="card">
        <h2 class="font-semibold text-gray-800 mb-4">Recent Registrations</h2>
        <div v-if="loading" class="py-8"><AppLoader /></div>
        <div v-else-if="!recentRegistrations.length" class="py-8"><AppEmptyState title="No students yet" /></div>
        <div v-else class="space-y-3">
          <div v-for="student in recentRegistrations" :key="student.id" class="flex items-center gap-3 py-2 border-b border-gray-50 last:border-0">
            <div class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center flex-shrink-0">
              <span class="text-primary-700 font-semibold text-xs">{{ getInitials(student.name) }}</span>
            </div>
            <div class="min-w-0 flex-1">
              <p class="text-sm font-medium text-gray-800 truncate">{{ student.name }}</p>
              <p class="text-xs text-gray-400 truncate">{{ student.email }}</p>
            </div>
            <AppBadge :variant="student.status" :label="student.status" dot />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { adminDashboardApi } from '../../api/admin/logs'
import { formatDate, getInitials } from '../../utils/helpers'
import AppStats     from '../../components/common/AppStats.vue'
import AppBadge     from '../../components/common/AppBadge.vue'
import AppLoader    from '../../components/common/AppLoader.vue'
import AppEmptyState from '../../components/common/AppEmptyState.vue'
import { UsersIcon, DocumentTextIcon, ComputerDesktopIcon, CalendarIcon } from '@heroicons/vue/24/outline'

const loading             = ref(true)
const stats               = ref({ total_students: 0, total_notes: 0, active_sessions: 0, notes_this_month: 0 })
const recentUploads       = ref([])
const recentRegistrations = ref([])

onMounted(async () => {
  try {
    const res = await adminDashboardApi.get()
    stats.value               = res.data.data.stats
    recentUploads.value       = res.data.data.recent_uploads
    recentRegistrations.value = res.data.data.recent_registrations
  } finally {
    loading.value = false
  }
})
</script>
