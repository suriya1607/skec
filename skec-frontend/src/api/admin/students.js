import api from '../../utils/axios'

export const adminStudentsApi = {
  list:       (params) => api.get('/admin/students', { params }),
  get:        (id)     => api.get(`/admin/students/${id}`),
  update:     (id, data) => api.patch(`/admin/students/${id}`, data),
  delete:     (id)     => api.delete(`/admin/students/${id}`),
  forceLogout:(id)     => api.post(`/admin/students/${id}/logout`),
}
