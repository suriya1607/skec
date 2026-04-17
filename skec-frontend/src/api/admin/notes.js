import api from '../../utils/axios'

export const adminNotesApi = {
  list:         (params) => api.get('/admin/notes', { params }),
  get:          (id)     => api.get(`/admin/notes/${id}`),
  upload:       (data, onProgress) => api.post('/admin/notes', data, {
    headers: { 'Content-Type': 'multipart/form-data' },
    onUploadProgress: onProgress,
  }),
  update:       (id, data) => api.patch(`/admin/notes/${id}`, data),
  delete:       (id)     => api.delete(`/admin/notes/${id}`),
  toggleStatus: (id)     => api.patch(`/admin/notes/${id}/status`),
}
