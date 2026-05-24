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
      <AppSelect v-model="categoryFilter" :options="categoryFilterOptions" class="w-48" @change="() => fetchNotes(1)" />
      <AppSelect v-model="subjectFilter"  :options="subjectFilterOptions"  class="w-48" @change="() => fetchNotes(1)" />
    </div>

    <AppTable :columns="columns" :rows="notes" :loading="loading">
      <template #cell-title="{ row }">
        <a :href="row.file_url" target="_blank" class="font-medium text-blue-600 hover:underline">
          {{ row.title }}
        </a>
      </template>
      <template #cell-categories="{ row }">
        <div v-if="row.categories && row.categories.length" class="flex flex-wrap gap-1">
          <span
            v-for="cat in row.categories"
            :key="cat.id"
            class="inline-flex items-center gap-1 text-xs font-medium px-2 py-0.5 rounded-full"
            :style="{ background: (cat.color || '#6b7280') + '18', color: cat.color || '#6b7280' }"
          >
            <span class="w-1.5 h-1.5 rounded-full" :style="{ background: cat.color || '#6b7280' }" />
            {{ cat.name }}
          </span>
        </div>
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
          <AppButton size="xs" variant="ghost" @click="openEditModal(row)">
            <PencilIcon class="w-3.5 h-3.5" /> Edit
          </AppButton>
          <AppButton size="xs" variant="ghost" @click="toggleStatus(row)">
            {{ row.status === 'published' ? 'Unpublish' : 'Publish' }}
          </AppButton>
          <AppButton size="xs" variant="danger" @click="deleteNote(row)">Delete</AppButton>
        </div>
      </template>
    </AppTable>
    <AppPagination :meta="meta" @change="fetchNotes" />

    <!-- Edit Modal -->
    <Teleport to="body">
      <Transition name="modal">
        <div v-if="editModal.open" class="fixed inset-0 z-50 flex items-center justify-center p-4">
          <!-- Backdrop -->
          <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="closeEditModal" />

          <!-- Modal -->
          <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto animate-in">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
              <h2 class="text-lg font-semibold text-gray-900">Edit Note</h2>
              <button @click="closeEditModal" class="p-1 rounded-lg hover:bg-gray-100 transition-colors">
                <XMarkIcon class="w-5 h-5 text-gray-400" />
              </button>
            </div>

            <form @submit.prevent="handleUpdate" class="px-6 py-5 space-y-5">
              <AppAlert v-if="editModal.error" type="error" :message="editModal.error" class="mb-3" />

              <AppInput
                v-model="editModal.form.title"
                label="Title"
                placeholder="Note title"
                required
              />

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Description</label>
                <textarea
                  v-model="editModal.form.description"
                  rows="3"
                  class="input-base resize-none"
                  placeholder="Optional description…"
                />
              </div>

              <AppMultiSelect
                v-model="editModal.form.category_ids"
                label="Categories"
                :options="categoryOptions"
                placeholder="Select categories…"
              />

              <AppSelect
                v-model="editModal.form.subject_id"
                label="Subject"
                :options="subjectOptions"
                placeholder="Select subject"
              />

              <AppSelect
                v-model="editModal.form.status"
                label="Status"
                :options="[{value:'draft',label:'Draft'},{value:'published',label:'Published'}]"
              />

              <div class="flex gap-3 pt-2">
                <AppButton type="submit" variant="primary" :loading="editModal.saving">
                  Save Changes
                </AppButton>
                <AppButton variant="secondary" @click="closeEditModal">Cancel</AppButton>
              </div>
            </form>
          </div>
        </div>
      </Transition>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { adminNotesApi }      from '../../api/admin/notes'
import { adminCategoriesApi } from '../../api/admin/categories'
import { adminSubjectsApi }   from '../../api/admin/subjects'
import { formatDate, debounce } from '../../utils/helpers'
import { PlusIcon, PencilIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import AppAlert       from '../../components/common/AppAlert.vue'
import AppInput       from '../../components/common/AppInput.vue'
import AppSelect      from '../../components/common/AppSelect.vue'
import AppMultiSelect from '../../components/common/AppMultiSelect.vue'
import AppTable       from '../../components/common/AppTable.vue'
import AppBadge       from '../../components/common/AppBadge.vue'
import AppButton      from '../../components/common/AppButton.vue'
import AppPagination  from '../../components/common/AppPagination.vue'

const loading        = ref(false)
const notes          = ref([])
const meta           = ref(null)
const search         = ref('')
const statusFilter   = ref('')
const categoryFilter = ref('')
const subjectFilter  = ref('')

// Filter dropdowns (with "All" option)
const categoryFilterOptions = ref([{ value: '', label: 'All Categories' }])
const subjectFilterOptions  = ref([{ value: '', label: 'All Subjects' }])

// Edit modal dropdowns (without "All" prefix)
const categoryOptions = ref([])
const subjectOptions  = ref([{ value: '', label: 'No Subject' }])

const statusOptions = [
  { value: '', label: 'All Statuses' },
  { value: 'published', label: 'Published' },
  { value: 'draft', label: 'Draft' },
]

const columns = [
  { key: 'title',       label: 'Title' },
  { key: 'categories',  label: 'Categories' },
  { key: 'subject',     label: 'Subject' },
  { key: 'file_size',   label: 'Size' },
  { key: 'status',      label: 'Status' },
  { key: 'view_count',  label: 'Views' },
  { key: 'created_at',  label: 'Uploaded' },
  { key: 'actions',     label: '' },
]

// Edit Modal
const editModal = reactive({
  open: false,
  saving: false,
  error: '',
  noteId: null,
  form: {
    title: '',
    description: '',
    category_ids: [],
    subject_id: '',
    status: 'draft',
  }
})

function openEditModal(note) {
  editModal.noteId = note.id
  editModal.form.title = note.title
  editModal.form.description = note.description || ''
  // Extract category IDs from the categories array
  editModal.form.category_ids = (note.categories || []).map(c => c.id)
  editModal.form.subject_id = note.subject?.id ?? ''
  editModal.form.status = note.status
  editModal.error = ''
  editModal.open = true
}

function closeEditModal() {
  editModal.open = false
  editModal.error = ''
}

async function handleUpdate() {
  editModal.saving = true
  editModal.error = ''
  try {
    const payload = {
      title: editModal.form.title,
      description: editModal.form.description,
      category_ids: editModal.form.category_ids,
      subject_id: editModal.form.subject_id || null,
      status: editModal.form.status,
    }
    await adminNotesApi.update(editModal.noteId, payload)
    closeEditModal()
    fetchNotes()
  } catch (err) {
    editModal.error = err.response?.data?.message || 'Failed to update note.'
  } finally {
    editModal.saving = false
  }
}

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

  const cats = catRes.data.data
  const subs = subRes.data.data

  categoryFilterOptions.value = [
    { value: '', label: 'All Categories' },
    ...cats.map(c => ({ value: c.id, label: c.name }))
  ]
  subjectFilterOptions.value = [
    { value: '', label: 'All Subjects' },
    ...subs.map(s => ({ value: s.id, label: s.name }))
  ]

  categoryOptions.value = cats.map(c => ({ value: c.id, label: c.name, color: c.color }))
  subjectOptions.value = [
    { value: '', label: 'No Subject' },
    ...subs.map(s => ({ value: s.id, label: s.name }))
  ]
})
</script>

<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.2s ease;
}
.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}
.animate-in {
  animation: slideUp 0.25s ease-out;
}
@keyframes slideUp {
  from { opacity: 0; transform: translateY(12px) scale(0.97); }
  to   { opacity: 1; transform: translateY(0) scale(1); }
}
</style>
