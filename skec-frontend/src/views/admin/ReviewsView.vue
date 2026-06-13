<template>
  <div>
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
      <div>
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Reviews</h1>
        <p class="text-sm text-gray-500 mt-0.5">Manage student reviews shown on the landing page</p>
      </div>
      <div class="flex items-center gap-3">
        <span
          v-if="pendingCount > 0"
          class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 border border-amber-200 text-amber-700 text-sm font-semibold rounded-xl"
        >
          <span class="w-2 h-2 bg-amber-500 rounded-full animate-pulse" />
          {{ pendingCount }} pending
        </span>
      </div>
    </div>

    <!-- Alerts -->
    <AppAlert v-if="actionMsg"   type="success" :message="actionMsg"   class="mb-4" dismissible />
    <AppAlert v-if="actionError" type="error"   :message="actionError" class="mb-4" dismissible />

    <!-- Filter Tabs + Search -->
    <div class="flex flex-col sm:flex-row gap-3 mb-5">
      <div class="flex gap-2 flex-wrap">
        <button
          v-for="tab in tabs"
          :key="tab.value"
          @click="filterStatus = tab.value; fetchReviews(1)"
          :class="[
            'px-3 py-1.5 rounded-xl text-xs font-semibold transition-all border capitalize',
            filterStatus === tab.value
              ? tab.activeClass
              : 'bg-white text-gray-600 border-gray-200 hover:border-gray-300',
          ]"
        >
          {{ tab.label }}
          <span v-if="tab.value && counts[tab.value] !== undefined" class="ml-1 opacity-70">({{ counts[tab.value] }})</span>
        </button>
      </div>
      <div class="flex-1 min-w-0">
        <AppInput v-model="search" placeholder="Search by student name…" @input="debouncedFetch" />
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="py-12 flex justify-center"><AppLoader /></div>

    <!-- Empty -->
    <AppEmptyState
      v-else-if="!reviews.length"
      :icon="StarOutlineIcon"
      title="No reviews found"
      :description="filterStatus ? `No ${filterStatus} reviews yet.` : 'No reviews have been submitted yet.'"
    />

    <!-- Reviews Grid -->
    <div v-else class="space-y-4">
      <div
        v-for="review in reviews"
        :key="review.id"
        class="card p-5 sm:p-6 hover:shadow-md transition-shadow"
      >
        <div class="flex flex-col sm:flex-row sm:items-start gap-4">

          <!-- Left: Student Info -->
          <div class="flex items-start gap-3 flex-1 min-w-0">
            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center">
              <span class="text-primary-700 font-bold text-sm">{{ review.student.name?.charAt(0) }}</span>
            </div>
            <div class="min-w-0">
              <p class="font-semibold text-gray-900 text-sm">{{ review.student.name }}</p>
              <p class="text-xs text-gray-400">{{ review.student.email }}</p>
              <p v-if="review.student.batch" class="text-xs text-primary-600 mt-0.5">{{ review.student.batch }}</p>
            </div>
          </div>

          <!-- Right: Status + Actions -->
          <div class="flex items-center gap-2 flex-shrink-0">
            <span
              class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold"
              :class="statusBadgeClass(review.status)"
            >
              {{ review.status }}
            </span>
            <p class="text-xs text-gray-400 hidden sm:block">{{ formatDate(review.updated_at) }}</p>
          </div>
        </div>

        <!-- Stars + Review Text -->
        <div class="mt-4">
          <div class="flex gap-0.5 mb-2">
            <StarSolidIcon
              v-for="s in 5"
              :key="s"
              class="w-4 h-4"
              :class="s <= review.rating ? 'text-amber-400' : 'text-gray-200'"
            />
            <span class="ml-2 text-xs font-semibold text-gray-500">{{ review.rating }}/5</span>
          </div>
          <p class="text-sm text-gray-700 leading-relaxed italic">"{{ review.review }}"</p>
          <p v-if="review.admin_note" class="mt-2 text-xs text-red-500">Admin note: "{{ review.admin_note }}"</p>
        </div>

        <!-- Actions -->
        <div class="mt-4 pt-4 border-t border-gray-100 flex flex-wrap gap-2">
          <!-- Approve -->
          <AppButton
            v-if="review.status !== 'approved'"
            size="xs"
            variant="success"
            :loading="actionLoading === `approve-${review.id}`"
            @click="approveReview(review)"
          >
            ✓ Approve
          </AppButton>

          <!-- Reject -->
          <AppButton
            v-if="review.status !== 'rejected'"
            size="xs"
            variant="secondary"
            :loading="actionLoading === `reject-${review.id}`"
            @click="openRejectModal(review)"
          >
            ✗ Reject
          </AppButton>

          <!-- Delete -->
          <AppButton
            size="xs"
            variant="danger"
            :loading="actionLoading === `delete-${review.id}`"
            @click="deleteReview(review)"
          >
            Delete
          </AppButton>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <AppPagination :meta="meta" @change="fetchReviews" class="mt-6" />

    <!-- Reject Modal -->
    <AppModal v-model="rejectModal.open" title="Reject Review">
      <p class="text-sm text-gray-600 mb-4">Optionally add a note for why this review is being rejected (visible to admin only).</p>
      <textarea
        v-model="rejectModal.note"
        rows="3"
        placeholder="Optional admin note…"
        class="input-base resize-none text-sm mb-4"
      />
      <div class="flex gap-2 justify-end">
        <AppButton variant="secondary" @click="rejectModal.open = false">Cancel</AppButton>
        <AppButton variant="danger" :loading="actionLoading === `reject-${rejectModal.review?.id}`" @click="confirmReject">
          Reject Review
        </AppButton>
      </div>
    </AppModal>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { adminReviewsApi } from '../../api/admin/reviews'
import { debounce } from '../../utils/helpers'
import { StarIcon as StarSolidIcon } from '@heroicons/vue/24/solid'
import { StarIcon as StarOutlineIcon } from '@heroicons/vue/24/outline'
import AppAlert      from '../../components/common/AppAlert.vue'
import AppInput      from '../../components/common/AppInput.vue'
import AppButton     from '../../components/common/AppButton.vue'
import AppLoader     from '../../components/common/AppLoader.vue'
import AppEmptyState from '../../components/common/AppEmptyState.vue'
import AppPagination from '../../components/common/AppPagination.vue'
import AppModal      from '../../components/common/AppModal.vue'

// ── State ─────────────────────────────────────────────────────────────────
const loading      = ref(false)
const reviews      = ref([])
const meta         = ref(null)
const search       = ref('')
const filterStatus = ref('')
const pendingCount = ref(0)
const actionLoading = ref('')
const actionMsg    = ref('')
const actionError  = ref('')
const counts       = ref({})

const rejectModal = ref({ open: false, review: null, note: '' })

const tabs = [
  { value: '',         label: 'All',      activeClass: 'bg-gray-900 text-white border-gray-900' },
  { value: 'pending',  label: 'Pending',  activeClass: 'bg-amber-500 text-white border-amber-500' },
  { value: 'approved', label: 'Approved', activeClass: 'bg-green-600 text-white border-green-600' },
  { value: 'rejected', label: 'Rejected', activeClass: 'bg-red-600 text-white border-red-600' },
]

// ── Fetch ─────────────────────────────────────────────────────────────────
async function fetchReviews(page = 1) {
  loading.value = true
  try {
    const res = await adminReviewsApi.list({
      page,
      search: search.value || undefined,
      status: filterStatus.value || undefined,
    })
    reviews.value = res.data.data
    meta.value    = res.data.meta
  } finally {
    loading.value = false
  }
}

async function fetchPendingCount() {
  try {
    const res = await adminReviewsApi.pendingCount()
    pendingCount.value = res.data.data.count || 0
  } catch {}
}

const debouncedFetch = debounce(() => fetchReviews(1), 300)

// ── Actions ───────────────────────────────────────────────────────────────
async function approveReview(review) {
  actionLoading.value = `approve-${review.id}`
  actionMsg.value = ''
  actionError.value = ''
  try {
    await adminReviewsApi.approve(review.id)
    actionMsg.value = `Review by ${review.student.name} approved!`
    fetchReviews(meta.value?.current_page || 1)
    fetchPendingCount()
  } catch (e) {
    actionError.value = e.response?.data?.message || 'Failed to approve.'
  } finally {
    actionLoading.value = ''
  }
}

function openRejectModal(review) {
  rejectModal.value = { open: true, review, note: '' }
}

async function confirmReject() {
  const review = rejectModal.value.review
  if (!review) return
  actionLoading.value = `reject-${review.id}`
  actionMsg.value = ''
  actionError.value = ''
  try {
    await adminReviewsApi.reject(review.id, rejectModal.value.note)
    actionMsg.value = `Review by ${review.student.name} rejected.`
    rejectModal.value.open = false
    fetchReviews(meta.value?.current_page || 1)
    fetchPendingCount()
  } catch (e) {
    actionError.value = e.response?.data?.message || 'Failed to reject.'
  } finally {
    actionLoading.value = ''
  }
}

async function deleteReview(review) {
  if (!confirm(`Delete review by ${review.student.name}? This cannot be undone.`)) return
  actionLoading.value = `delete-${review.id}`
  actionMsg.value = ''
  actionError.value = ''
  try {
    await adminReviewsApi.destroy(review.id)
    actionMsg.value = 'Review deleted.'
    fetchReviews(meta.value?.current_page || 1)
    fetchPendingCount()
  } catch (e) {
    actionError.value = e.response?.data?.message || 'Failed to delete.'
  } finally {
    actionLoading.value = ''
  }
}

// ── Helpers ───────────────────────────────────────────────────────────────
function statusBadgeClass(status) {
  return {
    'bg-amber-100 text-amber-700': status === 'pending',
    'bg-green-100 text-green-700': status === 'approved',
    'bg-red-100   text-red-700':   status === 'rejected',
  }
}

function formatDate(dt) {
  if (!dt) return ''
  return new Date(dt).toLocaleDateString('en-IN', { day: 'numeric', month: 'short', year: 'numeric' })
}

// ── Lifecycle ─────────────────────────────────────────────────────────────
onMounted(() => {
  fetchReviews()
  fetchPendingCount()
})
</script>
