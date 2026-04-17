import axios from 'axios'
import { API_BASE_URL } from './constants'

const api = axios.create({
  baseURL: API_BASE_URL,
  // withCredentials: true,
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json',
  },
})

// Request interceptor: attach auth + session tokens
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('skec_token')
  const sessionToken = localStorage.getItem('skec_session_token')

  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  if (sessionToken) {
    config.headers['X-Session-Token'] = sessionToken
  }
  return config
})

// Response interceptor: handle global errors
api.interceptors.response.use(
  (response) => response,
  (error) => {
    const status = error.response?.status
    const errorCode = error.response?.data?.error

    if (status === 401) {
      if (errorCode === 'session_expired') {
        localStorage.removeItem('skec_token')
        localStorage.removeItem('skec_session_token')
        localStorage.removeItem('skec_user')

        // Show toast via custom event (avoids circular pinia import)
        window.dispatchEvent(new CustomEvent('skec:session-expired'))
        window.location.href = '/login'
      } else if (errorCode === 'account_inactive') {
        localStorage.removeItem('skec_token')
        localStorage.removeItem('skec_session_token')
        localStorage.removeItem('skec_user')
        window.dispatchEvent(new CustomEvent('skec:account-inactive'))
        window.location.href = '/login'
      }
    }

    if (status === 403 && errorCode === 'forbidden') {
      window.location.href = '/unauthorized'
    }

    return Promise.reject(error)
  },
)

export default api
