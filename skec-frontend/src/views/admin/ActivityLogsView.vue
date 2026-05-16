<template>
  <div>
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Activity Logs</h1>

    <!-- Filters -->
    <div class="card mb-5">
      <div class="flex flex-wrap gap-3">
        <!-- Search -->
        <AppInput
          v-model="search"
          placeholder="Search student or note…"
          class="flex-1 min-w-48"
          @input="debouncedFetch"
        />

        <!-- Action type -->
        <AppSelect
          v-model="actionFilter"
          :options="actionOptions"
          class="w-44"
          @change="() => fetchLogs(1)"
        />

        <!-- Date from -->
        <div class="flex flex-col justify-end">
          <label class="block text-xs font-medium text-gray-500 mb-1">From</label>
          <input
            v-model="dateFrom"
            type="date"
            class="input-base text-sm h-10"
            @change="fetchLogs(1)"
          />
        </div>

        <!-- Date to -->
        <div class="flex flex-col justify-end">
          <label class="block text-xs font-medium text-gray-500 mb-1">To</label>
          <input
            v-model="dateTo"
            type="date"
            class="input-base text-sm h-10"
            @change="fetchLogs(1)"
          />
        </div>

        <!-- Reset -->
        <div class="flex items-end">
          <AppButton variant="ghost" size="sm" @click="resetFilters">Reset</AppButton>
        </div>
      </div>
    </div>

    <!-- Results summary -->
    <p v-if="meta" class="text-sm text-gray-400 mb-3">
      Showing {{ meta.from ?? 0 }}–{{ meta.to ?? 0 }} of {{ meta.total }} log entries
    </p>

    <AppTable :columns="columns" :rows="logs" :loading="loading" empty-title="No logs found">
      <template #cell-user="{ row }">
        <span class="font-medium text-gray-800">{{ row.user?.name ?? '—' }}</span>
      </template>
      <template #cell-note="{ row }">
        <span class="text-gray-700 line-clamp-1">{{ row.note?.title ?? '—' }}</span>
      </template>
      <template #cell-action="{ row }">
        <AppBadge :label="row.action" :variant="actionVariant(row.action)" />
      </template>
      <template #cell-page_number="{ row }">
        <span class="text-gray-500">{{ row.page_number ?? '—' }}</span>
      </template>
      <template #cell-duration_seconds="{ row }">
        <span class="text-gray-500">{{ row.duration_seconds != null ? row.duration_seconds + 's' : '—' }}</span>
      </template>
      <template #cell-ip_address="{ row }">
        <span class="text-xs text-gray-400 font-mono">{{ row.ip_address ?? '—' }}</span>
      </template>
      <template #cell-created_at="{ row }">
        <span class="text-gray-600 text-sm">{{ formatDateTime(row.created_at) }}</span>
      </template>
    </AppTable>
    <AppPagination :meta="meta" @change="fetchLogs" class="mt-4" />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { adminLogsApi } from '../../api/admin/logs'
import { formatDateTime, debounce } from '../../utils/helpers'
import AppTable      from '../../components/common/AppTable.vue'
import AppBadge      from '../../components/common/AppBadge.vue'
import AppButton     from '../../components/common/AppButton.vue'
import AppInput      from '../../components/common/AppInput.vue'
import AppSelect     from '../../components/common/AppSelect.vue'
import AppPagination from '../../components/common/AppPagination.vue'

const loading      = ref(false)
const logs         = ref([])
const meta         = ref(null)
const search       = ref('')
const actionFilter = ref('')
const dateFrom     = ref('')
const dateTo       = ref('')

const actionOptions = [
  { value: '',                   label: 'All Actions' },
  { value: 'opened',            label: 'Opened' },
  { value: 'closed',            label: 'Closed' },
  { value: 'page_changed',      label: 'Page Changed' },
  { value: 'screenshot_attempt',label: 'Screenshot Attempt' },
  { value: 'capture_attempt',   label: 'Capture Attempt' },
  { value: 'print_attempt',     label: 'Print Attempt' },
  { value: 'copy_attempt',      label: 'Copy Attempt' },
]

const columns = [
  { key: 'user',             label: 'Student' },
  { key: 'note',             label: 'Note' },
  { key: 'action',           label: 'Action' },
  { key: 'page_number',      label: 'Page' },
  { key: 'duration_seconds', label: 'Duration' },
  { key: 'ip_address',       label: 'IP Address' },
  { key: 'created_at',       label: 'Time' },
]

// Map action strings to badge variants
function actionVariant(action) {
  const map = {
    opened:             'active',
    closed:             'inactive',
    page_changed:       'gray',
    screenshot_attempt: 'danger',
    capture_attempt:    'danger',
    print_attempt:      'warning',
    copy_attempt:       'warning',
  }
  return map[action] ?? 'gray'
}

async function fetchLogs(p = 1) {
  loading.value = true
  try {
    const res = await adminLogsApi.list({
      page:      p,
      search:    search.value,
      action:    actionFilter.value,
      date_from: dateFrom.value,
      date_to:   dateTo.value,
    })
    logs.value = res.data.data
    meta.value = res.data.meta
  } finally {
    loading.value = false
  }
}

function resetFilters() {
  search.value       = ''
  actionFilter.value = ''
  dateFrom.value     = ''
  dateTo.value       = ''
  fetchLogs(1)
}

const debouncedFetch = debounce(() => fetchLogs(1), 300)

onMounted(fetchLogs)
</script>

