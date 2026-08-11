<template>
  <div>
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
      <div>
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Web Coordinators</h1>
        <p class="text-sm text-gray-500 mt-0.5">Manage web coordinator accounts and site administrator privileges</p>
      </div>
      <AppButton variant="primary" @click="openCreateModal">
        <UserPlusIcon class="w-4 h-4 mr-1.5" />
        New Web Coordinator
      </AppButton>
    </div>

    <!-- Global Alerts -->
    <AppAlert v-if="actionMsg"   type="success" :message="actionMsg"   class="mb-4" dismissible />
    <AppAlert v-if="actionError" type="error"   :message="actionError" class="mb-4" dismissible />

    <!-- Filters -->
    <div class="card mb-5 flex flex-wrap gap-4">
      <AppInput
        v-model="search"
        placeholder="Search web coordinator by name or email…"
        class="flex-1 min-w-48"
        @input="debouncedFetch"
      />
      <AppSelect
        v-model="statusFilter"
        :options="[{ value: '', label: 'All Statuses' }, { value: 'active', label: 'Active' }, { value: 'inactive', label: 'Inactive' }]"
        class="w-44"
        @change="() => fetchAdmins(1)"
      />
    </div>

    <!-- Table -->
    <AppTable :columns="columns" :rows="admins" :loading="loading" empty-title="No web coordinators found">
      <!-- Admin Name & Email -->
      <template #cell-name="{ row }">
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-lg bg-indigo-100 flex items-center justify-center flex-shrink-0">
            <span class="text-indigo-700 text-xs font-bold">{{ getInitials(row.name) }}</span>
          </div>
          <div class="min-w-0">
            <div class="flex items-center gap-2">
              <span class="font-medium text-gray-900 truncate">{{ row.name }}</span>
              <span
                v-if="row.is_super_admin"
                class="inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-full bg-purple-100 text-purple-700 border border-purple-200"
              >
                <ShieldCheckIcon class="w-3 h-3" /> Site Administrator
              </span>
            </div>
            <p class="text-xs text-gray-400 truncate">{{ row.email }}</p>
          </div>
        </div>
      </template>

      <!-- Status -->
      <template #cell-status="{ row }">
        <AppBadge :variant="row.status" :label="row.status" dot />
      </template>

      <!-- Created At -->
      <template #cell-created_at="{ row }">
        <span class="text-sm text-gray-600">{{ formatDate(row.created_at) }}</span>
      </template>

      <!-- Actions -->
      <template #cell-actions="{ row }">
        <div class="flex items-center gap-2">
          <AppButton size="xs" variant="secondary" @click="openEditModal(row)">
            <PencilIcon class="w-3.5 h-3.5 mr-1" /> Edit
          </AppButton>
          <AppButton
            size="xs"
            variant="ghost"
            :disabled="row.id === authStore.user?.id"
            @click="toggleStatus(row)"
          >
            {{ row.status === 'active' ? 'Deactivate' : 'Activate' }}
          </AppButton>
          <AppButton
            size="xs"
            variant="danger"
            :disabled="row.id === authStore.user?.id"
            @click="confirmDelete(row)"
          >
            Delete
          </AppButton>
        </div>
      </template>
    </AppTable>

    <AppPagination :meta="meta" @change="fetchAdmins" class="mt-4" />

    <!-- Create / Edit Modal -->
    <AppModal v-model="modal.open" :title="modal.isEdit ? 'Edit Web Coordinator' : 'Create Web Coordinator'">
      <form @submit.prevent="handleSave" class="space-y-4">
        <AppAlert v-if="modal.error" type="error" :message="modal.error" dismissible />

        <AppInput
          v-model="modal.form.name"
          label="Full Name"
          placeholder="e.g. John Doe"
          required
        />

        <AppInput
          v-model="modal.form.email"
          label="Email Address"
          type="email"
          placeholder="admin@srikumaran.in"
          required
        />

        <AppInput
          v-model="modal.form.password"
          label="Password"
          type="password"
          :placeholder="modal.isEdit ? 'Leave blank to keep current password' : 'Enter password (min 8 chars)'"
          :required="!modal.isEdit"
        />

        <AppSelect
          v-model="modal.form.status"
          label="Account Status"
          :options="[{ value: 'active', label: 'Active' }, { value: 'inactive', label: 'Inactive' }]"
        />

        <!-- Site Administrator privilege checkbox -->
        <div class="p-3 rounded-xl border border-purple-100 bg-purple-50/50 flex items-start gap-3">
          <input
            id="super-admin-checkbox"
            v-model="modal.form.is_super_admin"
            type="checkbox"
            class="mt-1 rounded border-purple-300 text-purple-600 focus:ring-purple-500"
          />
          <label for="super-admin-checkbox" class="text-sm cursor-pointer select-none">
            <span class="font-semibold text-purple-900 block">Grant Site Administrator Privileges</span>
            <span class="text-xs text-purple-700">Site Administrators can manage web coordinator accounts and system settings.</span>
          </label>
        </div>

        <div class="flex gap-2 justify-end pt-2">
          <AppButton variant="secondary" @click="modal.open = false">Cancel</AppButton>
          <AppButton type="submit" variant="primary" :loading="modal.saving">
            {{ modal.isEdit ? 'Update Web Coordinator' : 'Create Web Coordinator' }}
          </AppButton>
        </div>
      </form>
    </AppModal>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { adminAdministratorsApi } from '../../api/admin/administrators'
import { useAuthStore }           from '../../stores/auth'
import { formatDate, getInitials, debounce } from '../../utils/helpers'
import { UserPlusIcon, PencilIcon, ShieldCheckIcon } from '@heroicons/vue/24/outline'
import AppButton     from '../../components/common/AppButton.vue'
import AppTable      from '../../components/common/AppTable.vue'
import AppInput      from '../../components/common/AppInput.vue'
import AppSelect     from '../../components/common/AppSelect.vue'
import AppBadge      from '../../components/common/AppBadge.vue'
import AppAlert      from '../../components/common/AppAlert.vue'
import AppPagination from '../../components/common/AppPagination.vue'
import AppModal      from '../../components/common/AppModal.vue'

const authStore   = useAuthStore()
const loading     = ref(false)
const admins      = ref([])
const meta        = ref(null)
const search      = ref('')
const statusFilter= ref('')
const actionMsg   = ref('')
const actionError = ref('')

const columns = [
  { key: 'name',       label: 'Web Coordinator' },
  { key: 'status',     label: 'Status' },
  { key: 'created_at', label: 'Created' },
  { key: 'actions',    label: 'Actions' },
]

const modal = reactive({
  open: false,
  isEdit: false,
  saving: false,
  editId: null,
  error: '',
  form: {
    name: '',
    email: '',
    password: '',
    status: 'active',
    is_super_admin: false,
  },
})

async function fetchAdmins(p = 1) {
  loading.value = true
  try {
    const res = await adminAdministratorsApi.list({
      page: p,
      search: search.value || undefined,
      status: statusFilter.value || undefined,
    })
    admins.value = res.data.data
    meta.value   = res.data.meta
  } finally {
    loading.value = false
  }
}

const debouncedFetch = debounce(() => fetchAdmins(1), 300)

function openCreateModal() {
  modal.isEdit = false
  modal.editId = null
  modal.form.name = ''
  modal.form.email = ''
  modal.form.password = ''
  modal.form.status = 'active'
  modal.form.is_super_admin = false
  modal.error = ''
  modal.open = true
}

function openEditModal(admin) {
  modal.isEdit = true
  modal.editId = admin.id
  modal.form.name = admin.name
  modal.form.email = admin.email
  modal.form.password = ''
  modal.form.status = admin.status
  modal.form.is_super_admin = !!admin.is_super_admin
  modal.error = ''
  modal.open = true
}

async function handleSave() {
  modal.saving = true
  modal.error = ''
  actionMsg.value = ''
  actionError.value = ''

  try {
    const payload = {
      name: modal.form.name,
      email: modal.form.email,
      status: modal.form.status,
      is_super_admin: modal.form.is_super_admin,
    }
    if (modal.form.password) {
      payload.password = modal.form.password
    }

    if (modal.isEdit) {
      await adminAdministratorsApi.update(modal.editId, payload)
      actionMsg.value = `Web Coordinator "${modal.form.name}" updated successfully.`
    } else {
      await adminAdministratorsApi.create(payload)
      actionMsg.value = `Web Coordinator "${modal.form.name}" created successfully.`
    }
    modal.open = false
    fetchAdmins(meta.value?.current_page || 1)
  } catch (err) {
    modal.error = err.response?.data?.message || 'Failed to save web coordinator.'
  } finally {
    modal.saving = false
  }
}

async function toggleStatus(admin) {
  actionMsg.value = ''
  actionError.value = ''
  try {
    const res = await adminAdministratorsApi.toggleStatus(admin.id)
    actionMsg.value = res.data?.message || 'Status updated.'
    fetchAdmins(meta.value?.current_page || 1)
  } catch (err) {
    actionError.value = err.response?.data?.message || 'Failed to toggle status.'
  }
}

async function confirmDelete(admin) {
  if (!confirm(`Are you sure you want to delete web coordinator "${admin.name}"?`)) return
  actionMsg.value = ''
  actionError.value = ''
  try {
    await adminAdministratorsApi.delete(admin.id)
    actionMsg.value = `Web Coordinator "${admin.name}" deleted.`
    fetchAdmins(meta.value?.current_page || 1)
  } catch (err) {
    actionError.value = err.response?.data?.message || 'Failed to delete web coordinator.'
  }
}

onMounted(() => {
  fetchAdmins()
})
</script>
