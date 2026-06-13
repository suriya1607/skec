import api from '../../utils/axios'

export const reviewsApi = {
  getMyReview:   ()     => api.get('/reviews/mine'),
  submit:        (data) => api.post('/reviews', data),
  getPublic:     ()     => api.get('/reviews/public'),
}
