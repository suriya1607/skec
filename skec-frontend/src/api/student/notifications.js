import api from '../../utils/axios'

export const notificationsApi = {
  list:         (params) => api.get('/notifications', { params }),
  unreadCount:  ()       => api.get('/notifications/unread-count'),
  markRead:     (id)     => api.post(`/notifications/${id}/read`),
  markAllRead:  ()       => api.post('/notifications/read-all'),
}
