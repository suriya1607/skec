import api from '../../utils/axios'

export const adminReviewsApi = {
  list:         (params) => api.get('/admin/reviews', { params }),
  pendingCount: ()       => api.get('/admin/reviews/pending-count'),
  approve:      (id)     => api.patch(`/admin/reviews/${id}/approve`),
  reject:       (id, note) => api.patch(`/admin/reviews/${id}/reject`, { admin_note: note }),
  destroy:      (id)     => api.delete(`/admin/reviews/${id}`),
}
