<template>
  <div>
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Announcements</h1>
        <p class="text-sm text-gray-500 mt-0.5">Send notifications to students</p>
      </div>
      <AppButton variant="primary" @click="openCreateModal">
        <MegaphoneIcon class="w-4 h-4" />
        New Announcement
      </AppButton>
    </div>

    <!-- Type legend -->
    <div class="flex flex-wrap gap-2 mb-5">
      <span v-for="t in types" :key="t.value"
        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium"
        :class="t.badge">
        <component :is="t.icon" class="w-3.5 h-3.5" />
        {{ t.label }}
      </span>
    </div>

    <!-- Table -->
    <AppTable :columns="columns" :rows="announcements" :loading="loading">

      <template #cell-title="{ row }">
        <div class="max-w-xs whitespace-normal">
          <div class="flex items-center gap-2 mb-0.5 flex-wrap">
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold flex-shrink-0"
              :class="typeBadge(row.type)">
              <component :is="typeIcon(row.type)" class="w-3 h-3" />
              {{ row.type }}
            </span>
            <span class="font-semibold text-gray-900 break-all">{{ row.title }}</span>
          </div>
          <p class="text-xs text-gray-500 break-all line-clamp-3">{{ row.message }}</p>
        </div>
      </template>

      <!-- Target -->
      <template #cell-target="{ row }">
        <div v-if="row.target_categories && row.target_categories.length" class="flex flex-wrap gap-1">
          <span
            v-for="cat in row.target_categories"
            :key="cat.id"
            class="inline-flex items-center gap-1 text-xs font-medium px-2 py-0.5 rounded-full"
            :style="{ background: (cat.color || '#6b7280') + '20', color: cat.color || '#6b7280' }"
          >
            <span class="w-1.5 h-1.5 rounded-full" :style="{ background: cat.color || '#6b7280' }" />
            {{ cat.name }}
          </span>
        </div>
        <span v-else class="inline-flex items-center gap-1 text-xs font-medium px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-700">
          <UsersIcon class="w-3 h-3" /> All Students
        </span>
      </template>

      <!-- Sent count -->
      <template #cell-sent_count="{ row }">
        <span class="inline-flex items-center gap-1 text-sm text-gray-700">
          <UserGroupIcon class="w-4 h-4 text-gray-400" />
          {{ row.sent_count }}
        </span>
      </template>

      <!-- Created -->
      <template #cell-created_at="{ row }">
        <div>
          <p class="text-sm text-gray-700">{{ formatDate(row.created_at) }}</p>
          <p class="text-xs text-gray-400">by {{ row.creator?.name }}</p>
        </div>
      </template>

      <!-- Actions -->
      <template #cell-actions="{ row }">
        <div class="flex items-center gap-2">
          <AppButton size="xs" variant="ghost" :loading="row._resending" @click="resend(row)">
            <ArrowPathIcon class="w-3.5 h-3.5" /> Re-send
          </AppButton>
          <AppButton size="xs" variant="danger" @click="deleteAnnouncement(row)">
            Delete
          </AppButton>
        </div>
      </template>
    </AppTable>

    <AppPagination :meta="meta" @change="fetchAnnouncements" class="mt-4" />

    <!-- ── Create Announcement Modal ──────────────────────────────────── -->
    <Teleport to="body">
      <Transition name="modal">
        <div v-if="modal.open" class="fixed inset-0 z-50 flex items-center justify-center p-4">
          <!-- Backdrop -->
          <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="closeModal" />

          <!-- Card -->
          <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto animate-modal">

            <!-- Modal Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
              <div class="flex items-center gap-2">
                <MegaphoneIcon class="w-5 h-5 text-primary-600" />
                <h2 class="text-lg font-semibold text-gray-900">New Announcement</h2>
              </div>
              <button @click="closeModal" class="p-1 rounded-lg hover:bg-gray-100 transition-colors">
                <XMarkIcon class="w-5 h-5 text-gray-400" />
              </button>
            </div>

            <!-- Form -->
            <form @submit.prevent="handleCreate" class="px-6 py-5 space-y-5">
              <AppAlert v-if="modal.error" type="error" :message="modal.error" />

              <!-- Type selector -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Announcement Type</label>
                <div class="grid grid-cols-2 gap-2 sm:grid-cols-4">
                  <button
                    v-for="t in types"
                    :key="t.value"
                    type="button"
                    class="flex flex-col items-center gap-1.5 px-3 py-3 rounded-xl border-2 text-xs font-medium transition-all"
                    :class="modal.form.type === t.value
                      ? [t.activeBorder, t.activeBg, t.activeText]
                      : 'border-gray-200 text-gray-500 hover:border-gray-300 bg-white'"
                    @click="modal.form.type = t.value"
                  >
                    <component :is="t.icon" class="w-5 h-5" />
                    {{ t.label }}
                  </button>
                </div>
              </div>

              <!-- Preview strip -->
              <div class="rounded-xl px-4 py-3 flex items-start gap-3 border"
                :class="previewClass">
                <component :is="typeIcon(modal.form.type)" class="w-5 h-5 flex-shrink-0 mt-0.5" />
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-semibold">{{ modal.form.title || 'Announcement Title' }}</p>
                  <p class="text-xs mt-0.5 opacity-80 line-clamp-2">{{ modal.form.message || 'Your message will appear here…' }}</p>
                </div>
              </div>

              <AppInput
                v-model="modal.form.title"
                label="Title"
                placeholder="e.g. Class schedule change"
                required
              />

              <div>
                <div class="flex items-center justify-between mb-1.5">
                  <label class="block text-sm font-medium text-gray-700">Message</label>
                  <span
                    class="text-xs font-medium tabular-nums"
                    :class="msgCharsLeft <= 20 ? 'text-red-500' : msgCharsLeft <= 80 ? 'text-yellow-600' : 'text-gray-400'"
                  >
                    {{ modal.form.message.length }} / {{ MAX_MSG_LEN }}
                  </span>
                </div>
                <textarea
                  v-model="modal.form.message"
                  rows="4"
                  :maxlength="MAX_MSG_LEN"
                  class="input-base resize-none"
                  placeholder="Write your announcement message here…"
                  required
                />
                <p v-if="msgCharsLeft <= 0" class="text-xs text-red-500 mt-1">
                  Maximum {{ MAX_MSG_LEN }} characters reached.
                </p>
              </div>

              <!-- Target batches -->
              <AppMultiSelect
                v-model="modal.form.target_category_ids"
                label="Target Batches"
                :options="categoryOptions"
                placeholder="Leave empty to send to ALL students"
              />
              <p class="text-xs text-gray-400 -mt-3">
                <InformationCircleIcon class="w-3.5 h-3.5 inline mr-0.5" />
                Leave empty to notify all active students.
              </p>

              <!-- Footer -->
              <div class="flex gap-3 pt-1">
                <AppButton type="submit" variant="primary" :loading="modal.saving" class="gap-2">
                  <MegaphoneIcon class="w-4 h-4" /> Send Announcement
                </AppButton>
                <AppButton variant="secondary" @click="closeModal">Cancel</AppButton>
              </div>
            </form>
          </div>
        </div>
      </Transition>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useToast } from 'vue-toastification'
import { adminAnnouncementsApi } from '../../api/admin/announcements'
import { adminCategoriesApi }    from '../../api/admin/categories'
import { formatDate }            from '../../utils/helpers'
import {
  MegaphoneIcon, XMarkIcon, ArrowPathIcon, UsersIcon, UserGroupIcon,
  InformationCircleIcon, ExclamationTriangleIcon, CheckCircleIcon, XCircleIcon,
} from '@heroicons/vue/24/outline'
import AppButton      from '../../components/common/AppButton.vue'
import AppTable       from '../../components/common/AppTable.vue'
import AppInput       from '../../components/common/AppInput.vue'
import AppMultiSelect from '../../components/common/AppMultiSelect.vue'
import AppAlert       from '../../components/common/AppAlert.vue'
import AppPagination  from '../../components/common/AppPagination.vue'

const toast = useToast()

// ── Types config ─────────────────────────────────────────────────────────────
const types = [
  {
    value: 'info',
    label: 'Info',
    icon: InformationCircleIcon,
    badge: 'bg-blue-50 text-blue-700',
    activeBorder: 'border-blue-500',
    activeBg: 'bg-blue-50',
    activeText: 'text-blue-700',
    preview: 'bg-blue-50 border-blue-200 text-blue-800',
  },
  {
    value: 'warning',
    label: 'Warning',
    icon: ExclamationTriangleIcon,
    badge: 'bg-yellow-50 text-yellow-700',
    activeBorder: 'border-yellow-500',
    activeBg: 'bg-yellow-50',
    activeText: 'text-yellow-700',
    preview: 'bg-yellow-50 border-yellow-200 text-yellow-800',
  },
  {
    value: 'success',
    label: 'Success',
    icon: CheckCircleIcon,
    badge: 'bg-green-50 text-green-700',
    activeBorder: 'border-green-500',
    activeBg: 'bg-green-50',
    activeText: 'text-green-700',
    preview: 'bg-green-50 border-green-200 text-green-800',
  },
  {
    value: 'danger',
    label: 'Urgent',
    icon: XCircleIcon,
    badge: 'bg-red-50 text-red-700',
    activeBorder: 'border-red-500',
    activeBg: 'bg-red-50',
    activeText: 'text-red-700',
    preview: 'bg-red-50 border-red-200 text-red-800',
  },
]

function typeBadge(type) {
  return types.find(t => t.value === type)?.badge ?? 'bg-gray-100 text-gray-600'
}
function typeIcon(type) {
  return types.find(t => t.value === type)?.icon ?? InformationCircleIcon
}

const MAX_MSG_LEN  = 500
const msgCharsLeft = computed(() => MAX_MSG_LEN - modal.form.message.length)

const previewClass = computed(
  () => types.find(t => t.value === modal.form.type)?.preview ?? 'bg-blue-50 border-blue-200 text-blue-800'
)

// ── Table ─────────────────────────────────────────────────────────────────────
const columns = [
  { key: 'title',      label: 'Title / Message' },
  { key: 'target',     label: 'Target' },
  { key: 'sent_count', label: 'Sent To' },
  { key: 'created_at', label: 'Sent At' },
  { key: 'actions',    label: '' },
]

const loading       = ref(false)
const announcements = ref([])
const meta          = ref(null)
const categoryOptions = ref([])

async function fetchAnnouncements(p = 1) {
  loading.value = true
  try {
    const res = await adminAnnouncementsApi.list({ page: p })
    announcements.value = res.data.data
    meta.value          = res.data.meta
  } finally { loading.value = false }
}

// ── Modal ─────────────────────────────────────────────────────────────────────
const modal = reactive({
  open:  false,
  saving: false,
  error:  '',
  form: {
    title:               '',
    message:             '',
    type:                'info',
    target_category_ids: [],
  },
})

function openCreateModal() {
  modal.form.title               = ''
  modal.form.message             = ''
  modal.form.type                = 'info'
  modal.form.target_category_ids = []
  modal.error  = ''
  modal.open   = true
}

function closeModal() {
  modal.open  = false
  modal.error = ''
}

async function handleCreate() {
  modal.saving = true
  modal.error  = ''
  try {
    const res = await adminAnnouncementsApi.create({
      title:               modal.form.title,
      message:             modal.form.message,
      type:                modal.form.type,
      target_category_ids: modal.form.target_category_ids,
    })
    toast.success(res.data.message || 'Announcement sent!')
    closeModal()
    fetchAnnouncements()
  } catch (err) {
    modal.error = err.response?.data?.message || 'Failed to send announcement.'
  } finally {
    modal.saving = false
  }
}

async function resend(row) {
  row._resending = true
  try {
    const res = await adminAnnouncementsApi.resend(row.id)
    toast.success(res.data.message || 'Re-sent!')
    fetchAnnouncements()
  } catch {
    toast.error('Failed to re-send.')
  } finally {
    row._resending = false
  }
}

async function deleteAnnouncement(row) {
  if (!confirm(`Delete announcement "${row.title}"? All student notifications will also be removed.`)) return
  try {
    await adminAnnouncementsApi.delete(row.id)
    toast.success('Announcement deleted.')
    fetchAnnouncements()
  } catch {
    toast.error('Failed to delete.')
  }
}

onMounted(async () => {
  fetchAnnouncements()
  const catRes = await adminCategoriesApi.list()
  categoryOptions.value = (catRes.data.data || []).map(c => ({
    value: c.id,
    label: c.name,
    color: c.color,
  }))
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
.animate-modal {
  animation: slideUp 0.25s ease-out;
}
@keyframes slideUp {
  from { opacity: 0; transform: translateY(12px) scale(0.97); }
  to   { opacity: 1; transform: translateY(0) scale(1); }
}
</style>
