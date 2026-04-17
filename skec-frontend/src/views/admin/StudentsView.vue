<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold text-gray-900">Students</h1>
    </div>

    <!-- Filters -->
    <div class="card mb-5 flex flex-wrap gap-4">
      <AppInput v-model="search" placeholder="Search name or email…" class="flex-1 min-w-48" @input="debouncedFetch" />
      <AppSelect v-model="statusFilter" :options="[{value:'',label:'All Statuses'},{value:'active',label:'Active'},{value:'inactive',label:'Inactive'}]" class="w-44" @change="fetchStudents" />
    </div>

    <!-- Table -->
    <AppTable :columns="columns" :rows="students" :loading="loading" empty-title="No students found">
      <template #cell-name="{ row }">
        <div class="flex items-center gap-2">
          <div class="w-7 h-7 rounded-full bg-primary-100 flex items-center justify-center flex-shrink-0">
            <span class="text-primary-700 text-xs font-bold">{{ getInitials(row.name) }}</span>
          </div>
          <RouterLink :to="`/admin/students/${row.id}`" class="font-medium text-gray-800 hover:text-primary-600">{{ row.name }}</RouterLink>
        </div>
      </template>
      <template #cell-status="{ row }">
        <AppBadge :variant="row.status" :label="row.status" dot />
      </template>
      <template #cell-created_at="{ row }">{{ formatDate(row.created_at) }}</template>
      <template #cell-actions="{ row }">
        <div class="flex items-center gap-2">
          <AppButton size="xs" variant="ghost" @click="toggleStatus(row)">
            {{ row.status === 'active' ? 'Deactivate' : 'Activate' }}
          </AppButton>
          <AppButton size="xs" variant="danger" @click="confirmDelete(row)">Delete</AppButton>
        </div>
      </template>
    </AppTable>
    <AppPagination :meta="meta" @change="fetchStudents" />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { adminStudentsApi } from '../../api/admin/students'
import { formatDate, getInitials, debounce } from '../../utils/helpers'
import AppInput      from '../../components/common/AppInput.vue'
import AppSelect     from '../../components/common/AppSelect.vue'
import AppTable      from '../../components/common/AppTable.vue'
import AppBadge      from '../../components/common/AppBadge.vue'
import AppButton     from '../../components/common/AppButton.vue'
import AppPagination from '../../components/common/AppPagination.vue'

const loading      = ref(false)
const students     = ref([])
const meta         = ref(null)
const search       = ref('')
const statusFilter = ref('')
const page         = ref(1)

const columns = [
  { key: 'name',       label: 'Student' },
  { key: 'email',      label: 'Email' },
  { key: 'status',     label: 'Status' },
  { key: 'created_at', label: 'Joined' },
  { key: 'actions',    label: 'Actions' },
]

async function fetchStudents(p = 1) {
  loading.value = true
  page.value = p
  try {
    const res = await adminStudentsApi.list({ page: p, search: search.value, status: statusFilter.value })
    students.value = res.data.data
    meta.value     = res.data.meta
  } finally {
    loading.value = false
  }
}

const debouncedFetch = debounce(() => fetchStudents(1), 300)

async function toggleStatus(student) {
  const newStatus = student.status === 'active' ? 'inactive' : 'active'
  await adminStudentsApi.update(student.id, { status: newStatus })
  student.status = newStatus
}

async function confirmDelete(student) {
  if (!confirm(`Delete ${student.name}? This cannot be undone.`)) return
  await adminStudentsApi.delete(student.id)
  fetchStudents(page.value)
}

onMounted(() => fetchStudents())
</script>
