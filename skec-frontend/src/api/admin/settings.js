import api from '../../utils/axios'

export const adminSettingsApi = {
  get:    ()     => api.get('/admin/settings'),
  update: (data) => api.post('/admin/settings', { settings: data }),
}
