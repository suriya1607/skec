import api from '../utils/axios'
import { API_BASE_URL } from '../utils/constants'

export const freeNotesApi = {
  list: () => api.get('/free-notes'),

  // Returns the full public stream URL for a free note (opens in browser PDF viewer)
  streamUrl: (id) => `${API_BASE_URL}/free-notes/${id}/stream`,
}
