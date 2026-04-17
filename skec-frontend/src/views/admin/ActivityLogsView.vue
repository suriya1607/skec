<template>
  <div>
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Activity Logs</h1>
    <AppTable :columns="columns" :rows="logs" :loading="loading" empty-title="No logs yet">
      <template #cell-user="{ row }">{{ row.user?.name }}</template>
      <template #cell-note="{ row }">{{ row.note?.title }}</template>
      <template #cell-action="{ row }"><AppBadge :label="row.action" variant="gray" /></template>
      <template #cell-created_at="{ row }">{{ formatDateTime(row.created_at) }}</template>
    </AppTable>
    <AppPagination :meta="meta" @change="fetchLogs" />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { adminLogsApi } from '../../api/admin/logs'
import { formatDateTime } from '../../utils/helpers'
import AppTable      from '../../components/common/AppTable.vue'
import AppBadge      from '../../components/common/AppBadge.vue'
import AppPagination from '../../components/common/AppPagination.vue'

const loading = ref(false)
const logs    = ref([])
const meta    = ref(null)
const columns = [
  { key: 'user',       label: 'Student' },
  { key: 'note',       label: 'Note' },
  { key: 'action',     label: 'Action' },
  { key: 'page_number', label: 'Page' },
  { key: 'duration_seconds', label: 'Duration (s)' },
  { key: 'created_at', label: 'Time' },
]

async function fetchLogs(p = 1) {
  loading.value = true
  const res = await adminLogsApi.list({ page: p })
  logs.value = res.data.data
  meta.value = res.data.meta
  loading.value = false
}

onMounted(fetchLogs)
</script>
