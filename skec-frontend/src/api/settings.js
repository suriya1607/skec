import api from '../utils/axios'

export const settingsApi = {
  getPublic: () => api.get('/settings/public'),
}
