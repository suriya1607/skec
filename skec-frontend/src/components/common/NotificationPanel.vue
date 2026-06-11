<template>
  <Teleport to="body">
    <div v-if="open">
      <!-- Backdrop: z-40, closes panel on click -->
      <div
        class="fixed inset-0 z-40"
        @click="$emit('close')"
      />

      <!-- Panel: z-50 (above backdrop), positioned fixed below header -->
      <Transition
        enter-active-class="transition ease-out duration-200"
        enter-from-class="opacity-0 translate-y-1 scale-95"
        enter-to-class="opacity-100 translate-y-0 scale-100"
        leave-active-class="transition ease-in duration-150"
        leave-from-class="opacity-100 translate-y-0 scale-100"
        leave-to-class="opacity-0 translate-y-1 scale-95"
        appear
      >
        <div
          :style="panelStyle"
          class="fixed z-50 w-80 sm:w-96 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden"
          @click.stop
        >
          <!-- Header -->
          <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 bg-gray-50/60">
            <div class="flex items-center gap-2">
              <BellIcon class="w-4 h-4 text-primary-600" />
              <span class="font-semibold text-gray-800 text-sm">Notifications</span>
              <span
                v-if="store.unreadCount > 0"
                class="inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full bg-red-500 text-white text-[10px] font-bold"
              >
                {{ store.unreadCount > 99 ? '99+' : store.unreadCount }}
              </span>
            </div>
            <button
              v-if="store.unreadCount > 0"
              class="text-xs text-primary-600 hover:text-primary-800 font-medium transition-colors disabled:opacity-50"
              :disabled="markingAll"
              @click.stop="markAllRead"
            >
              {{ markingAll ? 'Marking…' : 'Mark all read' }}
            </button>
          </div>

          <!-- Loading -->
          <div v-if="store.loading && !store.notifications.length" class="py-8 flex justify-center">
            <div class="w-6 h-6 border-2 border-primary-500 border-t-transparent rounded-full animate-spin" />
          </div>

          <!-- Empty -->
          <div
            v-else-if="!store.notifications.length"
            class="py-10 text-center"
          >
            <BellSlashIcon class="w-8 h-8 text-gray-300 mx-auto mb-2" />
            <p class="text-sm text-gray-400">No notifications yet</p>
          </div>

          <!-- List -->
          <ul v-else class="divide-y divide-gray-50 max-h-[360px] overflow-y-auto overscroll-contain">
            <li
              v-for="notif in store.notifications"
              :key="notif.id"
              class="flex items-start gap-3 px-4 py-3 cursor-pointer transition-colors duration-150 group"
              :class="notif.is_read
                ? 'bg-white hover:bg-gray-50'
                : 'bg-red-50/40 hover:bg-red-50/70'"
              @click.stop="handleClick(notif)"
            >
              <!-- Unread indicator dot -->
              <div class="mt-1.5 flex-shrink-0">
                <span
                  class="block w-2 h-2 rounded-full transition-colors"
                  :class="notif.is_read ? 'bg-gray-200' : 'bg-red-500 animate-pulse'"
                />
              </div>

              <!-- Content -->
              <div class="flex-1 min-w-0">
                <p
                  class="text-sm leading-snug line-clamp-2"
                  :class="notif.is_read ? 'text-gray-500' : 'text-gray-800 font-medium'"
                >
                  {{ notif.message }}
                </p>
                <p class="text-[11px] text-gray-400 mt-0.5">{{ timeAgo(notif.created_at) }}</p>
              </div>

              <!-- Arrow on hover -->
              <ChevronRightIcon class="w-4 h-4 text-gray-300 group-hover:text-primary-400 transition-colors flex-shrink-0 mt-1" />
            </li>
          </ul>

          <!-- Footer: load more -->
          <div v-if="store.hasMore" class="border-t border-gray-100 px-4 py-2.5 text-center">
            <button
              class="text-xs text-primary-600 hover:text-primary-800 font-medium transition-colors"
              :disabled="store.loading"
              @click.stop="loadMore"
            >
              {{ store.loading ? 'Loading…' : 'Load more' }}
            </button>
          </div>
        </div>
      </Transition>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useRouter } from 'vue-router'
import { BellIcon, BellSlashIcon, ChevronRightIcon } from '@heroicons/vue/24/outline'
import { useNotificationsStore } from '../../stores/notifications'

const props = defineProps({
  open:      { type: Boolean, default: false },
  /** Pass the bell button element so we can position the panel correctly */
  anchorEl:  { type: Object, default: null },
})
const emit = defineEmits(['close'])

const store      = useNotificationsStore()
const router     = useRouter()
const markingAll = ref(false)
const currentPage = ref(1)

// ── Dynamic position: anchor below the bell button ───────────────────────────
const panelStyle = computed(() => {
  if (props.anchorEl) {
    const rect = props.anchorEl.getBoundingClientRect()
    return {
      top:   `${rect.bottom + 8}px`,
      right: `${window.innerWidth - rect.right}px`,
    }
  }
  // Fallback: top-right below the standard header (h-16 = 64px)
  return { top: '72px', right: '16px' }
})

// Load/refresh notifications when panel opens
watch(() => props.open, (val) => {
  if (val) {
    currentPage.value = 1
    store.fetchNotifications(1)
  }
})

async function loadMore() {
  currentPage.value++
  await store.fetchNotifications(currentPage.value)
}

async function markAllRead() {
  markingAll.value = true
  try {
    await store.markAllRead()
  } finally {
    markingAll.value = false
  }
}

async function handleClick(notif) {
  if (!notif.is_read) {
    await store.markRead(notif.id)
  }
  emit('close')
  const noteId = notif.note_id ?? notif.note?.id
  if (noteId) {
    router.push(`/notes/${noteId}/view`)
  }
}

// Relative time helper
function timeAgo(dateStr) {
  if (!dateStr) return ''
  const diff = Math.floor((Date.now() - new Date(dateStr).getTime()) / 1000)
  if (diff < 60)     return 'Just now'
  if (diff < 3600)   return `${Math.floor(diff / 60)}m ago`
  if (diff < 86400)  return `${Math.floor(diff / 3600)}h ago`
  if (diff < 604800) return `${Math.floor(diff / 86400)}d ago`
  return new Date(dateStr).toLocaleDateString()
}
</script>
