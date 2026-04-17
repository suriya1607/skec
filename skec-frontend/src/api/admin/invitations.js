import api from '../../utils/axios'

export const adminInvitationsApi = {
  list:   (params) => api.get('/admin/invitations', { params }),
  create: (data)   => api.post('/admin/invitations', data),
  delete: (id)     => api.delete(`/admin/invitations/${id}`),
  resend: (id)     => api.post(`/admin/invitations/${id}/resend`),
}
