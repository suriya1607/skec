import api from '../../utils/axios'

export const adminStudentsApi = {
  list: (params) => api.get('/admin/students', { params }),
  export: (params) => api.get('/admin/students/export', { params, responseType: 'blob' }),
  get: (id) => api.get(`/admin/students/${id}`),
  downloadPhoto: (id) => api.get(`/admin/students/${id}/photo`, { responseType: 'blob' }),
  update: (id, data) => api.patch(`/admin/students/${id}`, data),
  delete: (id) => api.delete(`/admin/students/${id}`),
  forceLogout: (id) => api.post(`/admin/students/${id}/logout`),
  profileUpdate: (id, data) => api.patch(`/admin/students/profile/${id}`, data),
}