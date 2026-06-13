<template>
  <div class="max-w-2xl mx-auto">

    <!-- Page Header -->
    <div class="mb-8 text-center">
      <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-amber-50 mb-4">
        <StarSolidIcon class="w-8 h-8 text-amber-400" />
      </div>
      <h1 class="text-2xl font-bold text-gray-900 mb-2">My Review</h1>
      <p class="text-gray-500 text-sm">Share your experience with SKEC — your review helps future students.</p>
    </div>

    <!-- Alerts -->
    <AppAlert v-if="submitted" type="success" message="Your review has been submitted and is pending admin approval." class="mb-5" dismissible />
    <AppAlert v-if="error" type="error" :message="error" class="mb-5" dismissible />

    <!-- Loading -->
    <div v-if="loading" class="flex items-center justify-center py-16">
      <AppLoader />
    </div>

    <template v-else>

      <!-- Existing Review Status Banner -->
      <div v-if="existing" class="mb-6">
        <div
          class="rounded-2xl border p-4 flex items-start gap-3"
          :class="{
            'bg-amber-50 border-amber-200': existing.status === 'pending',
            'bg-green-50 border-green-200': existing.status === 'approved',
            'bg-red-50  border-red-200':   existing.status === 'rejected',
          }"
        >
          <component
            :is="statusIcon"
            class="w-5 h-5 flex-shrink-0 mt-0.5"
            :class="{
              'text-amber-500': existing.status === 'pending',
              'text-green-600': existing.status === 'approved',
              'text-red-600':   existing.status === 'rejected',
            }"
          />
          <div>
            <p class="font-semibold text-sm" :class="statusTextClass">
              {{ statusLabel }}
            </p>
            <p class="text-xs mt-0.5 text-gray-500">
              Submitted on {{ existing.updated_at || existing.created_at }}.
              {{ existing.status === 'pending'  ? 'Your review will appear on the landing page once approved.' : '' }}
              {{ existing.status === 'approved' ? 'Your review is live on the landing page!' : '' }}
              {{ existing.status === 'rejected' ? 'You can update and resubmit your review below.' : '' }}
            </p>
            <p v-if="existing.admin_note" class="text-xs mt-1 text-gray-500 italic">
              Admin note: "{{ existing.admin_note }}"
            </p>
          </div>
        </div>
      </div>

      <!-- Review Form Card -->
      <div class="card p-6 sm:p-8 space-y-6">
        <h2 class="font-bold text-gray-800 text-base border-b border-gray-100 pb-3">
          {{ existing ? 'Update Your Review' : 'Write a Review' }}
        </h2>

        <!-- Star Rating -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-3">Your Rating</label>
          <div class="flex items-center gap-2">
            <button
              v-for="star in 5"
              :key="star"
              type="button"
              class="transition-transform hover:scale-110 focus:outline-none"
              @click="form.rating = star"
              @mouseenter="hoverRating = star"
              @mouseleave="hoverRating = 0"
            >
              <StarSolidIcon
                class="w-10 h-10 transition-colors"
                :class="(hoverRating || form.rating) >= star ? 'text-amber-400' : 'text-gray-200'"
              />
            </button>
            <span class="ml-2 text-sm font-semibold text-gray-600">
              {{ ratingLabel }}
            </span>
          </div>
        </div>

        <!-- Review Text -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Your Review</label>
          <textarea
            v-model="form.review"
            rows="5"
            maxlength="1000"
            placeholder="Share your experience studying at SKEC — what did you love? How did it help you?"
            class="input-base resize-none text-sm"
          />
          <div class="flex justify-between mt-1">
            <p class="text-xs text-gray-400">Minimum 10 characters</p>
            <p class="text-xs text-gray-400">{{ form.review.length }}/1000</p>
          </div>
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
          <AppButton
            variant="primary"
            :loading="saving"
            :disabled="form.rating === 0 || form.review.trim().length < 10"
            class="w-full"
            @click="submitReview"
          >
            {{ existing ? '🔄 Update Review' : '⭐ Submit Review' }}
          </AppButton>
        </div>
      </div>

      <!-- Preview of submitted review -->
      <div v-if="existing && existing.status === 'approved'" class="mt-6 card p-5 sm:p-6 bg-gradient-to-br from-amber-50 to-orange-50 border-amber-100">
        <p class="text-xs font-bold uppercase tracking-widest text-amber-600 mb-3">Live Preview — As Seen on Landing Page</p>
        <div class="flex gap-1 mb-3">
          <StarSolidIcon v-for="s in existing.rating" :key="s" class="w-4 h-4 text-amber-400" />
          <StarOutlineIcon v-for="s in (5 - existing.rating)" :key="'e'+s" class="w-4 h-4 text-gray-300" />
        </div>
        <p class="text-gray-700 text-sm leading-relaxed mb-4 italic">"{{ existing.review }}"</p>
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center">
            <span class="text-primary-700 font-bold text-xs">{{ userName?.charAt(0) }}</span>
          </div>
          <div>
            <div class="font-semibold text-gray-900 text-sm">{{ userName }}</div>
          </div>
        </div>
      </div>

    </template>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '../../stores/auth'
import { reviewsApi } from '../../api/student/reviews'
import AppAlert  from '../../components/common/AppAlert.vue'
import AppButton from '../../components/common/AppButton.vue'
import AppLoader from '../../components/common/AppLoader.vue'
import { StarIcon as StarSolidIcon } from '@heroicons/vue/24/solid'
import { StarIcon as StarOutlineIcon, ClockIcon, CheckCircleIcon, XCircleIcon } from '@heroicons/vue/24/outline'

const authStore = useAuthStore()
const userName  = computed(() => authStore.user?.name)

// ── State ─────────────────────────────────────────────────────────────────
const loading    = ref(true)
const saving     = ref(false)
const submitted  = ref(false)
const error      = ref('')
const existing   = ref(null)
const hoverRating = ref(0)

const form = ref({ rating: 0, review: '' })

// ── Helpers ───────────────────────────────────────────────────────────────
const ratingLabels = ['', 'Poor', 'Fair', 'Good', 'Great', 'Excellent!']
const ratingLabel  = computed(() => ratingLabels[hoverRating.value || form.value.rating] || 'Select a rating')

const statusIcon = computed(() => {
  if (!existing.value) return null
  return { pending: ClockIcon, approved: CheckCircleIcon, rejected: XCircleIcon }[existing.value.status] || ClockIcon
})
const statusLabel = computed(() => {
  return { pending: '⏳ Pending Approval', approved: '✅ Review Approved', rejected: '❌ Review Rejected' }[existing.value?.status] || ''
})
const statusTextClass = computed(() => ({
  'text-amber-700': existing.value?.status === 'pending',
  'text-green-700': existing.value?.status === 'approved',
  'text-red-700':   existing.value?.status === 'rejected',
}))

// ── Submit ────────────────────────────────────────────────────────────────
async function submitReview() {
  if (form.value.rating === 0 || form.value.review.trim().length < 10) return
  saving.value   = true
  error.value    = ''
  submitted.value = false
  try {
    const res = await reviewsApi.submit({
      rating: form.value.rating,
      review: form.value.review.trim(),
    })
    existing.value  = res.data.data
    submitted.value = true
  } catch (e) {
    error.value = e.response?.data?.message || 'Failed to submit review. Please try again.'
  } finally {
    saving.value = false
  }
}

// ── Load ──────────────────────────────────────────────────────────────────
onMounted(async () => {
  try {
    const res = await reviewsApi.getMyReview()
    existing.value = res.data.data
    if (existing.value) {
      form.value.rating = existing.value.rating
      form.value.review = existing.value.review
    }
  } catch { /* no review yet */ } finally {
    loading.value = false
  }
})
</script>
