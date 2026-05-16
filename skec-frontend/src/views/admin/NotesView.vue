<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold text-gray-900">Notes</h1>
      <RouterLink to="/admin/notes/upload">
        <AppButton variant="primary"><PlusIcon class="w-4 h-4" /> Upload Note</AppButton>
      </RouterLink>
    </div>

    <!-- Filters -->
    <div class="card mb-5 flex flex-wrap gap-4">
      <AppInput v-model="search" placeholder="Search notes…" class="flex-1 min-w-48" @input="debouncedFetch" />
      <AppSelect v-model="statusFilter"   :options="statusOptions"   class="w-40" @change="() => fetchNotes(1)" />
      <AppSelect v-model="categoryFilter" :options="categoryOptions" class="w-48" @change="() => fetchNotes(1)" />
      <AppSelect v-model="subjectFilter"  :options="subjectOptions"  class="w-48" @change="() => fetchNotes(1)" />
    </div>

    <AppTable :columns="columns" :rows="notes" :loading="loading">
      <template #cell-title="{ row }">
        <a
    :href="row.file_url"
    target="_blank"
    class="font-medium text-blue-600 hover:underline"
  >
    {{ row.title }}
  </a>
      </template>
      <template #cell-category="{ row }">
        <span v-if="row.category" class="inline-flex items-center gap-1.5">
          <span class="w-2 h-2 rounded-full" :style="{ background: row.category.color }" />
          {{ row.category.name }}
        </span>
        <span v-else class="text-gray-400">—</span>
      </template>
      <template #cell-subject="{ row }">
        <span v-if="row.subject" class="inline-flex items-center gap-1.5">
          <span class="w-2 h-2 rounded-full" :style="{ background: row.subject.color || '#6366f1' }" />
          {{ row.subject.name }}
        </span>
        <span v-else class="text-gray-400">—</span>
      </template>
      <template #cell-file_size="{ row }">{{ row.file_size_formatted }}</template>
      <template #cell-status="{ row }">
        <AppBadge :variant="row.status" :label="row.status" />
      </template>
      <template #cell-view_count="{ row }">
        <span class="text-gray-600">{{ row.view_count }}</span>
      </template>
      <template #cell-created_at="{ row }">{{ formatDate(row.created_at) }}</template>
      <template #cell-actions="{ row }">
        <div class="flex items-center gap-2">
          <AppButton size="xs" variant="ghost" @click="toggleStatus(row)">
            {{ row.status === 'published' ? 'Unpublish' : 'Publish' }}
          </AppButton>
          <AppButton size="xs" variant="danger" @click="deleteNote(row)">Delete</AppButton>
        </div>
      </template>
    </AppTable>
    <AppPagination :meta="meta" @change="fetchNotes" />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { adminNotesApi }      from '../../api/admin/notes'
import { adminCategoriesApi } from '../../api/admin/categories'
import { adminSubjectsApi }   from '../../api/admin/subjects'
import { formatDate, debounce } from '../../utils/helpers'
import { PlusIcon } from '@heroicons/vue/24/outline'
import AppInput      from '../../components/common/AppInput.vue'
import AppSelect     from '../../components/common/AppSelect.vue'
import AppTable      from '../../components/common/AppTable.vue'
import AppBadge      from '../../components/common/AppBadge.vue'
import AppButton     from '../../components/common/AppButton.vue'
import AppPagination from '../../components/common/AppPagination.vue'

const loading        = ref(false)
const notes          = ref([])
const meta           = ref(null)
const search         = ref('')
const statusFilter   = ref('')
const categoryFilter = ref('')
const subjectFilter  = ref('')
const categoryOptions = ref([{ value: '', label: 'All Categories' }])
const subjectOptions  = ref([{ value: '', label: 'All Subjects' }])

const statusOptions = [
  { value: '', label: 'All Statuses' },
  { value: 'published', label: 'Published' },
  { value: 'draft', label: 'Draft' },
]

const columns = [
  { key: 'title',       label: 'Title' },
  { key: 'category',    label: 'Category' },
  { key: 'subject',     label: 'Subject' },
  { key: 'file_size',   label: 'Size' },
  { key: 'status',      label: 'Status' },
  { key: 'view_count',  label: 'Views' },
  { key: 'created_at',  label: 'Uploaded' },
  { key: 'actions',     label: '' },
]

async function fetchNotes(p = 1) {
  loading.value = true
  try {
    const res = await adminNotesApi.list({
      page: p,
      search: search.value,
      status: statusFilter.value,
      category_id: categoryFilter.value,
      subject_id: subjectFilter.value,
    })
    notes.value = res.data.data
    meta.value  = res.data.meta
  } finally { loading.value = false }
}

const debouncedFetch = debounce(() => fetchNotes(1), 300)

async function toggleStatus(note) {
  await adminNotesApi.toggleStatus(note.id)
  note.status = note.status === 'published' ? 'draft' : 'published'
}

async function deleteNote(note) {
  if (!confirm(`Delete "${note.title}"?`)) return
  await adminNotesApi.delete(note.id)
  fetchNotes()
}

onMounted(async () => {
  fetchNotes()
  const [catRes, subRes] = await Promise.all([
    adminCategoriesApi.list(),
    adminSubjectsApi.list(),
  ])
  categoryOptions.value = [
    { value: '', label: 'All Categories' },
    ...catRes.data.data.map(c => ({ value: c.id, label: c.name }))
  ]
  subjectOptions.value = [
    { value: '', label: 'All Subjects' },
    ...subRes.data.data.map(s => ({ value: s.id, label: s.name }))
  ]
})
</script>
