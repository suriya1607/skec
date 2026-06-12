import api from '../../utils/axios'

export const adminAnnouncementsApi = {
  list:    (params) => api.get('/admin/announcements', { params }),
  create:  (data)   => api.post('/admin/announcements', data),
  delete:  (id)     => api.delete(`/admin/announcements/${id}`),
  resend:  (id)     => api.post(`/admin/announcements/${id}/resend`),
}
