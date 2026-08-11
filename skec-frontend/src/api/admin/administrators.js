import api from '../../utils/axios'

export const adminAdministratorsApi = {
  list: (params) => api.get('/admin/administrators', { params }),
  get: (id) => api.get(`/admin/administrators/${id}`),
  create: (data) => api.post('/admin/administrators', data),
  update: (id, data) => api.patch(`/admin/administrators/${id}`, data),
  delete: (id) => api.delete(`/admin/administrators/${id}`),
  toggleStatus: (id) => api.patch(`/admin/administrators/${id}/status`),
}
