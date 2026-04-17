import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { authApi } from '../api/auth'
import { getInitials } from '../utils/helpers'

export const useAuthStore = defineStore('auth', () => {
  const user         = ref(JSON.parse(localStorage.getItem('skec_user') || 'null'))
  const token        = ref(localStorage.getItem('skec_token') || null)
  const sessionToken = ref(localStorage.getItem('skec_session_token') || null)
  const isLoading    = ref(false)

  const isLoggedIn = computed(() => !!token.value && !!user.value)
  const isAdmin    = computed(() => user.value?.role === 'admin')
  const isStudent  = computed(() => user.value?.role === 'student')
  const userInitials = computed(() => getInitials(user.value?.name || ''))

  function _persist(userData, tok, sessionTok) {
    user.value         = userData
    token.value        = tok
    sessionToken.value = sessionTok
    localStorage.setItem('skec_user',          JSON.stringify(userData))
    localStorage.setItem('skec_token',         tok)
    localStorage.setItem('skec_session_token', sessionTok)
  }

  function _clear() {
    user.value         = null
    token.value        = null
    sessionToken.value = null
    localStorage.removeItem('skec_user')
    localStorage.removeItem('skec_token')
    localStorage.removeItem('skec_session_token')
  }

  async function login(credentials) {
    isLoading.value = true
    try {
      const res = await authApi.login(credentials)
      const { user: u, token: t, session_token: s } = res.data.data
      _persist(u, t, s)
      return res.data
    } finally {
      isLoading.value = false
    }
  }

  async function logout() {
    try {
      await authApi.logout()
    } catch {
      // Clear regardless of API error
    } finally {
      _clear()
    }
  }

  async function fetchMe() {
    const res = await authApi.me()
    user.value = res.data.data.user
    localStorage.setItem('skec_user', JSON.stringify(user.value))
    return user.value
  }

  async function initializeAuth() {
    if (!token.value) return
    try {
      await fetchMe()
    } catch {
      _clear()
    }
  }

  function setFromRegister(userData, tok, sessionTok) {
    _persist(userData, tok, sessionTok)
  }

  return {
    user, token, sessionToken, isLoading,
    isLoggedIn, isAdmin, isStudent, userInitials,
    login, logout, fetchMe, initializeAuth, setFromRegister,
  }
})
