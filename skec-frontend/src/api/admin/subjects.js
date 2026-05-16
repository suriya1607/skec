import api from '../../utils/axios'

export const adminSubjectsApi = {
  list:   ()         => api.get('/admin/subjects'),
  create: (data)     => api.post('/admin/subjects', data),
  update: (id, data) => api.patch(`/admin/subjects/${id}`, data),
  delete: (id)       => api.delete(`/admin/subjects/${id}`),
}
