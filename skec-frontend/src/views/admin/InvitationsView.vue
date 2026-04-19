<template>
  <div>
    <div class="flex items-center justify-between mb-5 sm:mb-6">
      <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Invitations</h1>
    </div>

    <!-- Invite form -->
    <div class="card mb-5">
      <h2 class="font-semibold text-gray-800 mb-4 text-base">Invite a Student</h2>
      <AppAlert v-if="inviteError"   type="error"   :message="inviteError"   class="mb-4" dismissible />
      <AppAlert v-if="inviteSuccess" type="success" :message="inviteSuccess" class="mb-4" dismissible />
      <form @submit.prevent="sendInvite" class="flex flex-col sm:flex-row gap-3">
        <AppInput
          v-model="inviteEmail"
          type="email"
          placeholder="student@email.com"
          :error="emailError"
          class="flex-1"
        />
        <AppButton type="submit" variant="primary" :loading="inviting" class="sm:flex-shrink-0">
          Send Invite
        </AppButton>
      </form>
    </div>

    <!-- Mobile card list -->
    <div v-if="isMobile" class="space-y-3">
      <div v-if="loading" class="py-12"><AppLoader /></div>
      <AppEmptyState v-else-if="!invitations.length" title="No invitations yet" />
      <div
        v-else
        v-for="inv in invitations"
        :key="inv.id"
        class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm"
      >
        <div class="flex items-start justify-between gap-2 mb-2">
          <p class="font-medium text-gray-900 text-sm truncate flex-1">{{ inv.email }}</p>
          <AppBadge :variant="getStatus(inv)" :label="getStatus(inv)" class="flex-shrink-0" />
        </div>
        <p class="text-xs text-gray-400 mb-3">Expires {{ formatDate(inv.expires_at) }}</p>
        <div class="flex gap-2 flex-wrap">
          <button v-if="!inv.used_at" @click="resend(inv)"
            class="text-xs text-primary-600 font-semibold bg-primary-50 hover:bg-primary-100 px-3 py-1.5 rounded-lg transition">
            Resend
          </button>
          <button @click="copyLink(inv)"
            class="text-xs text-gray-600 bg-gray-100 hover:bg-gray-200 px-3 py-1.5 rounded-lg transition">
            Copy Link
          </button>
          <button @click="deleteInv(inv)"
            class="text-xs text-red-600 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg transition ml-auto">
            Delete
          </button>
        </div>
      </div>
    </div>

    <!-- Desktop table -->
    <template v-else>
      <AppTable :columns="columns" :rows="invitations" :loading="loading">
        <template #cell-email="{ row }">
          <span class="font-medium">{{ row.email }}</span>
        </template>
        <template #cell-status="{ row }">
          <AppBadge :variant="getStatus(row)" :label="getStatus(row)" />
        </template>
        <template #cell-expires_at="{ row }">{{ formatDate(row.expires_at) }}</template>
        <template #cell-actions="{ row }">
          <div class="flex items-center gap-2">
            <button v-if="!row.used_at" @click="resend(row)"
              class="text-xs text-primary-600 font-medium hover:underline">Resend</button>
            <button @click="copyLink(row)"
              class="text-xs text-gray-500 hover:underline">Copy Link</button>
            <button @click="deleteInv(row)"
              class="text-xs text-red-500 hover:underline">Delete</button>
          </div>
        </template>
      </AppTable>
    </template>

    <AppPagination :meta="meta" @change="fetchInvitations" />
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { adminInvitationsApi }         from '../../api/admin/invitations'
import { formatDate, copyToClipboard } from '../../utils/helpers'
import AppInput      from '../../components/common/AppInput.vue'
import AppButton     from '../../components/common/AppButton.vue'
import AppTable      from '../../components/common/AppTable.vue'
import AppBadge      from '../../components/common/AppBadge.vue'
import AppAlert      from '../../components/common/AppAlert.vue'
import AppPagination from '../../components/common/AppPagination.vue'
import AppLoader     from '../../components/common/AppLoader.vue'
import AppEmptyState from '../../components/common/AppEmptyState.vue'

const loading       = ref(false)
const inviting      = ref(false)
const invitations   = ref([])
const meta          = ref(null)
const inviteEmail   = ref('')
const inviteError   = ref('')
const inviteSuccess = ref('')
const emailError    = ref('')
const isMobile      = ref(false)

function checkMobile() { isMobile.value = window.innerWidth < 768 }
onMounted(() => { checkMobile(); window.addEventListener('resize', checkMobile) })
onUnmounted(() => window.removeEventListener('resize', checkMobile))

const columns = [
  { key: 'email',      label: 'Email' },
  { key: 'status',     label: 'Status' },
  { key: 'expires_at', label: 'Expires' },
  { key: 'actions',    label: '' },
]

function getStatus(inv) {
  if (inv.used_at) return 'used'
  if (new Date(inv.expires_at) < new Date()) return 'expired'
  return 'pending'
}

async function fetchInvitations(p = 1) {
  loading.value = true
  const res = await adminInvitationsApi.list({ page: p })
  invitations.value = res.data.data
  meta.value        = res.data.meta
  loading.value = false
}

async function sendInvite() {
  inviteError.value   = ''
  inviteSuccess.value = ''
  emailError.value    = ''
  inviting.value      = true
  try {
    await adminInvitationsApi.create({ email: inviteEmail.value })
    inviteSuccess.value = `Invitation sent to ${inviteEmail.value}`
    inviteEmail.value   = ''
    fetchInvitations()
  } catch (err) {
    if (err.response?.data?.errors?.email) emailError.value = err.response.data.errors.email[0]
    else inviteError.value = err.response?.data?.message || 'Failed to send invitation.'
  } finally { inviting.value = false }
}

async function resend(inv) {
  await adminInvitationsApi.resend(inv.id)
  inviteSuccess.value = `Invitation resent to ${inv.email}`
  fetchInvitations()
}
async function deleteInv(inv) {
  if (!confirm(`Delete invitation for ${inv.email}?`)) return
  await adminInvitationsApi.delete(inv.id)
  fetchInvitations()
}
function copyLink(inv) {
  const link = `${window.location.origin}/register?token=${inv.token}`
  copyToClipboard(link)
  inviteSuccess.value = 'Link copied to clipboard!'
  setTimeout(() => { inviteSuccess.value = '' }, 2000)
}

onMounted(() => fetchInvitations())
</script>