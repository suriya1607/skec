import api from '../../utils/axios'

export const adminSessionsApi = {
  list:   (params) => api.get('/admin/sessions', { params }),
  delete: (id)     => api.delete(`/admin/sessions/${id}`),
}
