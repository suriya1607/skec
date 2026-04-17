<template>
  <div>
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Active Sessions</h1>
    <AppTable :columns="columns" :rows="sessions" :loading="loading" empty-title="No active sessions">
      <template #cell-user="{ row }">
        <span class="font-medium">{{ row.user?.name }}</span>
        <span class="text-gray-400 text-xs block">{{ row.user?.email }}</span>
      </template>
      <template #cell-device_type="{ row }"><AppBadge :label="row.device_type || 'desktop'" variant="gray" /></template>
      <template #cell-last_activity="{ row }">{{ formatDateTime(row.last_activity) }}</template>
      <template #cell-actions="{ row }">
        <AppButton size="xs" variant="danger" @click="terminate(row)">Terminate</AppButton>
      </template>
    </AppTable>
    <AppPagination :meta="meta" @change="fetchSessions" />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { adminSessionsApi } from '../../api/admin/sessions'
import { formatDateTime } from '../../utils/helpers'
import AppTable      from '../../components/common/AppTable.vue'
import AppBadge      from '../../components/common/AppBadge.vue'
import AppButton     from '../../components/common/AppButton.vue'
import AppPagination from '../../components/common/AppPagination.vue'

const loading  = ref(false)
const sessions = ref([])
const meta     = ref(null)
const columns  = [
  { key: 'user',          label: 'Student' },
  { key: 'ip_address',    label: 'IP Address' },
  { key: 'device_type',   label: 'Device' },
  { key: 'last_activity', label: 'Last Activity' },
  { key: 'actions',       label: '' },
]

async function fetchSessions(p = 1) {
  loading.value = true
  const res = await adminSessionsApi.list({ page: p })
  sessions.value = res.data.data
  meta.value     = res.data.meta
  loading.value = false
}

async function terminate(session) {
  await adminSessionsApi.delete(session.id)
  sessions.value = sessions.value.filter(s => s.id !== session.id)
}

onMounted(fetchSessions)
</script>
