import { defineStore } from 'pinia'
import { ref } from 'vue'
import { notificationsApi } from '../api/student/notifications'

export const useNotificationsStore = defineStore('notifications', () => {
  const unreadCount     = ref(0)
  const notifications   = ref([])
  const loading         = ref(false)
  const hasMore         = ref(false)
  let   _pollTimer      = null

  // ─── Fetch unread count (lightweight, used for badge) ──────────────────────
  async function fetchUnreadCount() {
    try {
      const res = await notificationsApi.unreadCount()
      unreadCount.value = res.data.data.count ?? 0
    } catch {
      // silently fail — badge is non-critical
    }
  }

  // ─── Fetch full notification list ──────────────────────────────────────────
  async function fetchNotifications(page = 1) {
    loading.value = true
    try {
      const res = await notificationsApi.list({ page })
      if (page === 1) {
        notifications.value = res.data.data
      } else {
        notifications.value.push(...res.data.data)
      }
      const meta = res.data.meta
      hasMore.value = meta ? meta.current_page < meta.last_page : false
    } finally {
      loading.value = false
    }
  }

  // ─── Mark one notification as read ─────────────────────────────────────────
  async function markRead(id) {
    try {
      await notificationsApi.markRead(id)
      const n = notifications.value.find((n) => n.id === id)
      if (n && !n.is_read) {
        n.is_read = true
        unreadCount.value = Math.max(0, unreadCount.value - 1)
      }
    } catch { /* ignore */ }
  }

  // ─── Mark all notifications as read ────────────────────────────────────────
  async function markAllRead() {
    try {
      await notificationsApi.markAllRead()
      // Optimistically update local state
      notifications.value.forEach((n) => (n.is_read = true))
      unreadCount.value = 0
    } catch (err) {
      // Re-sync from server on failure
      await fetchUnreadCount()
    }
  }

  // ─── Start polling unread count every 60 seconds ───────────────────────────
  function startPolling() {
    if (_pollTimer) return
    fetchUnreadCount()
    _pollTimer = setInterval(fetchUnreadCount, 60_000)
  }

  function stopPolling() {
    if (_pollTimer) {
      clearInterval(_pollTimer)
      _pollTimer = null
    }
  }

  return {
    unreadCount, notifications, loading, hasMore,
    fetchUnreadCount, fetchNotifications,
    markRead, markAllRead,
    startPolling, stopPolling,
  }
})
