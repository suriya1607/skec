import api from '../utils/axios'

export const contactApi = {
  sendMessage: (data) => api.post('/contact', data),
}
