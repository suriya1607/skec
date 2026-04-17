import api from '../utils/axios'

export const authApi = {
  login: (credentials) => api.post('/auth/login', credentials),
  logout: () => api.post('/auth/logout'),
  me: () => api.get('/auth/me'),
  validateInvitation: (token) => api.get(`/auth/invitation/${token}`),
  register: (data) => api.post('/auth/register', data),
}
