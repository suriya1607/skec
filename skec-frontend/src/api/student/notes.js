import api from '../../utils/axios'

export const notesApi = {
  list:       (params) => api.get('/notes', { params }),
  streamToken:(id)     => api.get(`/notes/${id}/stream-token`),
  logAccess:  (id, data) => api.post(`/notes/${id}/log`, data),
  categories: ()       => api.get('/categories'),
  subjects:   ()       => api.get('/subjects'),
}
