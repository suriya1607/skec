import api from '../../utils/axios'

export const adminCategoriesApi = {
  list:   ()                   => api.get('/admin/categories'),
  create: (data)               => api.post('/admin/categories', data),
  update: (id, data)           => api.patch(`/admin/categories/${id}`, data),
  delete: (id, securityKey)    => api.delete(`/admin/categories/${id}`, { data: { security_key: securityKey } }),
}
