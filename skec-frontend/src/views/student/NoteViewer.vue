<template>
  <div class="h-full">
    <!-- Loading state -->
    <div v-if="isLoading" class="flex items-center justify-center h-full bg-gray-900">
      <div class="text-center text-white">
        <div class="w-12 h-12 border-4 border-white/20 border-t-white rounded-full animate-spin mx-auto mb-4" />
        <p class="text-white/70">Loading viewer…</p>
      </div>
    </div>

    <div v-else-if="loadError" class="flex items-center justify-center h-full bg-gray-900 text-white text-center">
      <div>
        <p class="text-xl font-semibold mb-2">Failed to open note</p>
        <p class="text-white/50 text-sm mb-6">{{ loadError }}</p>
        <RouterLink to="/notes" class="text-primary-300 hover:underline text-sm">← Back to Notes</RouterLink>
      </div>
    </div>

    <!-- PDF Viewer -->
    <PdfViewer
      v-else-if="streamUrl"
      :note-id="noteId"
      :user-id="authStore.user?.id || 0"
      :stream-url="streamUrl"
      :student-email="authStore.user?.email || ''"
      :student-reg-no="authStore.user?.profile?.reg_no || ''"
      :student-name="authStore.user?.name || ''"
      :watermark-opacity="settingsStore.get('watermark_opacity', 0.15)"
      :watermark-template="settingsStore.get('watermark_text_template', '{name} | {email} | {reg_no} | {date}')"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue'
import { useRoute, useRouter }  from 'vue-router'
import { notesApi }             from '../../api/student/notes'
import { useAuthStore }         from '../../stores/auth'
import { useSettingsStore }     from '../../stores/settings'
import PdfViewer from '../../components/pdf/PdfViewer.vue'

const route         = useRoute()
const router        = useRouter()
const authStore     = useAuthStore()
const settingsStore = useSettingsStore()

const isLoading  = ref(true)
const loadError  = ref('')
const streamUrl  = ref('')
const noteId     = computed(() => parseInt(route.params.id))
const lastSecurityLogAt = new Map()
let originalGetDisplayMedia = null

function logSecurityEvent(action) {
  const now = Date.now()
  const last = lastSecurityLogAt.get(action) || 0
  if (now - last < 3000) return
  lastSecurityLogAt.set(action, now)
  notesApi.logAccess(noteId.value, { action }).catch(() => {})
}

function handleKeyDown(event) {
  if (event.key === 'PrintScreen') {
    logSecurityEvent('screenshot_attempt')
  }

  if ((event.ctrlKey || event.metaKey) && ['p', 's', 'u', 'i', 'j', 'c'].includes(event.key?.toLowerCase())) {
    logSecurityEvent('print_attempt')
  }
}

function handleCopyAttempt(event) {
  logSecurityEvent('copy_attempt')
  event.preventDefault()
}

function handleBeforePrint() {
  logSecurityEvent('print_attempt')
}

function patchScreenCaptureApi() {
  if (!navigator.mediaDevices?.getDisplayMedia) return
  originalGetDisplayMedia = navigator.mediaDevices.getDisplayMedia.bind(navigator.mediaDevices)
  navigator.mediaDevices.getDisplayMedia = (...args) => {
    logSecurityEvent('capture_attempt')
    return originalGetDisplayMedia(...args)
  }
}

function restoreScreenCaptureApi() {
  if (originalGetDisplayMedia && navigator.mediaDevices?.getDisplayMedia) {
    navigator.mediaDevices.getDisplayMedia = originalGetDisplayMedia
  }
}

onMounted(async () => {
  try {
    const res = await notesApi.streamToken(noteId.value)
    const data = res.data.data

    streamUrl.value = data.stream_url

    // Log opened
    await notesApi.logAccess(noteId.value, { action: 'opened' })

    window.addEventListener('keydown', handleKeyDown)
    window.addEventListener('beforeprint', handleBeforePrint)
    document.addEventListener('copy', handleCopyAttempt)
    document.addEventListener('cut', handleCopyAttempt)
    document.addEventListener('contextmenu', handleCopyAttempt)
    patchScreenCaptureApi()
  } catch (err) {
    loadError.value = err.response?.data?.message || 'Could not open this note.'
  } finally {
    isLoading.value = false
  }
})

onUnmounted(() => {
  window.removeEventListener('keydown', handleKeyDown)
  window.removeEventListener('beforeprint', handleBeforePrint)
  document.removeEventListener('copy', handleCopyAttempt)
  document.removeEventListener('cut', handleCopyAttempt)
  document.removeEventListener('contextmenu', handleCopyAttempt)
  restoreScreenCaptureApi()
})
</script>
