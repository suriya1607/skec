import api from '../utils/axios'
import { API_BASE_URL } from '../utils/constants'

export const freeNotesApi = {
  list: (params) => api.get('/free-notes', { params }),

  // Returns the full public stream URL for a free note (opens in browser PDF viewer)
  streamUrl: (id) => `${API_BASE_URL}/free-notes/${id}/stream`,
}
